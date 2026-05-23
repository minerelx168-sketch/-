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

    function renderResult(data) {
        var brand = data.brand || (data.details && (data.details.Brand || data.details['Brand Name'])) || 'Unknown';
        var model = data.model || (data.details && (data.details.Model || data.details['Model Name'])) || 'Unknown';

        var html = '';
        html += '<h2>' + escapeHtml(brand) + ' ' + escapeHtml(model);
        html += ' <span class="badge' + (data.cached ? ' cached' : '') + '">' +
                (data.cached ? 'Cached' : 'Verified') + '</span></h2>';
        html += '<p class="imei-meta">IMEI <code>' + escapeHtml(data.imei) + '</code>' +
                ' · TAC <code>' + escapeHtml(data.tac || '') + '</code></p>';

        var details = data.details || {};
        // Promote the common keys to the top of the grid.
        var ordered = {};
        ['Brand', 'Brand Name', 'Manufacturer', 'Model', 'Model Name',
         'Model Description', 'Model Number', 'IMEI', 'Serial Number',
         'Network', 'Carrier', 'Country', 'Color', 'Storage', 'Warranty Status',
         'Activation Status', 'iCloud Status', 'Find My iPhone'].forEach(function (k) {
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
                html += '<div class="result-item">';
                html +=   '<span class="k">' + escapeHtml(k) + '</span>';
                html +=   '<span class="v">' + escapeHtml(ordered[k]) + '</span>';
                html += '</div>';
            });
            html += '</div>';
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

        var paid = form.getAttribute('data-paid') === '1';
        var code = form.getAttribute('data-code');
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
