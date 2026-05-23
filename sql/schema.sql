-- IMEI Checker database schema
CREATE DATABASE IF NOT EXISTS `imei_checker`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `imei_checker`;

-- Cached lookup results so we don't burn API credits on repeat queries.
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

-- Simple per-IP rate limiting bucket.
CREATE TABLE IF NOT EXISTS `imei_rate_limit` (
    `ip`           VARBINARY(16) NOT NULL,
    `window_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `hits`         INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
