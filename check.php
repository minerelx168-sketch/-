<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/credits.php';
require __DIR__ . '/includes/db.php';

$user    = auth_require();
$userId  = (int) $user['id'];
$balance = credits_get_balance($userId, true);

$categories = require __DIR__ . '/data/service_categories.php';

// Pull all active services so the dropdown only shows things that
// actually charge correctly. Keyed by code for O(1) lookups while
// rendering the optgroups.
$services = [];
try {
    $stmt = db()->query(
        "SELECT code, name, cost FROM service_prices
         WHERE active = 1 AND code NOT LIKE '\\_%' ESCAPE '\\\\'"
    );
    foreach ($stmt->fetchAll() as $r) {
        $services[$r['code']] = $r;
    }
} catch (Throwable $e) {
    // Render the page without options if DB is down.
}

// Default selection: the cheapest paid Apple check, falls back to Free.
$defaultCode = 'IMEI_BASIC';
foreach (['APPLE_ICLOUD_STATUS', 'APPLE_BASIC', 'IMEI_BASIC'] as $candidate) {
    if (isset($services[$candidate])) { $defaultCode = $candidate; break; }
}

layout_head('Check IMEI · imeihub', 'Run any IMEI lookup from a single grouped service picker.');
?>
<section class="check-shell">
    <div class="container container--narrow">
        <header class="check-head">
            <p class="hero-eyebrow" style="color:var(--text-muted);border-color:var(--border);background:var(--surface-alt)">IMEI Check</p>
            <h1>Run a check</h1>
            <p class="check-sub">
                Pick a service, paste a 15-digit IMEI (or serial), and we'll
                run the lookup against our wholesale network.
            </p>
            <p class="check-balance">
                Balance: <strong><?= credits_format_usd($balance) ?></strong>
                &middot; <a href="/topup.php">Top up</a>
            </p>
        </header>

        <form id="check-form" class="check-form" autocomplete="off" novalidate>
            <fieldset>
                <legend>IMEI or serial</legend>
                <input
                    id="imei"
                    name="imei"
                    type="text"
                    inputmode="numeric"
                    pattern="\d*"
                    maxlength="17"
                    placeholder="Enter IMEI / Serial"
                    autocomplete="off"
                    required>
                <p class="hint" style="color:var(--text-muted);text-align:left;margin:8px 4px 0;">
                    Dial <code>*#06#</code> on the phone to display the IMEI.
                </p>
            </fieldset>

            <fieldset>
                <legend>Service</legend>
                <select name="code" id="service-select" required>
                    <?php foreach ($categories as $group):
                        $groupServices = array_filter(
                            $group['codes'],
                            fn($c) => isset($services[$c])
                        );
                        if (!$groupServices) continue;
                    ?>
                        <optgroup label="<?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php foreach ($groupServices as $code):
                                $svc  = $services[$code];
                                $cost = (float) $svc['cost'];
                                $label = $svc['name'];
                                $priceLabel = $cost === 0.0
                                    ? 'FREE'
                                    : '$' . number_format($cost, 2);
                            ?>
                                <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                                        data-cost="<?= htmlspecialchars((string) $cost, ENT_QUOTES, 'UTF-8') ?>"
                                        <?= $code === $defaultCode ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?> &mdash; <?= $priceLabel ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </fieldset>

            <div id="selected-summary" class="check-summary">
                <div>
                    <span class="check-summary-label">Selected</span>
                    <span class="check-summary-name" id="sel-name">&nbsp;</span>
                </div>
                <div class="check-summary-cost">
                    <span class="check-summary-label">Price</span>
                    <strong id="sel-cost">&nbsp;</strong>
                </div>
            </div>

            <button type="submit" id="check-submit" class="btn-primary-block">
                <span class="btn-label">Run check</span>
                <span class="btn-spinner" aria-hidden="true"></span>
            </button>
            <p id="check-error" class="field-error" hidden></p>
        </form>

        <div id="result" class="result" hidden></div>
    </div>
</section>

<script>
(function () {
    var form    = document.getElementById('check-form');
    var sel     = document.getElementById('service-select');
    var imei    = document.getElementById('imei');
    var submit  = document.getElementById('check-submit');
    var err     = document.getElementById('check-error');
    var resultEl = document.getElementById('result');
    var selName = document.getElementById('sel-name');
    var selCost = document.getElementById('sel-cost');

    function updateSummary() {
        var opt = sel.options[sel.selectedIndex];
        if (!opt) return;
        var raw = opt.text || '';
        // option text is "Name — $X.XX — ⚡ Instant"; show just the name part.
        var name = raw.split(' — ')[0];
        selName.textContent = name;
        var cost = parseFloat(opt.getAttribute('data-cost') || '0');
        selCost.textContent = cost === 0 ? 'FREE' : '$' + cost.toFixed(2);
    }
    sel.addEventListener('change', updateSummary);
    updateSummary();

    imei.addEventListener('input', function () {
        var cleaned = imei.value.replace(/\D+/g, '').slice(0, 17);
        if (cleaned !== imei.value) imei.value = cleaned;
    });

    function luhnOk(s) {
        if (!/^\d{15}$/.test(s)) return false;
        var sum = 0;
        for (var i = 0; i < 15; i++) {
            var d = parseInt(s[i], 10);
            if (i % 2 === 1) { d *= 2; if (d > 9) d -= 9; }
            sum += d;
        }
        return sum % 10 === 0;
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
        });
    }

    function showError(msg, withTopup) {
        resultEl.hidden = false;
        resultEl.classList.add('error');
        var topup = withTopup
            ? '<p style="margin-top:14px;"><a href="/topup.php" class="link-more">Top up credit &rarr;</a></p>'
            : '';
        resultEl.innerHTML = '<h2>Check failed</h2><p class="imei-meta">' + escapeHtml(msg) + '</p>' + topup;
    }

    function renderResult(data) {
        var brand = data.brand || 'Unknown';
        var model = data.model || 'GSM Phone';
        var html  = '<h2>' + escapeHtml(brand) + ' ' + escapeHtml(model);
        html += ' <span class="badge">Verified</span></h2>';
        html += '<p class="imei-meta">IMEI <code>' + escapeHtml(data.imei) + '</code>';
        if (data.tac) html += ' · TAC <code>' + escapeHtml(data.tac) + '</code>';
        if (data.cost) {
            var cost = parseFloat(data.cost);
            if (cost > 0) html += ' · charged $' + cost.toFixed(2);
        }
        html += '</p>';

        var details = data.details || {};
        var ordered = {};
        ['Brand Name','Model Name','Model Number','IMEI','Serial Number','Network','Carrier','Country',
         'Color','Storage','Warranty Status','Activation Status','iCloud Status','Find My iPhone',
         'Blacklist Status','SIM Lock'].forEach(function (k) {
            if (details[k]) ordered[k] = details[k];
        });
        Object.keys(details).forEach(function (k) {
            if (!ordered[k]) ordered[k] = details[k];
        });

        var keys = Object.keys(ordered);
        if (keys.length === 0) {
            html += '<p>No additional details were returned.</p>';
        } else {
            html += '<div class="result-grid">';
            keys.forEach(function (k) {
                html += '<div class="result-item">'
                      +   '<span class="k">' + escapeHtml(k) + '</span>'
                      +   '<span class="v">' + escapeHtml(ordered[k]) + '</span>'
                      + '</div>';
            });
            html += '</div>';
        }

        resultEl.hidden = false;
        resultEl.classList.remove('error');
        resultEl.innerHTML = html;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        err.hidden = true;
        resultEl.hidden = true;
        resultEl.classList.remove('error');

        var imeiVal = imei.value.replace(/\D+/g, '');
        if (!luhnOk(imeiVal)) {
            err.textContent = 'Invalid IMEI. Please enter 15 digits (check for typos).';
            err.hidden = false;
            return;
        }

        submit.classList.add('loading');
        submit.disabled = true;

        fetch('/api/services/use.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code: sel.value, input: { imei: imeiVal } })
        })
        .then(function (r) {
            return r.json().then(function (j) { return { status: r.status, body: j }; });
        })
        .then(function (resp) {
            if (resp.status === 401) {
                var next = encodeURIComponent(window.location.pathname);
                window.location.href = '/login.php?next=' + next;
                return;
            }
            if (resp.status === 402 || (resp.body && resp.body.error_code === 'INSUFFICIENT_CREDIT')) {
                showError(resp.body.error || 'Not enough credit.', true);
                return;
            }
            if (!resp.body || resp.body.ok !== true) {
                var note = resp.body && resp.body.refunded
                    ? ' Your credit has been refunded automatically.'
                    : '';
                showError(((resp.body && resp.body.error) || 'Lookup failed (HTTP ' + resp.status + ').') + note, false);
                return;
            }
            renderResult(resp.body);
            resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function () { showError('Network error. Please try again.', false); })
        .finally(function () {
            submit.classList.remove('loading');
            submit.disabled = false;
        });
    });
})();
</script>
<?php layout_foot(); ?>
