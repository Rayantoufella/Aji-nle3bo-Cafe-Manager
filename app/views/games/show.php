<?php
$pageId='games';
require __DIR__.'/../layout/header.php';
// Initialize game variable with defaults
$game = $game ?? [
    'id' => 0,
    'name' => 'Unknown Game',
    'status' => 'available',
    'difficulty' => 'medium',
    'description' => '',
    'category_name' => 'N/A',
    'nb_players' => 0,
    'duration' => 0,
    'image_url' => ''
];
$pageTitle = h($game['name']);

$diff = $game['difficulty'] ?? 'medium';
$diffClass = ['easy'=>'diff-easy','medium'=>'diff-medium','hard'=>'diff-hard'][$diff]??'';
$grads=['linear-gradient(135deg,#667eea 0%,#764ba2 100%)','linear-gradient(135deg,#f093fb 0%,#f5576c 100%)','linear-gradient(135deg,#4facfe 0%,#00f2fe 100%)','linear-gradient(135deg,#43e97b 0%,#38f9d7 100%)','linear-gradient(135deg,#fa709a 0%,#fee140 100%)','linear-gradient(135deg,#a18cd1 0%,#fbc2eb 100%)'];
$grad = $grads[(int)($game['id'] ?? 0) % 6];
?>

<div class="page-header">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="<?= base() ?>/games" class="btn btn-secondary btn-icon sm" title="Back">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
      <h1><?= h($game['name']) ?></h1>
      <div class="page-header-sub">Game details</div>
    </div>
  </div>
  <div class="page-header-actions">
    <a href="<?= base() ?>/games/<?= $game['id'] ?? 0 ?>/edit" class="btn btn-secondary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit
    </a>
    <form method="POST" action="<?= base() ?>/games/<?= $game['id'] ?? 0 ?>/delete" onsubmit="return confirm('Delete this game? This cannot be undone.')">
      <button type="submit" class="btn btn-danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        Delete
      </button>
    </form>
  </div>
</div>

<div class="create-layout" style="display:grid;grid-template-columns:360px 1fr;gap:22px">

  <!-- IMAGE -->
  <div class="card" style="overflow:hidden">
    <div style="height:280px;display:flex;align-items:center;justify-content:center;background:<?= $grad ?>">
      <?php if (!empty($game['image_url'])): ?>
        <img src="<?= h($game['image_url']) ?>" alt="<?= h($game['name']) ?>" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div style="display:none;align-items:center;justify-content:center;font-size:80px">🎲</div>
      <?php else: ?>
        <span style="font-size:80px;opacity:.8">🎲</span>
      <?php endif; ?>
    </div>
    <div class="card-body" style="padding:16px">
       <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
         <span class="badge <?= ($game['status'] ?? 'available')==='available' ? 'badge-success badge-dot' : 'badge-danger badge-dot' ?>"><?= ucfirst(h($game['status'] ?? 'available')) ?></span>
         <span class="badge badge-info"><?= h($game['category_name'] ?? 'N/A') ?></span>
         <span class="badge badge-gray <?= $diffClass ?>"><?= ucfirst($diff) ?> difficulty</span>
       </div>
    </div>
  </div>

  <!-- DETAILS -->
  <div class="card">
    <div class="card-body">
      <h2 style="font-size:22px;font-weight:800;margin-bottom:18px"><?= h($game['name']) ?></h2>

       <div class="detail-grid" style="margin-bottom:22px">
         <div class="detail-item">
           <div class="detail-item-label">Category</div>
           <div class="detail-item-value"><?= h($game['category_name'] ?? 'N/A') ?></div>
         </div>
         <div class="detail-item">
           <div class="detail-item-label">Max Players</div>
           <div class="detail-item-value"><?= h((string)($game['nb_players'] ?? 0)) ?></div>
         </div>
         <div class="detail-item">
           <div class="detail-item-label">Duration</div>
           <div class="detail-item-value"><?= h((string)($game['duration'] ?? 0)) ?> min</div>
         </div>
         <div class="detail-item">
           <div class="detail-item-label">Difficulty</div>
           <div class="detail-item-value <?= $diffClass ?>"><?= ucfirst($diff) ?></div>
         </div>
       </div>

      <h3 style="font-size:14px;font-weight:700;margin-bottom:8px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Description</h3>
      <p style="font-size:13.5px;color:var(--text-2);line-height:1.8">
        <?= nl2br(h($game['description']??'No description available.')) ?>
      </p>
    </div>
    <div class="card-footer" style="display:flex;gap:10px">
       <a href="<?= base() ?>/games/<?= $game['id'] ?? 0 ?>/edit" class="btn btn-primary">Edit Game</a>
       <a href="<?= base() ?>/games" class="btn btn-ghost">← Back to Catalogue</a>
    </div>
  </div>
</div>

<?php require __DIR__.'/../layout/footer.php'; ?>
