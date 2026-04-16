<?php require __DIR__ . '/../layout/header.php'; ?>

<!-- ═══════ STATS ═══════ -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Reservations</div>
            <div class="stat-value"><?= $totalReservations ?></div>
            <div class="stat-change up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                All time
            </div>
        </div>
        <div class="stat-icon blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Active Sessions</div>
            <div class="stat-value"><?= $activeSessions ?></div>
            <div class="stat-change up">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Right now
            </div>
        </div>
        <div class="stat-icon green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Available Tables</div>
            <div class="stat-value"><?= $availableTables ?></div>
            <div class="stat-change">
                <span style="color:var(--muted);">of <?= $totalTables ?> total</span>
            </div>
        </div>
        <div class="stat-icon orange">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Most Played Game</div>
            <div class="stat-value" style="font-size:20px;"><?= $mostPlayed ? htmlspecialchars($mostPlayed['name']) : 'N/A' ?></div>
            <div class="stat-change up">
                <?php if ($mostPlayed): ?>
                    <?= $mostPlayed['play_count'] ?> sessions
                <?php else: ?>
                    No data yet
                <?php endif; ?>
            </div>
        </div>
        <div class="stat-icon purple">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </div>
    </div>
</div>

<!-- ═══════ MAIN GRID ═══════ -->
<div class="dashboard-grid">
    <!-- LEFT: Recent Reservations -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Reservations</h3>
            <a href="<?= BASE_URL ?>/reservations" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Table</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentReservations)): ?>
                        <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">No reservations yet</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentReservations as $res): ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div class="topbar-avatar" style="width:32px;height:32px;font-size:12px;"><?= strtoupper(substr($res['client_name'], 0, 1)) ?></div>
                                    <span style="font-weight:600;"><?= htmlspecialchars($res['client_name']) ?></span>
                                </div>
                            </td>
                            <td style="color:var(--muted);"><?= date('M d, Y', strtotime($res['reservation_date'])) ?></td>
                            <td style="color:var(--muted);"><?= date('H:i', strtotime($res['reservation_time'])) ?></td>
                            <td><span class="badge badge-info">Table <?= $res['table_number'] ?? 'N/A' ?></span></td>
                            <td>
                                <?php 
                                $statusClass = match($res['status']) {
                                    'confirmed' => 'badge-success',
                                    'pending' => 'badge-warning',
                                    'cancelled' => 'badge-danger',
                                    'completed' => 'badge-primary',
                                    default => 'badge-info'
                                };
                                ?>
                                <span class="badge badge-dot <?= $statusClass ?>"><?= ucfirst($res['status']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RIGHT: Active Sessions -->
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <h3 class="card-title">Active Sessions</h3>
                <span class="badge badge-success badge-dot"><?= $activeSessions ?> Online</span>
            </div>
            <div class="card-body" style="padding:12px 16px;">
                <?php if (empty($activeSessionsList)): ?>
                    <div style="text-align:center;padding:30px;color:var(--muted);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.3;margin-bottom:8px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <p style="font-size:13px;">No active sessions</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($activeSessionsList as $session): ?>
                    <div class="session-card" style="margin-bottom:8px;">
                        <div class="session-card-icon active">🎮</div>
                        <div class="session-card-info">
                            <h4><?= htmlspecialchars($session['game_name']) ?></h4>
                            <p>Table <?= $session['table_number'] ?> · <?= htmlspecialchars($session['client_name'] ?? 'Walk-in') ?></p>
                        </div>
                        <div class="session-card-timer running" data-start="<?= $session['start_time'] ?>">
                            <?php 
                            $mins = $session['elapsed_minutes'] ?? 0;
                            printf('%02d:%02d:%02d', floor($mins/60), $mins%60, 0);
                            ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/sessions" class="btn btn-secondary btn-sm" style="width:100%;margin-top:8px;">Monitor All Tables</a>
            </div>
        </div>

        <!-- Popular Games -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Popular Games</h3>
            </div>
            <div class="card-body" style="padding:16px;">
                <?php if (empty($popularGames)): ?>
                    <p style="text-align:center;color:var(--muted);padding:20px;">No game data yet</p>
                <?php else: ?>
                    <div style="display:flex;gap:12px;overflow-x:auto;padding-bottom:8px;">
                        <?php foreach ($popularGames as $pg): ?>
                        <div style="min-width:100px;text-align:center;flex-shrink:0;">
                            <div style="width:72px;height:72px;border-radius:12px;background:linear-gradient(135deg,var(--primary-bg),#e0e7ff);display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:28px;">🎲</div>
                            <div style="font-size:12px;font-weight:600;margin-bottom:2px;"><?= htmlspecialchars($pg['name']) ?></div>
                            <div style="font-size:11px;color:var(--muted);"><?= $pg['category_name'] ?? '' ?></div>
                            <div style="font-size:10px;color:var(--success);font-weight:600;margin-top:2px;"><?= $pg['play_count'] ?> played</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Live timer update for active sessions
function updateTimers() {
    document.querySelectorAll('.session-card-timer[data-start]').forEach(el => {
        const start = new Date(el.dataset.start);
        const now = new Date();
        const diff = Math.floor((now - start) / 1000);
        const h = Math.floor(diff / 3600);
        const m = Math.floor((diff % 3600) / 60);
        const s = diff % 60;
        el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    });
}
setInterval(updateTimers, 1000);
updateTimers();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
