<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/db.php';
$cfg     = require __DIR__ . '/includes/config.php';
$appName = htmlspecialchars($cfg['app']['name'], ENT_QUOTES, 'UTF-8');

// Load the same catalog the /check page uses so the hero dropdown is
// always in sync with what the wallet actually charges for.
$categories = require __DIR__ . '/data/service_categories.php';
$services   = [];
try {
    $stmt = db()->query(
        "SELECT code, name, cost FROM service_prices
         WHERE active = 1 AND code NOT LIKE '\\_%' ESCAPE '\\\\'"
    );
    foreach ($stmt->fetchAll() as $r) {
        $services[$r['code']] = $r;
    }
} catch (Throwable $e) {
    // DB unavailable - mirror the canonical catalog from sql/seed.sql so the
    // demo (and any deploy with a temporarily down DB) still shows the full
    // dropdown. Prices match the operator's selling sheet, USD.
    $services = [
        'IMEI_BASIC'                => ['name' => 'Free IMEI Check',                                                                       'cost' => '0.00'],
        // Apple Featured
        'APPLE_BASIC'               => ['name' => 'Apple Basic Info',                                                                      'cost' => '0.10'],
        'APPLE_CARRIER_LITE'        => ['name' => 'Apple Carrier (Lite)',                                                                  'cost' => '0.08'],
        'APPLE_CARRIER_PRO'         => ['name' => 'Apple Carrier (Pro)',                                                                   'cost' => '0.16'],
        'APPLE_CARRIER_PRO_PLUS'    => ['name' => 'Apple Carrier (Pro Plus)',                                                              'cost' => '0.26'],
        'APPLE_MAX_INFO'            => ['name' => 'Apple Max Info (Premium)',                                                              'cost' => '0.70'],
        // Apple iCloud
        'APPLE_ICLOUD_STATUS'       => ['name' => 'Apple iCloud (ON / OFF)',                                                               'cost' => '0.01'],
        'APPLE_ICLOUD_CLEAN'        => ['name' => 'Apple iCloud (Clean / Lost)',                                                           'cost' => '0.03'],
        'APPLE_ICLOUD_CLEAN_SN'     => ['name' => 'Apple iCloud (Clean / Lost) SN',                                                        'cost' => '0.10'],
        'APPLE_ICLOUD_ID_HINT'      => ['name' => 'Apple iCloud ID Hint',                                                                  'cost' => '0.80'],
        'APPLE_MAC_ICLOUD_STATUS'   => ['name' => 'Apple iCloud MACBOOK/iMAC (ON / OFF)',                                                  'cost' => '0.30'],
        'APPLE_MAC_ICLOUD_CLEAN'    => ['name' => 'Apple iCloud MACBOOK/iMAC (Clean / Lost)',                                              'cost' => '0.40'],
        // Apple MDM
        'APPLE_MDM'                 => ['name' => 'Apple MDM (ON / OFF)',                                                                  'cost' => '0.35'],
        'APPLE_MDM_SN'              => ['name' => 'Apple MDM (ON / OFF) SN',                                                               'cost' => '0.30'],
        'APPLE_MDM_FMI'             => ['name' => 'Apple MDM + FMI (ON / OFF)',                                                            'cost' => '0.50'],
        // Apple Device Info
        'APPLE_WARRANTY'            => ['name' => 'Apple Warranty (Activation Info)',                                                      'cost' => '0.04'],
        'APPLE_WARRANTY_SN'         => ['name' => 'Apple Warranty (Activation Info) SN',                                                   'cost' => '0.05'],
        'APPLE_PART_NUMBER'         => ['name' => 'Apple Part Number / MPN',                                                               'cost' => '0.14'],
        'APPLE_SIM_LOCK'            => ['name' => 'Apple SIM-LOCK Status',                                                                 'cost' => '0.05'],
        'APPLE_GSX_TETHER'          => ['name' => 'Apple GSX Next Tether Policy',                                                          'cost' => '0.20'],
        // Apple GSX
        'APPLE_CASE_REPAIR_HISTORY' => ['name' => 'Apple Case History, Repair History',                                                    'cost' => '1.20'],
        'APPLE_SOLD_BY_COVERAGE'    => ['name' => 'Apple Sold By, Coverage (Max Info)',                                                    'cost' => '2.00'],
        'APPLE_SOLD_BY_HISTORY'     => ['name' => 'Apple Sold By, Case History, Activation Policy',                                        'cost' => '4.20'],
        'APPLE_GSX_LIGHT'           => ['name' => 'Apple Sold By, Case History, Replacement, GSX Activation Policy',                       'cost' => '1.00'],
        'APPLE_FULL_GSX'            => ['name' => 'Apple Sold By, Case History, Replacement, Activation Policy [ICCID & MAC] (Full GSX)',  'cost' => '2.30'],
        'APPLE_GSX_MAX'             => ['name' => 'Apple Sold By, Case History, Replacement, Repair, GSX Activation Policy (Max Info)',    'cost' => '2.60'],
        // Worldwide Blacklist
        'BLACKLIST_SIMPLE'          => ['name' => 'WorldWide Blacklist Status (SIMPLE INFO)',                                              'cost' => '0.05'],
        'BLACKLIST_FULL'            => ['name' => 'WorldWide Blacklist Status (FULL INFO)',                                                'cost' => '0.10'],
        // Other Brands
        'SAMSUNG_INFO'              => ['name' => 'SAMSUNG INFO',                                                                          'cost' => '0.10'],
        'SAMSUNG_KNOX'              => ['name' => 'SAMSUNG (Knox Guard Status, Samsung Lock - ON/OFF)',                                    'cost' => '0.20'],
        'XIAOMI_STATUS'             => ['name' => 'XIAOMI (ON / OFF)',                                                                     'cost' => '0.10'],
        'HUAWEI_INFO'               => ['name' => 'HUAWEI INFO',                                                                           'cost' => '0.08'],
        'HONOR_INFO'                => ['name' => 'HONOR INFO',                                                                            'cost' => '0.10'],
        'MOTOROLA_INFO'             => ['name' => 'MOTOROLA INFO',                                                                         'cost' => '0.10'],
        'LENOVO_INFO'               => ['name' => 'LENOVO INFO',                                                                           'cost' => '0.10'],
        'PIXEL_INFO'                => ['name' => 'Google Pixel Info',                                                                     'cost' => '0.20'],
        // US Carriers
        'TMOBILE_USA'               => ['name' => 'T-Mobile - USA iPhone/Generic Status',                                                  'cost' => '0.10'],
        'TMOBILE_USA_PRO'           => ['name' => 'T-Mobile - USA iPhone/Generic Status [PRO]',                                            'cost' => '0.06'],
        'VERIZON_USA_PRO'           => ['name' => 'Verizon - USA iPhone/Generic Status [PRO]',                                             'cost' => '0.06'],
        // Phone number lookup
        'YANDEX_ALICE'              => ['name' => 'Yandex Alice Info',                                                                     'cost' => '0.60'],
        'HLR_LOOKUP'                => ['name' => 'Home Location Register (HLR) Lookup',                                                   'cost' => '0.05'],
        'NUMBER_TYPE'               => ['name' => 'Number Type (NT) Lookup',                                                               'cost' => '0.14'],
        'PING_SMS'                  => ['name' => 'Ping-SMS',                                                                              'cost' => '0.20'],
        'PING_SMS_S2'               => ['name' => 'Ping-SMS (Server 2)',                                                                   'cost' => '0.70'],
    ];
    foreach ($services as $code => &$svc) $svc['code'] = $code;
    unset($svc);
}

layout_head(
    $appName . ' · Free IMEI Check & Phone Info Lookup',
    'Free IMEI checker. Pick a check, paste any 15-digit IMEI, and we instantly look up the brand, model and specs.'
);
?>
    <section class="hero" id="checker">
        <div class="container">
            <p class="hero-eyebrow">Free phone information lookup</p>
            <h1>Identify any mobile phone<br>by its IMEI number</h1>
            <p class="lede">
                Pick a check, paste a 15-digit IMEI, and we'll run it instantly.
                Free brand &amp; model lookup &mdash; premium reports for signed-in customers.
            </p>

            <form id="imei-form" class="hero-lookup" autocomplete="off" novalidate>
                <label for="imei" class="hero-field">
                    <span class="hero-field-icon"><?= icon('phone', 18) ?></span>
                    <input
                        id="imei"
                        name="imei"
                        type="text"
                        inputmode="numeric"
                        pattern="\d*"
                        maxlength="17"
                        placeholder="Enter IMEI / Serial"
                        required>
                </label>

                <label for="service-select" class="hero-field">
                    <span class="hero-field-icon"><?= icon('specs', 18) ?></span>
                    <select id="service-select" name="code" class="hero-select" required>
                        <?php foreach ($categories as $group):
                            $opts = array_filter($group['codes'], fn($c) => isset($services[$c]));
                            if (!$opts) continue; ?>
                            <optgroup label="<?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php foreach ($opts as $code):
                                    $svc  = $services[$code];
                                    $cost = (float) $svc['cost'];
                                    $priceLabel = $cost === 0.0 ? 'FREE' : '$' . number_format($cost, 2);
                                ?>
                                    <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                                            data-cost="<?= htmlspecialchars((string) $cost, ENT_QUOTES, 'UTF-8') ?>"
                                            <?= $code === 'IMEI_BASIC' ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($svc['name'], ENT_QUOTES, 'UTF-8') ?> &mdash; <?= $priceLabel ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </label>

                <button type="submit" id="submit-btn" class="hero-submit">
                    <span class="btn-label">Check IMEI</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>

                <p id="imei-hint" class="hint">
                    Dial <code>*#06#</code> on your phone to display the IMEI.
                    Premium checks require <a href="/login.php">sign-in</a>.
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
