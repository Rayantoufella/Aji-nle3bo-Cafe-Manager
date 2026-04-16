<?php $pageId='games'; $pageTitle='Edit Game'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= base() ?>/games/<?= $game['id'] ?>" class="btn btn-secondary btn-icon sm" title="Back">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div><h1>Edit Game</h1><div class="page-header-sub">Updating: <?= h($game['name']) ?></div></div>
  </div>
</div>

<div class="card" style="max-width:700px">
  <div class="card-header"><span class="card-title">Game Details</span></div>
  <div class="card-body">
    <form method="POST" action="<?= base() ?>/games/<?= $game['id'] ?>/update">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Game Name <span>*</span></label>
          <input type="text" name="name" class="form-control" value="<?= h($game['name']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Category <span>*</span></label>
          <select name="category_id" class="form-control" required>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $c['id']==$game['category_id']?'selected':'' ?>><?= h($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-row-3">
        <div class="form-group">
          <label class="form-label">Max Players <span>*</span></label>
          <input type="number" name="nb_players" class="form-control" value="<?= h($game['nb_players']) ?>" min="1" max="20" required>
        </div>
        <div class="form-group">
          <label class="form-label">Duration (min) <span>*</span></label>
          <input type="number" name="duration" class="form-control" value="<?= h($game['duration']) ?>" min="5" max="480" required>
        </div>
        <div class="form-group">
          <label class="form-label">Difficulty <span>*</span></label>
          <select name="difficulty" class="form-control" required>
            <?php foreach (['easy','medium','hard'] as $d): ?>
              <option value="<?= $d ?>" <?= $game['difficulty']===$d?'selected':'' ?>><?= ucfirst($d) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Image URL</label>
        <input type="url" name="image_url" class="form-control" value="<?= h($game['image_url']??'') ?>" placeholder="https://…/image.jpg">
      </div>

      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= h($game['description']??'') ?></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="available" <?= $game['status']==='available'?'selected':'' ?>>Available</option>
          <option value="unavailable" <?= $game['status']==='unavailable'?'selected':'' ?>>Unavailable</option>
        </select>
      </div>

      <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border)">
        <button type="submit" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save Changes
        </button>
        <a href="<?= base() ?>/games/<?= $game['id'] ?>" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
