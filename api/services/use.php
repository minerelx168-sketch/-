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

// Phase 2: call the provider. Map our service_code -> unlock-service ID
// using data/service_provider_map.php (single source of truth). null in
// the map means "free local lookup" - we parse the TAC ourselves with
// the demo provider so no API credit is spent.
$serviceMap        = require __DIR__ . '/../../data/service_provider_map.php';
$providerServiceId = $serviceMap[$code] ?? null;

try {
    if ($providerServiceId === null) {
        // Free tier: local TAC lookup, no external call.
        require_once __DIR__ . '/../../includes/imei_demo.php';
        $result = imei_demo_lookup($imei, '0');
    } else {
        $result = imei_provider_lookup($imei, (string) $providerServiceId);
    }
} catch (Throwable $e) {
    credits_refund_usage($publicId, 'provider exception: ' . $e->getMessage());
    fail(502, 'Lookup failed; your credit has been refunded.', [
        'public_id' => $publicId,
        'refunded'  => true,
    ]);
}

if (($result['status'] ?? '') !== 'success') {
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

echo json_encode([
    'ok'        => true,
    'public_id' => $publicId,
    'imei'      => $imei,
    'tac'       => imei_tac($imei),
    'cost'      => $cost,
    'brand'     => $result['brand'],
    'model'     => $result['model'],
    'details'   => $result['details'],
]);
