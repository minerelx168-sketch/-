<?php
/**
 * Smoke-test the configured IMEI provider end-to-end.
 *
 * Usage:
 *   php scripts/test-provider.php <imei> [service_id]
 *
 * Example:
 *   php scripts/test-provider.php 359152060000003 1
 *
 * Reads provider + key + URL from .env, builds the request the same way
 * /api/services/use.php would, prints:
 *   - the built URL (with the API key REDACTED)
 *   - raw provider response (truncated to 2 KB for readability)
 *   - HTTP status
 *   - parsed result via imei_provider_parse() so you can see what the
 *     normalized brand / model / details look like before the front-end
 *     ever touches it
 *
 * Note: must be run from a host with outbound network access to your
 * provider. The Claude Code sandbox blocks outbound to most hosts, so
 * run this on your hosting / VPS instead.
 */

declare(strict_types=1);

require __DIR__ . '/../includes/imei_provider.php';
require __DIR__ . '/../includes/functions.php';

$imei    = $argv[1] ?? '';
$service = $argv[2] ?? null;

if ($imei === '') {
    fwrite(STDERR, "Usage: php scripts/test-provider.php <imei> [service_id]\n");
    exit(1);
}

$imei = imei_normalize($imei);
if (!imei_is_valid($imei)) {
    fwrite(STDERR, "Invalid IMEI: must be 15 digits and pass the Luhn check.\n");
    exit(2);
}

$cfg = require __DIR__ . '/../includes/config.php';
$api = $cfg['api'];

if ($api['key'] === '' || str_starts_with($api['key'], 'replace-with')) {
    fwrite(STDERR, "API key not configured. Set IMEI_API_KEY in .env.\n");
    exit(3);
}

$service = $service ?: (string) $api['default_service'];
$url     = imei_provider_build_url($api, $imei, $service);
$redacted = preg_replace('/(API_KEY|key)=[^&]*/i', '$1=***REDACTED***', $url);

echo "provider:      {$api['provider']}\n";
echo "service:       $service\n";
echo "imei:          $imei\n";
echo "request url:   $redacted\n\n";

$start = microtime(true);
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_USERAGENT      => 'imeihub-test/1.0',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTPHEADER     => ['Accept: application/json, text/plain, */*'],
]);
$body  = curl_exec($ch);
$code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err   = curl_error($ch);
$ms    = (microtime(true) - $start) * 1000;
curl_close($ch);

echo sprintf("http status:   %d  (%.0f ms)\n", $code, $ms);
if ($err !== '') {
    echo "curl error:    $err\n";
}
echo "---- raw body (first 2KB) ----\n";
echo substr((string) $body, 0, 2048);
if (strlen((string) $body) > 2048) {
    echo "\n... (truncated, total " . strlen((string) $body) . " bytes)";
}
echo "\n---- parsed ----\n";

$parsed = imei_provider_parse(strtolower((string) $api['provider']), (string) $body);

echo "status:    {$parsed['status']}\n";
echo "brand:     " . ($parsed['brand'] ?? '-') . "\n";
echo "model:     " . ($parsed['model'] ?? '-') . "\n";
echo "error:     " . ($parsed['error'] ?? '-') . "\n";
echo "details:\n";
foreach (($parsed['details'] ?? []) as $k => $v) {
    echo sprintf("  %-22s  %s\n", $k, is_scalar($v) ? $v : json_encode($v));
}
exit($parsed['status'] === 'success' ? 0 : 1);
