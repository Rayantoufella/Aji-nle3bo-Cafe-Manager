<?php $pageId='reservations'; $pageTitle='New Reservation'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= base() ?>/reservations" class="btn btn-secondary btn-icon sm" title="Back">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div><h1>New Reservation</h1><div class="page-header-sub">Book a table for a gaming session</div></div>
  </div>
</div>

<div class="create-layout" style="display:grid;grid-template-columns:1fr 300px;gap:22px">
  <div class="card">
    <div class="card-header"><span class="card-title">Reservation Details</span></div>
    <div class="card-body">
      <form method="POST" action="<?= base() ?>/reservations/store">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Customer Name <span>*</span></label>
            <input type="text" name="client_name" class="form-control" placeholder="Full name" required>
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" class="form-control" placeholder="+212 6…">
          </div>
        </div>
        <div class="form-row-3">
          <div class="form-group">
            <label class="form-label">Date <span>*</span></label>
            <input type="date" name="reservation_date" class="form-control" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Time <span>*</span></label>
            <input type="time" name="reservation_time" class="form-control" value="18:00" required>
          </div>
          <div class="form-group">
            <label class="form-label">People <span>*</span></label>
            <input type="number" name="number_of_people" class="form-control" value="2" min="1" max="20" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Table <span>*</span></label>
          <select name="table_id" class="form-control" required>
            <option value="">— Choose a table —</option>
            <?php foreach ($tables as $t): ?>
              <option value="<?= $t['id'] ?>">Table #<?= $t['number'] ?> — <?= $t['capacity'] ?> seats · <?= ucfirst($t['status']??'') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border)">
          <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Create Reservation
          </button>
          <a href="<?= base() ?>/reservations" class="btn btn-ghost">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <!-- SIDEBAR INFO -->
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="card">
      <div class="card-body" style="text-align:center;padding:22px">
        <div style="font-size:44px;margin-bottom:12px">📅</div>
        <h3 style="font-size:14px;font-weight:700;margin-bottom:12px">Booking Tips</h3>
        <ul style="text-align:left;font-size:12.5px;color:var(--muted);list-style:none;display:flex;flex-direction:column;gap:8px">
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> Choose a table that fits your group</li>
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> Reservations start as "Pending"</li>
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> Admin will confirm before the slot</li>
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> You can cancel anytime before it starts</li>
        </ul>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><span class="card-title" style="font-size:13px">Available Tables</span></div>
      <div class="card-body" style="padding:12px">
        <?php if(empty($tables)): ?>
          <p style="text-align:center;font-size:12px;color:var(--muted);padding:12px">No tables configured yet.</p>
        <?php else: ?>
          <div style="display:flex;flex-direction:column;gap:6px">
          <?php foreach($tables as $t): $ok=$t['status']==='available'; ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;background:var(--surface-2)">
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:8px;height:8px;border-radius:50%;background:<?= $ok?'var(--success)':'var(--danger)' ?>"></div>
                <span style="font-size:13px;font-weight:600">Table #<?= $t['number'] ?></span>
              </div>
              <span class="badge <?= $ok?'badge-success':'badge-danger' ?>"><?= $t['capacity'] ?> seats</span>
            </div>
          <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
