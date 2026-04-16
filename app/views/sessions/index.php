<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Sessions</h1>
        <p>Monitor and manage gaming sessions</p>
    </div>
    <div class="page-header-right">
        <a href="<?= BASE_URL ?>/sessions/history" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            History
        </a>
        <a href="<?= BASE_URL ?>/sessions/create" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
            Start Session
        </a>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card">
        <div>
            <div class="stat-label">Active Now</div>
            <div class="stat-value"><?= $activeCount ?></div>
            <div class="stat-change up">
                <span class="badge badge-success badge-dot" style="font-size:10px;">Live</span>
            </div>
        </div>
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Today's Sessions</div>
            <div class="stat-value"><?= $todayCount ?></div>
        </div>
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Sessions</div>
            <div class="stat-value"><?= $totalCount ?></div>
        </div>
        <div class="stat-icon purple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
    </div>
</div>

<!-- Active Sessions -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <span style="display:flex;align-items:center;gap:8px;">
                Active Sessions
                <span class="badge badge-success badge-dot"><?= $activeCount ?> running</span>
            </span>
        </h3>
    </div>
    <div class="card-body">
        <?php if (empty($activeSessions)): ?>
            <div class="empty-state">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <h3>No Active Sessions</h3>
                <p>Start a new gaming session to see it here.</p>
                <a href="<?= BASE_URL ?>/sessions/create" class="btn btn-primary">Start Session</a>
            </div>
        <?php else: ?>
            <?php foreach ($activeSessions as $session): ?>
            <div class="session-card">
                <div class="session-card-icon active">🎮</div>
                <div class="session-card-info" style="flex:1;">
                    <h4><?= htmlspecialchars($session['game_name'] ?? 'Unknown Game') ?></h4>
                    <p>Table #<?= $session['table_number'] ?? '?' ?> · <?= htmlspecialchars($session['client_name'] ?? 'Walk-in') ?> · <?= $session['number_of_people'] ?? '?' ?> players</p>
                </div>
                <div class="session-card-timer running" data-start="<?= $session['start_time'] ?>">
                    <?php 
                    $mins = $session['elapsed_minutes'] ?? 0;
                    printf('%02d:%02d:%02d', floor($mins/60), $mins%60, 0);
                    ?>
                </div>
                <form method="POST" action="<?= BASE_URL ?>/sessions/<?= $session['id'] ?>/end" onsubmit="return confirm('End this session and free the table?');">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="6" width="12" height="12" rx="2"></rect></svg>
                        End
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
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
