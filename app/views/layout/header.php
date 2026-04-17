<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';
$user = $user ?? [
    'username' => $_SESSION['username'] ?? 'Guest',
    'role'     => $_SESSION['user_role'] ?? 'Member'
];
?>
<header class="navbar">
    <div class="container navbar-inner">
        <div class="nav-left">
            <a href="<?= $baseUrl ?>/dashboard" class="brand">
                <div class="brand-icon">
                    <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor" stroke="none"></circle>
                        <circle cx="15.5" cy="8.5" r="1.5" fill="currentColor" stroke="none"></circle>
                        <circle cx="15.5" cy="15.5" r="1.5" fill="currentColor" stroke="none"></circle>
                        <circle cx="8.5" cy="15.5" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </div>
                TableTop Hub
            </a>
            <nav class="nav-links">
                <a href="<?= $baseUrl ?>/dashboard" class="<?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>">Browse Games</a>
                <a href="<?= $baseUrl ?>/reservations" class="<?= (strpos($_SERVER['REQUEST_URI'], '/reservations') !== false) ? 'active' : '' ?>">My Bookings</a>
            </nav>
        </div>
        <div class="nav-right">
            <div class="search-bar">
                <svg class="search-icon icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Search games...">
            </div>

            <button class="icon-btn" id="theme-toggle" title="Toggle dark mode">
                <svg id="theme-icon" class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>

            <div class="icon-btn" title="Notifications">
                <div class="notif-dot"></div>
                <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </div>

            <div class="user-profile">
                <div class="avatar">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['username']) ?>&background=6366f1&color=fff" alt="Avatar" style="width:100%;height:100%;">
                </div>
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
                    <div class="user-role"><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
                </div>
                <a href="<?= $baseUrl ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>
</header>
