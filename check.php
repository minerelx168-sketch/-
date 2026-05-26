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
    var requestStartedAt = 0;

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

    function classifyValue(key, val) {
        var v = String(val).trim(), k = String(key).toLowerCase();
        if (/^(blacklisted|stolen|lost|fraud|denied|expired|invalid|sold)/i.test(v)) return 'danger';
        if (/^(locked)$/i.test(v)) return 'danger';
        if (/^(activated|active|clean|unlocked|covered|in warranty)$/i.test(v)) return 'success';
        var alertKey = /(find\s*my|fmi|icloud|mdm|sim.?lock|activation\s*lock|locked|jailbreak)/i;
        if (/^on$/i.test(v))  return alertKey.test(k) ? 'danger'  : 'success';
        if (/^off$/i.test(v)) return alertKey.test(k) ? 'success' : 'muted';
        var warnYesKey = /(repair|blacklist|fraud|lost|stolen|replaced|refurbished|demo|jailbreak|loaner)/i;
        if (/^yes$/i.test(v)) return warnYesKey.test(k) ? 'warn'    : 'success';
        if (/^no$/i.test(v))  return warnYesKey.test(k) ? 'success' : null;
        return null;
    }
    function valueHtml(k, v) {
        var c = classifyValue(k, v), t = /^[\w.+/-]{1,16}$/.test(String(v).trim());
        return (c && t) ? '<span class="pill pill-' + c + '">' + escapeHtml(v) + '</span>' : escapeHtml(v);
    }
    function renderStatus(type, title, innerHtml) {
        resultEl.hidden = false;
        resultEl.className = 'result result--report';
        resultEl.innerHTML =
            '<div class="result-banner result-banner--' + type + '">' + escapeHtml(title) + '</div>' +
            '<div class="result-card">' + innerHtml + '</div>';
    }
    function showBlacklistPopup(bl) {
        if (!bl) return;
        var oldp = document.getElementById('bl-popup'); if (oldp) oldp.remove();
        var n = bl.reports || 1, when = bl.first_reported ? String(bl.first_reported).slice(0, 10) : '';
        var reason = bl.reason ? '<p class="bl-reason">Reported reason: ' + escapeHtml(bl.reason) + '</p>' : '';
        var ov = document.createElement('div'); ov.id = 'bl-popup'; ov.className = 'bl-overlay';
        ov.innerHTML = '<div class="bl-card" role="alertdialog" aria-modal="true">' +
            '<div class="bl-badge">&#9888; BLACKLIST ALERT</div>' +
            '<h3>This IMEI has been reported</h3>' +
            '<p>Reported <strong>' + n + ' time' + (n > 1 ? 's' : '') + '</strong> by users on this platform' +
            (when ? ' (first on ' + escapeHtml(when) + ')' : '') + '. It may be lost, stolen, or carry outstanding debt &mdash; proceed with caution before buying or financing this device.</p>' +
            reason + '<button type="button" class="bl-dismiss">I understand</button></div>';
        document.body.appendChild(ov);
        function close() { ov.remove(); }
        ov.addEventListener('click', function (e) { if (e.target === ov) close(); });
        ov.querySelector('.bl-dismiss').addEventListener('click', close);
    }
    function showError(msg, title, type) {
        renderStatus(type || 'error', title || 'Order Failed', '<p class="result-msg">' + escapeHtml(msg) + '</p>');
    }
    function renderResult(data) {
        var details = data.details || {};
        var brand = data.brand || details['Brand Name'] || details.Brand || details.Manufacturer || '';
        var model = data.model || details['Model Name'] || details.Model || details['Model Description'] || '';
        var modelStr = (details['Model Description'] || details['Model'] || (brand + ' ' + model)).trim() || details['Model Name'] || 'Unknown device';
        var skip = {'brand name':1,'brand':1,'manufacturer':1,'model':1,'model name':1,'model description':1};
        var lines = '<div class="rline rline--model"><span class="rk">Model:</span> <strong>' + escapeHtml(modelStr) + '</strong></div>';
        Object.keys(details).forEach(function (k) {
            if (skip[String(k).toLowerCase()]) return;
            var v = details[k]; if (v === null || v === undefined || v === '') return;
            lines += '<div class="rline"><span class="rk">' + escapeHtml(k) + ':</span> ' + valueHtml(k, v) + '</div>';
        });
        var secs = requestStartedAt ? ((Date.now() - requestStartedAt) / 1000).toFixed(1) : null;
        var dateStr = new Date().toLocaleString('en-US', {month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'}).toUpperCase();
        var blWarn = data.blacklist ? '<div class="bl-inline">&#9888; This IMEI was reported ' + (data.blacklist.reports || 1) + ' time(s) as blacklisted &mdash; proceed with caution.</div>' : '';
        renderStatus('success', 'Order Processed!', blWarn +
            '<div class="result-lines">' + lines + '</div>' +
            '<div class="result-chips">' +
              '<span class="result-chip">' + (secs !== null ? escapeHtml(secs) + ' SECONDS' : 'COMPLETED') + '</span>' +
              '<span class="result-chip">' + escapeHtml(dateStr) + '</span>' +
            '</div>');
        showBlacklistPopup(data.blacklist);
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        err.hidden = true;
        resultEl.hidden = true;
        resultEl.className = 'result';

        var imeiVal = imei.value.replace(/\D+/g, '');
        if (!luhnOk(imeiVal)) {
            err.textContent = 'Invalid IMEI. Please enter 15 digits (check for typos).';
            err.hidden = false;
            return;
        }

        submit.classList.add('loading');
        submit.disabled = true;
        requestStartedAt = Date.now();

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
                renderStatus('warn', 'Insufficient Credit',
                    '<p class="result-msg">' + escapeHtml(resp.body.error || 'Please top up your wallet to run this check.') + '</p>' +
                    '<p class="result-cta"><a href="/topup.php" class="link-more">Top up credit &rarr;</a></p>');
                resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }
            if (resp.body && resp.body.status === 'processing' && resp.body.public_id) {
                renderStatus('info', 'Processing…',
                    '<p class="imei-meta" style="text-align:center">Reference <code>' + escapeHtml(resp.body.public_id) + '</code></p>' +
                    '<div class="result-processing"><div class="result-spinner" aria-hidden="true"></div><div><strong>Your order has been placed with the provider.</strong>' +
                    '<p class="dashboard-subtitle" style="margin:6px 0 0;">This service takes a few minutes; the result will appear in your <a href="/orders.php">order history</a>.</p></div></div>');
                resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                showBlacklistPopup(resp.body.blacklist);
                return;
            }
            if (!resp.body || resp.body.ok !== true) {
                var refunded = resp.body && resp.body.refunded;
                var note = refunded ? ' Your credit has been refunded automatically.' : '';
                showError(((resp.body && resp.body.error) || 'Lookup failed (HTTP ' + resp.status + ').') + note,
                    refunded ? 'Order Refunded' : 'Order Failed', refunded ? 'warn' : 'error');
                resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                return;
            }
            renderResult(resp.body);
            resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function () { showError('Network error — please check your connection and try again.', 'Connection Error', 'error'); })
        .finally(function () {
            submit.classList.remove('loading');
            submit.disabled = false;
        });
    });
})();
</script>
<?php layout_foot(); ?>
