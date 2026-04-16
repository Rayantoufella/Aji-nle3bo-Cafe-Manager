<?php 
$pageId='dashboard'; 
$pageTitle='Dashboard'; 
require __DIR__.'/../layout/header.php';
// Initialize variables
$totalReservations = $totalReservations ?? 0;
$activeSessions = $activeSessions ?? [];
$availableTables = $availableTables ?? 0;
$totalTables = $totalTables ?? 0;
$todayReservations = $todayReservations ?? 0;
$mpg = $mpg ?? null;
$recentRes = $recentRes ?? [];
$activeList = $activeList ?? [];
$popularGames = $popularGames ?? [];
?>

<!-- STATS -->
<div class="page-header">
  <div>
    <h1>Dashboard</h1>
    <div class="page-header-sub">Welcome back, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> 👋</div>
  </div>
  <div class="page-header-actions">
    <a href="<?= base() ?>/reservations/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Reservation
    </a>
    <a href="<?= base() ?>/sessions/create" class="btn btn-secondary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
      Start Session
    </a>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div>
      <div class="stat-label">Total Reservations</div>
      <div class="stat-value"><?= $totalReservations ?></div>
      <div class="stat-sub up">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        <?= $todayReservations ?> today
      </div>
    </div>
    <div class="stat-icon si-blue">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Active Sessions</div>
      <div class="stat-value"><?= $activeSessions ?></div>
      <div class="stat-sub up">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Live right now
      </div>
    </div>
    <div class="stat-icon si-green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Available Tables</div>
      <div class="stat-value"><?= $availableTables ?></div>
      <div class="stat-sub">of <?= $totalTables ?> total</div>
    </div>
    <div class="stat-icon si-yellow">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="9" x2="9" y2="21"/><line x1="15" y1="9" x2="15" y2="21"/></svg>
    </div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Most Played Game</div>
      <div class="stat-value" style="font-size:18px;letter-spacing:0"><?= $mpg ? h($mpg['name']) : 'N/A' ?></div>
      <div class="stat-sub up"><?= $mpg ? $mpg['cnt'].' sessions' : 'No data yet' ?></div>
    </div>
    <div class="stat-icon si-purple">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    </div>
  </div>
</div>

<div class="dash-grid">
  <!-- LEFT: Recent Reservations -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Recent Reservations</span>
      <a href="<?= base() ?>/reservations" class="btn btn-ghost btn-sm">View All →</a>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>Customer</th><th>Date</th><th>Time</th><th>Table</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($recentRes)): ?>
          <tr><td colspan="6" style="text-align:center;padding:36px;color:var(--muted)">No reservations yet</td></tr>
        <?php else: ?>
          <?php foreach ($recentRes as $r):
            $sc = match($r['status']??'') { 'confirmed'=>'badge-success','pending'=>'badge-warning','cancelled'=>'badge-danger','completed'=>'badge-info',default=>'badge-gray' };
          ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div class="avatar" style="font-size:11px"><?= strtoupper(substr($r['client_name'],0,1)) ?></div>
                <span style="font-weight:600"><?= h($r['client_name']) ?></span>
              </div>
            </td>
            <td><?= date('M d, Y', strtotime($r['reservation_date'])) ?></td>
            <td style="font-weight:600"><?= date('H:i', strtotime($r['reservation_time'])) ?></td>
            <td><span class="badge badge-info">Table <?= h($r['table_number']??'?') ?></span></td>
            <td><span class="badge badge-dot <?= $sc ?>"><?= ucfirst(h($r['status']??'')) ?></span></td>
            <td><a href="<?= base() ?>/reservations/<?= $r['id'] ?>" class="btn btn-ghost btn-sm btn-icon">→</a></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- RIGHT column -->
  <div style="display:flex;flex-direction:column;gap:18px">

    <!-- Active Sessions -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Active Sessions</span>
        <span class="badge badge-dot badge-success"><?= $activeSessions ?> Online</span>
      </div>
      <div class="card-body" style="padding:12px 16px">
        <?php if (empty($activeList)): ?>
          <div style="text-align:center;padding:24px;color:var(--muted);font-size:13px">
            <div style="font-size:32px;margin-bottom:8px">😴</div>
            No active sessions right now
          </div>
        <?php else: ?>
          <?php foreach ($activeList as $s): ?>
          <div class="session-row">
            <div class="session-icon active">🎮</div>
            <div class="session-info">
              <h4><?= h($s['game_name']??'Unknown') ?></h4>
              <p>Table #<?= h($s['table_number']??'?') ?></p>
            </div>
            <div class="session-timer live" data-start="<?= h($s['start_time']) ?>">
              <?php $m=$s['elapsed']??0; printf('%02d:%02d:00',floor($m/60),$m%60); ?>
            </div>
          </div>
          <?php endforeach; ?>
          <div style="margin-top:10px">
            <a href="<?= base() ?>/sessions" class="btn btn-secondary btn-sm" style="width:100%">Monitor All Tables</a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Popular Games -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Popular Games</span>
        <span style="font-size:11px;color:var(--muted)">This month</span>
      </div>
      <div class="card-body" style="padding:14px">
        <?php if (empty($popularGames)): ?>
          <p style="text-align:center;color:var(--muted);font-size:13px;padding:16px">No game data yet.</p>
        <?php else: ?>
          <div style="display:flex;flex-direction:column;gap:10px">
            <?php $colors=['#667eea','#f093fb','#4facfe','#43e97b','#fa709a','#a18cd1'];
            foreach ($popularGames as $i=>$g): ?>
            <div style="display:flex;align-items:center;gap:12px">
              <div style="width:38px;height:38px;border-radius:10px;background:<?= $colors[$i%6] ?>;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">🎲</div>
              <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= h($g['name']) ?></div>
                <div style="font-size:11px;color:var(--muted)"><?= $g['cnt'] ?> sessions</div>
              </div>
              <div style="width:40px;height:6px;background:var(--border);border-radius:3px;overflow:hidden">
                <?php $max=max(1,$popularGames[0]['cnt']); $w=round($g['cnt']/$max*100); ?>
                <div style="width:<?= $w ?>%;height:100%;background:var(--primary);border-radius:3px"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
