<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
$services = require __DIR__ . '/data/services.php';

$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]+/i', '', (string) $_GET['slug']) : '';
$service = null;
foreach ($services as $s) {
    if ($s['slug'] === $slug) { $service = $s; break; }
}

if (!$service) {
    http_response_code(404);
    layout_head('Service not found · imeicheck');
    ?>
    <section class="page-hero">
        <div class="container">
            <h1>Service not found</h1>
            <p class="lede">
                The service <code><?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?></code>
                does not exist. <a href="/services.php">See all services &rarr;</a>
            </p>
        </div>
    </section>
    <?php
    layout_foot();
    return;
}

$name = $service['name'];
layout_head(
    strip_tags(html_entity_decode($name)) . ' · imeicheck',
    strip_tags($service['tagline'])
);
?>
    <section class="page-hero">
        <div class="container">
            <p class="breadcrumbs">
                <a href="/services.php">Services</a> &rsaquo;
                <?= strip_tags($name) ?>
            </p>
            <h1>
                <span style="margin-right:8px"><?= htmlspecialchars($service['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                <?= $name ?>
                <?php if ($service['free']): ?>
                    <span class="pill pill-free" style="vertical-align:middle">Free</span>
                <?php endif; ?>
            </h1>
            <p class="lede"><?= htmlspecialchars($service['tagline'], ENT_QUOTES, 'UTF-8') ?></p>

            <form id="imei-form" class="lookup-form lookup-form--hero" autocomplete="off" novalidate
                  data-service="<?= htmlspecialchars((string) $service['provider_id'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="imei" class="sr-only">IMEI number</label>
                <input
                    id="imei"
                    name="imei"
                    type="text"
                    inputmode="numeric"
                    pattern="\d*"
                    maxlength="17"
                    placeholder="Enter 15-digit IMEI"
                    required>
                <button type="submit" id="submit-btn">
                    <span class="btn-label">Check</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </form>

            <div id="result" class="result" hidden></div>
        </div>
    </section>

    <section class="service-body">
        <div class="container">
            <h2>About this check</h2>
            <p><?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?></p>

            <h3>How to find your IMEI</h3>
            <ul>
                <li>Dial <code>*#06#</code> on the phone &mdash; the IMEI shows up immediately.</li>
                <li>Or open <em>Settings &rarr; About phone &rarr; IMEI</em>.</li>
                <li>Or check the printed label inside the SIM tray / on the box.</li>
            </ul>
        </div>
    </section>
<?php layout_foot(); ?>
