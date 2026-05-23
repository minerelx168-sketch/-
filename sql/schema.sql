-- =============================================================================
-- imeihub database schema
--
-- Append-only ledger for credits: NEVER UPDATE credit_transactions after insert.
-- Every mutation that affects credits must run inside a SERIALIZABLE transaction.
-- users.cached_balance is denormalized for speed only; the ledger is source of
-- truth and must be reconcilable at any time via:
--   SELECT user_id, SUM(amount) FROM credit_transactions GROUP BY user_id;
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `imei_checker`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `imei_checker`;

-- -----------------------------------------------------------------------------
-- IMEI lookup cache (unchanged from previous schema)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `imei_lookups` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `imei`         CHAR(15) NOT NULL,
    `tac`          CHAR(8) NOT NULL,
    `provider`     VARCHAR(32) NOT NULL,
    `service_id`   VARCHAR(32) DEFAULT NULL,
    `status`       ENUM('success','failed','pending') NOT NULL DEFAULT 'pending',
    `brand`        VARCHAR(64) DEFAULT NULL,
    `model`        VARCHAR(128) DEFAULT NULL,
    `raw_response` MEDIUMTEXT,
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_imei` (`imei`),
    KEY `idx_tac` (`tac`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `imei_rate_limit` (
    `ip`           VARBINARY(16) NOT NULL,
    `window_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `hits`         INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- Users + auth
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`          VARCHAR(255) NOT NULL,
    `name`           VARCHAR(255) DEFAULT NULL,
    `image`          VARCHAR(512) DEFAULT NULL,
    `email_verified` TIMESTAMP NULL DEFAULT NULL,

    -- Denormalized balance cache. SOURCE OF TRUTH IS THE LEDGER.
    -- Stored as DECIMAL(12,2) so we can hold up to 9,999,999,999.99.
    `cached_balance` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,

    `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_email` (`email`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Linked OAuth identities (one user can link multiple providers later;
-- for phase 1 only Google is wired up).
CREATE TABLE IF NOT EXISTS `oauth_accounts` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`           BIGINT UNSIGNED NOT NULL,
    `provider`          VARCHAR(32) NOT NULL,             -- "google"
    `provider_account_id` VARCHAR(255) NOT NULL,          -- Google sub (subject)
    `access_token`      TEXT DEFAULT NULL,                -- short-lived, can be NULL
    `refresh_token`     TEXT DEFAULT NULL,                -- optional
    `expires_at`        BIGINT UNSIGNED DEFAULT NULL,     -- unix ts
    `token_type`        VARCHAR(32) DEFAULT NULL,
    `scope`             VARCHAR(255) DEFAULT NULL,
    `id_token`          TEXT DEFAULT NULL,
    `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_provider_account` (`provider`, `provider_account_id`),
    KEY `idx_user` (`user_id`),
    CONSTRAINT `fk_oauth_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sessions: opaque session_id stored as a cookie; server-side row holds the user_id.
CREATE TABLE IF NOT EXISTS `sessions` (
    `id`           CHAR(64) NOT NULL,                    -- random 32-byte hex
    `user_id`      BIGINT UNSIGNED NOT NULL,
    `expires_at`   TIMESTAMP NOT NULL,
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_seen_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `user_agent`   VARCHAR(255) DEFAULT NULL,
    `ip`           VARBINARY(16) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_expires` (`expires_at`),
    CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Short-lived state tokens for the OAuth handshake (CSRF defence).
CREATE TABLE IF NOT EXISTS `oauth_states` (
    `state`      CHAR(64) NOT NULL,
    `nonce`      CHAR(64) NOT NULL,
    `redirect_to` VARCHAR(512) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`state`),
    KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- Credit ledger (APPEND-ONLY)
--
-- Insert only. NEVER UPDATE OR DELETE rows in this table once inserted.
-- Positive amount = credit IN, negative = credit OUT.
-- balance_after is a snapshot computed inside the same transaction.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `credit_transactions` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`        BIGINT UNSIGNED NOT NULL,
    `amount`         DECIMAL(12, 2) NOT NULL,
    `type`           ENUM('TOPUP', 'USAGE', 'REFUND', 'ADJUSTMENT', 'BONUS') NOT NULL,
    `reference_type` VARCHAR(64) DEFAULT NULL,
    `reference_id`   VARCHAR(64) DEFAULT NULL,
    `balance_after`  DECIMAL(12, 2) NOT NULL,
    `description`    VARCHAR(255) DEFAULT NULL,
    `created_at`     TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    PRIMARY KEY (`id`),
    KEY `idx_user_created` (`user_id`, `created_at`),
    KEY `idx_ref` (`reference_type`, `reference_id`),
    CONSTRAINT `fk_credit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- Top-up orders (payments)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `topup_orders` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `public_id`        CHAR(26) NOT NULL,                  -- ULID for URLs
    `user_id`          BIGINT UNSIGNED NOT NULL,

    `amount`           DECIMAL(12, 2) NOT NULL,            -- credit value
    `currency`         CHAR(3) NOT NULL DEFAULT 'USD',

    `status`           ENUM('PENDING','PAID','CREDITED','FAILED','EXPIRED','REFUNDED')
                       NOT NULL DEFAULT 'PENDING',

    `provider`         VARCHAR(32) NOT NULL,               -- "stripe"
    `provider_charge_id` VARCHAR(255) DEFAULT NULL,        -- e.g. Stripe session/PI id

    -- Idempotency: also used as Stripe idempotency-key so retries don't
    -- create duplicate orders or duplicate credits.
    `idempotency_key`  CHAR(64) NOT NULL,

    `paid_at`          TIMESTAMP NULL DEFAULT NULL,
    `credited_at`      TIMESTAMP NULL DEFAULT NULL,

    `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_public_id` (`public_id`),
    UNIQUE KEY `uniq_idempotency` (`idempotency_key`),
    UNIQUE KEY `uniq_charge_id` (`provider_charge_id`),
    KEY `idx_user_status` (`user_id`, `status`),
    CONSTRAINT `fk_topup_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- Service usages (paid lookups)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `service_usages` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `public_id`     CHAR(26) NOT NULL,
    `user_id`       BIGINT UNSIGNED NOT NULL,
    `service_code`  VARCHAR(32) NOT NULL,
    `cost`          DECIMAL(12, 2) NOT NULL,
    `input`         JSON NOT NULL,
    `output`        JSON DEFAULT NULL,
    `status`        ENUM('PENDING','SUCCESS','FAILED','REFUNDED') NOT NULL DEFAULT 'PENDING',
    `error_message` VARCHAR(512) DEFAULT NULL,
    `created_at`    TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `completed_at`  TIMESTAMP(3) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_public_id` (`public_id`),
    KEY `idx_user_created` (`user_id`, `created_at`),
    KEY `idx_service` (`service_code`),
    CONSTRAINT `fk_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- Service catalog (pricing)
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `service_prices` (
    `code`        VARCHAR(32) NOT NULL,
    `name`        VARCHAR(128) NOT NULL,
    `description` VARCHAR(512) DEFAULT NULL,
    `cost`        DECIMAL(12, 2) NOT NULL,
    `active`      TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- Chat bot integration
--
-- bot_link_tokens: short-lived single-use tokens the user generates from the
--   dashboard and pastes into the bot ("token XXXXXX") to bind a chat to
--   their account. Channel-agnostic: the same token can be redeemed in
--   either Telegram or LINE; the channel column gets set on consumption.
--
-- bot_links: the actual bindings. One user can have many links (one per
--   chat per channel). Looked up on every inbound message to resolve the
--   originating user.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bot_link_tokens` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `token`       CHAR(10) NOT NULL,         -- short, user-friendly: 10 hex chars
    `channel`     VARCHAR(16) DEFAULT NULL,  -- "telegram" / "line", set on consume
    `consumed_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at`  TIMESTAMP NOT NULL,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_token` (`token`),
    KEY `idx_user_created` (`user_id`, `created_at`),
    KEY `idx_expires` (`expires_at`),
    CONSTRAINT `fk_botlink_token_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `bot_links` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`          BIGINT UNSIGNED NOT NULL,
    `channel`          VARCHAR(16) NOT NULL,        -- "telegram" / "line"
    `channel_chat_id`  VARCHAR(128) NOT NULL,       -- Telegram chat_id, LINE userId
    `channel_user_id`  VARCHAR(128) DEFAULT NULL,   -- sender id when different
    `display_name`     VARCHAR(128) DEFAULT NULL,
    `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_used_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_channel_chat` (`channel`, `channel_chat_id`),
    KEY `idx_user` (`user_id`),
    CONSTRAINT `fk_botlink_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------------------------------
-- Webhook log (raw store of every inbound webhook before processing).
-- Used for replay, debugging, and audit.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `webhook_events` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `provider`       VARCHAR(32) NOT NULL,
    `event_id`       VARCHAR(255) DEFAULT NULL,
    `event_type`     VARCHAR(128) DEFAULT NULL,
    `signature_ok`   TINYINT(1) NOT NULL DEFAULT 0,
    `processed`      TINYINT(1) NOT NULL DEFAULT 0,
    `processing_error` VARCHAR(512) DEFAULT NULL,
    `raw_body`       MEDIUMTEXT NOT NULL,
    `received_at`    TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `processed_at`   TIMESTAMP(3) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_event_id` (`provider`, `event_id`),
    KEY `idx_processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
