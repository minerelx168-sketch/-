<?php
declare(strict_types=1);

header('Content-Type: application/xml; charset=utf-8');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base   = $scheme . '://' . $host;

$brands = require __DIR__ . '/data/brands.php';
$now    = date('c');

$urls = [
    ['loc' => $base . '/',          'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => $base . '/brands.php','priority' => '0.8', 'changefreq' => 'weekly'],
];
foreach ($brands as $b) {
    $urls[] = [
        'loc'        => $base . '/brand.php?slug=' . urlencode($b['slug']),
        'priority'   => '0.6',
        'changefreq' => 'monthly',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
    echo "    <lastmod>$now</lastmod>\n";
    echo "    <changefreq>{$u['changefreq']}</changefreq>\n";
    echo "    <priority>{$u['priority']}</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>' . "\n";
