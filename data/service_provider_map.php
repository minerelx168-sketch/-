<?php
declare(strict_types=1);

/**
 * Maps our internal service_code -> unlock-service.net's service ID.
 * Source for IDs: https://unlock-service.net/instantservices.php
 *
 * Semantics:
 *   - integer  -> POST to unlock-service with this service ID
 *   - null     -> free local TAC lookup (no external call, no credit
 *                 spend; only used for IMEI_BASIC)
 *
 * Codes that don't appear in this map will cause credits_deduct() to
 * throw "Unknown service code". Add a row here AND a sql/seed.sql entry
 * when introducing a new service.
 *
 * TODO entries below have no provider ID yet - they are present so that
 * credits_deduct() can recognise the code, but the matching service_prices
 * row is active=0 until a real provider ID lands.
 */

return [
    // Free local lookup
    'IMEI_BASIC'                  => null,

    // ----- Featured Apple tiers (provider IDs unknown - kept inactive) -----
    'APPLE_MINI'                  => null,   // TODO unlock-service id
    'APPLE_BASIC'                 => 214,
    'APPLE_PRO'                   => null,   // TODO unlock-service id
    'APPLE_ULTIMATE'              => null,   // TODO unlock-service id
    'UNIVERSAL_CHECK'             => null,   // TODO unlock-service id

    // ----- Apple iCloud / MDM / status -----
    'APPLE_ICLOUD_STATUS'         => 10,
    'APPLE_ICLOUD_CLEAN'          => 11,
    'APPLE_ICLOUD_CLEAN_S2'       => null,   // TODO unlock-service id
    'APPLE_MAC_ICLOUD_STATUS'     => 503,
    'APPLE_MAC_ICLOUD_CLEAN'      => 574,
    'APPLE_MDM'                   => 845,
    'APPLE_WARRANTY'              => 806,
    'APPLE_PART_NUMBER'           => 504,
    'APPLE_SIM_LOCK'              => 215,
    'APPLE_BLACKLIST_SIMPLE'      => null,   // TODO unlock-service id (Apple-specific)
    'APPLE_BLACKLIST_FULL'        => null,   // TODO unlock-service id (Apple-specific)

    // ----- US carriers -----
    'TMOBILE_USA'                 => 688,

    // ----- Other brands -----
    'ITEL_INFO'                   => null,   // TODO unlock-service id
    'SAMSUNG_INFO'                => 9,
    'XIAOMI_STATUS'               => 439,
    'ACER_INFO'                   => null,   // TODO unlock-service id
    'LG_INFO'                     => null,   // TODO unlock-service id
    'LENOVO_INFO'                 => 969,
    'OPPO_INFO'                   => null,   // TODO unlock-service id
    'ONEPLUS_INFO'                => null,   // TODO unlock-service id
    'PIXEL_INFO'                  => 584,
    'MOTOROLA_INFO'               => 132,
    'KYOCERA_INFO'                => null,   // TODO unlock-service id
    'HUAWEI_INFO'                 => 130,
    'HONOR_INFO'                  => 936,
    'ZTE_INFO'                    => null,   // TODO unlock-service id

    // ----- Generic blacklist -----
    'BLACKLIST_SIMPLE'            => 419,
    'BLACKLIST_FULL'              => 66,

    // ----- Apple GSX -----
    'APPLE_CASE_REPAIR_HISTORY'   => 691,
    'APPLE_SOLD_BY_COVERAGE'      => 623,
    'APPLE_GSX_LIGHT'             => 979,
    'APPLE_FULL_GSX'              => 348,

    // ----- Deprecated (kept for old service_usages refs; not on menu) -----
    'APPLE_CARRIER_LITE'          => 444,
    'APPLE_CARRIER_PRO'           => 445,
    'APPLE_CARRIER_PRO_PLUS'      => 448,
    'APPLE_MAX_INFO'              => 976,
    'APPLE_WARRANTY_SN'           => 343,
    'APPLE_ICLOUD_ID_HINT'        => 176,
    'APPLE_ICLOUD_CLEAN_SN'       => 847,
    'APPLE_MDM_SN'                => 457,
    'APPLE_MDM_FMI'               => 299,
    'APPLE_GSX_TETHER'            => 945,
    'APPLE_SOLD_BY_HISTORY'       => 707,
    'APPLE_GSX_MAX'               => 981,
    'SAMSUNG_KNOX'                => 851,
    'TMOBILE_USA_PRO'             => 127,
    'VERIZON_USA_PRO'             => 423,
    'YANDEX_ALICE'                => 932,
    'HLR_LOOKUP'                  => 616,
    'NUMBER_TYPE'                 => 617,
    'PING_SMS'                    => 618,
    'PING_SMS_S2'                 => 619,
];
