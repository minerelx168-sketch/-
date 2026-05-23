<?php
declare(strict_types=1);

/**
 * Groupings used by the unified /check.php dropdown.
 *
 * One entry = one <optgroup>. Codes inside are looked up against
 * service_prices.cost at render time so prices stay in sync with the
 * source of truth. Codes that aren't active are silently skipped.
 *
 * To add a new check tier: insert it into sql/seed.sql, slot the code
 * into the right group below (or create a new group). The provider ID
 * still has to land in data/service_provider_map.php before the
 * service can serve real lookups.
 */

return [
    [
        'name'  => 'Free',
        'codes' => ['IMEI_BASIC'],
    ],
    [
        'name'  => 'Featured Apple Checks',
        'codes' => ['APPLE_BASIC'],
    ],
    [
        'name'  => 'Apple iCloud Status',
        'codes' => [
            'APPLE_ICLOUD_STATUS',
            'APPLE_ICLOUD_CLEAN',
            'APPLE_MAC_ICLOUD_STATUS',
            'APPLE_MAC_ICLOUD_CLEAN',
        ],
    ],
    [
        'name'  => 'Apple Device Info',
        'codes' => [
            'APPLE_WARRANTY',
            'APPLE_SIM_LOCK',
            'APPLE_PART_NUMBER',
            'APPLE_MDM',
        ],
    ],
    [
        'name'  => 'Apple GSX (Premium)',
        'codes' => [
            'APPLE_GSX_LIGHT',
            'APPLE_CASE_REPAIR_HISTORY',
            'APPLE_SOLD_BY_COVERAGE',
            'APPLE_FULL_GSX',
        ],
    ],
    [
        'name'  => 'Brand Checks',
        'codes' => [
            'SAMSUNG_INFO',
            'HUAWEI_INFO',
            'XIAOMI_STATUS',
            'HONOR_INFO',
            'PIXEL_INFO',
            'MOTOROLA_INFO',
            'LENOVO_INFO',
        ],
    ],
    [
        'name'  => 'Carrier Checks',
        'codes' => ['TMOBILE_USA'],
    ],
    [
        'name'  => 'Worldwide Blacklist',
        'codes' => ['BLACKLIST_SIMPLE', 'BLACKLIST_FULL'],
    ],
];
