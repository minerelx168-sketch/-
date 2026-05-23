<?php
declare(strict_types=1);

/**
 * Calls an external IMEI lookup provider and normalizes the response.
 *
 * Currently supports:
 *   - imei.info  (https://imei.info/api/check/{service}/{imei}/?API_KEY=...&format=json)
 *   - sickw      (https://sickw.com/api.php?format=beta&key=...&imei=...&service=...)
 *
 * Returns:
 *   [
 *     'status'  => 'success' | 'failed',
 *     'brand'   => string|null,
 *     'model'   => string|null,
 *     'details' => array,        // any extra k/v pairs to render
 *     'raw'     => string,       // raw provider response
 *     'error'   => string|null,
 *     'url'     => string,       // URL we hit (with key redacted)
 *   ]
 */
function imei_provider_lookup(string $imei, ?string $service = null): array
{
    $cfg = require __DIR__ . '/config.php';
    $api = $cfg['api'];

    if ($api['key'] === '' || str_starts_with($api['key'], 'replace-with')) {
        return [
            'status' => 'failed',
            'brand'  => null,
            'model'  => null,
            'details'=> [],
            'raw'    => '',
            'error'  => 'API key not configured. Set IMEI_API_KEY in your .env file.',
            'url'    => '',
        ];
    }

    $service = $service ?: (string) $api['default_service'];
    $url     = imei_provider_build_url($api, $imei, $service);
    $redacted = preg_replace('/(API_KEY|key)=[^&]*/i', '$1=***', $url) ?? $url;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => 'imeicheck/1.0',
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

        if ($statusStr === 'success' || $statusStr === 'ok' || $statusStr === 'true') {
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
        $err = $decoded['response']
            ?? $decoded['error']
            ?? $decoded['message']
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
