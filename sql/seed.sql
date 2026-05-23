-- Seed: service price catalog (THB)
--
-- Pricing rule applied below:
--   thb = max(15, ceil(credit_cost * 50 / 5) * 5)
-- i.e. wholesale credit * 50, floor at ฿15, round up to nearest ฿5.
-- The (credit) values in the trailing comment let you re-derive the
-- selling price with a different markup later by re-running this file.
--
-- Provider IDs (the integer the unlock-service.net API expects)
-- live in data/service_provider_map.php so the database stays
-- provider-agnostic and the same catalog can be served by a different
-- backend later.

USE `imei_checker`;

INSERT INTO `service_prices` (`code`, `name`, `description`, `cost`, `active`) VALUES
    -- Free local lookup (no provider call)
    ('IMEI_BASIC',                'Free IMEI Check',                          'Brand, model and basic specs - resolved locally from the TAC.',                                 0.00, 1),

    -- ----- Apple -----
    ('APPLE_BASIC',               'Apple Basic Info',                         'iPhone / iPad brand, model and basic specs.',                                                  15.00, 1), -- credit 0.03
    ('APPLE_CARRIER_LITE',        'Apple Carrier (Lite)',                     'Original carrier the iPhone was sold on.',                                                     15.00, 1), -- credit 0.04
    ('APPLE_CARRIER_PRO',         'Apple Carrier (Pro)',                      'Carrier + country + SIM-lock status, deeper detail than Lite.',                                15.00, 1), -- credit 0.08
    ('APPLE_CARRIER_PRO_PLUS',    'Apple Carrier (Pro Plus)',                 'Most detailed carrier report available, includes purchase date.',                              15.00, 1), -- credit 0.13
    ('APPLE_MAX_INFO',            'Apple Max Info (Premium)',                 'Premium combined report: model, carrier, warranty, activation, sold-by.',                       20.00, 1), -- credit 0.35
    ('APPLE_WARRANTY',            'Apple Warranty (Activation Info)',         'Activation date and remaining warranty coverage.',                                              15.00, 1), -- credit 0.02
    ('APPLE_WARRANTY_SN',         'Apple Warranty by Serial Number',          'Same as Apple Warranty but accepts SN instead of IMEI.',                                        15.00, 1), -- credit 0.008
    ('APPLE_PART_NUMBER',         'Apple Part Number / MPN',                  'Returns the Apple part number / MPN of the device.',                                            15.00, 1), -- credit 0.07
    ('APPLE_ICLOUD_ID_HINT',      'Apple iCloud ID Hint',                     'Reveals the masked Apple ID the device is signed in to.',                                       20.00, 1), -- credit 0.40
    ('APPLE_MAC_ICLOUD_STATUS',   'Apple iCloud MacBook/iMac (ON/OFF)',       'Find My status for MacBook / iMac devices.',                                                    15.00, 1), -- credit 0.10
    ('APPLE_MAC_ICLOUD_CLEAN',    'Apple iCloud MacBook/iMac (Clean/Lost)',   'Lost-mode status for MacBook / iMac devices.',                                                  15.00, 1), -- credit 0.20
    ('APPLE_ICLOUD_STATUS',       'Apple iCloud (ON/OFF)',                    'Find My iPhone activation lock status.',                                                        15.00, 1), -- credit 0.008
    ('APPLE_ICLOUD_CLEAN',        'Apple iCloud (Clean/Lost)',                'Lost-mode / blacklist status as reported by Apple iCloud.',                                     15.00, 1), -- credit 0.02
    ('APPLE_ICLOUD_CLEAN_SN',     'Apple iCloud (Clean/Lost) by SN',          'Same as Apple iCloud Clean/Lost but accepts serial number.',                                    15.00, 1), -- credit 0.05
    ('APPLE_MDM',                 'Apple MDM (ON/OFF)',                       'Mobile Device Management enrollment status.',                                                   15.00, 1), -- credit 0.15
    ('APPLE_MDM_SN',              'Apple MDM by Serial Number',               'MDM status check accepting SN.',                                                                15.00, 1), -- credit 0.15
    ('APPLE_MDM_FMI',             'Apple MDM + Find My iPhone',               'Combined MDM + Find My iPhone status.',                                                         15.00, 1), -- credit 0.25
    ('APPLE_SIM_LOCK',            'Apple SIM-Lock Status',                    'Whether the SIM slot is locked to a carrier.',                                                  15.00, 1), -- credit 0.018
    ('APPLE_GSX_TETHER',          'Apple GSX Next Tether Policy',             'Apple GSX next tether policy lookup.',                                                          15.00, 1), -- credit 0.10
    ('APPLE_CASE_REPAIR_HISTORY', 'Apple Case & Repair History',              'Full Apple case and repair history report.',                                                    75.00, 1), -- credit 1.45
    ('APPLE_SOLD_BY_COVERAGE',    'Apple Sold By, Coverage (Max Info)',       'Original retailer, coverage status and max info report.',                                       85.00, 1), -- credit 1.70
    ('APPLE_SOLD_BY_HISTORY',     'Apple Sold By, Case History, Activation',  'Sold-by + case history + activation policy.',                                                  105.00, 1), -- credit 2.10
    ('APPLE_FULL_GSX',            'Apple Full GSX Report',                    'Sold-by + case history + replacement + activation policy with ICCID & MAC.',                  110.00, 1), -- credit 2.20
    ('APPLE_GSX_LIGHT',           'Apple GSX (Light)',                        'Sold-by + case history + replacement + GSX activation policy.',                                 40.00, 1), -- credit 0.75
    ('APPLE_GSX_MAX',             'Apple GSX (Max Info)',                     'Sold-by + case history + replacement + repair + GSX activation (max info).',                    65.00, 1), -- credit 1.30

    -- ----- Blacklist -----
    ('BLACKLIST_SIMPLE',          'WorldWide Blacklist (Simple)',             'Quick yes/no blacklist status across worldwide carriers.',                                      15.00, 1), -- credit 0.008
    ('BLACKLIST_FULL',            'WorldWide Blacklist (Full Info)',          'Full blacklist report with reporting carrier and date.',                                        15.00, 1), -- credit 0.04

    -- ----- Android brands -----
    ('PIXEL_INFO',                'Google Pixel Info',                        'Brand, model and basic specs for Google Pixel devices.',                                        15.00, 1), -- credit 0.10
    ('XIAOMI_STATUS',             'Xiaomi (ON/OFF)',                          'Mi Account / Find Device status for Xiaomi phones.',                                            15.00, 1), -- credit 0.01
    ('HUAWEI_INFO',               'Huawei Info',                              'Brand, model, color and basic specs for Huawei phones.',                                        15.00, 1), -- credit 0.04
    ('HONOR_INFO',                'Honor Info',                               'Brand, model, color and basic specs for Honor phones.',                                         15.00, 1), -- credit 0.04
    ('SAMSUNG_INFO',              'Samsung Info',                             'Brand, model, color and IMEI info for Samsung Galaxy phones.',                                  15.00, 1), -- credit 0.03
    ('SAMSUNG_KNOX',              'Samsung Knox Guard Status',                'Knox Guard / Samsung Lock status (ON/OFF).',                                                    15.00, 1), -- credit 0.10
    ('MOTOROLA_INFO',             'Motorola Info',                            'Brand, model and basic specs for Motorola phones.',                                             15.00, 1), -- credit 0.03
    ('LENOVO_INFO',               'Lenovo Info',                              'Brand, model and basic specs for Lenovo devices.',                                              15.00, 1), -- credit 0.04

    -- ----- US carriers -----
    ('TMOBILE_USA',               'T-Mobile USA Status',                      'T-Mobile USA iPhone / generic IMEI status.',                                                    15.00, 1), -- credit 0.01
    ('TMOBILE_USA_PRO',           'T-Mobile USA Status (Pro)',                'T-Mobile USA status with extra detail (Pro tier).',                                             15.00, 1), -- credit 0.03
    ('VERIZON_USA_PRO',           'Verizon USA Status (Pro)',                 'Verizon USA iPhone / generic status (Pro tier).',                                               15.00, 1), -- credit 0.03

    -- ----- Misc / Number lookups -----
    ('YANDEX_ALICE',              'Yandex Alice Info',                        'Phone owner info from Yandex Alice.',                                                           15.00, 1), -- credit 0.30
    ('HLR_LOOKUP',                'HLR Lookup',                               'Home Location Register lookup for a phone number.',                                             15.00, 1), -- credit 0.01
    ('NUMBER_TYPE',               'Number Type (NT) Lookup',                  'Determines if a number is mobile, landline, VoIP, etc.',                                        15.00, 1), -- credit 0.07
    ('PING_SMS',                  'Ping-SMS',                                 'Silent SMS ping to verify a number is active.',                                                 15.00, 1), -- credit 0.10
    ('PING_SMS_S2',               'Ping-SMS (Server 2)',                      'Silent SMS ping via secondary server (different routing).',                                     20.00, 1)  -- credit 0.35
ON DUPLICATE KEY UPDATE
    `name`        = VALUES(`name`),
    `description` = VALUES(`description`),
    `cost`        = VALUES(`cost`),
    `active`      = VALUES(`active`);
