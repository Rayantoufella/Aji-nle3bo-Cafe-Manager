<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

$user = $user ?? [
    'username' => $_SESSION['username'] ?? 'Guest User',
    'role' => $_SESSION['user_role'] ?? 'Member'
];

$games = $games ?? [];
$categories = $categories ?? [];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableTop Hub - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ==================== VARIABLES ==================== */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #e0e7ff;
            --accent: #f59e0b;
            --accent2: #ec4899;
            --text-main: #111827;
            --text-muted: #6b7280;
            --bg-body: #f8faff;
            --bg-gray: #f1f5f9;
            --bg-card: #ffffff;
            --bg-nav: rgba(255,255,255,0.85);
            --border-color: #e5e7eb;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.09);
            --shadow-lg: 0 16px 48px rgba(99,102,241,0.18);
            --card-radius: 16px;
            --transition: all 0.3s cubic-bezier(.4,0,.2,1);
            --glow: 0 0 0 0 transparent;
        }

        [data-theme="dark"] {
            --primary: #818cf8;
            --primary-dark: #6366f1;
            --primary-light: rgba(99,102,241,0.18);
            --accent: #fbbf24;
            --accent2: #f472b6;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --bg-body: #0d0d1a;
            --bg-gray: #13131f;
            --bg-card: #1a1a2e;
            --bg-nav: rgba(13,13,26,0.85);
            --border-color: #252540;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.3);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.4);
            --shadow-lg: 0 16px 48px rgba(99,102,241,0.25);
            --glow: 0 0 20px rgba(129,140,248,0.15);
        }

        /* ==================== RESET ==================== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        a { text-decoration: none; color: inherit; }

        .container { max-width: 1220px; margin: 0 auto; padding: 0 24px; }

        /* ==================== NAVBAR ==================== */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--bg-nav);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 0;
        }

        .nav-left { display: flex; align-items: center; gap: 36px; }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--primary);
            letter-spacing: -0.3px;
        }

        .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 100%);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(99,102,241,0.35);
        }

        .nav-links {
            display: flex;
            gap: 4px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .nav-links a {
            padding: 8px 14px;
            border-radius: 8px;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .nav-links a:hover { background: var(--primary-light); color: var(--primary); }

        .nav-links a.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
        }

        .nav-right { display: flex; align-items: center; gap: 12px; }

        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-bar input {
            padding: 9px 14px 9px 38px;
            border: 1.5px solid var(--border-color);
            background: var(--bg-gray);
            color: var(--text-main);
            border-radius: 24px;
            font-size: 0.88rem;
            outline: none;
            width: 230px;
            transition: var(--transition);
            font-family: inherit;
        }

        .search-bar input::placeholder { color: var(--text-muted); }

        .search-bar input:focus {
            border-color: var(--primary);
            background: var(--bg-card);
            box-shadow: 0 0 0 3px var(--primary-light);
            width: 260px;
        }

        .search-icon {
            position: absolute;
            left: 13px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .icon-btn {
            width: 38px; height: 38px;
            background: var(--bg-gray);
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .icon-btn:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
        }

        .notif-dot {
            position: absolute;
            top: 7px; right: 7px;
            width: 8px; height: 8px;
            background: var(--accent2);
            border-radius: 50%;
            border: 2px solid var(--bg-card);
        }

        /* Theme Toggle */
        #theme-toggle {
            background: var(--bg-gray);
            border: 1.5px solid var(--border-color);
        }

        #theme-toggle:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 6px 12px 6px 6px;
            border-radius: 40px;
            border: 1.5px solid var(--border-color);
            background: var(--bg-card);
            transition: var(--transition);
        }

        .user-profile:hover {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .user-info { line-height: 1.2; }
        .user-name { font-weight: 700; font-size: 0.88rem; }
        .user-role { font-size: 0.72rem; color: var(--text-muted); }

        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--primary-light);
        }

        .logout-btn {
            font-size: 0.78rem;
            color: #f87171;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            transition: var(--transition);
        }

        .logout-btn:hover { background: rgba(248,113,113,0.1); }

        /* ==================== HERO ==================== */
        .hero-section {
            position: relative;
            overflow: hidden;
            padding: 90px 0 80px;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(99,102,241,0.08) 0%, rgba(168,85,247,0.06) 50%, rgba(236,72,153,0.05) 100%);
            z-index: 0;
        }

        [data-theme="dark"] .hero-bg {
            background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(168,85,247,0.1) 50%, rgba(236,72,153,0.08) 100%);
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .hero-orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.3), transparent 70%);
            top: -150px; right: -100px;
        }

        .hero-orb-2 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(236,72,153,0.2), transparent 70%);
            bottom: -80px; left: 10%;
        }

        .hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .hero-content { flex: 1; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary-light), rgba(168,85,247,0.15));
            color: var(--primary);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 24px;
            border: 1.5px solid rgba(99,102,241,0.25);
            letter-spacing: 0.3px;
        }

        .badge-dot {
            width: 8px; height: 8px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .hero-title {
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            font-weight: 900;
            line-height: 1.05;
            margin-bottom: 22px;
            letter-spacing: -1.5px;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 60%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-text {
            color: var(--text-muted);
            font-size: 1.05rem;
            margin-bottom: 36px;
            max-width: 480px;
            line-height: 1.85;
        }

        .hero-stats {
            display: flex;
            gap: 32px;
            margin-bottom: 36px;
        }

        .hero-stat { text-align: left; }

        .hero-stat-num {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -1px;
        }

        .hero-stat-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }

        .btn {
            padding: 13px 26px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.15);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .btn:hover::after { opacity: 1; }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 100%);
            color: white;
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(99,102,241,0.45);
        }

        .btn-outline {
            background: var(--bg-card);
            color: var(--primary);
            border: 2px solid var(--border-color);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .hero-image {
            flex: 1;
            max-width: 480px;
            position: relative;
        }

        .hero-image-wrapper {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transform: perspective(1200px) rotateY(-6deg) rotateX(2deg);
            transition: transform 0.5s ease;
        }

        .hero-image-wrapper:hover {
            transform: perspective(1200px) rotateY(-1deg) rotateX(0deg);
        }

        .hero-image-wrapper img {
            width: 100%;
            display: block;
            object-fit: cover;
        }

        .hero-image-glow {
            position: absolute;
            inset: -20px;
            background: radial-gradient(ellipse at center, rgba(99,102,241,0.2), transparent 70%);
            z-index: -1;
            border-radius: 40px;
        }

        /* ==================== GAMES SECTION ==================== */
        .games-section { padding: 60px 0 80px; }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: -0.8px;
        }

        .section-title span {
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .search-input-games {
            flex: 1;
            min-width: 220px;
            max-width: 340px;
            padding: 11px 16px 11px 40px;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            font-size: 0.9rem;
            outline: none;
            background: var(--bg-card);
            color: var(--text-main);
            font-family: inherit;
            transition: var(--transition);
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%236b7280" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>');
            background-repeat: no-repeat;
            background-position: 14px center;
        }

        .search-input-games::placeholder { color: var(--text-muted); }

        .search-input-games:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .filter-btn {
            padding: 11px 18px;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-card);
            color: var(--text-main);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: inherit;
            transition: var(--transition);
        }

        .filter-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .categories {
            display: flex;
            gap: 10px;
            margin-bottom: 32px;
            flex-wrap: wrap;
            align-items: center;
        }

        .category-chip {
            padding: 9px 18px;
            border: 1.5px solid var(--border-color);
            border-radius: 30px;
            background: var(--bg-card);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
            user-select: none;
        }

        .category-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-1px);
        }

        .category-chip.active {
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(99,102,241,0.3);
            transform: translateY(-2px);
        }

        .stats-text {
            margin-left: auto;
            color: var(--text-muted);
            font-size: 0.82rem;
            font-weight: 500;
            background: var(--bg-gray);
            padding: 7px 14px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
        }

        /* ==================== GAME GRID ==================== */
        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .game-card {
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            background: var(--bg-card);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            position: relative;
        }

        .game-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent2), var(--accent));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .game-card:hover {
            box-shadow: var(--shadow-lg), var(--glow);
            transform: translateY(-6px);
            border-color: var(--primary);
        }

        .game-card:hover::before { opacity: 1; }

        .card-body { padding: 16px; display: flex; flex-direction: column; flex: 1; }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            gap: 8px;
        }

        .tag-popular {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 100%);
            color: white;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 4px 12px rgba(99,102,241,0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tag-new {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tag-regular { visibility: hidden; height: 0; }

        .rating {
            background: var(--bg-gray);
            color: var(--text-main);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
            border: 1px solid var(--border-color);
        }

        .rating svg { color: var(--accent); }

        .image-placeholder {
            width: 100%;
            height: 165px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.7);
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
        }

        .image-placeholder img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        .image-placeholder-overlay {
            position: absolute;
            inset: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 200"><circle cx="60" cy="50" r="80" fill="rgba(255,255,255,0.06)"/><circle cx="350" cy="170" r="100" fill="rgba(255,255,255,0.04)"/><circle cx="200" cy="100" r="60" fill="rgba(255,255,255,0.03)"/></svg>');
        }

        .game-category {
            display: inline-block;
            color: var(--primary);
            font-size: 0.64rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
            background: var(--primary-light);
            padding: 4px 10px;
            border-radius: 8px;
        }

        .game-title {
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 14px;
            color: var(--text-main);
            line-height: 1.3;
            letter-spacing: -0.2px;
        }

        .game-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            background: var(--bg-gray);
            border-radius: 8px;
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 500;
            border: 1px solid var(--border-color);
        }

        .info-item svg { color: var(--primary); flex-shrink: 0; }

        .difficulty {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 18px;
            padding: 8px 12px;
            background: linear-gradient(135deg, rgba(245,158,11,0.08), rgba(245,158,11,0.04));
            border-radius: 8px;
            border: 1px solid rgba(245,158,11,0.15);
            font-weight: 500;
        }

        .difficulty svg { color: var(--accent); }

        .btn-view {
            width: 100%;
            padding: 12px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            margin-top: auto;
            box-shadow: 0 4px 14px rgba(99,102,241,0.3);
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-view::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-view:hover::before { left: 100%; }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,0.45);
        }

        /* ==================== EMPTY STATE ==================== */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            color: var(--text-muted);
        }

        .empty-state svg { margin-bottom: 16px; opacity: 0.4; }
        .empty-state h3 { font-size: 1.2rem; margin-bottom: 8px; color: var(--text-main); }
        .empty-state p { font-size: 0.9rem; }

        /* ==================== LOAD MORE ==================== */
        .load-more-container { text-align: center; }

        .btn-load-more {
            padding: 13px 32px;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            color: var(--primary);
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }

        .btn-load-more:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .info-text-center {
            display: block;
            margin-top: 12px;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        /* ==================== FEATURES SECTION ==================== */
        .features-section {
            background: var(--bg-gray);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 70px 0;
        }

        .features-header { text-align: center; margin-bottom: 48px; }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 20px;
            padding: 32px 28px;
            text-align: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent2));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, var(--primary-light), rgba(236,72,153,0.1));
            color: var(--primary);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 1.5px solid var(--border-color);
        }

        .feature-title {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .feature-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            max-width: 260px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* ==================== FOOTER ==================== */
        footer {
            background: var(--bg-card);
            border-top: 1px solid var(--border-color);
            padding: 52px 0 24px;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-text { color: var(--text-muted); font-size: 0.88rem; line-height: 1.75; max-width: 280px; margin-top: 12px; }

        .footer-col-title { font-weight: 800; margin-bottom: 16px; font-size: 0.9rem; letter-spacing: 0.2px; }

        .footer-links { list-style: none; }

        .footer-links li { margin-bottom: 10px; }

        .footer-links a { color: var(--text-muted); font-size: 0.88rem; transition: color 0.2s; }

        .footer-links a:hover { color: var(--primary); }

        .social-links { display: flex; gap: 10px; margin-top: 4px; }

        .social-link {
            width: 36px; height: 36px;
            background: var(--bg-gray);
            border: 1.5px solid var(--border-color);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .social-link:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-legal-links { display: flex; gap: 20px; }
        .footer-legal-links a { transition: color 0.2s; }
        .footer-legal-links a:hover { color: var(--primary); }

        /* ==================== ICONS ==================== */
        .icon-sm { width: 14px; height: 14px; }
        .icon-md { width: 18px; height: 18px; }
        .icon-lg { width: 24px; height: 24px; }
        .icon-xl { width: 32px; height: 32px; }

        /* ==================== ANIMATIONS ==================== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .game-card { animation: fadeInUp 0.4s ease both; }

        <?php foreach(range(0, 11) as $i): ?>
        .game-card:nth-child(<?= $i + 1 ?>) { animation-delay: <?= $i * 0.05 ?>s; }
        <?php endforeach; ?>

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 900px) {
            .hero-inner { flex-direction: column; }
            .hero-image { max-width: 100%; }
            .hero-image-wrapper { transform: none !important; }
            .features-grid { grid-template-columns: 1fr; }
            .footer-top { grid-template-columns: 1fr 1fr; }
            .nav-links { display: none; }
        }

        @media (max-width: 600px) {
            .hero-title { font-size: 2rem; }
            .footer-top { grid-template-columns: 1fr; }
            .hero-stats { gap: 20px; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ==================== NAVBAR ==================== -->
    <?php require dirname(__DIR__) . '/layout/header.php'; ?>

    <!-- ==================== HERO ==================== -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="container hero-inner">
            <div class="hero-content">
                <div class="badge">
                    <span class="badge-dot"></span>
                    Discover the best games in town
                </div>
                <h1 class="hero-title">
                    Level Up Your<br>
                    <span class="gradient-text">Game Night</span><br>
                    at TableTop Hub
                </h1>
                <p class="hero-text">Explore our curated collection of <?= count($games) ?> board games. From epic strategy sagas to quick party favorites — we have the perfect table waiting for you.</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-num"><?= count($games) ?>+</div>
                        <div class="hero-stat-label">Games</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num"><?= count($categories) ?>+</div>
                        <div class="hero-stat-label">Categories</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">4.9</div>
                        <div class="hero-stat-label">Rating</div>
                    </div>
                </div>
                <div class="hero-buttons">
                    <a href="#games" class="btn btn-primary">
                        <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"></circle><circle cx="15.5" cy="15.5" r="1.5" fill="currentColor"></circle></svg>
                        Browse Catalogue
                    </a>
                    <button class="btn btn-outline">
                        <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Book a Table
                    </button>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-image-glow"></div>
                <div class="hero-image-wrapper">
                    <img src="<?= $baseUrl ?>/app/views/img/imageUserDash.jpg" alt="Board Games">
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== GAMES SECTION ==================== -->
    <section class="games-section container" id="games">
        <div class="section-header">
            <div>
                <div class="section-label">Collection</div>
                <h2 class="section-title">Our Game <span>Vault</span></h2>
            </div>
        </div>

        <div class="filter-row">
            <input type="text" class="search-input-games" id="gameSearch" placeholder="Search by name, theme, or category...">
            <button class="filter-btn">
                <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filters
            </button>
        </div>

        <div class="categories">
            <div class="category-chip active" data-category="all">
                <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5" fill="currentColor" stroke="none"></circle><circle cx="15.5" cy="15.5" r="1.5" fill="currentColor" stroke="none"></circle></svg>
                All Games
            </div>
            <?php foreach($categories as $category): ?>
            <div class="category-chip" data-category="<?= htmlspecialchars($category['name']) ?>">
                <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>
                <?= htmlspecialchars($category['name']) ?>
            </div>
            <?php endforeach; ?>
            <div class="stats-text" id="gamesCount">Showing <?= count($games) ?> games</div>
        </div>

        <div class="game-grid" id="gameGrid">
            <?php if(empty($games)): ?>
                <div class="empty-state">
                    <svg class="icon-xl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <h3>No games available</h3>
                    <p>Check back soon — we're updating the vault.</p>
                </div>
            <?php else: ?>
                <?php foreach($games as $index => $game): ?>
                <div class="game-card" data-name="<?= htmlspecialchars(strtolower($game['name'] ?? '')) ?>" data-category="<?= htmlspecialchars($game['category_name'] ?? '') ?>">
                    <div class="image-placeholder">
                        <?php
                        // Nomme ton image comme le jeu en minuscules
                        // Exemple: jeu "Catan" => mettre le fichier: app/views/img/game/catan.jpg
                        $imgName = strtolower(trim($game['name'] ?? ''));
                        $base    = "{$_SERVER['DOCUMENT_ROOT']}/Aji-nle3bo-Cafe-Manager/app/views/img/game/{$imgName}";
                        if (file_exists("$base.jpg")){
                            $imgUrl = "{$baseUrl}/app/views/img/game/{$imgName}.jpg"; 
                        }elseif (file_exists("$base.jpeg")){
                             $imgUrl = "{$baseUrl}/app/views/img/game/{$imgName}.jpeg"; 
                        }elseif(file_exists("$base.png")){
                            $imgUrl = "{$baseUrl}/app/views/img/game/{$imgName}.png";
                        }else{
                            $imgUrl = null;
                        }

                        if($imgUrl): ?>
                            <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($game['name'] ?? '') ?>">
                        <?php else: ?>
                            <div class="image-placeholder-overlay"></div>
                            <svg class="icon-xl" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <?php endif; ?>

                    </div>
                    <div class="card-body">
                        <div class="card-header">
                            <?php if($index < 3): ?>
                            <span class="tag-popular">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                Popular
                            </span>
                            <?php elseif($index >= 3 && $index < 6): ?>
                            <span class="tag-new">New</span>
                            <?php else: ?>
                            <span class="tag-regular"></span>
                            <?php endif; ?>
                            <span class="rating">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                4.<?= rand(2,9) ?>
                            </span>
                        </div>
                        <div class="game-category"><?= htmlspecialchars($game['category_name'] ?? 'GAME') ?></div>
                        <h3 class="game-title"><?= htmlspecialchars($game['name'] ?? 'Unknown Game') ?></h3>
                        <div class="game-info">
                            <div class="info-item">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                                <?= htmlspecialchars((string)($game['nb_players'] ?? 0)) ?> Players
                            </div>
                            <div class="info-item">
                                <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?= htmlspecialchars((string)($game['duration'] ?? 0)) ?> Min
                            </div>
                        </div>
                        <div class="difficulty">
                            <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            Difficulty: <strong><?= htmlspecialchars($game['difficulty'] ?? 'Medium') ?></strong>
                        </div>
                        <button class="btn-view" onclick="window.location.href='<?= $baseUrl ?>/games/<?= (int)($game['id'] ?? 0) ?>'">
                            View Details
                            <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if(!empty($games)): ?>
        <div class="load-more-container">
            <button class="btn-load-more">Load More Games</button>
            <span class="info-text-center">Showing 1–<?= count($games) ?> of <?= count($games) ?> games</span>
        </div>
        <?php endif; ?>
    </section>

    <!-- ==================== FEATURES SECTION ==================== -->
    <section class="features-section">
        <div class="container">
            <div class="features-header">
                <div class="section-label">Why TableTop Hub</div>
                <h2 class="section-title">Everything You <span>Need</span></h2>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg class="icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="6" width="20" height="12" rx="2" ry="2"></rect>
                            <circle cx="12" cy="12" r="2"></circle>
                            <path d="M6 12h.01M18 12h.01"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">500+ Board Games</h3>
                    <p class="feature-desc">From classics like Chess to the latest Kickstarter hits. Our library is updated weekly with new titles.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, rgba(236,72,153,0.12), rgba(236,72,153,0.05)); color: var(--accent2); border-color: rgba(236,72,153,0.2);">
                        <svg class="icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Expert Game Gurus</h3>
                    <p class="feature-desc">Our staff are masters of rulebooks. We'll help you set up and learn any game in minutes.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon" style="background: linear-gradient(135deg, rgba(245,158,11,0.12), rgba(245,158,11,0.05)); color: var(--accent); border-color: rgba(245,158,11,0.2);">
                        <svg class="icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Premium Service</h3>
                    <p class="feature-desc">Spacious tables, comfortable chairs, and a selection of artisanal snacks and craft drinks.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FOOTER ==================== -->
    <footer>
        <div class="container">
            <div class="footer-top">
                <div>
                    <div class="brand">
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
                    </div>
                    <p class="footer-text">The ultimate destination for tabletop enthusiasts. Book tables, track sessions, and discover your next favorite board game.</p>
                </div>
                <div>
                    <h4 class="footer-col-title">Platform</h4>
                    <ul class="footer-links">
                        <li><a href="#">Browse Games</a></li>
                        <li><a href="#">My Reservations</a></li>
                        <li><a href="#">Gaming History</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-col-title">Support</h4>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Rules & Conduct</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-col-title">Connect</h4>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                        <a href="#" class="social-link" title="Twitter"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>
                        <a href="#" class="social-link" title="Instagram"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                        <a href="#" class="social-link" title="GitHub"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2026 TableTop Hub. All rights reserved.</span>
                <div class="footer-legal-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Default dark mode handled by header.php now

        // ==================== CATEGORY FILTER ====================
        const chips = document.querySelectorAll('.category-chip');
        const cards = document.querySelectorAll('.game-card');
        const countEl = document.getElementById('gamesCount');
        const searchInput = document.getElementById('gameSearch');
        let activeCategory = 'all';

        function filterGames() {
            const query = searchInput.value.toLowerCase().trim();
            let visible = 0;
            cards.forEach(card => {
                const name = card.dataset.name || '';
                const cat = card.dataset.category || '';
                const matchCat = activeCategory === 'all' || cat.toLowerCase() === activeCategory.toLowerCase();
                const matchSearch = !query || name.includes(query) || cat.toLowerCase().includes(query);
                const show = matchCat && matchSearch;
                card.style.display = show ? '' : 'none';
                if(show) visible++;
            });
            if(countEl) countEl.textContent = 'Showing ' + visible + ' games';
        }

        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                chips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                activeCategory = chip.dataset.category || 'all';
                filterGames();
            });
        });

        searchInput && searchInput.addEventListener('input', filterGames);
    </script>
</body>
</html>
