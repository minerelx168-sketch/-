<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
$brands = require __DIR__ . '/data/brands.php';

layout_head(
    'Phone brands · imeicheck',
    'Browse mobile phone brands. Pick a brand to see common models and their IMEI/TAC info.'
);
?>
    <section class="page-hero">
        <div class="container">
            <h1>Phone brands</h1>
            <p class="lede">
                We cover <?= count($brands) ?> major mobile phone brands.
                Pick one to see common models, or jump straight to the
                <a href="/">IMEI checker</a> to look up a specific device.
            </p>
        </div>
    </section>

    <section class="brands-list">
        <div class="container">
            <div class="brand-grid">
                <?php foreach ($brands as $b): ?>
                    <a class="brand-tile" href="/brand.php?slug=<?= urlencode($b['slug']) ?>">
                        <span class="brand-tile-mark"><?= htmlspecialchars($b['emoji'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="brand-tile-name"><?= htmlspecialchars($b['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="brand-tile-meta"><?= count($b['models']) ?> models</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php layout_foot(); ?>
