<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
        margin: 0;
        padding: 0;
        background-color: #f4f6f8;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 32px;
        background-color: #ffffff;
        border-bottom: 1px solid #eef0f4;
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
        width: calc(100% - 260px);
        margin-left: 260px;
        position: fixed;
        top: 0;
        right: 0;
        z-index: 50;
    }

    .header-search {
        display: flex;
        align-items: center;
        background-color: #f9fafb;
        border: 1px solid #f3f4f6;
        border-radius: 12px;
        padding: 10px 16px;
        width: 380px;
        transition: all 0.2s ease;
    }
    
    .header-search:focus-within {
        border-color: #6366f1;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .header-search i {
        color: #9ca3af;
        font-size: 18px;
        margin-right: 12px;
    }

    .header-search input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 14px;
        color: #374151;
        width: 100%;
        font-family: 'Inter', sans-serif;
    }

    .header-search input::placeholder {
        color: #9ca3af;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .header-notification {
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #4b5563;
        transition: background-color 0.2s, transform 0.2s;
    }

    .header-notification:hover {
        background-color: #f3f4f6;
        transform: scale(1.05);
    }

    .header-notification i {
        font-size: 22px;
    }

    .header-notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        border: 2px solid #ffffff;
    }

    .header-divider {
        width: 1px;
        height: 24px;
        background-color: #e5e7eb;
    }

    .header-logout-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background-color: #fff1f2;
        color: #e11d48;
        border: 1px solid #ffe4e6;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Inter', sans-serif;
    }

    .header-logout-btn:hover {
        background-color: #ffe4e6;
        border-color: #fecdd3;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(225, 29, 72, 0.1);
    }

    .header-logout-btn:active {
        transform: translateY(0);
    }

    .header-logout-btn i {
        font-size: 18px;
    }
    
    .sidebar-container {
        width: 260px;
        background-color: #ffffff;
        height: 100vh;
        display: flex;
        flex-direction: column;
        padding: 24px 20px;
        box-sizing: border-box;
        border-right: 1px solid #eef0f4;
        font-family: 'Inter', sans-serif;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 100;
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 40px;
        padding: 0 12px;
    }

    .sidebar-logo-icon {
        width: 32px;
        height: 32px;
        background-color: #e0e7ff; 
        color: #4f46e5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);
    }

    .sidebar-logo-text {
        font-weight: 700;
        font-size: 18px;
        color: #4f46e5;
        letter-spacing: -0.02em;
    }

    .sidebar-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex-grow: 1;
    }

    .sidebar-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 8px;
        color: #6b7280;
        text-decoration: none;
        font-weight: 500;
        font-size: 15px;
        transition: all 0.2s ease;
    }

    .sidebar-nav-item i {
        font-size: 20px;
    }

    .sidebar-nav-item:hover {
        background-color: #f3f4f6;
        color: #111827;
        transform: translateX(2px);
    }

    .sidebar-nav-item.active {
        background-color: #6366f1;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }
    
    .sidebar-nav-item.active i {
        color: #ffffff;
    }

    .sidebar-user-section {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        margin-top: 20px;
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .sidebar-user-section:hover {
        background-color: #f3f4f6;
        border-color: #d1d5db;
    }

    .sidebar-user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #4f46e5;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
    }

    .sidebar-user-details {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar-user-name {
        font-weight: 600;
        font-size: 14px;
        color: #111827;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .sidebar-user-role {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }

    .main-content {
        margin-left: 260px;
        padding-top: 82px; /* Header height padding */
        padding-left: 24px;
        padding-right: 24px;
        min-height: calc(100vh - 82px);
    }
</style>

<script src="https://unpkg.com/@phosphor-icons/web"></script>

<aside class="sidebar-container">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">AL</div>
        <div class="sidebar-logo-text">Ajil L3bo Café</div>
    </div>

    <nav class="sidebar-nav">
        <a href="#" class="sidebar-nav-item active">
            <i class="ph ph-squares-four"></i>
            <span>Dashboard</span>
        </a>
        <a href="#" class="sidebar-nav-item">
            <i class="ph ph-game-controller"></i>
            <span>Games</span>
        </a>
        <a href="#" class="sidebar-nav-item">
            <i class="ph ph-calendar-blank"></i>
            <span>Reservations</span>
        </a>
        <a href="#" class="sidebar-nav-item">
            <i class="ph ph-timer"></i>
            <span>Sessions</span>
        </a>
        <a href="#" class="sidebar-nav-item">
            <i class="ph ph-chart-bar"></i>
            <span>Stats</span>
        </a>
    </nav>

    <div class="sidebar-user-section">
        <div class="sidebar-user-avatar">
            <i class="ph ph-user"></i>
        </div>
        <div class="sidebar-user-details">
            <span class="sidebar-user-name">Zaid Al-Bakri</span>
            <span class="sidebar-user-role">Administrator</span>
        </div>
    </div>
</aside>

<header class="header-container">
    <div class="header-search">
        <i class="ph ph-magnifying-glass"></i>
        <input type="text" placeholder="Search games, tables, or players...">
    </div>

    <div class="header-actions">
        <div class="header-notification">
            <i class="ph ph-bell"></i>
            <div class="header-notification-badge"></div>
        </div>
        
        <div class="header-divider"></div>

        <a href="logout.php" class="header-logout-btn">
            <span>Logout</span>
            <i class="ph ph-sign-out"></i>
        </a>
    </div>
</header>
