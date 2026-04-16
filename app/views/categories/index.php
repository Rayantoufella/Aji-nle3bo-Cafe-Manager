<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Categories</h1>
        <p>Manage game categories</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:28px;">
    <!-- Categories List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Categories</h3>
            <span class="badge badge-primary"><?= count($categories) ?> total</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Games</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--muted);">No categories yet. Add your first one!</td></tr>
                    <?php else: ?>
                        <?php 
                        $icons = ['🎯', '🎭', '👨‍👩‍👧‍👦', '🧠', '🎲', '🃏', '♟️', '🎪'];
                        foreach ($categories as $i => $cat): ?>
                        <tr>
                            <td style="color:var(--muted);font-weight:600;">#<?= $cat['id'] ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;font-size:18px;"><?= $icons[$i % count($icons)] ?></div>
                                    <span style="font-weight:700;font-size:14px;"><?= htmlspecialchars($cat['name']) ?></span>
                                </div>
                            </td>
                            <td><span class="badge badge-info"><?= $cat['game_count'] ?? 0 ?> games</span></td>
                            <td>
                                <a href="<?= BASE_URL ?>/categories/<?= $cat['id'] ?>/delete" class="btn btn-danger btn-sm btn-icon" title="Delete" onclick="return confirm('Delete this category? Games in this category will be unassigned.');">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Category -->
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add Category</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>/categories/store">
                    <div class="form-group">
                        <label class="form-label" for="name">Category Name *</label>
                        <input type="text" class="form-input" id="name" name="name" placeholder="e.g. Stratégie, Ambiance..." required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Add Category
                    </button>
                </form>
            </div>
        </div>

        <div class="card" style="margin-top:20px;">
            <div class="card-body" style="text-align:center;">
                <div style="font-size:40px;margin-bottom:12px;">📂</div>
                <h4 style="font-size:14px;font-weight:700;margin-bottom:6px;">Organize Your Games</h4>
                <p style="font-size:12px;color:var(--muted);line-height:1.6;">Categories help customers find the right game. Use clear names like "Stratégie", "Ambiance", "Famille", or "Experts".</p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
