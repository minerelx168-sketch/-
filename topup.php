<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/credits.php';

$user      = auth_require();
$balance   = credits_get_balance((int) $user['id'], true);
$cancelled = isset($_GET['cancelled']);
$methods   = require __DIR__ . '/data/payment_methods.php';

layout_head('Top up credit · imeihub', 'Add credit to your imeihub wallet via card, PayPal or Binance Pay.');
?>
    <section class="topup-shell">
        <div class="container container--narrow">
            <p class="breadcrumbs"><a href="/dashboard.php">Dashboard</a> &rsaquo; Top up</p>

            <h1>Top up credit</h1>
            <p class="dashboard-subtitle">
                Current balance: <strong><?= credits_format_usd($balance) ?></strong>
            </p>

            <?php if ($cancelled): ?>
                <div class="alert alert-warning" style="margin-top:20px;">
                    Top-up cancelled. No charge was made &mdash; pick another method or try again.
                </div>
            <?php endif; ?>

            <form id="topup-form" class="topup-form" autocomplete="off">
                <fieldset>
                    <legend>Choose an amount</legend>
                    <div class="topup-presets">
                        <?php foreach ([5, 10, 25, 50, 100, 250] as $preset): ?>
                            <label>
                                <input type="radio" name="amount" value="<?= $preset ?>"<?= $preset === 25 ? ' checked' : '' ?>>
                                <span class="preset-card">
                                    <span class="preset-card-amount">$<?= number_format($preset) ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <label class="topup-custom">
                        <span>Or enter a custom amount</span>
                        <input type="number" name="custom" min="1" max="3000" step="1" placeholder="e.g. 20">
                    </label>
                </fieldset>

                <fieldset>
                    <legend>Payment method</legend>
                    <p id="topup-error" class="field-error" hidden></p>

                    <div class="pm-list">
                        <?php foreach ($methods as $m): if (!($m['enabled'] ?? true)) continue; ?>
                            <div class="pm-card" data-method="<?= htmlspecialchars((string) $m['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <span class="pm-card-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="<?= htmlspecialchars((string) $m['icon'], ENT_QUOTES, 'UTF-8') ?>"/>
                                    </svg>
                                </span>
                                <div class="pm-card-body">
                                    <div class="pm-card-title">
                                        <span class="pm-card-name"><?= htmlspecialchars((string) $m['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php foreach ((array) ($m['badges'] ?? []) as $badge): ?>
                                            <span class="pm-badge"><?= htmlspecialchars((string) $badge, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="pm-card-meta">
                                        <span><span class="pm-meta-k">Fee</span><?= (float) $m['fee_pct'] === 0.0 ? 'Free' : number_format((float) $m['fee_pct'], 0) . '%' ?></span>
                                        <span><span class="pm-meta-k">Min</span>$<?= number_format((float) $m['min_usd'], 2) ?></span>
                                        <span><span class="pm-meta-k">Max</span>$<?= number_format((float) $m['max_usd'], 0) ?></span>
                                    </div>
                                </div>
                                <button type="button" class="pm-btn" data-method="<?= htmlspecialchars((string) $m['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="pm-btn-label">Top up</span>
                                    <span class="btn-spinner" aria-hidden="true"></span>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            </form>

            <p class="topup-foot">
                Need help? <a href="/contact.php">Contact us</a>.
                You'll only be charged once the top-up succeeds &mdash; cancelled
                or failed orders never touch your wallet.
            </p>
        </div>
    </section>

    <script>
    (function () {
        var form    = document.getElementById('topup-form');
        var error   = document.getElementById('topup-error');
        if (!form) return;

        function selectedAmount() {
            var custom = form.elements['custom'].value.trim();
            if (custom) {
                var c = parseInt(custom, 10);
                if (isFinite(c)) return c;
            }
            var radio = form.querySelector('input[name="amount"]:checked');
            return radio ? parseInt(radio.value, 10) : 0;
        }

        form.elements['custom'].addEventListener('input', function () {
            var radio = form.querySelector('input[name="amount"]:checked');
            if (radio && form.elements['custom'].value.trim() !== '') radio.checked = false;
        });

        // Idempotency key stays stable for a given (amount, method) so a
        // network retry can't bill twice - but regenerates when the user
        // changes the amount or method, so retrying after editing the amount
        // opens a fresh order at the NEW amount instead of reusing the old one.
        function idempotencyKey(amount, method) {
            var sig = amount + ':' + method;
            var saved;
            try { saved = JSON.parse(sessionStorage.getItem('topup_idem')); } catch (e) { saved = null; }
            if (!saved || saved.sig !== sig) {
                saved = {
                    sig: sig,
                    key: 'idem-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 12),
                };
                sessionStorage.setItem('topup_idem', JSON.stringify(saved));
            }
            return saved.key;
        }

        function showError(msg) {
            error.textContent = msg;
            error.hidden = false;
        }
        function clearError() {
            error.textContent = '';
            error.hidden = true;
        }

        form.querySelectorAll('.pm-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                clearError();
                var method = btn.getAttribute('data-method');
                var amount = selectedAmount();
                if (!amount || amount < 1 || amount > 3000) {
                    showError('Please choose an amount between $1 and $3,000.');
                    return;
                }

                form.querySelectorAll('.pm-btn').forEach(function (b) { b.disabled = true; });
                btn.classList.add('loading');

                fetch('/api/topup/create.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Idempotency-Key': idempotencyKey(amount, method),
                    },
                    body: JSON.stringify({ amount: amount, method: method }),
                }).then(function (r) {
                    return r.json().then(function (j) { return { status: r.status, body: j }; });
                }).then(function (resp) {
                    if (!resp.body || !resp.body.ok || !resp.body.url) {
                        throw new Error((resp.body && resp.body.error) || 'Top-up failed (HTTP ' + resp.status + ').');
                    }
                    sessionStorage.removeItem('topup_idem');
                    window.location.href = resp.body.url;
                }).catch(function (e) {
                    showError(e.message || 'Network error.');
                    form.querySelectorAll('.pm-btn').forEach(function (b) { b.disabled = false; });
                    btn.classList.remove('loading');
                });
            });
        });
    })();
    </script>
<?php layout_foot(); ?>
