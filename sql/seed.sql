-- Seed: service price catalog (THB)
-- Sample prices per user's chosen tier - tweak in the admin or via SQL later.

USE `imei_checker`;

INSERT INTO `service_prices` (`code`, `name`, `description`, `cost`, `active`) VALUES
    ('IMEI_BASIC',     'Free IMEI Check',          'Brand, model and basic specs.',                  0.00,  1),
    ('BLACKLIST',      'Blacklist Status Check',   'See if a device is reported lost or stolen.',   15.00, 1),
    ('CARRIER',        'Carrier & SIM-Lock Check', 'Original carrier and SIM-lock status.',         29.00, 1),
    ('ICLOUD_STATUS',  'iCloud Activation Lock',   'iCloud / Find My iPhone status (Apple only).',  39.00, 1),
    ('WARRANTY',       'Warranty & Activation Date', 'Activation date and warranty coverage.',      29.00, 1),
    ('MODEL_SPECS',    'Full Model Specifications', 'Detailed hardware specs from the TAC.',        15.00, 1)
ON DUPLICATE KEY UPDATE
    `name`        = VALUES(`name`),
    `description` = VALUES(`description`),
    `cost`        = VALUES(`cost`),
    `active`      = VALUES(`active`);
