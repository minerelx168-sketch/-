(function () {
    'use strict';

    var form    = document.getElementById('imei-form');
    var input   = document.getElementById('imei');
    var button  = document.getElementById('submit-btn');
    var resultEl = document.getElementById('result');
    var requestStartedAt = 0;

    if (!form) return;

    // Auto-strip non-digits as the user types.
    input.addEventListener('input', function () {
        var cleaned = input.value.replace(/\D+/g, '').slice(0, 17);
        if (cleaned !== input.value) input.value = cleaned;
    });

    function luhnOk(s) {
        if (!/^\d{15}$/.test(s)) return false;
        var sum = 0;
        for (var i = 0; i < 15; i++) {
            var d = parseInt(s[i], 10);
            if (i % 2 === 1) {
                d *= 2;
                if (d > 9) d -= 9;
            }
            sum += d;
        }
        return sum % 10 === 0;
    }

    function showError(msg) {
        resultEl.hidden = false;
        resultEl.className = 'result error';
        resultEl.innerHTML =
            '<h2>Lookup failed</h2>' +
            '<p class="imei-meta">' + escapeHtml(msg) + '</p>';
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
        });
    }

    // Pick a color class for a field based on both the key (context) and
    // the value. We need the key because "Yes" / "ON" / "OFF" flip
    // meaning depending on what is being asked - "Find My iPhone: ON" is
    // a problem for a buyer; "Activation Status: Activated" is fine;
    // "Refurbished: No" is good; "Open Repair Case: Yes" is a warning.
    function classifyValue(key, val) {
        var v = String(val).trim();
        var k = String(key).toLowerCase();

        // Unambiguous "danger" tokens.
        if (/^(blacklisted|stolen|lost|fraud|denied|expired|invalid|sold)/i.test(v)) return 'danger';
        if (/^(locked)$/i.test(v)) return 'danger';

        // Unambiguous "success" tokens.
        if (/^(activated|active|clean|unlocked|covered|in warranty)$/i.test(v)) return 'success';

        // ON / OFF - depends on what is being toggled.
        var alertKey = /(find\s*my|fmi|icloud|mdm|sim.?lock|activation\s*lock|locked|jailbreak)/i;
        if (/^on$/i.test(v))  return alertKey.test(k) ? 'danger'  : 'success';
        if (/^off$/i.test(v)) return alertKey.test(k) ? 'success' : 'muted';

        // Yes / No - the "yes is a problem" set is small and explicit.
        var warnYesKey = /(repair|blacklist|fraud|lost|stolen|replaced|refurbished|demo|jailbreak|loaner)/i;
        if (/^yes$/i.test(v)) return warnYesKey.test(k) ? 'warn'    : 'success';
        if (/^no$/i.test(v))  return warnYesKey.test(k) ? 'success' : null;

        return null;
    }

    // Render the "we placed the order, waiting on the provider" state.
    // Used for DHRU services (1-5+ min). The caller starts polling
    // /api/services/status.php?id=<publicId> right after this.
    function renderProcessing(publicId) {
        resultEl.hidden = false;
        resultEl.className = 'result';
        resultEl.innerHTML =
            '<h2>Processing&hellip; <span class="badge cached">Async</span></h2>' +
            '<p class="imei-meta">Reference <code>' + escapeHtml(publicId) + '</code></p>' +
            '<div class="result-processing">' +
              '<div class="result-spinner" aria-hidden="true"></div>' +
              '<div>' +
                '<strong>Your order has been placed with the provider.</strong>' +
                '<p class="dashboard-subtitle" style="margin:6px 0 0;">' +
                  'This service typically takes 1&ndash;5 minutes. This page updates ' +
                  'automatically when the result is ready &mdash; safe to leave open or ' +
                  'come back later via your dashboard.' +
                '</p>' +
              '</div>' +
            '</div>';
    }

    // Poll the status endpoint until the lookup completes or fails.
    // Backs off based on the retry_after the server suggests; gives up
    // after ~10 minutes of attempts, at which point the user can
    // revisit from the dashboard (a cron sweep also keeps stuck rows
    // moving). We re-arm the form so the user can submit a new one
    // without waiting for this poll to finish.
    var activePollTimer = null;
    function pollStatus(publicId, retryAfterSec) {
        if (activePollTimer) clearTimeout(activePollTimer);
        var delay = Math.max(3, parseInt(retryAfterSec, 10) || 8) * 1000;
        var deadline = Date.now() + 10 * 60 * 1000; // 10 min

        function tick() {
            if (Date.now() > deadline) return;
            fetch('/api/services/status.php?id=' + encodeURIComponent(publicId), {
                credentials: 'same-origin',
            }).then(function (r) {
                return r.json().then(function (j) { return { status: r.status, body: j }; });
            }).then(function (resp) {
                var body = resp.body || {};
                if (body.ok && body.status === 'success') {
                    renderResult(body);
                    resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return;
                }
                if (body.ok === false || body.status === 'failed' || body.status === 'refunded') {
                    var note = body.refunded ? ' Your credit has been refunded automatically.' : '';
                    showError((body.error || 'Lookup failed.') + note);
                    return;
                }
                // Still processing - schedule the next tick.
                activePollTimer = setTimeout(tick, Math.max(3, parseInt(body.retry_after, 10) || 8) * 1000);
            }).catch(function () {
                // Treat as transient - keep polling with a slightly
                // longer gap so we don't hammer the server.
                activePollTimer = setTimeout(tick, delay + 2000);
            });
        }

        activePollTimer = setTimeout(tick, delay);
    }

    // A value is rendered as a colored pill when it is a short status
    // token (EXPIRED / CLEAN / OFF / NO / UNLOCKED ...) that classifyValue
    // can color. Longer values (model names, dates, IMEI numbers, free
    // text like "Out Of Warranty") render as plain text.
    function valueHtml(key, val) {
        var cls = classifyValue(key, val);
        var token = /^[\w.+/-]{1,16}$/.test(String(val).trim());
        if (cls && token) {
            return '<span class="pill pill-' + cls + '">' + escapeHtml(val) + '</span>';
        }
        return escapeHtml(val);
    }

    function renderResult(data) {
        var details = data.details || {};
        var brand = data.brand || details.Brand || details['Brand Name'] || details.Manufacturer || '';
        var model = data.model || details.Model || details['Model Name'] || details['Model Description'] || '';
        var modelStr = (details['Model Description'] || details['Model'] || (brand + ' ' + model)).trim()
                       || details['Model Name'] || 'Unknown device';

        // Keys already represented by the Model line / not worth repeating.
        var skip = { 'brand name': 1, 'brand': 1, 'manufacturer': 1, 'model': 1, 'model name': 1, 'model description': 1 };

        var lines = '<div class="rline rline--model"><span class="rk">Model:</span> <strong>' + escapeHtml(modelStr) + '</strong></div>';
        Object.keys(details).forEach(function (k) {
            if (skip[String(k).toLowerCase()]) return;
            var v = details[k];
            if (v === null || v === undefined || v === '') return;
            lines += '<div class="rline"><span class="rk">' + escapeHtml(k) + ':</span> ' + valueHtml(k, v) + '</div>';
        });

        var secs = requestStartedAt ? ((Date.now() - requestStartedAt) / 1000).toFixed(1) : null;
        var dateStr = new Date().toLocaleString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit'
        }).toUpperCase();

        var html = '';
        html += '<div class="result-banner">Order Processed!</div>';
        html += '<div class="result-card">';
        html +=   '<div class="result-lines">' + lines + '</div>';
        html +=   '<div class="result-chips">';
        html +=     '<span class="result-chip">' + (secs !== null ? escapeHtml(secs) + ' SECONDS' : 'COMPLETED') + '</span>';
        html +=     '<span class="result-chip">' + escapeHtml(dateStr) + '</span>';
        html +=   '</div>';
        html += '</div>';

        resultEl.hidden = false;
        resultEl.className = 'result result--report';
        resultEl.innerHTML = html;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var imei = input.value.replace(/\D+/g, '');

        resultEl.hidden = true;
        resultEl.className = 'result';

        if (!luhnOk(imei)) {
            showError('Invalid IMEI. Please enter 15 digits (check for typos).');
            return;
        }

        form.classList.add('loading');
        button.disabled = true;
        requestStartedAt = Date.now();

        // If the form has a <select name="code">, use the selected option's
        // data-cost to decide between the free (IMEI_BASIC) and paid paths.
        // Otherwise fall back to the static data-paid + data-code attributes
        // that per-service landing pages use.
        var paid, code;
        var select = form.querySelector('select[name="code"]');
        if (select) {
            code = select.value;
            var opt = select.options[select.selectedIndex];
            paid = parseFloat(opt && opt.getAttribute('data-cost') || '0') > 0;
        } else {
            paid = form.getAttribute('data-paid') === '1';
            code = form.getAttribute('data-code');
        }
        var fetchPromise;

        if (paid && code) {
            // Auth-required, paid path. Deducts credit, refunds on failure.
            fetchPromise = fetch('/api/services/use.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ code: code, input: { imei: imei } })
            });
        } else {
            // Anonymous free path - posts to the legacy check endpoint.
            var body = new URLSearchParams();
            body.set('imei', imei);
            var serviceId = form.getAttribute('data-service');
            if (serviceId) body.set('service', serviceId);

            fetchPromise = fetch('/api/check.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            });
        }

        fetchPromise
            .then(function (res) {
                return res.json().then(function (json) {
                    return { status: res.status, body: json };
                });
            })
            .then(function (resp) {
                // 401 on a paid lookup -> push to /login and come back.
                if (resp.status === 401) {
                    var next = encodeURIComponent(window.location.pathname + window.location.search);
                    window.location.href = '/login.php?next=' + next;
                    return;
                }
                // 402 = insufficient credit. Surface a Top-up CTA inline.
                if (resp.status === 402 || (resp.body && resp.body.error_code === 'INSUFFICIENT_CREDIT')) {
                    resultEl.hidden = false;
                    resultEl.className = 'result error';
                    resultEl.innerHTML =
                        '<h2>Not enough credit</h2>' +
                        '<p class="imei-meta">' + escapeHtml(resp.body.error || 'Please top up your wallet.') + '</p>' +
                        '<p style="margin-top:14px;"><a href="/topup.php" class="link-more">Top up credit &rarr;</a></p>';
                    return;
                }
                if (!resp.body || resp.body.ok !== true) {
                    var note = resp.body && resp.body.refunded
                        ? ' Your credit has been refunded automatically.'
                        : '';
                    showError(((resp.body && resp.body.error) || 'Lookup failed (HTTP ' + resp.status + ').') + note);
                    return;
                }
                // DHRU async services: order was placed, no result yet.
                // Render the processing card and start polling.
                if (resp.body.status === 'processing' && resp.body.public_id) {
                    renderProcessing(resp.body.public_id);
                    resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    pollStatus(resp.body.public_id, resp.body.retry_after);
                    return;
                }
                renderResult(resp.body);
                resultEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(function () {
                showError('Network error. Please try again.');
            })
            .finally(function () {
                form.classList.remove('loading');
                button.disabled = false;
            });
    });
})();
