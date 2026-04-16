<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/games" class="btn btn-secondary btn-icon sm" title="Back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <h1>Edit Game</h1>
        </div>
        <p>Update <?= htmlspecialchars($game['name']) ?></p>
    </div>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>/games/<?= $game['id'] ?>/update">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Game Name *</label>
                    <input type="text" class="form-input" id="name" name="name" value="<?= htmlspecialchars($game['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="category_id">Category *</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $game['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="nb_players">Max Players *</label>
                    <input type="number" class="form-input" id="nb_players" name="nb_players" value="<?= $game['nb_players'] ?>" min="1" max="20" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="duration">Duration (min) *</label>
                    <input type="number" class="form-input" id="duration" name="duration" value="<?= $game['duration'] ?>" min="5" max="480" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="difficulty">Difficulty *</label>
                    <select class="form-select" id="difficulty" name="difficulty" required>
                        <option value="easy" <?= $game['difficulty'] === 'easy' ? 'selected' : '' ?>>Easy</option>
                        <option value="medium" <?= $game['difficulty'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="hard" <?= $game['difficulty'] === 'hard' ? 'selected' : '' ?>>Hard</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="image_url">Image URL</label>
                <input type="url" class="form-input" id="image_url" name="image_url" value="<?= htmlspecialchars($game['image_url'] ?? '') ?>" placeholder="https://example.com/image.jpg">
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-textarea" id="description" name="description"><?= htmlspecialchars($game['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="available" <?= $game['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="unavailable" <?= $game['status'] === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                    </select>
                </div>
                <div></div>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--border);">
                <a href="<?= BASE_URL ?>/games" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
