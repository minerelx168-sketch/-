<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/imei_provider.php';
require __DIR__ . '/../includes/blacklist.php';

function respond(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$imeiInput = $_POST['imei'] ?? $_GET['imei'] ?? '';
$service   = $_POST['service'] ?? $_GET['service'] ?? null;
$imei      = imei_normalize((string) $imeiInput);

if ($imei === '') {
    respond(400, ['ok' => false, 'error' => 'Please enter an IMEI number.']);
}

if (!imei_is_valid($imei)) {
    respond(422, ['ok' => false, 'error' => 'Invalid IMEI. Make sure it has 15 digits and passes the Luhn check.']);
}

if (!rate_limit_allow(client_ip(), 10)) {
    respond(429, ['ok' => false, 'error' => 'Too many requests. Please try again in a minute.']);
}

$cached = lookup_get_cached($imei);
if ($cached) {
    $details = json_decode($cached['raw_response'] ?? 'null', true);
    respond(200, [
        'ok'      => true,
        'cached'  => true,
        'imei'    => $imei,
        'tac'     => $cached['tac'],
        'brand'   => $cached['brand'],
        'model'   => $cached['model'],
        'details' => is_array($details) ? ($details['details'] ?? []) : [],
        'blacklist' => blacklist_status($imei),
    ]);
}

$result = imei_provider_lookup($imei, is_string($service) ? $service : null);

$cfg = require __DIR__ . '/../includes/config.php';
lookup_save([
    'imei'         => $imei,
    'tac'          => imei_tac($imei),
    'provider'     => $cfg['api']['provider'],
    'service_id'   => $service,
    'status'       => $result['status'],
    'brand'        => $result['brand'],
    'model'        => $result['model'],
    'raw_response' => json_encode([
        'details' => $result['details'],
        'raw'     => substr($result['raw'], 0, 8000),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
]);

if ($result['status'] !== 'success') {
    respond(502, [
        'ok'    => false,
        'error' => $result['error'] ?: 'Lookup failed.',
    ]);
}

respond(200, [
    'ok'      => true,
    'cached'  => false,
    'imei'    => $imei,
    'tac'     => imei_tac($imei),
    'brand'   => $result['brand'],
    'model'   => $result['model'],
    'details' => $result['details'],
    'blacklist' => blacklist_status($imei),
]);
