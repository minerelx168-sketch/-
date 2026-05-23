<?php
declare(strict_types=1);

/**
 * Service landing pages.
 *
 * One entry = one /service-<slug> page. Each entry MUST set:
 *   - slug         URL key (lowercase, dash-separated)
 *   - code         service_prices.code (matches sql/seed.sql)
 *                  This is also the key into data/service_provider_map.php
 *   - name         heading rendered on the page (HTML allowed)
 *   - icon         icon name registered in includes/icons.php
 *   - tagline      short subtitle under the heading
 *   - description  longer body copy in the "About this check" section
 *   - free         true for the local IMEI_BASIC tier (no charge)
 *
 * We deliberately do NOT keep a separate slug -> code map here -
 * service.php just reads `code` straight off the matched entry.
 *
 * The full price catalog (43+ services) lives in sql/seed.sql; this file
 * is just the curated short list we surface on the marketing nav.
 */

return [
    [
        'slug'        => 'free-imei-check',
        'code'        => 'IMEI_BASIC',
        'name'        => 'Free IMEI Check',
        'icon'        => 'info',
        'tagline'     => 'Brand, model and basic specs from any IMEI.',
        'description' =>
            'Identify any GSM mobile phone in under a second. We resolve the '
            . 'first 8 digits (TAC) against our local catalog and return the '
            . 'manufacturer, model and basic specs. No sign-in required and '
            . 'no credits are charged.',
        'free'        => true,
    ],
    [
        'slug'        => 'apple-basic',
        'code'        => 'APPLE_BASIC',
        'name'        => 'Apple Basic Info',
        'icon'        => 'phone',
        'tagline'     => 'iPhone / iPad brand, model and base specs.',
        'description' =>
            'A targeted lookup for Apple devices that returns the exact model, '
            . 'colour, storage, year of release and base spec sheet. Works on '
            . 'every iPhone and iPad ever sold worldwide.',
        'free'        => false,
    ],
    [
        'slug'        => 'apple-icloud',
        'code'        => 'APPLE_ICLOUD_CLEAN',
        'name'        => 'iCloud Activation Lock',
        'icon'        => 'cloud',
        'tagline'     => 'Find My iPhone status: Clean or Lost.',
        'description' =>
            'For Apple devices only. Returns the iCloud activation lock status '
            . '(Clean / Lost) as reported by Apple. A device flagged Lost has '
            . 'been reported missing and cannot be re-activated without the '
            . 'original Apple ID.',
        'free'        => false,
    ],
    [
        'slug'        => 'apple-carrier',
        'code'        => 'APPLE_CARRIER_PRO',
        'name'        => 'Apple Carrier &amp; SIM-Lock',
        'icon'        => 'signal',
        'tagline'     => 'Original carrier, country and SIM-lock status.',
        'description' =>
            'Reveals which carrier the iPhone was originally sold on, the country '
            . 'of purchase and whether the SIM is locked to that carrier. Essential '
            . 'before buying second-hand to avoid an unusable phone.',
        'free'        => false,
    ],
    [
        'slug'        => 'apple-warranty',
        'code'        => 'APPLE_WARRANTY',
        'name'        => 'Apple Warranty &amp; Activation Date',
        'icon'        => 'shield',
        'tagline'     => 'Activation date and remaining warranty coverage.',
        'description' =>
            'When was this iPhone first activated, and is it still under '
            . 'Apple\'s limited warranty? Activation Lock alone doesn\'t tell '
            . 'you that - this check does.',
        'free'        => false,
    ],
    [
        'slug'        => 'apple-sim-lock',
        'code'        => 'APPLE_SIM_LOCK',
        'name'        => 'Apple SIM-Lock Status',
        'icon'        => 'specs',
        'tagline'     => 'Quick check: is the SIM slot carrier-locked?',
        'description' =>
            'A lightweight check that returns just the SIM-lock status. Useful '
            . 'as a fast pre-purchase verification when you don\'t need the '
            . 'full carrier report.',
        'free'        => false,
    ],
    [
        'slug'        => 'apple-max-info',
        'code'        => 'APPLE_MAX_INFO',
        'name'        => 'Apple Max Info (Premium)',
        'icon'        => 'report',
        'tagline'     => 'Full Apple report: model, carrier, warranty, sold-by.',
        'description' =>
            'Our most popular Apple report. Combines model, carrier, warranty, '
            . 'activation date and sold-by retailer in a single response.',
        'free'        => false,
    ],
    [
        'slug'        => 'apple-full-gsx',
        'code'        => 'APPLE_FULL_GSX',
        'name'        => 'Apple Full GSX Report',
        'icon'        => 'report',
        'tagline'     => 'Full GSX dump: case history, replacement, ICCID &amp; MAC.',
        'description' =>
            'The full GSX dataset Apple gives to authorised service providers. '
            . 'Includes original retailer, case + repair history, replacement '
            . 'flags, activation policy, ICCID and MAC address.',
        'free'        => false,
    ],
    [
        'slug'        => 'blacklist',
        'code'        => 'BLACKLIST_FULL',
        'name'        => 'WorldWide Blacklist',
        'icon'        => 'ban',
        'tagline'     => 'Lost / stolen status across global carriers.',
        'description' =>
            'Cross-checks the IMEI against worldwide carrier blacklists. A '
            . 'blacklisted device usually cannot make calls or use data on '
            . 'the carrier that reported it, and resale is restricted in '
            . 'many countries.',
        'free'        => false,
    ],
    [
        'slug'        => 'samsung-info',
        'code'        => 'SAMSUNG_INFO',
        'name'        => 'Samsung Info',
        'icon'        => 'phone',
        'tagline'     => 'Galaxy brand, model, colour and IMEI info.',
        'description' =>
            'Returns the exact Samsung Galaxy model, colour, storage and base '
            . 'specs. Covers Galaxy S, Note, A, M, Z Fold/Flip and Tab lines.',
        'free'        => false,
    ],
    [
        'slug'        => 'samsung-knox',
        'code'        => 'SAMSUNG_KNOX',
        'name'        => 'Samsung Knox Guard Status',
        'icon'        => 'shield',
        'tagline'     => 'Knox Guard / Samsung Lock: ON or OFF.',
        'description' =>
            'Knox Guard is Samsung\'s carrier-side lock equivalent to iCloud '
            . 'activation lock. A Knox-locked Galaxy cannot be reset and '
            . 'remains tied to the original carrier or enterprise.',
        'free'        => false,
    ],
    [
        'slug'        => 'huawei-info',
        'code'        => 'HUAWEI_INFO',
        'name'        => 'Huawei Info',
        'icon'        => 'phone',
        'tagline'     => 'Huawei brand, model, colour and base specs.',
        'description' =>
            'Identifies any Huawei device by IMEI: model, colour, storage '
            . 'and base specs. Covers P, Mate, Nova and Y series.',
        'free'        => false,
    ],
    [
        'slug'        => 'xiaomi-status',
        'code'        => 'XIAOMI_STATUS',
        'name'        => 'Xiaomi (ON / OFF)',
        'icon'        => 'cloud',
        'tagline'     => 'Mi Account / Find Device lock status.',
        'description' =>
            'Returns the Mi Account / Find Device status (ON / OFF) for '
            . 'Xiaomi, Redmi and POCO phones. A locked device cannot be '
            . 'reset without the original Mi credentials.',
        'free'        => false,
    ],
];
