<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';
$user = $user ?? [
    'username' => $_SESSION['username'] ?? 'Guest',
    'role'     => $_SESSION['user_role'] ?? 'Member'
];
?>
<style>
/* Header styles for layouts that don't include them globally */
.navbar { position: sticky; top: 0; z-index: 100; background: var(--bg-nav, rgba(255,255,255,0.85)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid var(--border-color, #e5e7eb); transition: all 0.3s; }
.navbar-inner { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; max-width: 1220px; margin: 0 auto; }
.nav-left { display: flex; align-items: center; gap: 36px; }
.brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.15rem; color: var(--primary, #6366f1); text-decoration: none; }
.brand-icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary, #6366f1) 0%, var(--accent2, #ec4899) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(99,102,241,0.35); }
.icon-md { width: 22px; height: 22px; }
.icon-sm { width: 16px; height: 16px; }
.nav-links { display: flex; gap: 4px; font-size: 0.9rem; font-weight: 500; }
.nav-links a { padding: 8px 14px; border-radius: 8px; color: var(--text-muted, #6b7280); text-decoration: none; transition: all 0.3s; }
.nav-links a:hover { background: var(--primary-light, #e0e7ff); color: var(--primary, #6366f1); }
.nav-links a.active { background: var(--primary-light, #e0e7ff); color: var(--primary, #6366f1); font-weight: 700; }
.nav-right { display: flex; align-items: center; gap: 12px; }
.search-bar { position: relative; display: flex; align-items: center; }
.search-bar input { padding: 9px 14px 9px 38px; border: 1.5px solid var(--border-color, #e5e7eb); background: var(--bg-gray, #f1f5f9); color: var(--text-main, #111827); border-radius: 24px; font-size: 0.88rem; outline: none; width: 230px; transition: all 0.3s; font-family: inherit; }
.search-bar input::placeholder { color: var(--text-muted, #6b7280); }
.search-bar input:focus { border-color: var(--primary, #6366f1); background: var(--bg-card, #fff); width: 260px; }
.search-icon { position: absolute; left: 13px; color: var(--text-muted, #6b7280); pointer-events: none; }
.icon-btn { width: 38px; height: 38px; background: var(--bg-gray, #f1f5f9); border: 1.5px solid var(--border-color, #e5e7eb); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--text-muted, #6b7280); cursor: pointer; transition: all 0.3s; position: relative; }
.icon-btn:hover { background: var(--primary-light, #e0e7ff); color: var(--primary, #6366f1); border-color: var(--primary, #6366f1); }
.notif-dot { position: absolute; top: 7px; right: 7px; width: 8px; height: 8px; background: var(--accent2, #ec4899); border-radius: 50%; border: 2px solid var(--bg-card, #fff); }
#theme-toggle { background: var(--bg-gray, #f1f5f9); border: 1.5px solid var(--border-color, #e5e7eb); }
#theme-toggle:hover { background: var(--primary, #6366f1); color: white; border-color: var(--primary, #6366f1); }
.user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 6px 12px 6px 6px; border-radius: 40px; border: 1.5px solid var(--border-color, #e5e7eb); background: var(--bg-card, #fff); transition: all 0.3s; color: var(--text-main, #111827); }
.user-profile:hover { border-color: var(--primary, #6366f1); box-shadow: 0 0 0 3px var(--primary-light, #e0e7ff); }
.user-info { line-height: 1.2; text-align: left; }
.user-name { font-weight: 700; font-size: 0.88rem; }
.user-role { font-size: 0.72rem; color: var(--text-muted, #6b7280); }
.avatar { width: 32px; height: 32px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary-light, #e0e7ff); }
.logout-btn { font-size: 0.78rem; color: #f87171; font-weight: 600; padding: 4px 10px; border-radius: 6px; transition: all 0.3s; text-decoration: none; }
.logout-btn:hover { background: rgba(248,113,113,0.1); }

/* Dark mode basic overrides for header if missing */
[data-theme="dark"] .navbar { background: rgba(13,13,26,0.85); border-color: #252540; }
[data-theme="dark"] .search-bar input { background: #13131f; border-color: #252540; color: #f1f5f9; }
[data-theme="dark"] .icon-btn, [data-theme="dark"] #theme-toggle { background: #13131f; border-color: #252540; }
[data-theme="dark"] .user-profile { background: #1a1a2e; border-color: #252540; color: #f1f5f9; }
[data-theme="dark"] .user-role, [data-theme="dark"] .search-icon { color: #94a3b8; }

@media (max-width: 768px) {
    .navbar-inner { flex-wrap: wrap; gap: 10px; padding: 10px 16px; }
    .search-bar, .user-name, .user-role { display: none; }
    .nav-links { overflow-x: auto; padding-bottom: 5px; }
}
</style>
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
                <?php if(($user['role'] ?? '') === 'admin'): ?>
                    <a href="<?= $baseUrl ?>/admin" style="font-weight: 700; color: var(--primary, #6366f1); background: var(--primary-light, #e0e7ff);">🛡️ Admin Hub</a>
                <?php endif; ?>
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

<script>
// Unified Dark Mode script
(function() {
    // Apply theme immediately to prevent flashing
    const theme = localStorage.getItem('tabletop-theme') || 'light';
    if(theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        document.body.setAttribute('data-theme', 'dark'); // Some old pages might use body
    }

    // Toggle on load if btn exists
    window.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                const nextTheme = isDark ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', nextTheme);
                document.body.setAttribute('data-theme', nextTheme);
                localStorage.setItem('tabletop-theme', nextTheme);
                
                // Keep compatibility with old theme key
                localStorage.setItem('theme', nextTheme);
                
                // Update icon if needed
                const themeIcon = document.getElementById('theme-icon');
                if (themeIcon) {
                    if (nextTheme === 'dark') {
                        themeIcon.innerHTML = '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>';
                    } else {
                        themeIcon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>';
                    }
                }
            });
            
            // Set initial icon
            const themeIcon = document.getElementById('theme-icon');
            if (themeIcon && theme === 'dark') {
                themeIcon.innerHTML = '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>';
            }
        }
    });
})();
</script>
