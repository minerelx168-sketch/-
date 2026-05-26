<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Community IMEI blacklist helpers.
 *
 * A report flags an IMEI immediately (community-sourced warning); admins
 * can dismiss false reports. All reads tolerate a missing table (returns
 * "not flagged") so the lookup flow never breaks if the migration hasn't
 * run yet.
 */

if (!function_exists('blacklist_status')) {
/**
 * Returns the active-report summary for an IMEI, or null if it has none.
 *   ['reports' => int, 'first_reported' => 'Y-m-d H:i:s', 'reason' => string|null]
 */
function blacklist_status(string $imei): ?array
{
    $imei = imei_normalize($imei);
    if ($imei === '') {
        return null;
    }
    try {
        $stmt = db()->prepare(
            'SELECT COUNT(*) AS reports, MIN(created_at) AS first_reported,
                    SUBSTRING_INDEX(MAX(CONCAT(created_at, "|", COALESCE(reason, ""))), "|", -1) AS reason
             FROM blacklist_reports
             WHERE imei = ? AND status = "active"'
        );
        $stmt->execute([$imei]);
        $row = $stmt->fetch();
    } catch (Throwable $e) {
        return null; // table may not exist yet
    }
    if (!$row || (int) $row['reports'] === 0) {
        return null;
    }
    return [
        'reports'        => (int) $row['reports'],
        'first_reported' => (string) $row['first_reported'],
        'reason'         => ($row['reason'] ?? '') !== '' ? (string) $row['reason'] : null,
    ];
}
}

if (!function_exists('blacklist_report')) {
/**
 * File (or refresh) a report for an IMEI by a given user. One active
 * report per (imei, user); re-reporting updates the reason and re-activates.
 * Returns the active report count for the IMEI afterwards.
 */
function blacklist_report(string $imei, ?string $serial, int $userId, ?string $reason): int
{
    $imei = imei_normalize($imei);
    if (strlen($imei) !== 15) {
        throw new RuntimeException('A valid 15-digit IMEI is required to report.');
    }
    $tac = imei_tac($imei);
    db()->prepare(
        'INSERT INTO blacklist_reports (imei, tac, serial_number, reported_by, reason, status)
         VALUES (?, ?, ?, ?, ?, "active")
         ON DUPLICATE KEY UPDATE
            serial_number = VALUES(serial_number),
            reason        = VALUES(reason),
            status        = "active",
            created_at    = created_at'
    )->execute([
        $imei,
        $tac,
        $serial !== null && $serial !== '' ? substr($serial, 0, 64) : null,
        $userId,
        $reason !== null && $reason !== '' ? substr($reason, 0, 255) : null,
    ]);

    $s = db()->prepare('SELECT COUNT(*) FROM blacklist_reports WHERE imei = ? AND status = "active"');
    $s->execute([$imei]);
    return (int) $s->fetchColumn();
}
}
