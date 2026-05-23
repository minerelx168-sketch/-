<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/icons.php';
$cfg = require __DIR__ . '/includes/config.php';
$appName = htmlspecialchars($cfg['app']['name'], ENT_QUOTES, 'UTF-8');
layout_head(
    $appName . ' · Free IMEI Check & Phone Info Lookup',
    'Free IMEI checker. Enter any 15-digit IMEI to instantly look up the brand, model and specs of a mobile phone.'
);
?>
    <section class="hero" id="checker">
        <div class="container">
            <p class="hero-eyebrow">Free phone information lookup</p>
            <h1>Identify any mobile phone<br>by its IMEI number</h1>
            <p class="lede">
                Enter the 15-digit IMEI of any GSM phone to instantly retrieve
                its brand, model and full specifications &mdash; no signup required.
            </p>

            <form id="imei-form" class="lookup-form" autocomplete="off" novalidate>
                <label for="imei" class="sr-only">IMEI number</label>
                <input
                    id="imei"
                    name="imei"
                    type="text"
                    inputmode="numeric"
                    pattern="\d*"
                    maxlength="17"
                    placeholder="Enter 15-digit IMEI (e.g. 359152060000003)"
                    required
                    aria-describedby="imei-hint">
                <button type="submit" id="submit-btn">
                    <span class="btn-label">Check IMEI</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
                <p id="imei-hint" class="hint">
                    Dial <code>*#06#</code> on your phone to display the IMEI.
                </p>
            </form>

            <div id="result" class="result" hidden></div>

            <div class="hero-stats">
                <div><strong>120k+</strong><span>TACs indexed</span></div>
                <div><strong>12</strong><span>major brands</span></div>
                <div><strong>&lt; 1s</strong><span>median lookup</span></div>
            </div>
        </div>
    </section>

    <section class="brands-preview">
        <div class="container">
            <div class="section-head">
                <h2>Browse by brand</h2>
                <p>Pick a manufacturer to see the models we recognise.</p>
            </div>
            <div class="brand-grid">
                <?php foreach (array_slice(require __DIR__ . '/data/brands.php', 0, 8) as $b): ?>
                    <a class="brand-tile" href="/brand.php?slug=<?= urlencode($b['slug']) ?>">
                        <span class="brand-tile-mark" style="background:<?= htmlspecialchars($b['color'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars(brand_initial($b['name']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span class="brand-tile-name"><?= htmlspecialchars($b['name'], ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <p class="brand-grid-more">
                <a class="link-more" href="/brands.php">View all brands <?= icon('arrow-right', 14) ?></a>
            </p>
        </div>
    </section>

    <section class="how" id="how">
        <div class="container">
            <div class="section-head">
                <h2>How an IMEI check works</h2>
                <p>Three steps. About fifteen seconds, including the typing.</p>
            </div>
            <div class="cards">
                <article class="card">
                    <span class="card-icon"><?= icon('magnifier', 22) ?></span>
                    <div class="card-num">01</div>
                    <h3>Find your IMEI</h3>
                    <p>Dial <code>*#06#</code> on any phone, or check Settings &rarr; About &rarr; IMEI.</p>
                </article>
                <article class="card">
                    <span class="card-icon"><?= icon('paste', 22) ?></span>
                    <div class="card-num">02</div>
                    <h3>Paste &amp; check</h3>
                    <p>Paste the 15-digit number above and press <em>Check IMEI</em>.</p>
                </article>
                <article class="card">
                    <span class="card-icon"><?= icon('report', 22) ?></span>
                    <div class="card-num">03</div>
                    <h3>Read the report</h3>
                    <p>We return the brand, model and any device data the provider exposes.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="faq" id="faq">
        <div class="container">
            <h2>Frequently asked questions</h2>

            <details>
                <summary>What is an IMEI number?</summary>
                <p>The IMEI (International Mobile Equipment Identity) is a unique 15-digit
                number that identifies a mobile device on cellular networks.</p>
            </details>

            <details>
                <summary>Is checking my IMEI safe?</summary>
                <p>Yes. An IMEI alone does not give anyone access to your account or data.
                It only identifies the hardware. Still, treat it like any other ID and
                avoid posting it publicly.</p>
            </details>

            <details>
                <summary>What is a TAC?</summary>
                <p>The first 8 digits of an IMEI form the Type Allocation Code (TAC), which
                identifies the brand and model of the device.</p>
            </details>

            <details>
                <summary>Why did my IMEI fail validation?</summary>
                <p>IMEIs must be exactly 15 digits and pass the Luhn checksum. If you
                typed it from a screen, double-check for missing or duplicated digits.</p>
            </details>
        </div>
    </section>
<?php layout_foot(); ?>
