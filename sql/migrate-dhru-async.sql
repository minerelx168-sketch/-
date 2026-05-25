-- Migration: enable async (DHRU) services in service_usages.
--
-- The PHP flow (1-60s) closes a usage in a single HTTP request:
--   PENDING -> SUCCESS / FAILED / REFUNDED
--
-- The DHRU flow (1-5+ min) submits an order, gets back a provider
-- reference ID, and we then poll for completion:
--   PENDING -> PROCESSING -> SUCCESS / FAILED / REFUNDED
--
-- This migration:
--   1. Adds PROCESSING to the status enum.
--   2. Adds provider_order_id to track the DHRU reference id.
--   3. Adds last_polled_at to debounce polling (do not hammer the
--      provider; default debounce is 5 seconds in PHP).
--   4. Adds an index on (status, last_polled_at) so a cron worker
--      can efficiently sweep PROCESSING rows.
--
-- Idempotent enough to re-run: the ALTER TABLE ... ADD COLUMN IF NOT
-- EXISTS path is MySQL 8+; on older MySQL the second run errors with
-- "Duplicate column name" which is safe to ignore.

USE `imei_checker`;

ALTER TABLE `service_usages`
    MODIFY COLUMN `status`
        ENUM('PENDING','PROCESSING','SUCCESS','FAILED','REFUNDED')
        NOT NULL DEFAULT 'PENDING';

ALTER TABLE `service_usages`
    ADD COLUMN IF NOT EXISTS `provider_order_id` VARCHAR(64)  DEFAULT NULL AFTER `output`,
    ADD COLUMN IF NOT EXISTS `last_polled_at`    TIMESTAMP(3) NULL DEFAULT NULL AFTER `created_at`;

-- Sweep index for the cron worker (scripts/poll-dhru-orders.php).
-- Only rows that are PROCESSING get scanned.
ALTER TABLE `service_usages`
    ADD KEY IF NOT EXISTS `idx_status_polled` (`status`, `last_polled_at`);
