<?php $pageId='sessions'; $pageTitle='Session History'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= base() ?>/sessions" class="btn btn-secondary btn-icon sm" title="Back">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div><h1>Session History</h1><div class="page-header-sub">Complete log of all finished gaming sessions</div></div>
  </div>
  <div class="page-header-actions">
    <a href="<?= base() ?>/sessions/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
      Start Session
    </a>
  </div>
</div>

<div class="card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>#</th><th>Game</th><th>Table</th><th>Started</th><th>Ended</th><th>Duration</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php if(empty($sessions)): ?>
        <tr><td colspan="7" style="text-align:center;padding:56px;color:var(--muted)">
          <div style="font-size:44px;margin-bottom:12px">🕹️</div>
          <div style="font-size:15px;font-weight:600;margin-bottom:4px">No completed sessions yet</div>
          <div style="font-size:13px">Start and end sessions to build up the history.</div>
        </td></tr>
      <?php else: ?>
        <?php foreach($sessions as $s): ?>
        <tr>
          <td style="color:var(--muted);font-size:12px;font-weight:600">#<?= $s['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:9px">
              <div style="width:34px;height:34px;border-radius:9px;
                background:linear-gradient(135deg,var(--primary),var(--secondary));
                display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">🎲</div>
              <span style="font-weight:600"><?= h($s['game_name']??'Unknown') ?></span>
            </div>
          </td>
          <td><span class="badge badge-primary">Table #<?= h($s['table_number']??'?') ?></span></td>
          <td style="font-size:12.5px;color:var(--muted)"><?= $s['start_time'] ? date('M d, H:i',strtotime($s['start_time'])) : '—' ?></td>
          <td style="font-size:12.5px;color:var(--muted)"><?= $s['end_time'] ? date('M d, H:i',strtotime($s['end_time'])) : '—' ?></td>
          <td>
            <?php if(isset($s['duration_minutes']) && $s['duration_minutes'] !== null):
              $dm = (int)$s['duration_minutes'];
              $display = ($dm >= 60 ? floor($dm/60).'h ' : '') . ($dm%60).'min';
            ?>
              <span class="badge badge-info">⏱ <?= $display ?></span>
            <?php else: ?>
              <span class="badge badge-gray">N/A</span>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-dot badge-success">Finished</span></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
