<?php
declare(strict_types=1);

/**
 * Static catalog of common mobile phone brands and a few representative
 * models per brand. Mirrors the baseimei.com /imei/<brand> structure so
 * each entry can render its own page without requiring a DB.
 *
 * Each brand: slug (URL key), name, emoji (placeholder logo), models.
 */

return [
    [
        'slug'    => 'apple',
        'name'    => 'Apple',
        'emoji'   => '🍎',
        'country' => 'United States',
        'models'  => [
            'iPhone 15 Pro Max', 'iPhone 15 Pro', 'iPhone 15 Plus', 'iPhone 15',
            'iPhone 14 Pro Max', 'iPhone 14 Pro', 'iPhone 14', 'iPhone 13',
            'iPhone 12', 'iPhone SE (3rd gen)', 'iPhone 11', 'iPhone XR',
        ],
    ],
    [
        'slug'    => 'samsung',
        'name'    => 'Samsung',
        'emoji'   => '📱',
        'country' => 'South Korea',
        'models'  => [
            'Galaxy S24 Ultra', 'Galaxy S24+', 'Galaxy S24', 'Galaxy S23 Ultra',
            'Galaxy S23', 'Galaxy Z Fold5', 'Galaxy Z Flip5', 'Galaxy A54 5G',
            'Galaxy A34 5G', 'Galaxy A14', 'Galaxy M14', 'Galaxy Note 20 Ultra',
        ],
    ],
    [
        'slug'    => 'huawei',
        'name'    => 'Huawei',
        'emoji'   => '🌸',
        'country' => 'China',
        'models'  => [
            'P60 Pro', 'P60', 'Mate 60 Pro', 'Mate 60', 'Mate X5',
            'Nova 12 Pro', 'Nova 11', 'Nova 10', 'Y90', 'Y70',
        ],
    ],
    [
        'slug'    => 'xiaomi',
        'name'    => 'Xiaomi',
        'emoji'   => '🔶',
        'country' => 'China',
        'models'  => [
            'Xiaomi 14 Ultra', 'Xiaomi 14 Pro', 'Xiaomi 14', 'Xiaomi 13T Pro',
            'Redmi Note 13 Pro+', 'Redmi Note 13 Pro', 'Redmi Note 13',
            'Redmi 12', 'POCO X6 Pro', 'POCO F5', 'POCO M6 Pro',
        ],
    ],
    [
        'slug'    => 'oppo',
        'name'    => 'OPPO',
        'emoji'   => '🟢',
        'country' => 'China',
        'models'  => [
            'Find X7 Ultra', 'Find X7', 'Find N3 Flip', 'Reno 11 Pro',
            'Reno 11', 'Reno 10 Pro+', 'A98', 'A78', 'A58', 'A18',
        ],
    ],
    [
        'slug'    => 'vivo',
        'name'    => 'vivo',
        'emoji'   => '🔵',
        'country' => 'China',
        'models'  => [
            'X100 Pro', 'X100', 'X90 Pro', 'V29 Pro', 'V29', 'V27',
            'Y36', 'Y27', 'Y17s', 'iQOO 12', 'iQOO Neo 9 Pro',
        ],
    ],
    [
        'slug'    => 'realme',
        'name'    => 'realme',
        'emoji'   => '🟡',
        'country' => 'China',
        'models'  => [
            'GT 5 Pro', 'GT Neo 6', '12 Pro+', '12 Pro', '11 Pro+', '11 Pro',
            'C67', 'C55', 'C53', 'Narzo 60 Pro',
        ],
    ],
    [
        'slug'    => 'oneplus',
        'name'    => 'OnePlus',
        'emoji'   => '🔴',
        'country' => 'China',
        'models'  => [
            '12', '12R', 'Open', '11', '11R', 'Nord 3', 'Nord CE 3 Lite',
            'Nord N30', 'Ace 3', 'Ace 2 Pro',
        ],
    ],
    [
        'slug'    => 'motorola',
        'name'    => 'Motorola',
        'emoji'   => '🦇',
        'country' => 'United States',
        'models'  => [
            'Edge 50 Ultra', 'Edge 50 Pro', 'Edge 40 Neo', 'Razr 40 Ultra',
            'Razr 40', 'Moto G84', 'Moto G54', 'Moto G34', 'Moto G14',
        ],
    ],
    [
        'slug'    => 'nokia',
        'name'    => 'Nokia',
        'emoji'   => '🔷',
        'country' => 'Finland',
        'models'  => [
            'G42 5G', 'G22', 'X30 5G', 'XR21', 'C32', 'C22', 'C12',
            '5710 XpressAudio', '110 4G',
        ],
    ],
    [
        'slug'    => 'sony',
        'name'    => 'Sony',
        'emoji'   => '⚫',
        'country' => 'Japan',
        'models'  => [
            'Xperia 1 V', 'Xperia 5 V', 'Xperia 10 V', 'Xperia 1 IV',
            'Xperia 5 IV', 'Xperia 10 IV', 'Xperia Pro-I',
        ],
    ],
    [
        'slug'    => 'google',
        'name'    => 'Google',
        'emoji'   => '🔍',
        'country' => 'United States',
        'models'  => [
            'Pixel 8 Pro', 'Pixel 8', 'Pixel 8a', 'Pixel 7 Pro', 'Pixel 7',
            'Pixel 7a', 'Pixel 6a', 'Pixel Fold',
        ],
    ],
];
