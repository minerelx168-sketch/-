<?php
declare(strict_types=1);
$cfg = require __DIR__ . '/includes/config.php';
$appName = htmlspecialchars($cfg['app']['name'], ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $appName ?> &middot; Free IMEI Check &amp; Phone Info Lookup</title>
<meta name="description" content="Free IMEI checker. Enter any 15-digit IMEI to instantly look up the brand, model and specs of a mobile phone.">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='22'>📱</text></svg>">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="brand">
            <span class="brand-mark">📱</span>
            <span class="brand-text"><?= $appName ?></span>
        </a>
        <nav class="site-nav">
            <a href="#checker">IMEI Check</a>
            <a href="#how">How it works</a>
            <a href="#faq">FAQ</a>
        </nav>
    </div>
</header>

<main>
    <section class="hero" id="checker">
        <div class="container">
            <h1>Free IMEI Check</h1>
            <p class="lede">
                Enter the 15-digit IMEI number of any mobile phone to instantly look up
                its brand, model and full specifications.
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
        </div>
    </section>

    <section class="how" id="how">
        <div class="container">
            <h2>How an IMEI check works</h2>
            <div class="cards">
                <article class="card">
                    <div class="card-num">1</div>
                    <h3>Find your IMEI</h3>
                    <p>Dial <code>*#06#</code> on any phone, or check Settings &rarr; About &rarr; IMEI.</p>
                </article>
                <article class="card">
                    <div class="card-num">2</div>
                    <h3>Paste &amp; check</h3>
                    <p>Paste the 15-digit number above and press <em>Check IMEI</em>.</p>
                </article>
                <article class="card">
                    <div class="card-num">3</div>
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
</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> <?= $appName ?>. For informational use only.</p>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
