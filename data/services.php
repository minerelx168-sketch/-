<?php
declare(strict_types=1);

/**
 * Catalog of IMEI lookup services we expose to visitors. Each one is a
 * landing page that pitches a specific type of check and forwards the
 * IMEI to /api/check.php with the matching service ID.
 *
 * Adjust the `provider_id` to match your IMEI.info (or sickw) service IDs.
 * Set `provider_id` to null for "Basic info" / default service.
 */

return [
    [
        'slug'        => 'free-imei-check',
        'name'        => 'Free IMEI Check',
        'icon'        => '🔍',
        'tagline'     => 'Brand, model and basic specs from any IMEI.',
        'description' =>
            'The fastest way to identify any mobile phone. Enter the 15-digit '
            . 'IMEI, and we return the manufacturer, model, model number and '
            . 'basic specifications. Works on iPhone, Samsung, Huawei, Xiaomi, '
            . 'and any GSM phone in the world.',
        'provider_id' => '0',
        'free'        => true,
    ],
    [
        'slug'        => 'blacklist-check',
        'name'        => 'Blacklist Status Check',
        'icon'        => '⛔',
        'tagline'     => 'See if a device is reported lost or stolen.',
        'description' =>
            'Check whether an IMEI has been blacklisted by carriers worldwide. '
            . 'Useful before buying a used phone — a blacklisted IMEI usually '
            . 'cannot make calls or use data on the affected networks.',
        'provider_id' => '1',
        'free'        => false,
    ],
    [
        'slug'        => 'carrier-check',
        'name'        => 'Carrier &amp; SIM-Lock Check',
        'icon'        => '📶',
        'tagline'     => 'Find the original carrier and whether the phone is locked.',
        'description' =>
            'Discover which network the device was originally sold on, the '
            . 'country of purchase, and whether the SIM is locked to a specific '
            . 'carrier. Crucial before buying second-hand.',
        'provider_id' => '2',
        'free'        => false,
    ],
    [
        'slug'        => 'icloud-status',
        'name'        => 'iCloud Activation Lock',
        'icon'        => '☁️',
        'tagline'     => 'Verify iCloud / Find My iPhone status on Apple devices.',
        'description' =>
            'For Apple devices only. Reveals whether iCloud (Find My iPhone) '
            . 'activation lock is enabled. A locked device cannot be re-activated '
            . 'without the original Apple ID and password.',
        'provider_id' => '3',
        'free'        => false,
    ],
    [
        'slug'        => 'warranty-check',
        'name'        => 'Warranty &amp; Activation Date',
        'icon'        => '🛡️',
        'tagline'     => 'Activation date, warranty status and coverage info.',
        'description' =>
            'Check when a device was first activated, whether it is still '
            . 'under manufacturer warranty, and what coverage remains. '
            . 'Particularly useful for Apple and Samsung devices.',
        'provider_id' => '4',
        'free'        => false,
    ],
    [
        'slug'        => 'model-info',
        'name'        => 'Full Model Specifications',
        'icon'        => '📋',
        'tagline'     => 'Detailed hardware specs based on the TAC.',
        'description' =>
            'A deeper report on the device hardware: chipset, storage, RAM, '
            . 'colour, year of release, and release region. Derived from the '
            . 'Type Allocation Code (first 8 digits of the IMEI).',
        'provider_id' => '5',
        'free'        => true,
    ],
];
