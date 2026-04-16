<?php $pageId='categories'; $pageTitle='Categories'; require __DIR__.'/../layout/header.php'; ?>

<div class="page-header">
  <div><h1>Categories</h1><div class="page-header-sub">Organize games into groups for easy filtering</div></div>
</div>

<div class="create-layout" style="display:grid;grid-template-columns:1fr 340px;gap:22px">

  <!-- CATEGORIES LIST -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">All Categories</span>
      <span class="badge badge-primary"><?= count($cats) ?> total</span>
    </div>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr><th>#</th><th>Name</th><th>Games</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if(empty($cats)): ?>
          <tr><td colspan="4" style="text-align:center;padding:44px;color:var(--muted)">
            <div style="font-size:36px;margin-bottom:10px">📂</div>
            No categories yet. Add your first one!
          </td></tr>
        <?php else:
          $catIcons=['🎯','🎭','👨‍👩‍👧‍👦','🧠','🎲','🃏','♟️','🎪','🔮','🎰','🧩','🎻'];
          foreach($cats as $i=>$c):
        ?>
          <tr>
            <td style="color:var(--muted);font-size:12px;font-weight:600">#<?= $c['id'] ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:12px">
                <div style="width:38px;height:38px;border-radius:10px;background:var(--primary-light);font-size:20px;display:flex;align-items:center;justify-content:center"><?= $catIcons[$i % count($catIcons)] ?></div>
                <span style="font-weight:700;font-size:14px"><?= h($c['name']) ?></span>
              </div>
            </td>
            <td>
              <span class="badge badge-info"><?= $c['game_count'] ?> game<?= $c['game_count']!=1?'s':'' ?></span>
            </td>
            <td>
              <?php if($c['game_count'] == 0): ?>
              <form method="POST" action="<?= base() ?>/categories/<?= $c['id'] ?>/delete" style="display:inline"
                onsubmit="return confirm('Delete category \'<?= h(addslashes($c['name'])) ?>\'?')">
                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </form>
              <?php else: ?>
              <span title="Cannot delete: category has games" style="cursor:not-allowed;opacity:.4">
                <button class="btn btn-danger btn-sm btn-icon" disabled>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ADD CATEGORY FORM -->
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="card">
      <div class="card-header"><span class="card-title">Add Category</span></div>
      <div class="card-body">
        <form method="POST" action="<?= base() ?>/categories/store">
          <div class="form-group">
            <label class="form-label">Category Name <span>*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Stratégie, Ambiance, Famille…" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Category
          </button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body" style="text-align:center;padding:22px">
        <div style="font-size:44px;margin-bottom:12px">📂</div>
        <h3 style="font-size:14px;font-weight:700;margin-bottom:8px">Organize Your Games</h3>
        <p style="font-size:12.5px;color:var(--muted);line-height:1.7">Categories help customers find the perfect game. Use clear names like <strong>Stratégie</strong>, <strong>Ambiance</strong>, <strong>Famille</strong>, or <strong>Experts</strong>.</p>
      </div>
    </div>

    <div class="info-box">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Categories with games cannot be deleted. Remove or reassign the games first.
    </div>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
