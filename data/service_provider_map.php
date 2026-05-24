<?php
declare(strict_types=1);

/**
 * service_code  ->  upstream provider service ID
 *
 * 1:1 anchor on the upstream API catalog (60 IMEI Check services:
 * 43 ACTIVE wired below + 17 PENDING listed in seed.sql with active=0).
 * Source synced from the provider's imeiservicelist endpoint - see
 * scripts/fetch-provider-services.php to re-fetch.
 *
 * `null` = free local TAC lookup (no provider call, no charge).
 * Reserved for IMEI_BASIC.
 *
 * Codes that don't appear in this map will cause credits_deduct() to
 * throw "Unknown service code". That's fine for the 17 pending rows -
 * they're also active=0 in seed.sql so check.php's dropdown query
 * (WHERE active=1) hides them, and a direct POST is rejected before
 * any wallet mutation.
 *
 * To activate a pending row:
 *   1. Get the upstream Service ID from provider support.
 *   2. Uncomment the matching `'CODE' => <id>` line in the PENDING
 *      block below.
 *   3. In sql/seed.sql flip the row's active=0 -> 1 and set cost to
 *      max(0.05, wholesale * 2) (or the operator's selling-sheet
 *      price); re-run the seed.
 *
 * The phantom codes that used to live here (APPLE_MINI / APPLE_PRO /
 * APPLE_ULTIMATE / UNIVERSAL_CHECK / APPLE_ICLOUD_CLEAN_S2 /
 * APPLE_BLACKLIST_* / ITEL_INFO / ACER_INFO / LG_INFO / OPPO_INFO /
 * ONEPLUS_INFO / KYOCERA_INFO) have been removed because the provider
 * does not carry them. If a future provider does, add new codes here
 * AND a matching seed.sql row.
 */

return [
    // Free local lookup
    'IMEI_BASIC'                  => null,

    // Apple Featured
    'APPLE_BASIC'                 => 214,
    'APPLE_CARRIER_LITE'          => 444,
    'APPLE_CARRIER_PRO'           => 445,
    'APPLE_CARRIER_PRO_PLUS'      => 448,
    'APPLE_MAX_INFO'              => 976,

    // Apple iCloud
    'APPLE_ICLOUD_STATUS'         => 10,
    'APPLE_ICLOUD_CLEAN'          => 11,
    'APPLE_ICLOUD_CLEAN_SN'       => 847,
    'APPLE_ICLOUD_ID_HINT'        => 176,
    'APPLE_MAC_ICLOUD_STATUS'     => 503,
    'APPLE_MAC_ICLOUD_CLEAN'      => 574,

    // Apple MDM
    'APPLE_MDM'                   => 845,
    'APPLE_MDM_SN'                => 457,
    'APPLE_MDM_FMI'               => 299,

    // Apple Device Info
    'APPLE_WARRANTY'              => 806,
    'APPLE_WARRANTY_SN'           => 343,
    'APPLE_PART_NUMBER'           => 504,
    'APPLE_SIM_LOCK'              => 215,
    'APPLE_GSX_TETHER'            => 945,

    // Apple GSX (Premium)
    'APPLE_CASE_REPAIR_HISTORY'   => 691,
    'APPLE_SOLD_BY_COVERAGE'      => 623,
    'APPLE_SOLD_BY_HISTORY'       => 707,
    'APPLE_GSX_LIGHT'             => 979,
    'APPLE_FULL_GSX'              => 348,
    'APPLE_GSX_MAX'               => 981,

    // Worldwide Blacklist
    'BLACKLIST_SIMPLE'            => 419,
    'BLACKLIST_FULL'              => 66,

    // Other Brands
    'PIXEL_INFO'                  => 584,
    'XIAOMI_STATUS'               => 439,
    'HUAWEI_INFO'                 => 130,
    'HONOR_INFO'                  => 936,
    'SAMSUNG_INFO'                => 9,
    'SAMSUNG_KNOX'                => 851,
    'MOTOROLA_INFO'               => 132,
    'LENOVO_INFO'                 => 969,

    // US Carriers
    'TMOBILE_USA'                 => 688,
    'TMOBILE_USA_PRO'             => 127,
    'VERIZON_USA_PRO'             => 423,

    // Phone number lookup
    'YANDEX_ALICE'                => 932,
    'HLR_LOOKUP'                  => 616,
    'NUMBER_TYPE'                 => 617,
    'PING_SMS'                    => 618,
    'PING_SMS_S2'                 => 619,

    // ----- PENDING (awaiting Service ID from provider) -----
    // Uncomment each row when its real Service ID arrives, then flip
    // the matching seed.sql row's active to 1 and set its cost.
    //
    // Apple IMEI Check
    // 'APPLE_OWNER_ID_INFO'      => 0,
    // 'APPLE_IMEI_SN_CONVERT'    => 0,
    //
    // GSX Services (Instant)
    // 'APPLE_GSX_CASE_REPLACE'   => 0,
    // 'APPLE_GSX_SOLD_BY'        => 0,
    //
    // GSX Services (server 2 / non-instant)
    // 'APPLE_GSX_CASE_S2'        => 0,
    // 'APPLE_GSX_REPAIRS_S2'     => 0,
    // 'APPLE_GSX_CASE_DIAG_S2'   => 0,
    // 'APPLE_GSX_SOLD_POLICY_S2' => 0,
    // 'APPLE_GSX_FULL_LITE_S2'   => 0,
    // 'APPLE_FULL_GSX_S2'        => 0,
    //
    // GSX Services (Picture)
    // 'APPLE_GSX_CASE_PIC'         => 0,
    // 'APPLE_GSX_CASE_REPAIR_PIC'  => 0,
    // 'APPLE_GSX_SOLD_REPLACE_PIC' => 0,
    // 'APPLE_GSX_SOLD_DIAG_PIC'    => 0,
    // 'APPLE_FULL_GSX_PIC'         => 0,
    //
    // Other IMEI Check
    // 'ZTE_INFO'                 => 0,
    // 'SAMSUNG_INFO_S2'          => 0,
];
