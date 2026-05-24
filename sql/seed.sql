-- Seed: service price catalog (USD)
--
-- Names are taken VERBATIM from selling_price.xlsx (the operator's sheet)
-- so the dropdown in /check + the homepage hero match what the operator
-- actually charges. No marketing copy or icons here; rendering layers
-- (data/services.php landing-page short list, /check option label) are
-- free to prettify, but the truth lives here.
--
-- Provider IDs (the integer the unlock-service.net API expects) live in
-- data/service_provider_map.php so the database stays provider-agnostic.
-- Rows marked active = 0 below need a real provider ID before they go
-- live; credits_deduct() refuses to charge for them in the meantime.
--
-- Idempotent: ON DUPLICATE KEY UPDATE - safe to re-run.

USE `imei_checker`;

INSERT INTO `service_prices` (`code`, `name`, `description`, `cost`, `active`) VALUES
    -- Free local TAC lookup (no provider call, no charge).
    ('IMEI_BASIC',                'Free IMEI Check',                                                                'Brand, model and basic specs - resolved locally from the TAC.',                          0.00, 1),

    -- Featured Apple tiers
    ('APPLE_MINI',                'Apple Check Mini',                                                               'Cheapest Apple tier - basic model info only.',                                          0.05, 0), -- TODO provider id
    ('APPLE_BASIC',               'Apple Check Basic',                                                              'Apple basic info: model, color, storage.',                                              0.10, 1),
    ('APPLE_PRO',                 'Apple Check Pro',                                                                'Apple Pro tier: model + carrier + warranty.',                                           0.15, 0), -- TODO provider id
    ('APPLE_ULTIMATE',            'Apple Check Ultimate',                                                           'Apple Ultimate: model + carrier + warranty + sold-by.',                                 0.40, 0), -- TODO provider id
    ('UNIVERSAL_CHECK',           'Universal Check Service',                                                        'Generic IMEI lookup across any GSM brand.',                                             0.10, 0), -- TODO provider id

    -- Apple iCloud / MDM / status
    ('APPLE_ICLOUD_STATUS',       'Apple Check Service - FMI iCloud (ON/OFF)',                                      'Find My iPhone activation lock status.',                                                0.01, 1),
    ('APPLE_ICLOUD_CLEAN',        'Apple Check Service - iCloud (Clean/Lost)',                                      'iCloud Clean / Lost status (primary server).',                                          0.03, 1),
    ('APPLE_ICLOUD_CLEAN_S2',     'Apple Check Service - iCloud (Clean/Lost) (Server 2)',                           'iCloud Clean / Lost via fallback server.',                                              0.30, 0), -- TODO provider id
    ('APPLE_MAC_ICLOUD_STATUS',   'Apple Check Service - MacBook, iMac iCloud (ON/OFF)',                            'Find My status for MacBook / iMac devices.',                                            0.30, 1),
    ('APPLE_MAC_ICLOUD_CLEAN',    'Apple Check Service - MacBook, iMac iCloud (Clean/Lost)',                        'iCloud Clean / Lost status for Macs.',                                                  0.40, 1),
    ('APPLE_MDM',                 'Apple Check Service - MDM (ON/OFF)',                                             'Mobile Device Management enrollment status.',                                           0.35, 1),
    ('APPLE_WARRANTY',            'Apple Check Service - Warranty/Activation Status',                               'Activation date and remaining warranty.',                                               0.04, 1),
    ('APPLE_PART_NUMBER',         'Apple Check Service - Part Number (MPN)',                                        'Apple Part Number / MPN of the device.',                                                0.10, 1),
    ('APPLE_SIM_LOCK',            'Apple Check Service - SimLock Status',                                           'Whether the SIM slot is locked to a carrier.',                                          0.03, 1),
    ('APPLE_BLACKLIST_SIMPLE',    'Apple Check Service - GSMA Blacklist Status',                                    'Quick yes/no GSMA blacklist status (Apple devices).',                                   0.01, 0), -- TODO provider id
    ('APPLE_BLACKLIST_FULL',      'Apple Check Service - GSMA Blacklist Full Report',                               'Detailed GSMA blacklist report (Apple devices).',                                       0.06, 0), -- TODO provider id

    -- US carriers
    ('TMOBILE_USA',               'Check Service - T-Mobile USA Clean/Blocked/Unpaid Status',                       'T-Mobile USA IMEI status (Clean / Blocked / Unpaid).',                                  0.10, 1),

    -- Other brands
    ('ITEL_INFO',                 'Itel / Tecno / Sonim / Infinix - Model, Warranty',                               'Model + warranty info for entry-level brands.',                                         0.10, 0), -- TODO provider id
    ('SAMSUNG_INFO',              'Samsung Check Service - Model, Warranty, Carrier, Country, Knox Guard (ON/OFF)','Combined Samsung Galaxy report.',                                                       0.10, 1),
    ('XIAOMI_STATUS',             'Xiaomi Check Service - Model, Warranty, Country, Mi ID (ON/OFF)',                'Xiaomi: model + warranty + country + Mi Account status.',                               0.10, 1),
    ('ACER_INFO',                 'Acer Check Service - Model, Warranty',                                           'Acer: model + warranty.',                                                               0.10, 0), -- TODO provider id
    ('LG_INFO',                   'LG Check Service - Model, Carrier, Country',                                     'LG: model + carrier + country.',                                                        0.10, 0), -- TODO provider id
    ('LENOVO_INFO',               'Lenovo Check Service - Model, Warranty, Country',                                'Lenovo: model + warranty + country.',                                                   0.10, 1),
    ('OPPO_INFO',                 'Oppo Check Service - Model, Warranty, Country',                                  'OPPO: model + warranty + country.',                                                     0.23, 0), -- TODO provider id
    ('ONEPLUS_INFO',              'OnePlus Check Service - Model, Warranty, Country',                               'OnePlus: model + warranty + country.',                                                  0.23, 0), -- TODO provider id
    ('PIXEL_INFO',                'Google Pixel Check Service - Model, Warranty, Country',                          'Google Pixel: model + warranty + country.',                                             0.10, 1),
    ('MOTOROLA_INFO',             'Motorola Check Service - Model, Warranty, Country',                              'Motorola: model + warranty + country.',                                                 0.10, 1),
    ('KYOCERA_INFO',              'Kyocera Check Service - Model, Warranty, Country, Carrier',                      'Kyocera: model + warranty + country + carrier.',                                        0.10, 0), -- TODO provider id
    ('HUAWEI_INFO',               'Huawei Check Service - Model, Warranty, Country',                                'Huawei: model + warranty + country.',                                                   0.03, 1),
    ('HONOR_INFO',                'Honor Check Service - Model, Warranty, Country',                                 'Honor: model + warranty + country.',                                                    0.10, 1),
    ('ZTE_INFO',                  'ZTE Check Service - Model, Warranty',                                            'ZTE: model + warranty.',                                                                0.10, 0), -- TODO provider id

    -- Generic blacklist (any brand)
    ('BLACKLIST_SIMPLE',          'Check Service - GSMA Blacklist Status',                                          'Quick yes/no GSMA blacklist status across worldwide carriers.',                         0.01, 1),
    ('BLACKLIST_FULL',            'Check Service - GSMA Blacklist Full Report',                                     'Full GSMA blacklist report with reporting carrier and date.',                           0.10, 1),

    -- Apple GSX (premium)
    ('APPLE_CASE_REPAIR_HISTORY', 'GSX - Case History, Repair History',                                             'Apple case and repair history from GSX.',                                               1.20, 1),
    ('APPLE_SOLD_BY_COVERAGE',    'GSX - Sold By, Coverage',                                                        'Original retailer + coverage status from GSX.',                                         2.00, 1),
    ('APPLE_GSX_LIGHT',           'GSX - Sold By, Case History, Replacement History, Activation Policy',            'Sold-by + case + replacement + activation policy.',                                     1.00, 1),
    ('APPLE_FULL_GSX',            'GSX - Sold By, Case History, Replacement History, Activation Policy (FULL INFO)','Full GSX report including extended info.',                                              2.30, 1)
ON DUPLICATE KEY UPDATE
    `name`        = VALUES(`name`),
    `description` = VALUES(`description`),
    `cost`        = VALUES(`cost`),
    `active`      = VALUES(`active`);

-- Codes that used to exist but are not on the current selling sheet.
UPDATE `service_prices` SET `active` = 0 WHERE `code` IN (
    'APPLE_CARRIER_LITE','APPLE_CARRIER_PRO','APPLE_CARRIER_PRO_PLUS',
    'APPLE_MAX_INFO','APPLE_WARRANTY_SN','APPLE_ICLOUD_ID_HINT',
    'APPLE_ICLOUD_CLEAN_SN','APPLE_MDM_SN','APPLE_MDM_FMI','APPLE_GSX_TETHER',
    'APPLE_SOLD_BY_HISTORY','APPLE_GSX_MAX','SAMSUNG_KNOX',
    'TMOBILE_USA_PRO','VERIZON_USA_PRO',
    'YANDEX_ALICE','HLR_LOOKUP','NUMBER_TYPE','PING_SMS','PING_SMS_S2'
);
