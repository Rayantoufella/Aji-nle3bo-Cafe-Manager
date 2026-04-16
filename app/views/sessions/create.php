<?php $pageId='sessions'; $pageTitle='Start Session'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= base() ?>/sessions" class="btn btn-secondary btn-icon sm" title="Back">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div><h1>Start New Session</h1><div class="page-header-sub">Associate a game with an available table</div></div>
  </div>
</div>

<div class="create-layout" style="display:grid;grid-template-columns:1fr 300px;gap:22px">
  <div class="card">
    <div class="card-header"><span class="card-title">Session Setup</span></div>
    <div class="card-body">
      <form method="POST" action="<?= base() ?>/sessions/start">

        <!-- Link to confirmed reservation (optional) -->
        <?php if(!empty($confirmedRes)): ?>
        <div class="form-group">
          <label class="form-label">Link to Today's Reservation <span style="color:var(--muted);font-weight:400">(optional)</span></label>
          <select name="reservation_id" class="form-control" id="resSelect" onchange="prefillFromReservation(this)">
            <option value="">— Walk-in (no reservation) —</option>
            <?php foreach($confirmedRes as $r): ?>
              <option value="<?= $r['id'] ?>" data-table="<?= $r['table_id'] ?>"><?= h($r['client_name']) ?> — Table #<?= h($r['table_number']??'?') ?> at <?= date('H:i',strtotime($r['reservation_time'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Game <span>*</span></label>
            <select name="game_id" class="form-control" required>
              <option value="">— Select a game —</option>
              <?php foreach($games as $g): ?>
                <option value="<?= $g['id'] ?>"><?= h($g['name']) ?> (<?= $g['nb_players'] ?> players · <?= $g['duration'] ?> min)</option>
              <?php endforeach; ?>
            </select>
            <?php if(empty($games)): ?>
              <div class="form-hint" style="color:var(--danger)">⚠ No available games. <a href="<?= base() ?>/games/create">Add one first.</a></div>
            <?php endif; ?>
          </div>
          <div class="form-group">
            <label class="form-label">Table <span>*</span></label>
            <select name="table_id" class="form-control" id="tableSelect" required>
              <option value="">— Select a table —</option>
              <?php foreach($tables as $t): ?>
                <option value="<?= $t['id'] ?>">Table #<?= $t['number'] ?> (<?= $t['capacity'] ?> seats)</option>
              <?php endforeach; ?>
            </select>
            <?php if(empty($tables)): ?>
              <div class="form-hint" style="color:var(--warning)">⚠ All tables are currently occupied.</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="info-box" style="margin-bottom:20px">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          The session timer starts immediately and the selected table will be marked as <strong>Occupied</strong> until you end the session.
        </div>

        <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border)">
          <button type="submit" class="btn btn-primary" <?= empty($games)||empty($tables)?'disabled style="opacity:.5;cursor:not-allowed"':'' ?>>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Start Session Now
          </button>
          <a href="<?= base() ?>/sessions" class="btn btn-ghost">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <!-- TABLE STATUS -->
  <div class="card">
    <div class="card-header"><span class="card-title">Table Status</span></div>
    <div class="card-body" style="padding:14px">
      <?php
      // $allTables fetched from router or use existing $pdo from scope
      if (!isset($allTables)) {
          $allTables = $pdo->query("SELECT * FROM tables_cafe ORDER BY number")->fetchAll();
      }
      if(empty($allTables)): ?>
        <p style="text-align:center;font-size:12px;color:var(--muted);padding:20px">No tables configured.</p>
      <?php else: ?>
      <div class="tables-grid" style="grid-template-columns:repeat(2,1fr)">
        <?php foreach($allTables as $t): $ok=$t['status']==='available'; ?>
        <div class="table-box <?= $ok?'available':'occupied' ?>">
          <div class="table-box-num">#<?= $t['number'] ?></div>
          <div class="table-box-cap"><?= $t['capacity'] ?> seats</div>
          <div style="margin-top:6px;font-size:10px;font-weight:700;color:<?= $ok?'var(--success)':'var(--danger)' ?>;text-transform:uppercase"><?= $ok?'Free':'Busy' ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function prefillFromReservation(sel) {
  const opt = sel.selectedOptions[0];
  if (!opt) return;
  const tableId = opt.dataset.table;
  if (tableId) {
    const ts = document.getElementById('tableSelect');
    if (ts) {
      for (let o of ts.options) { if (o.value === tableId) { o.selected = true; break; } }
    }
  }
}
</script>

<?php require __DIR__.'/../layout/footer.php'; ?>
