<?php
declare(strict_types=1);

/**
 * Shared header/footer chrome so brand pages and the homepage stay in sync.
 * Use:
 *   layout_head('Page title', 'Optional meta description');
 *   // ... page content ...
 *   layout_foot();
 */

function layout_head(string $title, string $description = ''): void
{
    $cfg = require __DIR__ . '/config.php';
    $appName = htmlspecialchars($cfg['app']['name'], ENT_QUOTES, 'UTF-8');
    $title   = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $desc    = htmlspecialchars(
        $description !== ''
            ? $description
            : 'Free IMEI checker. Look up brand, model and specs of any mobile phone by its IMEI number.',
        ENT_QUOTES,
        'UTF-8'
    );
    ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $title ?></title>
<meta name="description" content="<?= $desc ?>">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='22'>📱</text></svg>">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="brand">
            <span class="brand-mark">📱</span>
            <span class="brand-text"><?= $appName ?></span>
        </a>
        <nav class="site-nav">
            <a href="/">IMEI Check</a>
            <a href="/services.php">Services</a>
            <a href="/brands.php">Brands</a>
            <a href="/articles.php">Articles</a>
            <a href="/about.php">About</a>
        </nav>
    </div>
</header>
<main>
    <?php
}

function layout_foot(): void
{
    $cfg = require __DIR__ . '/config.php';
    $appName = htmlspecialchars($cfg['app']['name'], ENT_QUOTES, 'UTF-8');
    ?>
</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-col">
            <strong><?= $appName ?></strong>
            <p>Free IMEI lookup for any GSM mobile phone.</p>
        </div>
        <div class="footer-col">
            <h4>Tools</h4>
            <a href="/">IMEI Check</a>
            <a href="/services.php">All services</a>
            <a href="/brands.php">Brands</a>
        </div>
        <div class="footer-col">
            <h4>Site</h4>
            <a href="/articles.php">Articles</a>
            <a href="/about.php">About</a>
            <a href="/contact.php">Contact</a>
            <a href="/privacy.php">Privacy</a>
        </div>
        <div class="footer-col footer-col--bottom">
            <p>&copy; <?= date('Y') ?> <?= $appName ?>. For informational use only.</p>
        </div>
    </div>
</footer>
<script src="/assets/js/main.js"></script>
</body>
</html>
    <?php
}
