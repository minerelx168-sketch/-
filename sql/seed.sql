-- Seed: service price catalog (USD)
--
-- ANCHORED on unlock-service.net's live API catalog (43 services, IDs
-- as of last sync). One row per upstream service; the API name is
-- copied VERBATIM into the `name` column so the dropdown matches what
-- the provider shows. Internal `code` is our own opaque ID.
--
-- Pricing rule:
--   - If the operator's selling sheet (selling_price.xlsx) sets a price
--     for the matching service, use it directly (trailing -- sheet ...
--     comment marks these).
--   - Otherwise default to max(0.05, wholesale * 2) rounded to $0.01.
--     Comment shows the wholesale credit so you can re-derive at any
--     time with a different markup.
--
-- All 43 catalog rows are active = 1 - the provider IDs are known and
-- the wallet is allowed to charge for them.
--
-- IMEI_BASIC stays at $0.00 / null provider for the always-free local
-- TAC lookup. The 13 phantom codes that lived here pre-anchor (Apple
-- Mini / Pro / Ultimate / Universal Check / Apple iCloud S2 / Apple
-- GSMA Blacklist x2 / Itel / Acer / LG / Oppo / OnePlus / Kyocera /
-- ZTE) have been removed - unlock-service.net does not carry them.
--
-- Idempotent: ON DUPLICATE KEY UPDATE - safe to re-run.

USE `imei_checker`;

INSERT INTO `service_prices` (`code`, `name`, `description`, `cost`, `active`) VALUES
    -- Free local TAC lookup (no provider call).
    ('IMEI_BASIC',                'Free IMEI Check',                                                                                'Brand, model and basic specs - resolved locally from the TAC.',                  0.00, 1),

    -- ----- Apple Featured -----
    ('APPLE_BASIC',               'Apple Basic Info',                                                                               'Apple basic info from upstream.',                                                0.10, 1), -- wholesale 0.03 - sheet "Apple Check Basic"
    ('APPLE_CARRIER_LITE',        'Apple Carrier (Lite)',                                                                           'Original carrier - lite tier.',                                                  0.08, 1), -- wholesale 0.04 (×2)
    ('APPLE_CARRIER_PRO',         'Apple Carrier (Pro)',                                                                            'Original carrier + extra detail.',                                               0.16, 1), -- wholesale 0.08 (×2)
    ('APPLE_CARRIER_PRO_PLUS',    'Apple Carrier (Pro Plus)',                                                                       'Carrier report with deepest detail.',                                            0.26, 1), -- wholesale 0.13 (×2)
    ('APPLE_MAX_INFO',            'Apple Max Info (Premium)',                                                                       'Premium combined report.',                                                       0.70, 1), -- wholesale 0.35 (×2)

    -- ----- Apple iCloud -----
    ('APPLE_ICLOUD_STATUS',       'Apple iCloud (ON / OFF)',                                                                        'Find My iPhone activation lock status.',                                         0.01, 1), -- wholesale 0.008 - sheet "FMI iCloud (ON/OFF)"
    ('APPLE_ICLOUD_CLEAN',        'Apple iCloud (Clean / Lost)',                                                                    'iCloud Clean / Lost status.',                                                    0.03, 1), -- wholesale 0.02 - sheet
    ('APPLE_ICLOUD_CLEAN_SN',     'Apple iCloud (Clean / Lost) SN',                                                                 'Clean / Lost status by serial number.',                                          0.10, 1), -- wholesale 0.05 (×2)
    ('APPLE_ICLOUD_ID_HINT',      'Apple iCloud ID Hint',                                                                           'Masked Apple ID the device is signed in to.',                                    0.80, 1), -- wholesale 0.40 (×2)
    ('APPLE_MAC_ICLOUD_STATUS',   'Apple iCloud MACBOOK/iMAC (ON / OFF)',                                                           'Find My status for MacBook / iMac.',                                             0.30, 1), -- wholesale 0.10 - sheet
    ('APPLE_MAC_ICLOUD_CLEAN',    'Apple iCloud MACBOOK/iMAC (Clean / Lost)',                                                       'Lost-mode status for MacBook / iMac.',                                           0.40, 1), -- wholesale 0.20 - sheet

    -- ----- Apple MDM -----
    ('APPLE_MDM',                 'Apple MDM (ON / OFF)',                                                                           'MDM enrollment status.',                                                         0.35, 1), -- wholesale 0.15 - sheet
    ('APPLE_MDM_SN',              'Apple MDM (ON / OFF) SN',                                                                        'MDM status by serial number.',                                                   0.30, 1), -- wholesale 0.15 (×2)
    ('APPLE_MDM_FMI',             'Apple MDM + FMI (ON / OFF)',                                                                     'Combined MDM + Find My iPhone status.',                                          0.50, 1), -- wholesale 0.25 (×2)

    -- ----- Apple Device Info -----
    ('APPLE_WARRANTY',            'Apple Warranty (Activation Info)',                                                               'Activation date and remaining warranty.',                                        0.04, 1), -- wholesale 0.02 - sheet
    ('APPLE_WARRANTY_SN',         'Apple Warranty (Activation Info) SN',                                                            'Warranty / activation by serial number.',                                        0.05, 1), -- wholesale 0.008 (×2 capped at min 0.05)
    ('APPLE_PART_NUMBER',         'Apple Part Number / MPN',                                                                        'Apple Part Number / MPN.',                                                       0.14, 1), -- wholesale 0.07 (×2)
    ('APPLE_SIM_LOCK',            'Apple SIM-LOCK Status',                                                                          'SIM-lock status.',                                                               0.05, 1), -- wholesale 0.018 (×2 capped at min 0.05)
    ('APPLE_GSX_TETHER',          'Apple GSX Next Tether Policy',                                                                   'Apple GSX next tether policy.',                                                  0.20, 1), -- wholesale 0.10 (×2)

    -- ----- Apple GSX (Premium) -----
    ('APPLE_CASE_REPAIR_HISTORY', 'Apple Case History, Repair History',                                                             'Apple case and repair history from GSX.',                                        1.20, 1), -- wholesale 1.45 - sheet (note: below wholesale!)
    ('APPLE_SOLD_BY_COVERAGE',    'Apple Sold By, Coverage (Max Info)',                                                             'Original retailer + coverage status.',                                           2.00, 1), -- wholesale 1.70 - sheet
    ('APPLE_SOLD_BY_HISTORY',     'Apple Sold By, Case History, Activation Policy',                                                 'Sold-by + case history + activation policy.',                                    4.20, 1), -- wholesale 2.10 (×2)
    ('APPLE_GSX_LIGHT',           'Apple Sold By, Case History, Replacement, GSX Activation Policy',                                'Sold-by + case + replacement + activation policy.',                              1.00, 1), -- wholesale 0.75 - sheet
    ('APPLE_FULL_GSX',            'Apple Sold By, Case History, Replacement, Activation Policy [ICCID & MAC] (Full GSX)',           'Full GSX dataset including ICCID & MAC.',                                        2.30, 1), -- wholesale 2.20 - sheet
    ('APPLE_GSX_MAX',             'Apple Sold By, Case History, Replacement, Repair, GSX Activation Policy (Max Info)',             'GSX max-info: sold-by + case + replacement + repair + activation policy.',       2.60, 1), -- wholesale 1.30 (×2)

    -- ----- Worldwide Blacklist -----
    ('BLACKLIST_SIMPLE',          'WorldWide Blacklist Status (SIMPLE INFO)',                                                       'Quick yes/no GSMA blacklist status.',                                            0.05, 1), -- wholesale 0.008 (×2 capped at min 0.05) - sheet "GSMA Blacklist Status" set 0.01 below provider; using min
    ('BLACKLIST_FULL',            'WorldWide Blacklist Status (FULL INFO)',                                                         'Full GSMA blacklist report with reporting carrier and date.',                    0.10, 1), -- wholesale 0.04 - sheet "GSMA Blacklist Full Report"

    -- ----- Other Brands -----
    ('PIXEL_INFO',                'Google Pixel Info',                                                                              'Google Pixel: model + warranty + country.',                                      0.20, 1), -- wholesale 0.10 (×2)
    ('XIAOMI_STATUS',             'XIAOMI (ON / OFF)',                                                                              'Xiaomi: Mi Account / Find Device lock.',                                         0.10, 1), -- wholesale 0.01 - sheet
    ('HUAWEI_INFO',               'HUAWEI INFO',                                                                                    'Huawei: model + warranty + country.',                                            0.08, 1), -- wholesale 0.04 (×2)
    ('HONOR_INFO',                'HONOR INFO',                                                                                     'Honor: model + warranty + country.',                                             0.10, 1), -- wholesale 0.04 - sheet
    ('SAMSUNG_INFO',              'SAMSUNG INFO',                                                                                   'Samsung: model + warranty.',                                                     0.10, 1), -- wholesale 0.03 - sheet
    ('SAMSUNG_KNOX',              'SAMSUNG (Knox Guard Status, Samsung Lock - ON/OFF)',                                             'Samsung Knox Guard / Samsung Lock status.',                                      0.20, 1), -- wholesale 0.10 (×2)
    ('MOTOROLA_INFO',             'MOTOROLA INFO',                                                                                  'Motorola: model + warranty.',                                                    0.10, 1), -- wholesale 0.03 - sheet
    ('LENOVO_INFO',               'LENOVO INFO',                                                                                    'Lenovo: model + warranty.',                                                      0.10, 1), -- wholesale 0.04 - sheet

    -- ----- US Carriers -----
    ('TMOBILE_USA',               'T-Mobile - USA iPhone/Generic Status',                                                           'T-Mobile USA IMEI status.',                                                      0.10, 1), -- wholesale 0.01 - sheet
    ('TMOBILE_USA_PRO',           'T-Mobile - USA iPhone/Generic Status [PRO]',                                                     'T-Mobile USA status (Pro tier).',                                                0.06, 1), -- wholesale 0.03 (×2)
    ('VERIZON_USA_PRO',           'Verizon - USA iPhone/Generic Status [PRO]',                                                      'Verizon USA status (Pro tier).',                                                 0.06, 1), -- wholesale 0.03 (×2)

    -- ----- Phone number lookup -----
    ('YANDEX_ALICE',              'Yandex Alice Info',                                                                              'Phone owner info from Yandex Alice.',                                            0.60, 1), -- wholesale 0.30 (×2)
    ('HLR_LOOKUP',                'Home Location Register (HLR) Lookup',                                                            'HLR lookup for a phone number.',                                                 0.05, 1), -- wholesale 0.01 (×2 capped at min 0.05)
    ('NUMBER_TYPE',               'Number Type (NT) Lookup',                                                                        'Mobile / landline / VoIP classification.',                                       0.14, 1), -- wholesale 0.07 (×2)
    ('PING_SMS',                  'Ping-SMS',                                                                                       'Silent SMS ping.',                                                               0.20, 1), -- wholesale 0.10 (×2)
    ('PING_SMS_S2',               'Ping-SMS (Server 2)',                                                                            'Silent SMS ping via secondary route.',                                           0.70, 1)  -- wholesale 0.35 (×2)
ON DUPLICATE KEY UPDATE
    `name`        = VALUES(`name`),
    `description` = VALUES(`description`),
    `cost`        = VALUES(`cost`),
    `active`      = VALUES(`active`);

-- Hard-deactivate any phantom codes that lived here pre-anchor.
-- (Rows stay so historical service_usages refs remain valid.)
UPDATE `service_prices` SET `active` = 0 WHERE `code` IN (
    'APPLE_MINI','APPLE_PRO','APPLE_ULTIMATE','UNIVERSAL_CHECK',
    'APPLE_ICLOUD_CLEAN_S2','APPLE_BLACKLIST_SIMPLE','APPLE_BLACKLIST_FULL',
    'ITEL_INFO','ACER_INFO','LG_INFO','OPPO_INFO','ONEPLUS_INFO',
    'KYOCERA_INFO','ZTE_INFO'
);
