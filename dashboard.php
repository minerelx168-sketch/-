<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/auth.php';
$user = auth_require();

layout_head('Dashboard · imeicheck', 'Your imeicheck account dashboard.');
?>
    <section class="dashboard">
        <div class="container">
            <header class="dashboard-head">
                <div>
                    <p class="hero-eyebrow" style="color:var(--text-muted);border-color:var(--border);background:var(--surface-alt)">Dashboard</p>
                    <h1>Hi, <?= htmlspecialchars((string) ($user['name'] ?: $user['email']), ENT_QUOTES, 'UTF-8') ?></h1>
                    <p class="dashboard-subtitle">Manage your credits, run lookups, and review your usage.</p>
                </div>
                <a href="/logout.php" class="link-quiet">Sign out</a>
            </header>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <span class="stat-card-label">Credit balance</span>
                    <strong class="stat-card-value">฿<?= number_format((float) $user['cached_balance'], 2) ?></strong>
                    <a href="/topup.php" class="btn-primary">Top up credit</a>
                </div>
                <div class="stat-card">
                    <span class="stat-card-label">Lookups this month</span>
                    <strong class="stat-card-value">0</strong>
                    <span class="stat-card-meta">No usage yet</span>
                </div>
                <div class="stat-card">
                    <span class="stat-card-label">Spent this month</span>
                    <strong class="stat-card-value">฿0.00</strong>
                    <span class="stat-card-meta">All transactions are stored on your account.</span>
                </div>
            </div>

            <div class="dashboard-coming">
                <p>Top-up and usage history will appear here once the wallet flow ships in the next phase.</p>
            </div>
        </div>
    </section>
<?php layout_foot(); ?>
