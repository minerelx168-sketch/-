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
                    <legend>All Payment Methods</legend>
                    <p id="topup-error" class="field-error" hidden></p>

                    <div class="pm-table">
                        <div class="pm-table-head">
                            <span class="pm-col-method">Method</span>
                            <span class="pm-col-pay">Pay</span>
                            <span class="pm-col-fee">Fee</span>
                            <span class="pm-col-min">Minimum</span>
                            <span class="pm-col-max">Maximum</span>
                        </div>
                        <?php foreach ($methods as $m): if (!($m['enabled'] ?? true)) continue; ?>
                            <div class="pm-row" data-method="<?= htmlspecialchars((string) $m['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <div class="pm-col-method">
                                    <span class="pm-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="<?= htmlspecialchars((string) $m['icon'], ENT_QUOTES, 'UTF-8') ?>"/>
                                        </svg>
                                    </span>
                                    <div class="pm-name">
                                        <span class="pm-label"><?= htmlspecialchars((string) $m['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php foreach ((array) ($m['badges'] ?? []) as $badge): ?>
                                            <span class="pm-badge"><?= htmlspecialchars((string) $badge, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="pm-col-pay">
                                    <button type="button" class="pm-btn" data-method="<?= htmlspecialchars((string) $m['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M13 2L4 14h7l-1 8 9-12h-7z"/>
                                        </svg>
                                        <span class="pm-btn-label">Top up</span>
                                        <span class="btn-spinner" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="pm-col-fee"><?= (float) $m['fee_pct'] === 0.0 ? '0%' : number_format((float) $m['fee_pct'], 0) . '%' ?></div>
                                <div class="pm-col-min">$<?= number_format((float) $m['min_usd'], 2) ?></div>
                                <div class="pm-col-max">$<?= number_format((float) $m['max_usd'], 0) ?></div>
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

        // Stable idempotency key per intent so a network retry can't bill twice.
        function idempotencyKey() {
            var k = sessionStorage.getItem('topup_idem');
            if (!k) {
                k = 'idem-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 12);
                sessionStorage.setItem('topup_idem', k);
            }
            return k;
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
                        'Idempotency-Key': idempotencyKey(),
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
