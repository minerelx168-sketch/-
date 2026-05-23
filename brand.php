<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
$brands = require __DIR__ . '/data/brands.php';

$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]+/i', '', (string) $_GET['slug']) : '';
$brand = null;
foreach ($brands as $b) {
    if ($b['slug'] === $slug) {
        $brand = $b;
        break;
    }
}

if (!$brand) {
    http_response_code(404);
    layout_head('Brand not found · imeicheck');
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>Brand not found</h1>
            <p class="lede">The brand <code><?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?></code> is not in our catalog. <a href="/brands.php">Browse all brands &rarr;</a></p>
        </div>
    </section>
    <?php
    layout_foot();
    return;
}

$brandName = htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8');
$brandEmoji = htmlspecialchars($brand['emoji'], ENT_QUOTES, 'UTF-8');
$country = htmlspecialchars($brand['country'], ENT_QUOTES, 'UTF-8');

layout_head(
    "$brandName IMEI Check · imeicheck",
    "Look up any $brandName phone by IMEI. Common $brandName models and how to find the IMEI on your device."
);
?>
    <section class="page-hero">
        <div class="container">
            <p class="breadcrumbs">
                <a href="/brands.php">Brands</a> &rsaquo; <?= $brandName ?>
            </p>
            <h1><span style="margin-right:8px"><?= $brandEmoji ?></span><?= $brandName ?> phones</h1>
            <p class="lede">
                <?= $brandName ?> is a mobile manufacturer from <?= $country ?>.
                Below are common <?= $brandName ?> models we recognise.
                Have a specific device? Enter its IMEI on the <a href="/">checker</a>.
            </p>
        </div>
    </section>

    <section class="model-list">
        <div class="container">
            <h2>Common <?= $brandName ?> models</h2>
            <ul class="models">
                <?php foreach ($brand['models'] as $m): ?>
                    <li><?= htmlspecialchars($m, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
            <p class="lede-soft" style="margin-top:32px">
                Don't see your model? Models are identified from the first 8 digits
                (TAC) of the IMEI. <a href="/">Run a check</a> and we'll resolve the
                exact name through our lookup provider.
            </p>
        </div>
    </section>
<?php layout_foot(); ?>
