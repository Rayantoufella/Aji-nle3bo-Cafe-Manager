<?php $pageId='reservations'; $pageTitle='Reservations'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div>
    <h1>Reservations</h1>
    <div class="page-header-sub">Manage all café table reservations</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= base() ?>/reservations/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Reservation
    </a>
  </div>
</div>

<!-- STATS -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
  <div class="stat-card">
    <div><div class="stat-label">Total</div><div class="stat-value"><?= $totalCount ?></div></div>
    <div class="stat-icon si-blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div><div class="stat-label">Today</div><div class="stat-value"><?= $todayCount ?></div></div>
    <div class="stat-icon si-green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div><div class="stat-label">Pending</div><div class="stat-value"><?= $pendingCount ?></div></div>
    <div class="stat-icon si-yellow">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div><div class="stat-label">Confirmed</div><div class="stat-value"><?= $confirmedCount ?></div></div>
    <div class="stat-icon si-purple">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
  </div>
</div>

<!-- FILTER TABS -->
<div class="filter-bar" style="margin-bottom:16px">
  <?php $f=$filter??'all'; foreach(['all'=>'All','today'=>'Today','upcoming'=>'Upcoming','mine'=>'My Reservations'] as $k=>$lbl): ?>
    <a href="<?= base() ?>/reservations?filter=<?= $k ?>" class="filter-chip <?= $f===$k?'active':'' ?>"><?= $lbl ?></a>
  <?php endforeach; ?>
</div>

<!-- TABLE -->
<div class="card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>#</th><th>Customer</th><th>Phone</th><th>Date</th><th>Time</th><th>People</th><th>Table</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php if (empty($reservations)): ?>
        <tr><td colspan="9" style="text-align:center;padding:48px;color:var(--muted)">
          <div style="font-size:40px;margin-bottom:10px">📅</div>
          No reservations found
        </td></tr>
      <?php else: foreach ($reservations as $r):
        $sc=match($r['status']??''){
          'confirmed'=>'badge-success','pending'=>'badge-warning',
          'cancelled'=>'badge-danger','completed'=>'badge-info',default=>'badge-gray'
        };
      ?>
        <tr>
          <td style="color:var(--muted);font-size:12px;font-weight:600">#<?= $r['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <div class="avatar" style="font-size:11px"><?= strtoupper(substr($r['client_name'],0,1)) ?></div>
              <div style="font-weight:600;font-size:13px"><?= h($r['client_name']) ?></div>
            </div>
          </td>
          <td style="color:var(--muted);font-size:12px"><?= h($r['phone']??'—') ?></td>
          <td style="font-weight:600"><?= date('M d, Y',strtotime($r['reservation_date'])) ?></td>
          <td><span style="font-weight:700;color:var(--primary)"><?= date('H:i',strtotime($r['reservation_time'])) ?></span></td>
          <td><span class="badge badge-info"><?= $r['number_of_people'] ?> pers.</span></td>
          <td><span class="badge badge-primary">Table <?= h($r['table_number']??'?') ?></span></td>
          <td><span class="badge badge-dot <?= $sc ?>"><?= ucfirst(h($r['status']??'')) ?></span></td>
          <td>
            <div style="display:flex;gap:6px;align-items:center">
              <a href="<?= base() ?>/reservations/<?= $r['id'] ?>" class="btn btn-ghost btn-sm btn-icon" title="View">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <?php if(($r['status']??'')==='pending'): ?>
              <form method="POST" action="<?= base() ?>/reservations/<?= $r['id'] ?>/confirm" style="display:inline">
                <button type="submit" class="btn btn-success btn-sm btn-icon" title="Confirm">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </button>
              </form>
              <?php endif; ?>
              <?php if(in_array($r['status']??'',['pending','confirmed'])): ?>
              <form method="POST" action="<?= base() ?>/reservations/<?= $r['id'] ?>/cancel" style="display:inline" onsubmit="return confirm('Cancel this reservation?')">
                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Cancel">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
