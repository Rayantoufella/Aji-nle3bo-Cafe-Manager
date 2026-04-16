<?php $pageId='games'; $pageTitle='Add New Game'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= base() ?>/games" class="btn btn-secondary btn-icon sm" title="Back">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div><h1>Add New Game</h1><div class="page-header-sub">Add a game to the café's collection</div></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:22px">
  <div class="card">
    <div class="card-header"><span class="card-title">Game Information</span></div>
    <div class="card-body">
      <form method="POST" action="<?= base() ?>/games/store">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Game Name <span>*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Catan, Dixit…" required>
          </div>
          <div class="form-group">
            <label class="form-label">Category <span>*</span></label>
            <select name="category_id" class="form-control" required>
              <option value="">Select category…</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label class="form-label">Max Players <span>*</span></label>
            <input type="number" name="nb_players" class="form-control" value="4" min="1" max="20" required>
          </div>
          <div class="form-group">
            <label class="form-label">Duration (min) <span>*</span></label>
            <input type="number" name="duration" class="form-control" value="30" min="5" max="480" required>
          </div>
          <div class="form-group">
            <label class="form-label">Difficulty <span>*</span></label>
            <select name="difficulty" class="form-control" required>
              <option value="easy">Easy</option>
              <option value="medium" selected>Medium</option>
              <option value="hard">Hard</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Image URL <span style="color:var(--muted);font-weight:400">(optional)</span></label>
          <input type="url" name="image_url" class="form-control" placeholder="https://…/image.jpg">
          <div class="form-hint">Leave empty to use default placeholder icon.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" placeholder="Describe the game rules, theme, and what makes it fun…"></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
          </select>
        </div>

        <div style="display:flex;gap:10px;padding-top:6px;border-top:1px solid var(--border);margin-top:6px">
          <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Game
          </button>
          <a href="<?= base() ?>/games" class="btn btn-ghost">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <!-- TIPS -->
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="card">
      <div class="card-body" style="text-align:center;padding:24px">
        <div style="font-size:48px;margin-bottom:12px">🎲</div>
        <h3 style="font-size:15px;font-weight:700;margin-bottom:8px">Adding a Game</h3>
        <ul style="text-align:left;font-size:12.5px;color:var(--muted);list-style:none;display:flex;flex-direction:column;gap:8px">
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> Use the full French or English title</li>
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> Match the correct category for filtering</li>
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> Set difficulty to help customers choose</li>
          <li style="display:flex;gap:6px"><span style="color:var(--success)">✓</span> Paste an image URL for visual cards</li>
        </ul>
      </div>
    </div>
    <div class="info-box">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Games with "unavailable" status won't appear in session creation but remain in the catalogue.
    </div>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
