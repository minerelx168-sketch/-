<?php
declare(strict_types=1);

/**
 * Paid service usage endpoint.
 *
 *   POST /api/services/use.php
 *   Content-Type: application/json
 *   { "code": "BLACKLIST", "input": { "imei": "359..." } }
 *
 * Flow:
 *   1. Require session.
 *   2. credits_deduct() in a SERIALIZABLE tx: creates PENDING usage,
 *      deducts the cost atomically.
 *   3. Call the IMEI provider with the input.
 *   4. provider success -> credits_mark_usage_success(), return result.
 *      provider failure -> credits_refund_usage(), return error.
 *
 * The deduct is atomic and the refund is idempotent, so a slow client
 * retrying the same request CAN cost a user one extra deduct if they
 * retry before our refund commits. We accept that for now - the brief
 * says client-side idempotency is optional, and the dashboard's full
 * history makes any discrepancy auditable.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

require __DIR__ . '/../../includes/auth.php';
require __DIR__ . '/../../includes/credits_write.php';
require __DIR__ . '/../../includes/imei_provider.php';
require __DIR__ . '/../../includes/functions.php';
require __DIR__ . '/../../includes/blacklist.php';
require __DIR__ . '/../../includes/service_fields.php';

function fail(int $code, string $error, array $extra = []): never
{
    http_response_code($code);
    echo json_encode(array_merge(['ok' => false, 'error' => $error], $extra));
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    fail(405, 'POST only.');
}

$user = auth_user();
if (!$user) {
    fail(401, 'Not signed in.');
}

// Parse body (JSON or form-encoded).
$body = [];
$raw  = (string) file_get_contents('php://input');
$ct   = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
if (str_contains($ct, 'application/json') && $raw !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $body = $decoded;
} else {
    $body = $_POST;
}

$code  = isset($body['code'])  ? strtoupper(trim((string) $body['code'])) : '';
$input = isset($body['input']) ? $body['input'] : [];
if (!is_array($input)) $input = [];

if (!preg_match('/^[A-Z0-9_]{2,32}$/', $code)) {
    fail(400, 'Invalid service code.');
}

// All current services need an IMEI input. Validate it up-front so we don't
// charge the user for a guaranteed failure.
$imei = isset($input['imei']) ? imei_normalize((string) $input['imei']) : '';
if (!imei_is_valid($imei)) {
    fail(422, 'Invalid IMEI. Make sure it has 15 digits and passes the Luhn check.');
}
$input['imei'] = $imei; // canonicalized

// Rate limit on user id, not IP, since these are authenticated calls.
if (!rate_limit_allow('user:' . $user['id'], 30)) {
    fail(429, 'Too many requests. Please try again in a minute.');
}

// Phase 1: ATOMIC deduct (or InsufficientCreditError).
try {
    $usage = credits_deduct((int) $user['id'], $code, $input);
} catch (InsufficientCreditError $e) {
    fail(402, $e->getMessage(), ['error_code' => 'INSUFFICIENT_CREDIT']);
} catch (Throwable $e) {
    fail(500, $e->getMessage());
}

$publicId = (string) $usage['public_id'];
$cost     = (string) $usage['cost'];

// Phase 2: call the provider. Map our service_code -> upstream service.
// Entry shapes (data/service_provider_map.php):
//   null                          -> free local TAC lookup, no API call
//   123                           -> PHP API (sync)
//   ['id'=>123,'type'=>'dhru']    -> DHRU API (async), place order +
//                                    return status=processing; the
//                                    client then polls api/services/status.php
$serviceMap = require __DIR__ . '/../../data/service_provider_map.php';
$entry      = $serviceMap[$code] ?? null;

if ($entry === null) {
    $providerServiceId = null;
    $apiType           = 'php';
} elseif (is_array($entry)) {
    $providerServiceId = (string) ($entry['id']   ?? '');
    $apiType           = strtolower((string) ($entry['type'] ?? 'php'));
} else {
    $providerServiceId = (string) $entry;
    $apiType           = 'php';
}

try {
    if ($providerServiceId === null) {
        // Free tier: local TAC lookup, no external call.
        require_once __DIR__ . '/../../includes/imei_demo.php';
        $result = imei_demo_lookup($imei, '0');
    } else {
        $result = imei_provider_lookup($imei, $providerServiceId, $apiType);
    }
} catch (Throwable $e) {
    credits_refund_usage($publicId, 'provider exception: ' . $e->getMessage());
    fail(502, 'Lookup failed; your credit has been refunded.', [
        'public_id' => $publicId,
        'refunded'  => true,
    ]);
}

$status = (string) ($result['status'] ?? '');

// DHRU async: the order is placed, the wallet stays deducted, the row
// flips to PROCESSING with the provider reference id stamped. The
// client now polls /api/services/status.php?id=<public_id> for the
// final result (or a refund if the provider rejects it).
if ($status === 'processing') {
    credits_mark_usage_processing($publicId, (string) ($result['provider_order_id'] ?? ''));
    echo json_encode([
        'ok'                => true,
        'status'            => 'processing',
        'public_id'         => $publicId,
        'imei'              => $imei,
        'tac'               => imei_tac($imei),
        'cost'              => $cost,
        'provider_order_id' => $result['provider_order_id'] ?? null,
        'blacklist'         => blacklist_status($imei),
        // Suggested next-poll delay in seconds. The provider documents
        // 1-5 min turnaround so we use a wide initial gap; main.js
        // backs off further if /status.php returns processing again.
        'retry_after'       => 8,
    ]);
    exit;
}

if ($status !== 'success') {
    credits_refund_usage($publicId, (string) ($result['error'] ?? 'provider returned failure'));
    fail(502, (string) ($result['error'] ?? 'Lookup failed.'), [
        'public_id' => $publicId,
        'refunded'  => true,
    ]);
}

// Phase 3: persist + return.
credits_mark_usage_success($publicId, [
    'brand'   => $result['brand'],
    'model'   => $result['model'],
    'details' => $result['details'],
]);

$curated = service_result_has_template($code);

echo json_encode([
    'ok'              => true,
    'status'          => 'success',
    'public_id'       => $publicId,
    'imei'            => $imei,
    'tac'             => imei_tac($imei),
    'cost'            => $cost,
    'brand'           => $result['brand'],
    'model'           => $result['model'],
    'details'         => $curated ? service_filter_details($code, (array) $result['details']) : $result['details'],
    'details_curated' => $curated,
    'blacklist'       => blacklist_status($imei),
]);
