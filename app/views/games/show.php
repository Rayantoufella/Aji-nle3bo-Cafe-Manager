<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/games" class="btn btn-secondary btn-icon sm" title="Back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <h1><?= htmlspecialchars($game['name']) ?></h1>
        </div>
        <p>Game details and information</p>
    </div>
    <div class="page-header-right">
        <a href="<?= BASE_URL ?>/games/<?= $game['id'] ?>/edit" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            Edit Game
        </a>
        <form method="POST" action="<?= BASE_URL ?>/games/<?= $game['id'] ?>/delete" onsubmit="return confirm('Are you sure you want to delete this game?');" style="display:inline;">
            <button type="submit" class="btn btn-danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                Delete
            </button>
        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:400px 1fr;gap:28px;">
    <!-- Image -->
    <div class="card" style="overflow:hidden;">
        <div style="height:300px;display:flex;align-items:center;justify-content:center;">
            <?php if (!empty($game['image_url'])): ?>
                <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['name']) ?>" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                <?php 
                $gradients = [
                    'linear-gradient(135deg, #667eea, #764ba2)',
                    'linear-gradient(135deg, #f093fb, #f5576c)',
                    'linear-gradient(135deg, #4facfe, #00f2fe)',
                    'linear-gradient(135deg, #43e97b, #38f9d7)',
                ];
                $gradient = $gradients[$game['id'] % count($gradients)];
                ?>
                <div style="width:100%;height:100%;background:<?= $gradient ?>;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;">
                    <span style="font-size:72px;">🎲</span>
                    <span style="color:rgba(255,255,255,.8);font-weight:600;"><?= htmlspecialchars($game['name']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details -->
    <div class="card">
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
                <h2 style="font-size:24px;font-weight:800;"><?= htmlspecialchars($game['name']) ?></h2>
                <?php if ($game['status'] === 'available'): ?>
                    <span class="badge badge-success badge-dot">Available</span>
                <?php else: ?>
                    <span class="badge badge-danger badge-dot">Unavailable</span>
                <?php endif; ?>
            </div>

            <!-- Meta grid -->
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;">
                <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                    <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Category</div>
                    <div style="font-size:15px;font-weight:700;"><?= htmlspecialchars($game['category_name'] ?? 'N/A') ?></div>
                </div>
                <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                    <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Players</div>
                    <div style="font-size:15px;font-weight:700;"><?= $game['nb_players'] ?></div>
                </div>
                <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                    <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Duration</div>
                    <div style="font-size:15px;font-weight:700;"><?= $game['duration'] ?> min</div>
                </div>
                <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;text-align:center;">
                    <div style="font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Difficulty</div>
                    <div style="font-size:15px;font-weight:700;">
                        <?php 
                        $diffColors = ['easy' => 'var(--success)', 'medium' => 'var(--warning)', 'hard' => 'var(--danger)'];
                        ?>
                        <span style="color:<?= $diffColors[$game['difficulty']] ?? 'var(--text)' ?>;"><?= ucfirst($game['difficulty']) ?></span>
                    </div>
                </div>
            </div>

            <h3 style="font-size:16px;font-weight:700;margin-bottom:12px;">Description</h3>
            <p style="font-size:14px;color:var(--text-secondary);line-height:1.8;">
                <?= nl2br(htmlspecialchars($game['description'] ?? 'No description available.')) ?>
            </p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
