(function () {
    'use strict';

    var form    = document.getElementById('imei-form');
    var input   = document.getElementById('imei');
    var button  = document.getElementById('submit-btn');
    var resultEl = document.getElementById('result');

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
        resultEl.classList.add('error');
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

    function renderResult(data) {
        var details = data.details || {};
        var brand = data.brand || details.Brand || details['Brand Name'] || details.Manufacturer || 'Unknown';
        var model = data.model || details.Model || details['Model Name'] || details['Model Description'] || 'Unknown';

        var html = '';
        html += '<h2>' + escapeHtml(brand) + ' ' + escapeHtml(model);
        html += ' <span class="badge' + (data.cached ? ' cached' : '') + '">' +
                (data.cached ? 'Cached' : 'Verified') + '</span></h2>';
        html += '<p class="imei-meta">IMEI <code>' + escapeHtml(data.imei) + '</code>' +
                ' &middot; TAC <code>' + escapeHtml(data.tac || '') + '</code></p>';

        var keys = Object.keys(details);
        if (keys.length === 0) {
            html += '<p class="result-empty">No additional details were returned.</p>';
        } else {
            html += '<dl class="result-fields">';
            keys.forEach(function (k) {
                var v = details[k];
                if (v === null || v === undefined || v === '') return;
                var cls = classifyValue(k, v);
                html += '<div class="result-field">';
                html +=   '<dt>' + escapeHtml(k) + '</dt>';
                html +=   '<dd' + (cls ? ' class="rv-' + cls + '"' : '') + '>' + escapeHtml(v) + '</dd>';
                html += '</div>';
            });
            html += '</dl>';
        }

        resultEl.hidden = false;
        resultEl.classList.remove('error');
        resultEl.innerHTML = html;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var imei = input.value.replace(/\D+/g, '');

        resultEl.hidden = true;
        resultEl.classList.remove('error');

        if (!luhnOk(imei)) {
            showError('Invalid IMEI. Please enter 15 digits (check for typos).');
            return;
        }

        form.classList.add('loading');
        button.disabled = true;

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
                    resultEl.classList.add('error');
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
