<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

require __DIR__ . '/../../includes/auth.php';
require __DIR__ . '/../../includes/credits_write.php';
require __DIR__ . '/../../includes/stripe.php';

function fail(int $code, string $error): never
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $error]);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    fail(405, 'POST only.');
}

$user = auth_user();
if (!$user) {
    fail(401, 'Not signed in.');
}

// Accept JSON or form-encoded.
$raw = file_get_contents('php://input');
$body = [];
$ct = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
if (str_contains($ct, 'application/json') && $raw !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) $body = $decoded;
} else {
    $body = $_POST;
}

$amount = isset($body['amount']) ? trim((string) $body['amount']) : '';
if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount)) {
    fail(400, 'Invalid amount.');
}
$amountFloat = (float) $amount;

// Business rules: min 2, max 500 USD per top-up. Tweak as needed.
if ($amountFloat < 2 || $amountFloat > 500) {
    fail(422, 'Top-up must be between $2 and $500.');
}

// Idempotency: trust an explicit header from the client if provided
// (lets the front-end retry safely after a network hiccup). Otherwise
// derive one server-side from a fresh ULID.
require_once __DIR__ . '/../../includes/ulid.php';
$idempotencyKey = (string) ($_SERVER['HTTP_IDEMPOTENCY_KEY'] ?? '');
if (!preg_match('/^[A-Za-z0-9_-]{16,80}$/', $idempotencyKey)) {
    $idempotencyKey = ulid() . '-' . ((int) $user['id']);
}

$cfg = require __DIR__ . '/../../includes/config.php';

try {
    $order = credits_create_topup_order(
        (int) $user['id'],
        number_format($amountFloat, 2, '.', ''),
        'USD',
        $idempotencyKey
    );

    // If the same idempotent request has been served already AND has a
    // Stripe session, return the existing URL. Otherwise build a fresh one.
    if (!empty($order['provider_charge_id'])) {
        try {
            $session = stripe_retrieve_session((string) $order['provider_charge_id']);
            if (!empty($session['url']) && ($session['status'] ?? '') === 'open') {
                echo json_encode([
                    'ok'        => true,
                    'url'       => $session['url'],
                    'public_id' => $order['public_id'],
                ]);
                exit;
            }
        } catch (Throwable $e) {
            // Stripe lookup failed - fall through and create a new session.
        }
    }

    $amountCents = (int) round($amountFloat * 100); // Stripe wants minor units (cents)

    $session = stripe_create_checkout_session([
        'mode'                 => 'payment',
        'client_reference_id'  => $order['public_id'],
        'customer_email'       => $user['email'],
        // Card is supported globally for USD; promptpay is THB-only so drop it.
        'payment_method_types' => ['card'],
        'line_items'           => [[
            'price_data' => [
                'currency'     => 'usd',
                'unit_amount'  => $amountCents,
                'product_data' => [
                    'name'        => 'imeihub credit top-up',
                    'description' => 'Adds $' . number_format($amountFloat, 2) . ' to your imeihub wallet.',
                ],
            ],
            'quantity' => 1,
        ]],
        'metadata' => [
            'topup_public_id' => $order['public_id'],
            'user_id'         => (string) $user['id'],
        ],
        'payment_intent_data' => [
            'metadata' => [
                'topup_public_id' => $order['public_id'],
                'user_id'         => (string) $user['id'],
            ],
        ],
        'success_url' => $cfg['app']['url'] . '/topup/return.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => $cfg['app']['url'] . '/topup.php?cancelled=1',
    ], $idempotencyKey);

    credits_attach_charge_id((int) $order['id'], (string) $session['id']);

    echo json_encode([
        'ok'        => true,
        'url'       => $session['url'] ?? null,
        'public_id' => $order['public_id'],
    ]);
} catch (Throwable $e) {
    fail(500, $e->getMessage());
}
