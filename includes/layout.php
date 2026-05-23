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
    require_once __DIR__ . '/icons.php';
    require_once __DIR__ . '/auth.php';
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

    // SVG favicon (no emoji). Use single-quote SVG attributes so the
    // markup can sit safely inside an HTML double-quoted href.
    $favicon = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'>"
        . "<defs><linearGradient id='g' x1='0' y1='0' x2='1' y2='1'><stop offset='0' stop-color='%234f46e5'/><stop offset='1' stop-color='%230ea5e9'/></linearGradient></defs>"
        . "<rect width='32' height='32' rx='8' fill='url(%23g)'/>"
        . "<circle cx='16' cy='9.5' r='2' fill='%23fff'/>"
        . "<rect x='13.5' y='13' width='5' height='13' rx='1.5' fill='%23fff'/></svg>";
    ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $title ?></title>
<meta name="description" content="<?= $desc ?>">
<link rel="icon" href="data:image/svg+xml;charset=utf-8,<?= $favicon ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="/" class="brand">
            <?= icon('logo', 32, 'brand-mark') ?>
            <span class="brand-text"><?= $appName ?></span>
        </a>
        <?php $u = auth_user(); ?>
        <nav class="site-nav">
            <?php if ($u): ?>
                <a href="/check.php">Check</a>
            <?php else: ?>
                <a href="/">IMEI Check</a>
            <?php endif; ?>
            <a href="/services.php">Services</a>
            <a href="/brands.php">Brands</a>
            <a href="/articles.php">Articles</a>
            <a href="/about.php">About</a>
        </nav>
        <div class="site-account">
            <?php if ($u): ?>
                <a href="/dashboard.php" class="account-chip">
                    <span class="account-balance">$<?= number_format((float) $u['cached_balance'], 2) ?></span>
                    <?php if (!empty($u['image'])): ?>
                        <img src="<?= htmlspecialchars((string) $u['image'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="" width="28" height="28" class="account-avatar" referrerpolicy="no-referrer">
                    <?php else: ?>
                        <span class="account-avatar account-avatar--initial"><?= htmlspecialchars(strtoupper(substr((string) ($u['name'] ?? $u['email']), 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="/login.php" class="btn-signin">Sign in</a>
            <?php endif; ?>
        </div>
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
