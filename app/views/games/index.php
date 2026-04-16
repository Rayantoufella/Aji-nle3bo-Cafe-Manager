<?php
$pageId='games';
$pageTitle='Game Catalogue';
require __DIR__.'/../layout/header.php';

// Initialize variables with defaults
$games = $games ?? [];
$categories = $categories ?? [];
$availableCount = $availableCount ?? 0;
$inUseCount = $inUseCount ?? 0;
$totalGames = $totalGames ?? 0;
?>

<div class="page-header">
  <div>
    <h1>Game Catalogue</h1>
    <div class="page-header-sub">Manage and browse the café's gaming collection</div>
  </div>
  <div class="page-header-actions">
    <span class="badge badge-success badge-dot"><?= $availableCount ?> Available</span>
    <span class="badge badge-warning badge-dot"><?= $inUseCount ?> In-Use</span>
    <a href="<?= base() ?>/games/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Game
    </a>
  </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
  <div class="filter-search">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="searchInput" placeholder="Search by title or keyword..." value="<?= h($_GET['search']??'') ?>"
      onkeyup="if(event.key==='Enter') window.location='<?= base() ?>/games?search='+encodeURIComponent(this.value)">
  </div>
  <a href="<?= base() ?>/games" class="filter-chip <?= empty($_GET['category']) && empty($_GET['search']) ? 'active' : '' ?>">All</a>
  <?php foreach ($categories as $c): ?>
    <a href="<?= base() ?>/games?category=<?= $c['id'] ?>" class="filter-chip <?= ($_GET['category']??'')==$c['id'] ? 'active' : '' ?>"><?= h($c['name']) ?></a>
  <?php endforeach; ?>
</div>

<!-- GAMES GRID -->
<?php if (empty($games)): ?>
<div class="card"><div class="empty">
  <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
  <h3>No games found</h3>
  <p>Try a different search or add your first game.</p>
  <a href="<?= base() ?>/games/create" class="btn btn-primary">Add New Game</a>
</div></div>
<?php else: ?>
<div class="games-grid">
<?php
$grads=['linear-gradient(135deg,#667eea 0%,#764ba2 100%)','linear-gradient(135deg,#f093fb 0%,#f5576c 100%)','linear-gradient(135deg,#4facfe 0%,#00f2fe 100%)','linear-gradient(135deg,#43e97b 0%,#38f9d7 100%)','linear-gradient(135deg,#fa709a 0%,#fee140 100%)','linear-gradient(135deg,#a18cd1 0%,#fbc2eb 100%)','linear-gradient(135deg,#fccb90 0%,#d57eeb 100%)','linear-gradient(135deg,#a1c4fd 0%,#c2e9fb 100%)'];
foreach ($games as $g):
  $sc = ($g['status'] ?? 'available')==='available' ? 'badge-success' : 'badge-danger';
  $sl = ($g['status'] ?? 'available')==='available' ? 'Available'    : 'In Use';
  $diff = $g['difficulty']??'medium';
  $diffClass = ['easy'=>'diff-easy','medium'=>'diff-medium','hard'=>'diff-hard'][$diff] ?? '';
  $grad = $grads[(int)($g['id'] ?? 0)%8];
?>
<div class="game-card anim-up">
  <div class="game-card-img" style="background:<?= $grad ?>">
    <?php if (!empty($g['image_url'])): ?>
      <img src="<?= h($g['image_url']) ?>" alt="<?= h($g['name']) ?>" onerror="this.parentNode.innerHTML='<span style=\'font-size:52px;opacity:.7\'>🎲</span>'">
    <?php else: ?>
      <span style="font-size:52px;opacity:.75">🎲</span>
    <?php endif; ?>
    <div class="game-card-badge"><span class="badge <?= $sc ?> badge-dot"><?= $sl ?></span></div>
  </div>
  <div class="game-card-body">
    <div class="game-card-cat"><?= h($g['category_name'] ?? 'Uncategorized') ?></div>
    <div class="game-card-name"><?= h($g['name'] ?? 'Unknown') ?></div>
    <div class="game-card-meta">
      <span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <?= (int)($g['nb_players'] ?? 0) ?> players
      </span>
      <span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <?= (int)($g['duration'] ?? 0) ?> min
      </span>
      <span class="<?= $diffClass ?>">● <?= ucfirst($diff) ?></span>
    </div>
    <div class="game-card-footer">
      <a href="<?= base() ?>/games/<?= (int)($g['id'] ?? 0) ?>" class="btn btn-primary btn-sm">View Details</a>
      <a href="<?= base() ?>/games/<?= (int)($g['id'] ?? 0) ?>/edit" class="btn btn-secondary btn-sm btn-icon" title="Edit">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </a>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__.'/../layout/footer.php'; ?>
