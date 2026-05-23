/* imeihub static demo — backend-less IMEI lookup */
(function () {
  var DEMO_DB = {
    "35915206": {
        "brand": "Apple",
        "model": "iPhone 15 Pro Max",
        "release": "2023",
        "os": "iOS 17",
        "color": "Natural Titanium",
        "storage": "256 GB",
        "model_no": "A2849"
    },
    "35373708": {
        "brand": "Apple",
        "model": "iPhone 14 Pro",
        "release": "2022",
        "os": "iOS 16",
        "color": "Deep Purple",
        "storage": "128 GB",
        "model_no": "A2890"
    },
    "35690103": {
        "brand": "Apple",
        "model": "iPhone 13",
        "release": "2021",
        "os": "iOS 15",
        "color": "Midnight",
        "storage": "128 GB",
        "model_no": "A2633"
    },
    "35328211": {
        "brand": "Apple",
        "model": "iPhone 12",
        "release": "2020",
        "os": "iOS 14",
        "color": "Pacific Blue",
        "storage": "64 GB",
        "model_no": "A2403"
    },
    "35316110": {
        "brand": "Samsung",
        "model": "Galaxy S24 Ultra",
        "release": "2024",
        "os": "Android 14",
        "color": "Titanium Black",
        "storage": "256 GB",
        "model_no": "SM-S928B"
    },
    "35692611": {
        "brand": "Samsung",
        "model": "Galaxy S23",
        "release": "2023",
        "os": "Android 13",
        "color": "Phantom Black",
        "storage": "128 GB",
        "model_no": "SM-S911B"
    },
    "35276011": {
        "brand": "Samsung",
        "model": "Galaxy A54 5G",
        "release": "2023",
        "os": "Android 13",
        "color": "Awesome Lime",
        "storage": "128 GB",
        "model_no": "SM-A546B"
    },
    "86891306": {
        "brand": "Xiaomi",
        "model": "Redmi Note 13 Pro",
        "release": "2024",
        "os": "Android 13",
        "color": "Midnight Black",
        "storage": "256 GB",
        "model_no": "23090RA98G"
    },
    "35840911": {
        "brand": "Google",
        "model": "Pixel 8 Pro",
        "release": "2023",
        "os": "Android 14",
        "color": "Obsidian",
        "storage": "256 GB",
        "model_no": "GE9DP"
    },
    "86432105": {
        "brand": "OnePlus",
        "model": "12",
        "release": "2024",
        "os": "Android 14",
        "color": "Flowy Emerald",
        "storage": "256 GB",
        "model_no": "CPH2581"
    }
};
  window.fetch = (function (orig) {
    return function (url, opts) {
      if (typeof url === 'string' && url.indexOf('/api/check.php') !== -1) {
        var body = opts && opts.body ? new URLSearchParams(opts.body) : new URLSearchParams();
        var imei = body.get('imei') || '';
        var tac  = imei.substr(0, 8);
        var info = DEMO_DB[tac] || { brand: 'Unknown', model: 'GSM Phone', release: '-', os: '-' };
        var details = {
          'Brand Name': info.brand, 'Model Name': info.model,
          'Model Number': info.model_no || '-',
          'IMEI': imei, 'TAC': tac,
          'Serial Number': imei.substr(8, 6),
          'Color': info.color || '-', 'Storage': info.storage || '-',
          'Release Year': info.release || '-', 'Operating System': info.os || '-'
        };
        var resp = { ok: true, cached: false, imei: imei, tac: tac, brand: info.brand, model: info.model, details: details };
        return Promise.resolve(new Response(JSON.stringify(resp), { status: 200, headers: { 'Content-Type': 'application/json' } }));
      }
      return orig.apply(this, arguments);
    };
  })(window.fetch);
})();

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
