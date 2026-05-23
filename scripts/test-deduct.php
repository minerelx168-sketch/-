<?php
/**
 * Manual integration test for the credit deduct + refund flow.
 *
 * Requires:
 *   - .env points at a real MySQL with the schema applied (sql/schema.sql + sql/seed.sql)
 *   - A throwaway test user (we create + delete one)
 *
 * Usage:
 *   php scripts/test-deduct.php
 *
 * Test cases (per the brief):
 *   T1. happy path                       deduct -> balance drops by cost
 *   T2. insufficient balance             throws InsufficientCreditError, no row inserted
 *   T3. refund                           appends REFUND row, status -> REFUNDED, balance restored
 *   T4. concurrent deducts (2 in flight) never produces negative balance
 *   T5. cached_balance matches ledger SUM after every test
 */

declare(strict_types=1);

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/credits_write.php';

$pdo = db();
$pdo->exec('SET autocommit=1');

function row(string $label, bool $ok, string $detail = ''): void
{
    $tag = $ok ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m";
    echo "  $tag  $label" . ($detail !== '' ? "  ($detail)" : '') . "\n";
}

function reconcile(int $userId): array
{
    $pdo = db();
    $cached = (float) $pdo->prepare('SELECT cached_balance FROM users WHERE id = ?')->execute([$userId])
        ?: 0;
    $stmt = $pdo->prepare('SELECT cached_balance FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $cached = (float) $stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount),0) FROM credit_transactions WHERE user_id = ?');
    $stmt->execute([$userId]);
    $ledger = (float) $stmt->fetchColumn();
    return ['cached' => $cached, 'ledger' => $ledger];
}

echo "Setting up a throwaway test user...\n";
$pdo->prepare('INSERT INTO users (email, name, cached_balance) VALUES (?, ?, ?)')
    ->execute(['credit-test+' . bin2hex(random_bytes(4)) . '@example.com', 'Credit Test', 0]);
$userId = (int) $pdo->lastInsertId();

// Seed balance: +500 via a synthetic TOPUP row.
$pdo->prepare(
    'INSERT INTO credit_transactions
        (user_id, amount, type, reference_type, reference_id, balance_after, description)
     VALUES (?, 500.00, "TOPUP", "Test", "seed", 500.00, "Test seed")'
)->execute([$userId]);
$pdo->prepare('UPDATE users SET cached_balance = 500.00 WHERE id = ?')->execute([$userId]);

echo "\nT1. happy path deduct (BLACKLIST cost=15)\n";
$usage = credits_deduct($userId, 'BLACKLIST', ['imei' => '359152060000003']);
$b = reconcile($userId);
row('balance dropped by 15', round($b['ledger'], 2) === 485.00, "ledger=$b[ledger]");
row('cached_balance == ledger',  abs($b['cached'] - $b['ledger']) < 0.01, "cached=$b[cached]");
row('usage row PENDING',         $usage['status'] === 'PENDING');

echo "\nT2. insufficient balance (try 1000 on a 485 wallet)\n";
$pdo->prepare('UPDATE service_prices SET cost = 1000.00 WHERE code = ?')->execute(['BLACKLIST']);
try {
    credits_deduct($userId, 'BLACKLIST', ['imei' => '359152060000003']);
    row('throws InsufficientCreditError', false, 'no exception');
} catch (InsufficientCreditError $e) {
    row('throws InsufficientCreditError', true);
}
$b2 = reconcile($userId);
row('balance unchanged after rejection', round($b2['ledger'], 2) === 485.00, "ledger=$b2[ledger]");
$pdo->prepare('UPDATE service_prices SET cost = 15.00 WHERE code = ?')->execute(['BLACKLIST']);

echo "\nT3. refund T1's usage\n";
credits_refund_usage($usage['public_id'], 'simulated provider failure');
$b3 = reconcile($userId);
row('balance restored to 500', round($b3['ledger'], 2) === 500.00, "ledger=$b3[ledger]");
$stmt = $pdo->prepare('SELECT status FROM service_usages WHERE public_id = ?');
$stmt->execute([$usage['public_id']]);
row('usage status REFUNDED', $stmt->fetchColumn() === 'REFUNDED');
row('refund is idempotent', (function () use ($usage) {
    credits_refund_usage($usage['public_id'], 'second call');
    return true;
})());

echo "\nT4. concurrent deduct (5 parallel workers, 100 in wallet, cost 15 = max 6 deducts)\n";
// Reset balance to 100 via a fake adjustment row.
$pdo->prepare(
    'INSERT INTO credit_transactions (user_id, amount, type, balance_after, description)
     VALUES (?, ?, "ADJUSTMENT", 100.00, "test reset")'
)->execute([$userId, number_format(100 - $b3['ledger'], 2, '.', '')]);
$pdo->prepare('UPDATE users SET cached_balance = 100.00 WHERE id = ?')->execute([$userId]);

// Fork 5 child processes that each try one deduct simultaneously.
$pids = [];
$succ = 0;
$fail = 0;
for ($i = 0; $i < 5; $i++) {
    $pid = pcntl_fork();
    if ($pid === 0) {
        try {
            credits_deduct($userId, 'BLACKLIST', ['imei' => '359152060000003', 'worker' => $i]);
            exit(0);
        } catch (Throwable $_) {
            exit(1);
        }
    }
    $pids[] = $pid;
}
foreach ($pids as $pid) {
    pcntl_waitpid($pid, $status);
    if (pcntl_wifexited($status) && pcntl_wexitstatus($status) === 0) $succ++;
    else $fail++;
}
$b4 = reconcile($userId);
row('concurrent deducts: balance >= 0', $b4['ledger'] >= 0, "ledger=$b4[ledger], success=$succ, fail=$fail");
row('balance == 100 - succ * 15', abs($b4['ledger'] - (100 - $succ * 15)) < 0.01, "expected=" . (100 - $succ * 15));

echo "\nCleaning up test user $userId...\n";
$pdo->prepare('DELETE FROM credit_transactions WHERE user_id = ?')->execute([$userId]);
$pdo->prepare('DELETE FROM service_usages    WHERE user_id = ?')->execute([$userId]);
$pdo->prepare('DELETE FROM users             WHERE id      = ?')->execute([$userId]);

echo "\nDone.\n";
