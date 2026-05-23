<?php
declare(strict_types=1);

/**
 * Maps our internal service_code -> unlock-service.net's service ID.
 * Sourced from https://unlock-service.net/instantservices.php
 *
 * `null` = no provider call (local IMEI parse from TAC). Used for the
 * free IMEI_BASIC tier so the homepage check works without API credits.
 *
 * To add a service:
 *   1. Add a row here mapping CODE -> unlock-service ID (integer or null).
 *   2. Add a matching INSERT to sql/seed.sql with name/description/cost.
 *   3. Optionally add a /service-<slug> landing page in data/services.php.
 */

return [
    // Free local lookup
    'IMEI_BASIC'                  => null,

    // ----- Apple -----
    'APPLE_BASIC'                 => 214,
    'APPLE_CARRIER_LITE'          => 444,
    'APPLE_CARRIER_PRO'           => 445,
    'APPLE_CARRIER_PRO_PLUS'      => 448,
    'APPLE_MAX_INFO'              => 976,
    'APPLE_WARRANTY'              => 806,
    'APPLE_WARRANTY_SN'           => 343,
    'APPLE_PART_NUMBER'           => 504,
    'APPLE_ICLOUD_ID_HINT'        => 176,
    'APPLE_MAC_ICLOUD_STATUS'     => 503,
    'APPLE_MAC_ICLOUD_CLEAN'      => 574,
    'APPLE_ICLOUD_STATUS'         => 10,
    'APPLE_ICLOUD_CLEAN'          => 11,
    'APPLE_ICLOUD_CLEAN_SN'       => 847,
    'APPLE_MDM'                   => 845,
    'APPLE_MDM_SN'                => 457,
    'APPLE_MDM_FMI'               => 299,
    'APPLE_SIM_LOCK'              => 215,
    'APPLE_GSX_TETHER'            => 945,
    'APPLE_CASE_REPAIR_HISTORY'   => 691,
    'APPLE_SOLD_BY_COVERAGE'      => 623,
    'APPLE_SOLD_BY_HISTORY'       => 707,
    'APPLE_FULL_GSX'              => 348,
    'APPLE_GSX_LIGHT'             => 979,
    'APPLE_GSX_MAX'               => 981,

    // ----- Blacklist -----
    'BLACKLIST_SIMPLE'            => 419,
    'BLACKLIST_FULL'              => 66,

    // ----- Android brands -----
    'PIXEL_INFO'                  => 584,
    'XIAOMI_STATUS'               => 439,
    'HUAWEI_INFO'                 => 130,
    'HONOR_INFO'                  => 936,
    'SAMSUNG_INFO'                => 9,
    'SAMSUNG_KNOX'                => 851,
    'MOTOROLA_INFO'               => 132,
    'LENOVO_INFO'                 => 969,

    // ----- US carriers -----
    'TMOBILE_USA'                 => 688,
    'TMOBILE_USA_PRO'             => 127,
    'VERIZON_USA_PRO'             => 423,

    // ----- Misc / SMS -----
    'YANDEX_ALICE'                => 932,
    'HLR_LOOKUP'                  => 616,
    'NUMBER_TYPE'                 => 617,
    'PING_SMS'                    => 618,
    'PING_SMS_S2'                 => 619,
];
