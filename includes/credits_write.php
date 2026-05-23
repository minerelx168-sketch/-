<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/ulid.php';

/**
 * MUTATING credit helpers. Every function in this file:
 *   - runs inside a single DB transaction
 *   - takes a SELECT ... FOR UPDATE lock on the user row
 *   - inserts into credit_transactions (append-only, NEVER updates)
 *   - updates users.cached_balance to keep the cache aligned
 *
 * If you see something here that mutates an existing credit_transactions
 * row, that is a bug - fix the call site, do not change this file.
 */

class InsufficientCreditError extends RuntimeException {}
class TopUpAlreadyCreditedError extends RuntimeException {}

/**
 * Insert a PENDING top-up order. The caller (api/topup/create) will then ask
 * Stripe to create a checkout session and store the session id on this row.
 */
if (!function_exists('credits_create_topup_order')) {
function credits_create_topup_order(int $userId, string $amount, string $currency, string $idempotencyKey): array
{
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $amount) || (float) $amount <= 0) {
        throw new RuntimeException('Invalid top-up amount.');
    }

    $pdo = db();

    // Idempotency: if a row already exists for this key, return it.
    $stmt = $pdo->prepare('SELECT * FROM topup_orders WHERE idempotency_key = ? LIMIT 1');
    $stmt->execute([$idempotencyKey]);
    $existing = $stmt->fetch();
    if ($existing) {
        return $existing;
    }

    $publicId = ulid();
    $pdo->prepare(
        'INSERT INTO topup_orders
            (public_id, user_id, amount, currency, status, provider, idempotency_key)
         VALUES (?, ?, ?, ?, "PENDING", "stripe", ?)'
    )->execute([$publicId, $userId, $amount, $currency, $idempotencyKey]);

    $stmt = $pdo->prepare('SELECT * FROM topup_orders WHERE id = ?');
    $stmt->execute([$pdo->lastInsertId()]);
    return $stmt->fetch();
}
}

if (!function_exists('credits_attach_charge_id')) {
function credits_attach_charge_id(int $orderId, string $chargeId): void
{
    db()->prepare('UPDATE topup_orders SET provider_charge_id = ? WHERE id = ?')
        ->execute([$chargeId, $orderId]);
}
}

/**
 * Credit a paid top-up order. Idempotent: if the order is already CREDITED,
 * returns silently without double-issuing credits. This is the function the
 * Stripe webhook calls.
 */
if (!function_exists('credits_issue_topup')) {
function credits_issue_topup(string $publicId): array
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT * FROM topup_orders WHERE public_id = ? FOR UPDATE');
        $stmt->execute([$publicId]);
        $order = $stmt->fetch();
        if (!$order) {
            throw new RuntimeException('Top-up order not found: ' . $publicId);
        }

        if ($order['status'] === 'CREDITED') {
            $pdo->commit();
            return ['order' => $order, 'credited_now' => false];
        }
        if (!in_array($order['status'], ['PENDING', 'PAID'], true)) {
            throw new RuntimeException('Top-up cannot be credited in status: ' . $order['status']);
        }

        // Lock the user row.
        $stmt = $pdo->prepare('SELECT id, cached_balance FROM users WHERE id = ? FOR UPDATE');
        $stmt->execute([$order['user_id']]);
        $user = $stmt->fetch();
        if (!$user) {
            throw new RuntimeException('User not found for top-up: ' . $order['user_id']);
        }

        // Source of truth balance (ledger). Don't trust cached_balance.
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) FROM credit_transactions WHERE user_id = ?');
        $stmt->execute([$order['user_id']]);
        $balance = (float) $stmt->fetchColumn();

        $amount = (float) $order['amount'];
        $newBalance = $balance + $amount;

        // Append the credit row.
        $pdo->prepare(
            'INSERT INTO credit_transactions
                (user_id, amount, type, reference_type, reference_id, balance_after, description)
             VALUES (?, ?, "TOPUP", "TopUpOrder", ?, ?, ?)'
        )->execute([
            $order['user_id'],
            number_format($amount, 2, '.', ''),
            $order['public_id'],
            number_format($newBalance, 2, '.', ''),
            'Top-up via Stripe',
        ]);

        // Update cached balance + order status.
        $pdo->prepare('UPDATE users SET cached_balance = ? WHERE id = ?')
            ->execute([number_format($newBalance, 2, '.', ''), $order['user_id']]);

        $pdo->prepare(
            'UPDATE topup_orders
             SET status = "CREDITED",
                 paid_at    = COALESCE(paid_at, NOW()),
                 credited_at = NOW()
             WHERE id = ?'
        )->execute([$order['id']]);

        $pdo->commit();

        // Re-fetch the order to return current state.
        $stmt = $pdo->prepare('SELECT * FROM topup_orders WHERE id = ?');
        $stmt->execute([$order['id']]);
        return ['order' => $stmt->fetch(), 'credited_now' => true];
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
}

/**
 * Mark an order as FAILED (e.g. Stripe sent us a payment_failed event).
 * Does NOT issue credits, does NOT touch the ledger.
 */
if (!function_exists('credits_mark_order_failed')) {
function credits_mark_order_failed(string $publicId, string $reason = ''): void
{
    db()->prepare(
        'UPDATE topup_orders
         SET status = "FAILED", updated_at = NOW()
         WHERE public_id = ? AND status IN ("PENDING", "PAID")'
    )->execute([$publicId]);
}
}
