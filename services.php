<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
$services = require __DIR__ . '/data/services.php';

layout_head(
    'IMEI lookup services · imeicheck',
    'All IMEI lookup services — free phone info check, blacklist, carrier, iCloud activation lock, warranty status and more.'
);
?>
    <section class="page-hero">
        <div class="container">
            <h1>IMEI lookup services</h1>
            <p class="lede">
                Different checks return different information. Pick the one that
                matches what you need to know about a device.
            </p>
        </div>
    </section>

    <section class="services-list">
        <div class="container">
            <div class="service-grid">
                <?php foreach ($services as $s): ?>
                    <a class="service-card" href="/service.php?slug=<?= urlencode($s['slug']) ?>">
                        <div class="service-card-head">
                            <span class="service-icon"><?= htmlspecialchars($s['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if ($s['free']): ?>
                                <span class="pill pill-free">Free</span>
                            <?php else: ?>
                                <span class="pill pill-paid">Premium</span>
                            <?php endif; ?>
                        </div>
                        <h3><?= $s['name'] /* names contain &amp; etc, already entity-encoded */ ?></h3>
                        <p><?= htmlspecialchars($s['tagline'], ENT_QUOTES, 'UTF-8') ?></p>
                        <span class="service-link">Run check &rarr;</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php layout_foot(); ?>
