<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Game Catalogue</h1>
        <p>Manage and browse the café's gaming collection</p>
    </div>
    <div class="page-header-right">
        <span class="badge badge-success badge-dot"><?= $availableCount ?> Games Available</span>
        <span class="badge badge-warning badge-dot"><?= $inUseCount ?> In-Use</span>
        <a href="<?= BASE_URL ?>/games/create" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Game
        </a>
    </div>
</div>

<!-- FILTERS -->
<div class="filter-bar">
    <div class="filter-search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" placeholder="Search by game title, keyword or publisher..." id="gameSearch" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" onkeyup="if(event.key==='Enter') window.location='<?= BASE_URL ?>/games?search='+this.value">
    </div>
    <a href="<?= BASE_URL ?>/games" class="filter-btn <?= empty($_GET['category']) ? 'active' : '' ?>">All</a>
    <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>/games?category=<?= $cat['id'] ?>" class="filter-btn <?= ($_GET['category'] ?? '') == $cat['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- GAMES GRID -->
<?php if (empty($games)): ?>
    <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
        <h3>No games found</h3>
        <p>Try a different search or add your first game to the collection.</p>
        <a href="<?= BASE_URL ?>/games/create" class="btn btn-primary">Add New Game</a>
    </div>
<?php else: ?>
    <div class="games-grid">
        <?php foreach ($games as $game): ?>
        <div class="game-card animate-slide">
            <div class="game-card-image">
                <?php if (!empty($game['image_url'])): ?>
                    <img src="<?= htmlspecialchars($game['image_url']) ?>" alt="<?= htmlspecialchars($game['name']) ?>">
                <?php else: ?>
                    <?php 
                    $gradients = [
                        'linear-gradient(135deg, #667eea, #764ba2)',
                        'linear-gradient(135deg, #f093fb, #f5576c)',
                        'linear-gradient(135deg, #4facfe, #00f2fe)',
                        'linear-gradient(135deg, #43e97b, #38f9d7)',
                        'linear-gradient(135deg, #fa709a, #fee140)',
                        'linear-gradient(135deg, #a18cd1, #fbc2eb)',
                    ];
                    $gradient = $gradients[$game['id'] % count($gradients)];
                    ?>
                    <div style="width:100%;height:100%;background:<?= $gradient ?>;display:flex;align-items:center;justify-content:center;">
                        <span style="font-size:48px;opacity:.8;">🎲</span>
                    </div>
                <?php endif; ?>
                <div class="game-card-status">
                    <?php if ($game['status'] === 'available'): ?>
                        <span class="badge badge-success">Available</span>
                    <?php else: ?>
                        <span class="badge badge-danger">In Use</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="game-card-body">
                <div class="game-card-title">
                    <?= htmlspecialchars($game['name']) ?>
                    <span class="badge badge-primary" style="font-size:10px;"><?= htmlspecialchars($game['category_name'] ?? 'N/A') ?></span>
                </div>
                <div class="game-card-meta">
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <?= $game['nb_players'] ?> Players
                    </span>
                    <span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <?= $game['duration'] ?> min
                    </span>
                </div>
                <div class="game-card-actions">
                    <a href="<?= BASE_URL ?>/games/<?= $game['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                    <a href="<?= BASE_URL ?>/games/<?= $game['id'] ?>/edit" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
