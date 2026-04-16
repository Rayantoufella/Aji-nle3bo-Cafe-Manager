<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/sessions" class="btn btn-secondary btn-icon sm" title="Back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <h1>Start New Session</h1>
        </div>
        <p>Associate a game, table, and optionally a reservation</p>
    </div>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/sessions/start">
            <div class="form-group">
                <label class="form-label" for="reservation_id">Link to Reservation (optional)</label>
                <select class="form-select" id="reservation_id" name="reservation_id">
                    <option value="">— No reservation (walk-in) —</option>
                    <?php foreach ($reservations as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['client_name']) ?> — Table #<?= $r['table_number'] ?? '?' ?> at <?= date('H:i', strtotime($r['reservation_time'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="game_id">Game *</label>
                    <select class="form-select" id="game_id" name="game_id" required>
                        <option value="">Select a game</option>
                        <?php foreach ($games as $g): ?>
                            <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?> (<?= $g['nb_players'] ?> players, <?= $g['duration'] ?> min)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="table_id">Table *</label>
                    <select class="form-select" id="table_id" name="table_id" required>
                        <option value="">Select a table</option>
                        <?php foreach ($tables as $t): ?>
                            <option value="<?= $t['id'] ?>">Table #<?= $t['number'] ?> (<?= $t['capacity'] ?> seats)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="background:var(--primary-bg);border-radius:var(--radius-sm);padding:16px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span style="font-size:13px;color:var(--primary);font-weight:500;">The session timer will start immediately. The table status will be set to "occupied".</span>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border);">
                <a href="<?= BASE_URL ?>/sessions" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                    Start Session
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
