<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/sessions" class="btn btn-secondary btn-icon sm" title="Back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <h1>Session History</h1>
        </div>
        <p>Complete log of all finished gaming sessions</p>
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Game</th>
                    <th>Table</th>
                    <th>Customer</th>
                    <th>Started</th>
                    <th>Ended</th>
                    <th>Duration</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sessions)): ?>
                    <tr><td colspan="8" style="text-align:center;padding:50px;color:var(--muted);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.3;display:block;margin:0 auto 12px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        No session history yet
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($sessions as $s): ?>
                    <tr>
                        <td style="color:var(--muted);font-weight:600;">#<?= $s['id'] ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:8px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;font-size:16px;">🎲</div>
                                <span style="font-weight:600;"><?= htmlspecialchars($s['game_name'] ?? 'Unknown') ?></span>
                            </div>
                        </td>
                        <td><span class="badge badge-primary">Table #<?= $s['table_number'] ?? '?' ?></span></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($s['client_name'] ?? 'Walk-in') ?></td>
                        <td style="font-size:12px;color:var(--muted);"><?= date('M d H:i', strtotime($s['start_time'])) ?></td>
                        <td style="font-size:12px;color:var(--muted);"><?= $s['end_time'] ? date('M d H:i', strtotime($s['end_time'])) : '-' ?></td>
                        <td>
                            <?php if (isset($s['duration_minutes'])): ?>
                                <span class="badge badge-info">
                                    <?php 
                                    $dm = (int)$s['duration_minutes'];
                                    echo ($dm >= 60 ? floor($dm/60).'h ' : '') . ($dm%60).'m'; 
                                    ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning">N/A</span>
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

<?php require __DIR__ . '/../layout/footer.php'; ?>
