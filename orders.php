<?php
declare(strict_types=1);

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';

$user   = auth_require();
$userId = (int) $user['id'];

$perPage = 20;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$pdo = db();
$cnt = $pdo->prepare('SELECT COUNT(*) FROM service_usages WHERE user_id = ?');
$cnt->execute([$userId]);
$total = (int) $cnt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));

$stmt = $pdo->prepare(
    'SELECT u.id, u.public_id, u.service_code, u.cost, u.status, u.input, u.created_at,
            sp.name AS service_name
     FROM service_usages u
     LEFT JOIN service_prices sp ON sp.code = u.service_code
     WHERE u.user_id = ?
     ORDER BY u.id DESC
     LIMIT ' . $perPage . ' OFFSET ' . $offset
);
$stmt->execute([$userId]);
$rows = $stmt->fetchAll();

// status -> dot color
function order_dot(string $status): string
{
    switch (strtoupper($status)) {
        case 'SUCCESS':    return '#16a34a';
        case 'PROCESSING':
        case 'PENDING':    return '#d97706';
        case 'REFUNDED':   return '#6b7280';
        case 'FAILED':     return '#dc2626';
        default:           return '#9ca3af';
    }
}
function order_label(string $status): string
{
    return strtoupper($status) === 'SUCCESS' ? 'Completed' : ucfirst(strtolower($status));
}

layout_head('Order history · imeihub', 'Your past IMEI lookups.');
?>
<section class="dashboard">
  <div class="container">
    <header class="dashboard-head">
      <div>
        <p class="hero-eyebrow" style="color:var(--text-muted);border-color:var(--border);background:var(--surface-alt)">History</p>
        <h1>IMEI orders</h1>
        <p class="dashboard-subtitle"><?= $total ?> order(s) on record</p>
      </div>
    </header>

    <section class="dashboard-block" style="margin-top:24px">
      <?php if (!$rows): ?>
        <div class="empty-state"><p><strong>No orders yet.</strong></p>
          <p>Run a check from the <a href="/">homepage</a> to see it here.</p></div>
      <?php else: ?>
        <div style="overflow-x:auto">
        <table class="ledger-table">
          <thead><tr>
            <th>Order</th><th>Service</th><th>Status</th><th>Date</th><th>IMEI</th>
            <th class="num">Credits</th><th></th>
          </tr></thead>
          <tbody>
          <?php foreach ($rows as $r):
              $imei = (string) (json_decode((string) $r['input'], true)['imei'] ?? '');
              $pid  = (string) $r['public_id'];
          ?>
            <tr>
              <td class="muted">#<?= (int) $r['id'] ?></td>
              <td><strong><?= htmlspecialchars((string) ($r['service_name'] ?: $r['service_code']), ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td><span style="display:inline-flex;align-items:center;gap:7px">
                <span style="width:9px;height:9px;border-radius:50%;background:<?= order_dot((string) $r['status']) ?>"></span>
                <?= htmlspecialchars(order_label((string) $r['status']), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td class="muted"><?= htmlspecialchars(substr((string) $r['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
              <td style="font-family:var(--font-mono)"><?= htmlspecialchars($imei, ENT_QUOTES, 'UTF-8') ?></td>
              <td class="num"><?= number_format((float) $r['cost'], 3) ?></td>
              <td style="white-space:nowrap;text-align:right">
                <?php if (strtoupper((string) $r['status']) === 'SUCCESS'): ?>
                <button type="button" class="order-act js-view" data-id="<?= htmlspecialchars($pid, ENT_QUOTES, 'UTF-8') ?>" title="View result" aria-label="View result">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                <?php endif; ?>
                <button type="button" class="order-act order-act--report js-report" data-id="<?= htmlspecialchars($pid, ENT_QUOTES, 'UTF-8') ?>" title="Report this IMEI as blacklisted" aria-label="Report IMEI">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22V4h13l-2 4 2 4H4"/></svg>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        </div>
      <?php endif; ?>
    </section>

    <?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Pagination">
      <?php if ($page > 1): ?><a class="pagination-link" href="?page=<?= $page - 1 ?>">&larr; Previous</a>
      <?php else: ?><span class="pagination-link is-disabled">&larr; Previous</span><?php endif; ?>
      <span class="pagination-info">Page <?= $page ?> of <?= $pages ?></span>
      <?php if ($page < $pages): ?><a class="pagination-link" href="?page=<?= $page + 1 ?>">Next &rarr;</a>
      <?php else: ?><span class="pagination-link is-disabled">Next &rarr;</span><?php endif; ?>
    </nav>
    <?php endif; ?>
  </div>
</section>

<div id="order-modal" class="bl-overlay" hidden>
  <div class="result result--report" style="max-width:560px;width:100%">
    <div class="result-banner result-banner--success" id="om-banner">Result</div>
    <div class="result-card"><div id="om-body" class="result-lines"></div>
      <p style="text-align:center;margin:18px 0 0"><button type="button" class="bl-dismiss" id="om-close" style="background:var(--text-muted)">Close</button></p>
    </div>
  </div>
</div>

<script>
(function () {
  var modal = document.getElementById('order-modal');
  var body  = document.getElementById('om-body');
  var banner = document.getElementById('om-banner');

  function esc(s){return String(s).replace(/[&<>"']/g,function(c){return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];});}
  function classify(k,v){var x=String(v).trim(),key=String(k).toLowerCase();
    if(/^(blacklisted|stolen|lost|fraud|denied|expired|invalid|sold|locked)$/i.test(x))return 'danger';
    if(/^(activated|active|clean|unlocked|covered|in warranty)$/i.test(x))return 'success';
    var alert=/(find\s*my|fmi|icloud|mdm|sim.?lock|activation\s*lock|locked|jailbreak)/i;
    if(/^on$/i.test(x))return alert.test(key)?'danger':'success';
    if(/^off$/i.test(x))return alert.test(key)?'success':'muted';
    var warnY=/(repair|blacklist|fraud|lost|stolen|replaced|refurbished|demo|jailbreak|loaner)/i;
    if(/^yes$/i.test(x))return warnY.test(key)?'warn':'success';
    if(/^no$/i.test(x))return warnY.test(key)?'success':null;
    return null;}
  function valueHtml(k,v){var c=classify(k,v),t=/^[\w.+/-]{1,16}$/.test(String(v).trim());
    return (c&&t)?'<span class="pill pill-'+c+'">'+esc(v)+'</span>':esc(v);}

  function openModal(){ modal.hidden=false; }
  function closeModal(){ modal.hidden=true; }
  document.getElementById('om-close').addEventListener('click', closeModal);
  modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });

  document.querySelectorAll('.js-view').forEach(function(btn){
    btn.addEventListener('click', function(){
      var id = btn.getAttribute('data-id');
      banner.textContent = 'Loading…';
      body.innerHTML = '<div class="rline">Loading result…</div>';
      openModal();
      fetch('/api/orders/view.php?id='+encodeURIComponent(id), {credentials:'same-origin'})
        .then(function(r){return r.json();})
        .then(function(d){
          if(!d.ok){ banner.textContent='Not found'; body.innerHTML='<div class="rline">'+esc(d.error||'Error')+'</div>'; return; }
          banner.textContent = (d.service||'Result');
          var det = d.details||{};
          var brand=d.brand||det['Brand Name']||det.Brand||'';
          var model=d.model||det['Model Name']||det.Model||'';
          var html='<div class="rline rline--model"><span class="rk">Model:</span> <strong>'+esc((brand+' '+model).trim()||'Device')+'</strong></div>';
          var skip={'brand name':1,'brand':1,'model':1,'model name':1,'model description':1,'manufacturer':1};
          Object.keys(det).forEach(function(k){ if(skip[k.toLowerCase()])return; var v=det[k]; if(v===''||v==null)return;
            html+='<div class="rline"><span class="rk">'+esc(k)+':</span> '+valueHtml(k,v)+'</div>'; });
          body.innerHTML = html;
        }).catch(function(){ banner.textContent='Error'; body.innerHTML='<div class="rline">Network error.</div>'; });
    });
  });

  document.querySelectorAll('.js-report').forEach(function(btn){
    btn.addEventListener('click', function(){
      if(btn.disabled) return;
      if(!confirm('Report this IMEI as blacklisted? Anyone who checks it afterwards will be warned.')) return;
      var reason = prompt('Reason (optional) — e.g. stolen, unpaid installment:') || '';
      var id = btn.getAttribute('data-id');
      btn.disabled = true;
      fetch('/api/blacklist/report.php', {method:'POST',credentials:'same-origin',
        headers:{'Content-Type':'application/json'},body:JSON.stringify({public_id:id,reason:reason})})
        .then(function(r){return r.json();})
        .then(function(d){
          if(d.ok){ btn.title='Reported ('+d.reports+')'; btn.classList.add('is-reported'); }
          else { alert(d.error||'Could not report.'); btn.disabled=false; }
        }).catch(function(){ alert('Network error.'); btn.disabled=false; });
    });
  });
})();
</script>
<?php layout_foot(); ?>
