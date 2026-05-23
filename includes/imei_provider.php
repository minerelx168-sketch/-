<?php
declare(strict_types=1);

/**
 * Calls an external IMEI lookup provider and normalizes the response.
 *
 * Currently supports:
 *   - sickw   (https://sickw.com/api.php?key=...&imei=...&service=...&format=beta)
 *   - imei.info / imeicheck-style (generic JSON: { success, properties: { brand, model, ... } })
 *
 * Returns:
 *   [
 *     'status'  => 'success' | 'failed',
 *     'brand'   => string|null,
 *     'model'   => string|null,
 *     'details' => array,        // any extra k/v pairs to render
 *     'raw'     => string,       // raw provider response
 *     'error'   => string|null,
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
        ];
    }

    $service = $service ?: (string) $api['default_service'];

    $params = match ($api['provider']) {
        'sickw' => [
            'format'  => 'beta',
            'key'     => $api['key'],
            'imei'    => $imei,
            'service' => $service,
        ],
        default => [
            'key'     => $api['key'],
            'imei'    => $imei,
            'service' => $service,
        ],
    };

    $url = $api['url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => 'imeicheck/1.0',
        CURLOPT_FOLLOWLOCATION => true,
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
        ];
    }

    return imei_provider_parse($api['provider'], (string) $body);
}

function imei_provider_parse(string $provider, string $body): array
{
    $decoded = json_decode($body, true);

    if (is_array($decoded)) {
        // sickw beta format: { status: "success", result: "<html or text>", imei: ..., service: ... }
        $status = strtolower((string) ($decoded['status'] ?? ''));
        if ($status === 'success' || ($decoded['success'] ?? false) === true) {
            $brand = $decoded['brand']
                ?? $decoded['Brand']
                ?? ($decoded['properties']['brand'] ?? null);
            $model = $decoded['model']
                ?? $decoded['Model']
                ?? ($decoded['properties']['model'] ?? null);
            $resultText = $decoded['result'] ?? '';

            $details = imei_extract_details($resultText, $decoded);
            $brand = $brand ?: ($details['Brand Name'] ?? $details['Brand'] ?? null);
            $model = $model ?: ($details['Model Name'] ?? $details['Model'] ?? null);

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
            'error'  => $decoded['result'] ?? $decoded['message'] ?? 'Lookup failed.',
        ];
    }

    // Plain text fallback - sickw sometimes returns "IMEI: ...<br>Brand: ..."
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

    // Strip HTML and split on <br> or newlines.
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

    // Merge any structured properties the provider supplied directly.
    foreach (($decoded['properties'] ?? []) as $k => $v) {
        if (is_scalar($v)) {
            $details[ucwords(str_replace('_', ' ', (string) $k))] = (string) $v;
        }
    }

    return $details;
}
