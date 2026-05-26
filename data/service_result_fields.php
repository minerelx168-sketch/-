<?php
declare(strict_types=1);

/**
 * Per-service result field templates.
 *
 * Each provider returns a big "Key: Value" blob that often repeats the same
 * fact under several aliases (Find My iPhone / Fmi, Sim-Lock / Sim Lock
 * Status, Registered / Is Registered ...). That makes the result card a long,
 * duplicated dump. This file pins, per service, exactly which fields to show
 * and in what order - mirroring the provider's own "Example:" for each
 * service so the customer sees a clean, predictable report.
 *
 * Shape:
 *   'SERVICE_CODE' => [ field, field, ... ]
 * where each field is either:
 *   'Label'                       match the response key "Label", show as "Label"
 *   ['Label', 'Alias1', 'Alias2'] show as "Label", but read the value from the
 *                                 first of Label/Alias1/Alias2 present in the
 *                                 response (case-insensitive). Lets one template
 *                                 absorb provider key variants.
 *
 * Matching is case-insensitive on trimmed keys; missing fields are simply
 * skipped ("Results depend on the data available in database"). A service
 * with NO entry here falls back to showing the full raw response unchanged,
 * so free/local lookups and the list-heavy GSX/warranty-history reports are
 * untouched until they get their own curated template.
 */

// Shared alias-bearing fields reused across the Apple device reports.
$model       = ['Model'];
$modelDesc   = ['Model Description', 'Model Configuration', 'Model Desc'];
$fmi         = ['Find My iPhone', 'Fmi', 'FMI'];
$icloud      = ['iCloud Status', 'Icloud'];
$blacklist   = ['Blacklist Status', 'US Block Status'];
$simlock     = ['Sim-Lock', 'Sim Lock', 'SIM-Lock', 'Sim Lock Status', 'SIM-Lock Status', 'SIM Lock'];
$replaced    = ['Replaced Device', 'Replaced', 'Replaced by Apple'];
$replacement = ['Replacement Device', 'Replacement'];
$refurb      = ['Refurbished', 'Refurbish'];
$demo        = ['Demo Device', 'Demo'];
$registered  = ['Registered', 'Is Registered'];
$validPurch  = ['Valid Purchase Date', 'Is Valid Purchase Date'];
$applecare   = ['AppleCare Eligible', 'Apple Care Eligible'];

// Apple carrier reports share a long common spine.
$carrierProSpine = [
    $model, $modelDesc, 'Model Number', 'Model Region',
    'Activation Status',
    'Telephone Technical Support', 'Telephone Technical Support Expiration Date',
    'Repairs and Service Coverage', 'Repairs and Service Coverage Expiration Date',
    $applecare, 'AppleCare Eligible Description',
    $validPurch, $registered, $refurb, $demo, $replacement, $replaced,
];

return [
    // ---- Apple Basic / Carrier tiers -------------------------------------
    'APPLE_BASIC' => [
        $model, $modelDesc, $refurb, $demo, $replacement, $replaced,
        $fmi, $icloud, $blacklist,
        'Warranty Status', 'Estimated Purchase Date', 'Purchased In', $simlock,
    ],

    'APPLE_CARRIER_LITE' => [
        $model, $modelDesc, $refurb, $demo, $replacement, $replaced,
        'Estimated Purchase Date', 'Purchased In',
        'Next Activation Policy ID', 'Next Activation Policy', $simlock,
    ],

    'APPLE_CARRIER_PRO' => array_merge($carrierProSpine, [
        $fmi, $icloud, $blacklist,
        'Warranty Status', 'Estimated Purchase Date', 'Purchased In',
        'Next Activation Policy ID', 'Next Activation Policy', $simlock,
    ]),

    'APPLE_CARRIER_PRO_PLUS' => array_merge($carrierProSpine, [
        'Open Repair Case',
        $fmi, $icloud, $blacklist,
        'Part Number', 'Part Number Type',
        'Warranty Status', 'Estimated Purchase Date', 'Purchased In',
        'Next Activation Policy ID', 'Next Activation Policy', $simlock,
    ]),

    'APPLE_MAX_INFO' => array_merge($carrierProSpine, [
        'Open Repair Case',
        $fmi, $icloud, 'MDM Status', $blacklist,
        'Blacklisted By', 'Blacklisted Country', 'Blacklisted On',
        'Blacklist Reason', 'Blacklist Description',
        'Part Number', 'Part Number Type',
        'Warranty Status', 'Estimated Purchase Date', 'Purchased In',
        'Next Activation Policy ID', 'Next Activation Policy', $simlock,
    ]),

    // ---- Apple iCloud ----------------------------------------------------
    'APPLE_ICLOUD_STATUS' => [$fmi],
    'APPLE_ICLOUD_CLEAN'  => [['FMI Status', 'FMI STATUS', 'iCloud Status', 'Find My iPhone']],
    'APPLE_ICLOUD_CLEAN_SN' => [['FMI Status', 'FMI STATUS', 'iCloud Status', 'Find My iPhone']],
    'APPLE_ICLOUD_ID_HINT'  => [['Apple ID', 'iCloud ID', 'Apple ID Hint']],
    'APPLE_MAC_ICLOUD_STATUS' => [$model, 'Serial Number', ['Find My Mac', 'Find My', 'Fmi']],
    'APPLE_MAC_ICLOUD_CLEAN'  => [$model, $modelDesc, 'Serial Number', ['Find My Mac', 'Find My', 'Fmi'], $icloud],

    // ---- Apple MDM -------------------------------------------------------
    'APPLE_MDM'     => [$model, 'IMEI', ['MDM Enrollment Status', 'MDM Status']],
    'APPLE_MDM_SN'  => [['MDM Enrollment Status', 'MDM Status']],
    'APPLE_MDM_FMI' => [$model, 'IMEI', ['MDM Enrollment Status', 'MDM Status'], ['Find My Mac', 'Find My iPhone', 'Fmi']],

    // ---- Apple device info ----------------------------------------------
    'APPLE_WARRANTY' => [
        $model, ['IMEI/SN', 'IMEI', 'Serial Number'],
        'Activation Status', 'Telephone Technical Support',
        ['Coverage Status', 'Warranty Status'], 'Coverage End Date',
        $applecare, 'Estimated Purchase Date', $registered, 'Loaner',
    ],
    'APPLE_WARRANTY_SN' => [
        $model, 'Serial Number', 'Activation Status', $applecare, $validPurch,
        ['Registered Device', 'Registered'], $replaced,
        'Telephone Technical Support', 'Telephone Technical Support Expiration Date',
        'Repairs and Service Coverage', 'Repairs and Service Expiration Date',
        'Warranty Status', 'Your device is covered for', 'Agreement Code',
        'Estimated Purchase Date',
    ],
    'APPLE_PART_NUMBER' => [
        $model, ['IMEI Number', 'IMEI'], 'Part Number',
        ['Part Number Country', 'Part Number Country'], ['Part Type', 'Part Number Type'],
    ],
    'APPLE_SIM_LOCK' => [
        $model, 'IMEI', 'MEID', 'Serial Number', 'Estimated Purchase Date', $simlock,
    ],
    'APPLE_GSX_TETHER' => [
        $model, ['IMEI/SN', 'IMEI', 'Serial Number'],
        ['Find My', 'Find My iPhone', 'Fmi'], 'Next Tether Policy', $simlock,
    ],

    // ---- Apple Sold By / Coverage (flat) --------------------------------
    'APPLE_SOLD_BY_COVERAGE' => [
        ['Description', 'Model Description'], $model,
        ['IMEI Number', 'IMEI'], ['IMEI2 Number', 'IMEI2'],
        ['MEID Number', 'MEID'], 'Serial Number', 'Product Version',
        'SIM1 Carrier', 'SIM2 Carrier',
        ['MDM Lock', 'MDM Status'], ['iCloud Lock', 'iCloud Status'], 'iCloud Status',
        'GSX Case History', 'GSX Replacement History',
        ['Coverage Status', 'Warranty Status'], 'Estimated Purchase Date',
        'Sold To Name', ['Purchase Country', 'Purchased In'],
        'Next Tether Policy', $simlock,
    ],

    // ---- Apple Full GSX (flat header; cases/replacement shown as "Not Found") --
    'APPLE_FULL_GSX' => [
        $modelDesc, 'Product Description', 'Config Code', 'Part Number',
        'Model Number', 'Product Line', 'Product Version', 'Last Unbrick Os Build',
        'Wireless Mac Address', ['CSN/CSN2/EID', 'CSN/CSN2E ID', 'CSNCSN2E ID'],
        'Purchase Date', 'First Activation Date', 'Last Restore Date', 'Unlock Date',
        ['Warranty Status Description', 'Warranty Status'],
        'Loaner', 'Unlocked', 'Personalized', 'Part Covered', 'Labor Covered',
        'Onsite Coverage', 'Limited Warranty',
        'Initial Activation Policy', 'Applied Activation Policy', 'Next Tether Policy',
        'Sold To Name', ['Purchase Country', 'Purchased In'],
        ['MDM Lock', 'MDM Status'], ['iCloud Lock', 'iCloud Status'], 'iCloud Status',
        ['SIM Lock', 'Sim-Lock', 'Sim Lock'], 'Cases', 'Replacement',
    ],

    // ---- Worldwide blacklist --------------------------------------------
    'BLACKLIST_SIMPLE' => [['Blacklist Status', 'BLACKLIST STATUS']],
    'BLACKLIST_FULL' => [
        ['Model Name', 'Model'], 'Model Number', 'Manufacturer',
        ['Blacklist Status', 'BLACKLIST STATUS'],
        'Blacklisted By', 'Blacklisted Country', 'Blacklisted On',
        'Blacklisted (FRAUD) By', 'Blacklisted (FRAUD) Country', 'Blacklisted (FRAUD) On',
        'Note', 'Details', 'IMEI',
    ],

    // ---- Other brands (flat) --------------------------------------------
    'PIXEL_INFO' => [
        $model, $modelDesc, 'Model Number', 'IMEI', 'IMEI2', 'Serial',
        ['Purchase Country', 'Purchased In'], 'Date',
        'Warranty Status', 'Warranty Start Date', 'Warranty End Date', 'Device Age',
    ],
    'XIAOMI_STATUS' => [
        $model, 'IMEI', 'Serial Number', 'Unlock Number (FSN)',
        'Warranty Status', 'Warranty Description', 'Warranty Start Date', 'Warranty End Date',
        ['Purchased In', 'Purchase Country'], 'MI Activation Lock', 'IMEI/KeyLock',
    ],
    'SAMSUNG_INFO' => [
        'IMEI Number', 'Master Number', 'Serial Number', 'Un Number',
        'Model Info', 'Model Number', 'Model Name',
        'Warranty Status', ['Purchase Country', 'Purchased In'], 'Warranty Until',
        'Production Date', 'Manufacturer', 'Carrier', 'Sold By', 'Ship To',
    ],
    'SAMSUNG_KNOX' => [
        'Model Info', 'IMEI', 'Serial Number', 'Model Number', 'Model Name', 'Model Desc',
        'Warranty Status', 'Production Date', 'Warranty Until', 'Manufacturer', 'Carrier',
        'Samsung Lock', 'Samsung Status', 'Lost Number', 'Lost Message',
        '[KG] Registed', '[KG] Company', '[KG] Device ID', '[KG] Device state',
    ],

    // ---- US carriers -----------------------------------------------------
    'TMOBILE_USA' => [
        $model, 'Manufacturer', 'IMEI', 'IMEI2', 'Status', 'ESN Status',
    ],
    'TMOBILE_USA_PRO' => [
        $model, 'Manufacturer', 'IMEI', 'IMEI2', 'eSIM CSN', 'eSIM Supported',
        'Status', 'Status Description', 'ESN Status',
    ],
    'VERIZON_USA_PRO' => [
        $model, 'IMEI', 'Status', 'Part Number', 'Product ID', 'Device SKU',
    ],

    // ---- Phone number lookups -------------------------------------------
    'HLR_LOOKUP' => [
        'Number', 'Original Network Name', 'Original Country Name', 'Original Country Prefix',
        'Ported', 'Ported Network Name', 'Ported Country Name', 'Ported Country Prefix',
        'Roaming', 'Local Time', 'Status',
    ],
    'PING_SMS' => [
        'Number', 'Network Name', 'Country', 'Region', 'Status', 'Status Description',
    ],
    'PING_SMS_S2' => [
        'Number', 'Network Name', 'Country', 'Region', 'Status', 'Status Description',
    ],
];
