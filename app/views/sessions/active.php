<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/sessions" class="btn btn-secondary btn-icon sm" title="Back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <h1>Active Sessions</h1>
        </div>
        <p>Real-time view of all active gaming tables</p>
    </div>
    <div class="page-header-right">
        <a href="<?= BASE_URL ?>/sessions/create" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
            Start Session
        </a>
    </div>
</div>

<?php if (empty($activeSessions)): ?>
    <div class="card">
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <h3>All Tables Are Free</h3>
            <p>No active gaming sessions right now. Start one to begin tracking.</p>
            <a href="<?= BASE_URL ?>/sessions/create" class="btn btn-primary">Start a Session</a>
        </div>
    </div>
<?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:20px;">
        <?php foreach ($activeSessions as $session): ?>
        <div class="card" style="overflow:hidden;">
            <!-- Header with gradient -->
            <div style="background:linear-gradient(135deg,var(--primary),var(--secondary));padding:20px 24px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <span style="background:rgba(255,255,255,.2);padding:4px 12px;border-radius:100px;font-size:12px;font-weight:600;">Table #<?= $session['table_number'] ?? '?' ?></span>
                    <span class="badge" style="background:rgba(255,255,255,.2);color:white;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#4ade80;display:inline-block;margin-right:4px;animation:pulse-timer 2s infinite;"></span>
                        Active
                    </span>
                </div>
                <h3 style="font-size:20px;font-weight:800;margin-bottom:4px;"><?= htmlspecialchars($session['game_name'] ?? 'Unknown Game') ?></h3>
                <p style="opacity:.85;font-size:13px;"><?= htmlspecialchars($session['client_name'] ?? 'Walk-in customer') ?></p>
            </div>
            <!-- Body -->
            <div class="card-body">
                <div style="text-align:center;margin-bottom:16px;">
                    <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;">Time Elapsed</div>
                    <div class="session-card-timer running" data-start="<?= $session['start_time'] ?>" style="font-size:32px;font-weight:800;background:none;padding:0;display:block;">
                        <?php $mins = $session['elapsed_minutes'] ?? 0; printf('%02d:%02d:%02d', floor($mins/60), $mins%60, 0); ?>
                    </div>
                </div>
                <div style="display:flex;gap:12px;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:16px;">
                    <span>Started: <?= date('H:i', strtotime($session['start_time'])) ?></span>
                    <span><?= $session['number_of_people'] ?? '?' ?> players</span>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/sessions/<?= $session['id'] ?>/end" onsubmit="return confirm('End this session?');">
                    <button type="submit" class="btn btn-danger" style="width:100%;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="6" width="12" height="12" rx="2"></rect></svg>
                        End Session & Free Table
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function updateTimers() {
    document.querySelectorAll('.session-card-timer[data-start]').forEach(el => {
        const start = new Date(el.dataset.start);
        const diff = Math.floor((new Date() - start) / 1000);
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
