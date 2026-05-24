<?php
declare(strict_types=1);

/**
 * service_code  ->  unlock-service.net service ID
 *
 * 1:1 anchor on the upstream API catalog (43 services). Source synced
 * from https://api.unlock-service.net/?action=imeiservicelist - see
 * scripts/fetch-provider-services.php to re-fetch.
 *
 * `null` = free local TAC lookup (no provider call, no charge).
 * Reserved for IMEI_BASIC.
 *
 * Codes that don't appear in this map will cause credits_deduct() to
 * throw "Unknown service code". The 13 phantom codes that used to live
 * here (APPLE_MINI / APPLE_PRO / APPLE_ULTIMATE / UNIVERSAL_CHECK /
 * APPLE_ICLOUD_CLEAN_S2 / APPLE_BLACKLIST_* / ITEL_INFO / ACER_INFO /
 * LG_INFO / OPPO_INFO / ONEPLUS_INFO / KYOCERA_INFO / ZTE_INFO) have
 * been removed because unlock-service.net does not carry them. If a
 * future provider does, add new codes here AND a matching seed.sql row.
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
];
