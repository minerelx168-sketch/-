<?php
declare(strict_types=1);

/**
 * Calls an external IMEI lookup provider and normalizes the response.
 *
 * Supports both the synchronous PHP API (sub-60s services like
 * blacklist / iCloud / warranty) and the asynchronous DHRU Fusion API
 * (slow services like GSX Picture - manual fulfillment, 1-5+ min).
 *
 * For PHP services this is a single HTTP round-trip returning a
 * status=success result. For DHRU services we place an order and
 * return status=processing along with a `provider_order_id`; the
 * caller persists that and polls imei_provider_query_dhru() to drive
 * the usage from PROCESSING -> SUCCESS/FAILED.
 *
 * Returns:
 *   [
 *     'status'             => 'success' | 'failed' | 'processing',
 *     'brand'              => string|null,
 *     'model'              => string|null,
 *     'details'            => array,
 *     'provider_order_id'  => string|null,  // DHRU reference id
 *     'raw'                => string,
 *     'error'              => string|null,
 *     'url'                => string,       // key redacted
 *   ]
 */
function imei_provider_lookup(string $imei, ?string $service = null, string $apiType = 'php'): array
{
    $cfg = require __DIR__ . '/config.php';
    $api = $cfg['api'];

    // Offline demo / simulator - no external call, returns realistic data
    // from a small TAC database. Useful for previews and screenshots.
    if (strtolower((string) $api['provider']) === 'demo') {
        require_once __DIR__ . '/imei_demo.php';
        return imei_demo_lookup($imei, $service ?: (string) $api['default_service']);
    }

    if ($api['key'] === '' || str_starts_with($api['key'], 'replace-with')) {
        return [
            'status'            => 'failed',
            'brand'             => null,
            'model'             => null,
            'details'           => [],
            'provider_order_id' => null,
            'raw'               => '',
            'error'             => 'API key not configured. Set IMEI_API_KEY in your .env file.',
            'url'               => '',
        ];
    }

    $service = $service ?: (string) $api['default_service'];

    // Async (DHRU): place an order and hand the reference id back to
    // the caller. The caller is responsible for storing the id +
    // polling for completion via imei_provider_query_dhru().
    if (strtolower($apiType) === 'dhru') {
        return imei_provider_place_dhru($api, $imei, $service);
    }

    $url     = imei_provider_build_url($api, $imei, $service);
    $redacted = preg_replace('/(API_KEY|key|apiaccesskey)=[^&]*/i', '$1=***', $url) ?? $url;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 65, // PHP API guarantees response within 60s
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => 'imeihub/1.0',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => ['Accept: application/json, text/plain, */*'],
    ]);
    $body     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($body === false || $httpCode >= 500) {
        return [
            'status' => 'failed',
            'brand'  => null,
            'model'  => null,
            'details'=> [],
            'raw'    => (string) $body,
            'error'  => $curlErr ?: ('Provider returned HTTP ' . $httpCode),
            'url'    => $redacted,
        ];
    }

    $parsed = imei_provider_parse(strtolower((string) $api['provider']), (string) $body);
    $parsed['url'] = $redacted;
    return $parsed;
}

function imei_provider_build_url(array $api, string $imei, string $service): string
{
    $provider = strtolower((string) $api['provider']);
    $base     = rtrim((string) $api['url'], '/');

    if ($provider === 'imei.info' || $provider === 'imeiinfo') {
        // Path-based: https://imei.info/api/check/{service}/{imei}/?API_KEY=...&format=json
        return sprintf(
            '%s/%s/%s/?%s',
            $base,
            rawurlencode($service),
            rawurlencode($imei),
            http_build_query(['API_KEY' => $api['key'], 'format' => 'json'])
        );
    }

    if ($provider === 'unlock-service' || $provider === 'unlockservice') {
        // Query-based: https://api.unlock-service.net/?service=X&imei=Y&key=Z
        return $base . '/?' . http_build_query([
            'service' => $service,
            'imei'    => $imei,
            'key'     => $api['key'],
        ]);
    }

    // sickw and other query-string providers
    return $base . '?' . http_build_query([
        'format'  => 'beta',
        'key'     => $api['key'],
        'imei'    => $imei,
        'service' => $service,
    ]);
}

function imei_provider_parse(string $provider, string $body): array
{
    $decoded = json_decode($body, true);

    if (is_array($decoded)) {
        // Normalize the success flag across providers:
        //   imei.info  -> { "result": "success", "response": "<text/html>" | { ... } }
        //   sickw beta -> { "status": "success", "result": "<text/html>" }
        $statusRaw = $decoded['status']
            ?? $decoded['result']
            ?? ($decoded['success'] ?? false);
        $statusStr = is_bool($statusRaw)
            ? ($statusRaw ? 'success' : 'failed')
            : strtolower((string) $statusRaw);

        if (in_array($statusStr, ['success', 'successful', 'ok', 'true', 'done', 'completed'], true)) {
            // The body of the report can live under different keys.
            $bodyField = $decoded['response']
                ?? $decoded['result']
                ?? $decoded['data']
                ?? '';

            // If "response" is itself a structured object, fold it into details too.
            $extra = [];
            if (is_array($bodyField)) {
                foreach ($bodyField as $k => $v) {
                    if (is_scalar($v)) {
                        $extra[(string) $k] = (string) $v;
                    }
                }
                $bodyText = '';
            } else {
                $bodyText = (string) $bodyField;
            }

            $details = imei_extract_details($bodyText, $decoded);
            $details = array_merge($details, $extra);

            // unlock-service.net returns structured data in 'object' field.
            if (isset($decoded['object']) && is_array($decoded['object'])) {
                foreach ($decoded['object'] as $k => $v) {
                    if (is_scalar($v)) {
                        $label = ucwords(str_replace(['_', '-'], ' ', preg_replace('/([a-z])([A-Z])/', '$1 $2', (string) $k)));
                        $details[$label] = is_bool($v) ? ($v ? 'Yes' : 'No') : (string) $v;
                    }
                }
            }

            $brand = $decoded['brand']
                ?? $decoded['Brand']
                ?? ($decoded['properties']['brand'] ?? null)
                ?? $details['Brand Name'] ?? $details['Brand'] ?? $details['Manufacturer'] ?? null;
            $model = $decoded['model']
                ?? $decoded['Model']
                ?? ($decoded['properties']['model'] ?? null)
                ?? $details['Model Name'] ?? $details['Model'] ?? $details['Model Description'] ?? null;

            return [
                'status' => 'success',
                'brand'  => $brand,
                'model'  => $model,
                'details'=> $details,
                'raw'    => $body,
                'error'  => null,
            ];
        }

        // Failed JSON response - try to surface the provider's reason.
        $err = $decoded['error']
            ?? $decoded['message']
            ?? $decoded['response']
            ?? $decoded['result']
            ?? 'Lookup failed.';
        if (is_array($err)) {
            $err = json_encode($err, JSON_UNESCAPED_SLASHES);
        }
        return [
            'status' => 'failed',
            'brand'  => null,
            'model'  => null,
            'details'=> [],
            'raw'    => $body,
            'error'  => (string) $err,
        ];
    }

    // Plain text / HTML fallback - some providers return raw "Key: Value\n..." bodies.
    $details = imei_extract_details($body, []);
    $brand   = $details['Brand Name'] ?? $details['Brand'] ?? $details['Manufacturer'] ?? null;
    $model   = $details['Model Name'] ?? $details['Model'] ?? $details['Model Description'] ?? null;

    if ($brand || $model) {
        return [
            'status' => 'success',
            'brand'  => $brand,
            'model'  => $model,
            'details'=> $details,
            'raw'    => $body,
            'error'  => null,
        ];
    }

    return [
        'status' => 'failed',
        'brand'  => null,
        'model'  => null,
        'details'=> [],
        'raw'    => $body,
        'error'  => 'Could not parse provider response.',
    ];
}

/**
 * Pulls "Key: Value" pairs out of a result body (HTML, plain text or JSON).
 */
function imei_extract_details(string $text, array $decoded): array
{
    $details = [];

    $clean = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $text));
    foreach (preg_split('/\r?\n/', $clean) as $line) {
        $line = trim($line);
        if ($line === '' || !str_contains($line, ':')) {
            continue;
        }
        [$k, $v] = array_map('trim', explode(':', $line, 2));
        if ($k !== '' && $v !== '') {
            $details[$k] = $v;
        }
    }

    foreach (($decoded['properties'] ?? []) as $k => $v) {
        if (is_scalar($v)) {
            $details[ucwords(str_replace('_', ' ', (string) $k))] = (string) $v;
        }
    }

    return $details;
}

/* =========================================================================
 *  DHRU async API (placeimeiorder + getimeiorder)
 *
 *  The provider documents two flavours of their API: a synchronous PHP
 *  endpoint that returns the report inline (under 60s) and an async
 *  DHRU Fusion endpoint that queues the order and returns a reference
 *  id. Slow services (GSX Picture, manual fulfillment) are DHRU-only.
 *
 *  Standard DHRU shape:
 *    {base}/?username=X&apiaccesskey=Y&action=placeimeiorder
 *           &service=Z&imei=W
 *      -> { "SUCCESS": [{ "REFERENCEID": "abc", "STATUS": "...", ... }] }
 *      -> { "ERROR":   [{ "FULL_DESCRIPTION": "...", ... }] }
 *
 *    {base}/?username=X&apiaccesskey=Y&action=getimeiorder&id=abc
 *      -> { "SUCCESS": [{ "STATUS": "Successful|Rejected|Processing",
 *                         "REPLY":  "<report text>" }] }
 *
 *  Some installs use camelCase keys / different status verbs - the
 *  parser below tries a few common shapes.
 * ========================================================================= */

function imei_provider_place_dhru(array $api, string $imei, string $service): array
{
    $base = rtrim((string) $api['url'], '/');
    $url  = $base . '/?' . http_build_query([
        'username'     => (string) ($api['username'] ?? ''),
        'apiaccesskey' => (string) ($api['dhru_key'] ?? $api['key']),
        'action'       => 'placeimeiorder',
        'service'      => $service,
        'imei'         => $imei,
    ]);
    $redacted = preg_replace('/(apiaccesskey|key)=[^&]*/i', '$1=***', $url) ?? $url;

    [$body, $err] = imei_provider_http_get($url);
    if ($body === null) {
        return imei_provider_dhru_fail($err ?: 'DHRU network error', $redacted);
    }

    $decoded = json_decode($body, true);

    // SUCCESS shape - pluck the reference id wherever it lives.
    $refId = imei_provider_dhru_pluck($decoded, ['REFERENCEID', 'referenceid', 'reference_id', 'orderid', 'OrderID', 'id']);
    if ($refId !== '') {
        return [
            'status'            => 'processing',
            'brand'             => null,
            'model'             => null,
            'details'           => [],
            'provider_order_id' => $refId,
            'raw'               => $body,
            'error'             => null,
            'url'               => $redacted,
        ];
    }

    // Failure - surface whatever description the provider gave us.
    $msg = imei_provider_dhru_pluck($decoded, ['FULL_DESCRIPTION', 'MESSAGE', 'message', 'error', 'description'])
        ?: 'DHRU rejected the order.';
    return imei_provider_dhru_fail((string) $msg, $redacted, $body);
}

/**
 * Poll a DHRU order. Returns the canonical lookup-result shape with
 * status set to one of:
 *   'success'    - REPLY parsed into details + brand/model
 *   'failed'     - provider rejected the order, error is set
 *   'processing' - still in flight, caller should leave the usage in
 *                  PROCESSING and try again later
 */
function imei_provider_query_dhru(string $referenceId): array
{
    $cfg = require __DIR__ . '/config.php';
    $api = $cfg['api'];
    $base = rtrim((string) $api['url'], '/');
    $url  = $base . '/?' . http_build_query([
        'username'     => (string) ($api['username'] ?? ''),
        'apiaccesskey' => (string) ($api['dhru_key'] ?? $api['key']),
        'action'       => 'getimeiorder',
        'id'           => $referenceId,
    ]);
    $redacted = preg_replace('/(apiaccesskey|key)=[^&]*/i', '$1=***', $url) ?? $url;

    [$body, $err] = imei_provider_http_get($url);
    if ($body === null) {
        // Network blip - keep the order processing, caller will retry.
        return [
            'status'            => 'processing',
            'brand'             => null,
            'model'             => null,
            'details'           => [],
            'provider_order_id' => $referenceId,
            'raw'               => '',
            'error'             => $err ?: 'DHRU poll network error',
            'url'               => $redacted,
        ];
    }

    $decoded = json_decode($body, true);
    $statusStr = strtolower((string) imei_provider_dhru_pluck($decoded, ['STATUS', 'status']));
    $reply     = (string)         imei_provider_dhru_pluck($decoded, ['REPLY',  'reply', 'response', 'output']);

    // Successful terminal states.
    if (in_array($statusStr, ['successful', 'success', 'done', 'complete', 'completed'], true)) {
        $details = imei_extract_details($reply, is_array($decoded) ? $decoded : []);
        $brand   = $details['Brand Name'] ?? $details['Brand'] ?? $details['Manufacturer'] ?? null;
        $model   = $details['Model Name'] ?? $details['Model'] ?? $details['Model Description'] ?? null;
        return [
            'status'            => 'success',
            'brand'             => $brand,
            'model'             => $model,
            'details'           => $details,
            'provider_order_id' => $referenceId,
            'raw'               => $body,
            'error'             => null,
            'url'               => $redacted,
        ];
    }

    // Failed terminal states - refund.
    if (in_array($statusStr, ['rejected', 'failed', 'cancelled', 'canceled', 'error'], true)) {
        $msg = imei_provider_dhru_pluck($decoded, ['FULL_DESCRIPTION', 'MESSAGE', 'message', 'description'])
            ?: $reply ?: 'DHRU returned status: ' . $statusStr;
        return imei_provider_dhru_fail((string) $msg, $redacted, $body, $referenceId);
    }

    // Still in flight (Processing / Submitted / Pending / In Queue).
    return [
        'status'            => 'processing',
        'brand'             => null,
        'model'             => null,
        'details'           => [],
        'provider_order_id' => $referenceId,
        'raw'               => $body,
        'error'             => null,
        'url'               => $redacted,
    ];
}

/* ---- DHRU helpers ---- */

function imei_provider_http_get(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => 'imeihub/1.0',
        CURLOPT_HTTPHEADER     => ['Accept: application/json, text/plain, */*'],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($body === false || $code >= 500) return [null, $err ?: ('HTTP ' . $code)];
    return [(string) $body, null];
}

function imei_provider_dhru_pluck($decoded, array $keys): string
{
    if (!is_array($decoded)) return '';
    // Walk the common DHRU envelopes: SUCCESS[0] / ERROR[0] / data / root.
    $candidates = [
        $decoded['SUCCESS'][0] ?? null,
        $decoded['ERROR'][0]   ?? null,
        $decoded['success'][0] ?? null,
        $decoded['error'][0]   ?? null,
        $decoded['data']       ?? null,
        $decoded,
    ];
    foreach ($candidates as $node) {
        if (!is_array($node)) continue;
        foreach ($keys as $k) {
            if (isset($node[$k]) && is_scalar($node[$k]) && (string) $node[$k] !== '') {
                return (string) $node[$k];
            }
        }
    }
    return '';
}

function imei_provider_dhru_fail(string $msg, string $url, string $body = '', ?string $refId = null): array
{
    return [
        'status'            => 'failed',
        'brand'             => null,
        'model'             => null,
        'details'           => [],
        'provider_order_id' => $refId,
        'raw'               => $body,
        'error'             => $msg,
        'url'               => $url,
    ];
}
