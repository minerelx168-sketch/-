<?php
declare(strict_types=1);
require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/credits.php';

$user    = auth_require();
$balance = credits_get_balance((int) $user['id'], true);
$cancelled = isset($_GET['cancelled']);

layout_head('Top up credit · imeicheck', 'Add credit to your imeicheck wallet via Stripe.');
?>
    <section class="topup-shell">
        <div class="container container--narrow">
            <p class="breadcrumbs"><a href="/dashboard.php">Dashboard</a> &rsaquo; Top up</p>

            <h1>Top up credit</h1>
            <p class="dashboard-subtitle">
                Current balance: <strong><?= credits_format_thb($balance) ?></strong>
            </p>

            <?php if ($cancelled): ?>
                <div class="alert alert-warning" style="margin-top:20px;">
                    Top-up cancelled. No charge was made to your card.
                </div>
            <?php endif; ?>

            <form id="topup-form" class="topup-form" autocomplete="off">
                <fieldset>
                    <legend>Choose an amount</legend>
                    <div class="topup-presets">
                        <?php foreach ([100, 300, 500, 1000, 3000, 5000] as $preset): ?>
                            <label>
                                <input type="radio" name="amount" value="<?= $preset ?>"<?= $preset === 300 ? ' checked' : '' ?>>
                                <span class="preset-card">
                                    <span class="preset-card-amount">฿<?= number_format($preset) ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <label class="topup-custom">
                        <span>Or enter a custom amount (฿50 - ฿10,000)</span>
                        <input type="number" name="custom" min="50" max="10000" step="1" placeholder="e.g. 750">
                    </label>
                </fieldset>

                <fieldset>
                    <legend>Payment method</legend>
                    <p class="dashboard-subtitle" style="margin:0 0 10px;">
                        You'll be redirected to Stripe's secure checkout. Pay with
                        <strong>credit / debit card</strong> or <strong>PromptPay</strong>.
                    </p>
                </fieldset>

                <button type="submit" id="topup-submit" class="btn-primary btn-primary--lg">
                    <span class="btn-label">Continue to payment</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
                <p id="topup-error" class="field-error" hidden></p>
            </form>

            <p class="topup-foot">
                Need help? <a href="/contact.php">Contact us</a>.
                You'll only be charged once the top-up succeeds &mdash; cancelled
                orders never touch your wallet.
            </p>
        </div>
    </section>

    <script>
    (function () {
        var form    = document.getElementById('topup-form');
        var submit  = document.getElementById('topup-submit');
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

        // Pick "Custom" if the user starts typing.
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

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            error.hidden = true;
            error.textContent = '';

            var amount = selectedAmount();
            if (!amount || amount < 50 || amount > 10000) {
                error.textContent = 'Please choose an amount between ฿50 and ฿10,000.';
                error.hidden = false;
                return;
            }

            submit.classList.add('loading');
            submit.disabled = true;

            fetch('/api/topup/create.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Idempotency-Key': idempotencyKey(),
                },
                body: JSON.stringify({ amount: amount }),
            }).then(function (r) {
                return r.json().then(function (j) { return { status: r.status, body: j }; });
            }).then(function (resp) {
                if (!resp.body || !resp.body.ok || !resp.body.url) {
                    throw new Error((resp.body && resp.body.error) || 'Top-up failed (HTTP ' + resp.status + ').');
                }
                // Clear the idempotency key once we've successfully created a session
                // so the *next* top-up starts fresh.
                sessionStorage.removeItem('topup_idem');
                window.location.href = resp.body.url;
            }).catch(function (e) {
                error.textContent = e.message || 'Network error.';
                error.hidden = false;
                submit.classList.remove('loading');
                submit.disabled = false;
            });
        });
    })();
    </script>
<?php layout_foot(); ?>
