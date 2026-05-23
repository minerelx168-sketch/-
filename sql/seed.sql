-- Seed: service price catalog (USD)
--
-- Prices below are taken DIRECTLY from the operator's selling-price
-- sheet (selling_price.xlsx). The trailing  -- sheet  comment notes
-- the exact line from the spreadsheet so the catalog is auditable.
--
-- Provider IDs (the integer the unlock-service.net API expects) live
-- in data/service_provider_map.php. Codes marked TODO below need an
-- unlock-service ID before they can serve real lookups - they're
-- inserted as active = 0 so credits_deduct() refuses to charge for
-- them in the meantime. Flip to 1 after wiring the ID.
--
-- Idempotent: ON DUPLICATE KEY UPDATE - safe to re-run.

USE `imei_checker`;

INSERT INTO `service_prices` (`code`, `name`, `description`, `cost`, `active`) VALUES
    -- Free local TAC lookup (no provider call, no charge).
    ('IMEI_BASIC',                'Free IMEI Check',                          'Brand, model and basic specs - resolved locally from the TAC.',                                  0.00, 1),

    -- ----- Featured Apple tiers (Mini / Basic / Pro / Ultimate) -----
    ('APPLE_MINI',                'Apple Check Mini',                         'Cheapest Apple tier - basic model info only.',                                                   0.05, 0), -- sheet: Apple Check Mini  TODO provider id
    ('APPLE_BASIC',               'Apple Check Basic',                        'Apple basic info: model, color, storage.',                                                       0.10, 1), -- sheet: Apple Check Basic
    ('APPLE_PRO',                 'Apple Check Pro',                          'Apple Pro tier: model + carrier + warranty.',                                                    0.15, 0), -- sheet: Apple Check Pro  TODO provider id
    ('APPLE_ULTIMATE',            'Apple Check Ultimate',                     'Apple Ultimate: model + carrier + warranty + sold-by.',                                          0.40, 0), -- sheet: Apple Check Ultimate  TODO provider id
    ('UNIVERSAL_CHECK',           'Universal Check Service',                  'Generic IMEI lookup across any GSM brand.',                                                      0.10, 0), -- sheet: Universal Check Service  TODO provider id

    -- ----- Apple iCloud / MDM / status -----
    ('APPLE_ICLOUD_STATUS',       'Apple FMI iCloud (ON/OFF)',                'Find My iPhone activation-lock status.',                                                         0.01, 1), -- sheet: Apple Check Service - FMI iCloud (ON/OFF)
    ('APPLE_ICLOUD_CLEAN',        'Apple iCloud (Clean/Lost)',                'iCloud Clean / Lost status (primary server).',                                                   0.03, 1), -- sheet: Apple Check Service - iCloud (Clean/Lost)
    ('APPLE_ICLOUD_CLEAN_S2',     'Apple iCloud (Clean/Lost) Server 2',       'iCloud Clean / Lost via fallback server.',                                                       0.30, 0), -- sheet: Apple Check Service - iCloud (Clean/Lost) Server 2  TODO provider id
    ('APPLE_MAC_ICLOUD_STATUS',   'Apple iCloud MacBook/iMac (ON/OFF)',       'Find My status for MacBook / iMac devices.',                                                     0.30, 1), -- sheet: Apple Check Service - MacBook, iMac iCloud (ON/OFF)
    ('APPLE_MAC_ICLOUD_CLEAN',    'Apple iCloud MacBook/iMac (Clean/Lost)',   'iCloud Clean / Lost status for Macs.',                                                           0.40, 1), -- sheet: Apple Check Service - MacBook, iMac iCloud (Clean/Lost)
    ('APPLE_MDM',                 'Apple MDM (ON/OFF)',                       'Mobile Device Management enrollment status.',                                                    0.35, 1), -- sheet: Apple Check Service - MDM (ON/OFF)
    ('APPLE_WARRANTY',            'Apple Warranty / Activation',              'Activation date and remaining warranty.',                                                        0.04, 1), -- sheet: Apple Check Service - Warranty/Activation Status
    ('APPLE_PART_NUMBER',         'Apple Part Number (MPN)',                  'Apple Part Number / MPN of the device.',                                                         0.10, 1), -- sheet: Apple Check Service - Part Number (MPN)
    ('APPLE_SIM_LOCK',            'Apple SIM-Lock Status',                    'Whether the SIM slot is locked to a carrier.',                                                   0.03, 1), -- sheet: Apple Check Service - SimLock Status
    ('APPLE_BLACKLIST_SIMPLE',    'Apple GSMA Blacklist (Simple)',            'Quick yes/no GSMA blacklist status (Apple devices).',                                            0.01, 0), -- sheet: Apple Check Service - GSMA Blacklist Status  TODO provider id
    ('APPLE_BLACKLIST_FULL',      'Apple GSMA Blacklist (Full Report)',       'Detailed GSMA blacklist report (Apple devices).',                                                0.06, 0), -- sheet: Apple Check Service - GSMA Blacklist Full Report  TODO provider id

    -- ----- US carriers -----
    ('TMOBILE_USA',               'T-Mobile USA Clean/Blocked/Unpaid Status', 'T-Mobile USA IMEI status (Clean / Blocked / Unpaid).',                                           0.10, 1), -- sheet: Check Service - T-Mobile USA

    -- ----- Other brands -----
    ('ITEL_INFO',                 'Itel / Tecno / Sonim / Infinix Info',      'Model + warranty info for entry-level African / SEA brands.',                                    0.10, 0), -- sheet: Itel / Tecno / Sonim / Infinix  TODO provider id
    ('SAMSUNG_INFO',              'Samsung Info + Knox Guard',                'Samsung Galaxy: model + warranty + carrier + country + Knox Guard (ON/OFF).',                    0.10, 1), -- sheet: Samsung Check Service - Model, Warranty, Carrier, Country, Knox Guard
    ('XIAOMI_STATUS',             'Xiaomi Info + Mi ID',                      'Xiaomi: model + warranty + country + Mi ID (ON/OFF).',                                           0.10, 1), -- sheet: Xiaomi Check Service - Model, Warranty, Country, Mi ID
    ('ACER_INFO',                 'Acer Info',                                'Acer: model + warranty.',                                                                        0.10, 0), -- sheet: Acer Check Service  TODO provider id
    ('LG_INFO',                   'LG Info',                                  'LG: model + carrier + country.',                                                                 0.10, 0), -- sheet: LG Check Service  TODO provider id
    ('LENOVO_INFO',               'Lenovo Info',                              'Lenovo: model + warranty + country.',                                                            0.10, 1), -- sheet: Lenovo Check Service - Model, Warranty, Country
    ('OPPO_INFO',                 'OPPO Info',                                'OPPO: model + warranty + country.',                                                              0.23, 0), -- sheet: Oppo Check Service  TODO provider id
    ('ONEPLUS_INFO',              'OnePlus Info',                             'OnePlus: model + warranty + country.',                                                           0.23, 0), -- sheet: OnePlus Check Service  TODO provider id
    ('PIXEL_INFO',                'Google Pixel Info',                        'Google Pixel: model + warranty + country.',                                                      0.10, 1), -- sheet: Google Pixel Check Service
    ('MOTOROLA_INFO',             'Motorola Info',                            'Motorola: model + warranty + country.',                                                          0.10, 1), -- sheet: Motorola Check Service
    ('KYOCERA_INFO',              'Kyocera Info',                             'Kyocera: model + warranty + country + carrier.',                                                 0.10, 0), -- sheet: Kyocera Check Service  TODO provider id
    ('HUAWEI_INFO',               'Huawei Info',                              'Huawei: model + warranty + country.',                                                            0.03, 1), -- sheet: Huawei Check Service
    ('HONOR_INFO',                'Honor Info',                               'Honor: model + warranty + country.',                                                             0.10, 1), -- sheet: Honor Check Service
    ('ZTE_INFO',                  'ZTE Info',                                 'ZTE: model + warranty.',                                                                         0.10, 0), -- sheet: ZTE Check Service  TODO provider id

    -- ----- Generic blacklist (any brand) -----
    ('BLACKLIST_SIMPLE',          'WorldWide Blacklist (Simple)',             'Quick yes/no GSMA blacklist status across worldwide carriers.',                                  0.01, 1), -- sheet: Check Service - GSMA Blacklist Status
    ('BLACKLIST_FULL',            'WorldWide Blacklist (Full Info)',          'Full GSMA blacklist report with reporting carrier and date.',                                    0.10, 1), -- sheet: Check Service - GSMA Blacklist Full Report

    -- ----- Apple GSX (premium) -----
    ('APPLE_CASE_REPAIR_HISTORY', 'Apple GSX: Case &amp; Repair History',     'Apple case and repair history from GSX.',                                                        1.20, 1), -- sheet: GSX - Case History, Repair History
    ('APPLE_SOLD_BY_COVERAGE',    'Apple GSX: Sold By, Coverage',             'Original retailer + coverage status from GSX.',                                                  2.00, 1), -- sheet: GSX - Sold By, Coverage
    ('APPLE_GSX_LIGHT',           'Apple GSX: Sold By, Case, Replacement',    'Sold-by + case + replacement + activation policy.',                                              1.00, 1), -- sheet: GSX - Sold By, Case History, Replacement, Activation Policy
    ('APPLE_FULL_GSX',            'Apple GSX: Full Report',                   'Full GSX report: sold-by + case + replacement + activation policy + extended info.',             2.30, 1)  -- sheet: GSX - ... Activation Policy (FULL INFO)
ON DUPLICATE KEY UPDATE
    `name`        = VALUES(`name`),
    `description` = VALUES(`description`),
    `cost`        = VALUES(`cost`),
    `active`      = VALUES(`active`);

-- Codes that used to exist but are not on the current selling sheet.
-- Hidden from the menu so they can't be sold, but rows are kept in the
-- table so existing service_usages references stay valid.
UPDATE `service_prices` SET `active` = 0 WHERE `code` IN (
    'APPLE_CARRIER_LITE',
    'APPLE_CARRIER_PRO',
    'APPLE_CARRIER_PRO_PLUS',
    'APPLE_MAX_INFO',
    'APPLE_WARRANTY_SN',
    'APPLE_ICLOUD_ID_HINT',
    'APPLE_ICLOUD_CLEAN_SN',
    'APPLE_MDM_SN',
    'APPLE_MDM_FMI',
    'APPLE_GSX_TETHER',
    'APPLE_SOLD_BY_HISTORY',
    'APPLE_GSX_MAX',
    'SAMSUNG_KNOX',
    'TMOBILE_USA_PRO',
    'VERIZON_USA_PRO',
    'YANDEX_ALICE',
    'HLR_LOOKUP',
    'NUMBER_TYPE',
    'PING_SMS',
    'PING_SMS_S2'
);
