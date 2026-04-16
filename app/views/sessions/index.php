<?php $pageId='sessions'; $pageTitle='Sessions'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div>
    <h1>Sessions</h1>
    <div class="page-header-sub">Monitor and manage gaming sessions in real-time</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= base() ?>/sessions/history" class="btn btn-secondary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      History
    </a>
    <a href="<?= base() ?>/sessions/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
      Start Session
    </a>
  </div>
</div>

<!-- STATS -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
  <div class="stat-card">
    <div>
      <div class="stat-label">Active Now</div>
      <div class="stat-value"><?= $activeCount ?></div>
      <div class="stat-sub up" style="color:var(--success)">● Live</div>
    </div>
    <div class="stat-icon si-green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div><div class="stat-label">Today's Sessions</div><div class="stat-value"><?= $todayCount ?></div></div>
    <div class="stat-icon si-blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div><div class="stat-label">All Time</div><div class="stat-value"><?= $totalCount ?></div></div>
    <div class="stat-icon si-purple">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
  </div>
</div>

<!-- ACTIVE SESSIONS CARDS -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Active Sessions</span>
    <?php if($activeCount > 0): ?>
      <span class="badge badge-success badge-dot"><?= $activeCount ?> running</span>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <?php if (empty($activeSessions)): ?>
    <div class="empty">
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <h3>No Active Sessions</h3>
      <p>All tables are free. Start a session to see it here.</p>
      <a href="<?= base() ?>/sessions/create" class="btn btn-primary">Start a Session</a>
    </div>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
    <?php foreach($activeSessions as $s): ?>
      <div class="card" style="border:2px solid var(--success);box-shadow:0 0 0 4px rgba(16,185,129,.08)">
        <!-- Gradient bar -->
        <div style="height:6px;background:linear-gradient(90deg,var(--success),var(--accent));border-radius:var(--radius-lg) var(--radius-lg) 0 0"></div>
        <div class="card-body" style="padding:16px">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px">
            <div>
              <div style="font-size:11px;font-weight:600;color:var(--success);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px">Table #<?= h($s['table_number']) ?></div>
              <div style="font-size:16px;font-weight:800"><?= h($s['game_name']??'Unknown') ?></div>
              <div style="font-size:12px;color:var(--muted);margin-top:2px"><?= h($s['capacity']??'?') ?> seats max</div>
            </div>
            <div style="background:var(--success-bg);border-radius:10px;width:38px;height:38px;display:flex;align-items:center;justify-content:center;font-size:20px">🎮</div>
          </div>

          <!-- Timer -->
          <div style="background:var(--surface-2);border-radius:10px;padding:12px;text-align:center;margin-bottom:14px">
            <div style="font-size:10px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Elapsed Time</div>
            <div class="session-timer live" data-start="<?= h($s['start_time']) ?>" style="font-size:26px;font-weight:800;font-family:'Courier New',monospace;color:var(--danger)">
              <?php $m=$s['elapsed']??0; printf('%02d:%02d:00',floor($m/60),$m%60); ?>
            </div>
          </div>

          <div style="font-size:11.5px;color:var(--muted);margin-bottom:14px">
            Started at <?= date('H:i',strtotime($s['start_time'])) ?>
          </div>

          <form method="POST" action="<?= base() ?>/sessions/<?= $s['id'] ?>/end" onsubmit="return confirm('End this session and free the table?')">
            <button type="submit" class="btn btn-danger" style="width:100%">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
              End Session & Free Table
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
