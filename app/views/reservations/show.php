<?php $pageId='reservations'; $pageTitle='Reservation #'.$reservation['id']; require __DIR__.'/../layout/header.php';
$sc=match($reservation['status']??''){
  'confirmed'=>'badge-success','pending'=>'badge-warning',
  'cancelled'=>'badge-danger','completed'=>'badge-info',default=>'badge-gray'
};
?>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= base() ?>/reservations" class="btn btn-secondary btn-icon sm" title="Back">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
      <h1>Reservation #<?= $reservation['id'] ?></h1>
      <div class="page-header-sub">Reservation details</div>
    </div>
  </div>
  <div class="page-header-actions">
    <?php if(($reservation['status']??'')==='pending'): ?>
    <form method="POST" action="<?= base() ?>/reservations/<?= $reservation['id'] ?>/confirm">
      <button type="submit" class="btn btn-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        Confirm
      </button>
    </form>
    <?php endif; ?>
    <?php if(in_array($reservation['status']??'',['pending','confirmed'])): ?>
    <form method="POST" action="<?= base() ?>/reservations/<?= $reservation['id'] ?>/cancel" onsubmit="return confirm('Cancel this reservation?')">
      <button type="submit" class="btn btn-danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Cancel Reservation
      </button>
    </form>
    <?php endif; ?>
  </div>
</div>

<div style="max-width:620px">
  <div class="card">
    <div class="card-body">
      <!-- Header -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:14px">
          <div class="avatar" style="width:48px;height:48px;font-size:18px"><?= strtoupper(substr($reservation['client_name'],0,1)) ?></div>
          <div>
            <div style="font-size:18px;font-weight:800"><?= h($reservation['client_name']) ?></div>
            <div style="font-size:13px;color:var(--muted)"><?= h($reservation['phone']??'No phone') ?></div>
          </div>
        </div>
        <span class="badge badge-dot <?= $sc ?>" style="font-size:12px;padding:5px 12px"><?= ucfirst(h($reservation['status'])) ?></span>
      </div>

      <div class="detail-grid">
        <div class="detail-item">
          <div class="detail-item-label">📅 Date</div>
          <div class="detail-item-value"><?= date('l, M d Y',strtotime($reservation['reservation_date'])) ?></div>
        </div>
        <div class="detail-item">
          <div class="detail-item-label">🕐 Time</div>
          <div class="detail-item-value" style="color:var(--primary)"><?= date('H:i',strtotime($reservation['reservation_time'])) ?></div>
        </div>
        <div class="detail-item">
          <div class="detail-item-label">👥 Group Size</div>
          <div class="detail-item-value"><?= $reservation['number_of_people'] ?> persons</div>
        </div>
        <div class="detail-item">
          <div class="detail-item-label">🪑 Table</div>
          <div class="detail-item-value">Table #<?= h($reservation['table_number']??'N/A') ?>
            <?php if(!empty($reservation['capacity'])): ?>
              <span class="badge badge-info" style="margin-left:6px"><?= $reservation['capacity'] ?> seats</span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);font-size:12px;color:var(--muted)">
        Booked on <?= date('M d, Y \a\t H:i',strtotime($reservation['created_at']??'now')) ?>
      </div>
    </div>
    <div class="card-footer">
      <a href="<?= base() ?>/reservations" class="btn btn-ghost">← Back to Reservations</a>
    </div>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
