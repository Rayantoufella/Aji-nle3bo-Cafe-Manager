<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

// Protection in case the game is not found
if (!isset($game) || !$game) {
    $game = [
        'id' => 0,
        'name' => 'Demo Game (Not Found)',
        'category_name' => 'Unknown',
        'description' => 'This is a fallback description because the game could not be loaded from the database.',
        'nb_players' => '?',
        'duration' => '?',
        'difficulty' => '?'
    ];
}

// Map game names to their image files
$imageMap = [
    'catan'          => 'catan.jpg',
    'dixit'          => 'Dixit.jpg',
    'pandemic'       => 'pandemic.jpg',
    'ticket to ride' => 'Ticket to Ride.jpg',
    'chess'          => 'chess.jpg',
    'codenames'      => 'Codenames.png',
    '7 wonders'      => '7 Wonders.png',
    'splendor'       => 'Splendor.jpg',
];
$gameNameKey = strtolower($game['name'] ?? '');
$gameImage = isset($imageMap[$gameNameKey])
    ? $baseUrl . '/app/views/img/game/' . $imageMap[$gameNameKey]
    : $baseUrl . '/app/views/img/imageUserDash.jpg';

// Normalize gameStats: model returns average_rating/total_reviews, view expec ts rating/reviews
if (!isset($gameStats) || !$gameStats) {
    $gameStats = ['rating' => 4.8, 'reviews' => 128, 'complexity' => 'Medium', 'age' => '12+'];
} elseif (!isset($gameStats['rating'])) {
    $gameStats['rating']     = $gameStats['average_rating'] ? round((float)$gameStats['average_rating'], 1) : 4.8;
    $gameStats['reviews']    = (int)($gameStats['total_reviews'] ?? 0);
    $gameStats['complexity'] = ucfirst($game['difficulty'] ?? 'medium');
    $gameStats['age']        = '12+';
}

// Normalize reviews: model returns user_name/created_at, view expects user/date
if (!isset($gameReviews) || empty($gameReviews)) {
    $gameReviews = [
        ['user' => 'Marcus V.',  'date' => 'OCT 12, 2023', 'rating' => 5, 'comment' => 'Absolutely the best strategy game we\'ve played in months. The engine building is rewarding and the theme is incredible.'],
        ['user' => 'Sarah Chen', 'date' => 'SEP 29, 2023', 'rating' => 4, 'comment' => 'Complex but worth the learning curve. Great for groups that like crunching numbers and planning long-term.'],
    ];
} else {
    $gameReviews = array_map(function($r) {
        return [
            'user'    => $r['user_name'] ?? $r['user'] ?? 'Anonymous',
            'date'    => isset($r['created_at']) ? strtoupper(date('M d, Y', strtotime($r['created_at']))) : ($r['date'] ?? ''),
            'rating'  => (int)($r['rating'] ?? 0),
            'comment' => $r['comment'] ?? '',
        ];
    }, $gameReviews);
}

// Related games fallback
if (!isset($relatedGames) || empty($relatedGames)) {
    $relatedGames = [
        ['id' => 1, 'name' => 'Catan',    'category_name' => 'Stratégie', 'nb_players' => '4'],
        ['id' => 6, 'name' => 'Codenames','category_name' => 'Ambiance',  'nb_players' => '8'],
    ];
}

$title      = htmlspecialchars($game['name']          ?? 'Game Details');
$category   = htmlspecialchars($game['category_name'] ?? 'Strategy');
$description = htmlspecialchars($game['description']  ?? '');
$players    = htmlspecialchars($game['nb_players']    ?? '1-5');
$duration   = htmlspecialchars($game['duration']      ?? '90-120');
$difficulty = htmlspecialchars($game['difficulty']    ?? 'Advanced');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - TableTop Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-light: #e0e7ff;
            --primary-muted: #e0e7ff;
            --accent2: #ec4899;
            --text-main: #111827;
            --text-muted: #6b7280;
            --text-light: #9ca3af;
            --bg-body: #f8faff;
            --bg-gray: #f1f5f9;
            --bg-card: #ffffff;
            --bg-nav: rgba(255,255,255,0.85);
            --border-color: #e5e7eb;
            --border-light: #f3f4f6;
            --card-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.09);
            --shadow-elevated: 0 20px 40px -5px rgba(0, 0, 0, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); color: var(--text-main); line-height: 1.6; }
        a { text-decoration: none; color: inherit; transition: var(--transition); }
        ul { list-style: none; }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        
        /* ICONS UTILS */
        .icon { display: inline-flex; align-items: center; justify-content: center; }
        .icon-sm { width: 16px; height: 16px; }
        .icon-md { width: 20px; height: 20px; }
        .icon-lg { width: 24px; height: 24px; }

        /* --- NAVBAR --- */
        .navbar { position: sticky; top: 0; z-index: 100; background: var(--bg-nav); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid var(--border-color); transition: var(--transition); }
        .navbar-inner { display: flex; align-items: center; justify-content: space-between; padding: 16px 0; }
        .nav-left { display: flex; align-items: center; gap: 36px; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.15rem; color: var(--primary); letter-spacing: -0.3px; }
        .brand-icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(99,102,241,0.35); }
        .nav-links { display: flex; gap: 4px; font-size: 0.9rem; font-weight: 500; }
        .nav-links a { padding: 8px 14px; border-radius: 8px; color: var(--text-muted); transition: var(--transition); }
        .nav-links a:hover { background: var(--primary-light); color: var(--primary); }
        .nav-links a.active { background: var(--primary-light); color: var(--primary); font-weight: 700; }
        .nav-right { display: flex; align-items: center; gap: 12px; }
        .search-bar { position: relative; display: flex; align-items: center; }
        .search-bar input { padding: 9px 14px 9px 38px; border: 1.5px solid var(--border-color); background: var(--bg-gray); color: var(--text-main); border-radius: 24px; font-size: 0.88rem; outline: none; width: 230px; transition: var(--transition); font-family: inherit; }
        .search-bar input::placeholder { color: var(--text-muted); }
        .search-bar input:focus { border-color: var(--primary); background: var(--bg-card); box-shadow: 0 0 0 3px var(--primary-light); width: 260px; }
        .search-icon { position: absolute; left: 13px; color: var(--text-muted); pointer-events: none; }
        .icon-btn { width: 38px; height: 38px; background: var(--bg-gray); border: 1.5px solid var(--border-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); cursor: pointer; transition: var(--transition); position: relative; }
        .icon-btn:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }
        #theme-toggle { background: var(--bg-gray); border: 1.5px solid var(--border-color); }
        #theme-toggle:hover { background: var(--primary); color: white; border-color: var(--primary); }
        .notif-dot { position: absolute; top: 7px; right: 7px; width: 8px; height: 8px; background: var(--accent2); border-radius: 50%; border: 2px solid var(--bg-card); }
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 6px 12px 6px 6px; border-radius: 40px; border: 1.5px solid var(--border-color); background: var(--bg-card); transition: var(--transition); }
        .user-profile:hover { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }
        .user-info { line-height: 1.2; }
        .user-name { font-weight: 700; font-size: 0.88rem; }
        .user-role { font-size: 0.72rem; color: var(--text-muted); }
        .avatar { width: 32px; height: 32px; border-radius: 50%; overflow: hidden; border: 2px solid var(--primary-light); }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .logout-btn { font-size: 0.78rem; color: #f87171; font-weight: 600; padding: 4px 10px; border-radius: 6px; transition: var(--transition); }
        .logout-btn:hover { background: rgba(248,113,113,0.1); }

        /* --- BREADCRUMB --- */
        .breadcrumb { padding: 30px 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 8px; }
        .breadcrumb a { color: var(--text-light); display: inline-flex; align-items: center; gap: 6px; }
        .breadcrumb a:hover { color: var(--text-main); }
        .breadcrumb span { color: var(--text-main); font-weight: 600; }

        /* --- MAIN LAYOUT --- */
        .main-layout { display: grid; grid-template-columns: 1fr 400px; gap: 48px; padding-bottom: 80px; align-items: start; }
        
        @media (max-width: 1024px) {
            .main-layout { grid-template-columns: 1fr; }
        }

        /* === LEFT COLUMN (GAME DETAILS) === */
        
        /* Gallery */
        .gallery-main { width: 100%; height: 420px; border-radius: var(--card-radius); background-color: var(--bg-gray); position: relative; overflow: hidden; margin-bottom: 16px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); }
        .gallery-main img { width: 100%; height: 100%; object-fit: cover; }
        .gallery-badge { position: absolute; top: 20px; left: 20px; background: rgba(99, 102, 241, 0.9); backdrop-filter: blur(4px); color: white; padding: 6px 16px; border-radius: 100px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: var(--shadow-sm); }
        .thumbnail-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 40px; }
        .thumbnail { height: 100px; border-radius: 12px; background-color: var(--bg-gray); overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: var(--transition); opacity: 0.7; }
        .thumbnail:hover { opacity: 1; transform: translateY(-2px); }
        .thumbnail.active { border-color: var(--primary); opacity: 1; box-shadow: 0 4px 12px rgba(99,102,241,0.2); }
        .thumbnail img { width: 100%; height: 100%; object-fit: cover; }

        /* Tabs */
        .tabs { display: flex; border-bottom: 1px solid var(--border-color); margin-bottom: 32px; }
        .tab { padding: 12px 24px; font-weight: 600; font-size: 0.95rem; color: var(--text-muted); cursor: pointer; position: relative; transition: var(--transition); }
        .tab:hover { color: var(--text-main); }
        .tab.active { color: var(--primary); }
        .tab.active::after { content: ''; position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background-color: var(--primary); border-radius: 3px 3px 0 0; }

        /* Content Sections */
        .content-section { margin-bottom: 40px; }
        .section-title { display: flex; align-items: center; gap: 10px; font-size: 1.4rem; font-weight: 700; color: var(--text-main); margin-bottom: 16px; }
        .section-title .icon { color: var(--primary); }
        .description { font-size: 1.05rem; color: var(--text-muted); line-height: 1.8; margin-bottom: 24px; }
        
        .expert-take { background: linear-gradient(145deg, #f8fafc, #eff6ff); border-left: 4px solid var(--primary); padding: 24px 32px; border-radius: 0 12px 12px 0; display: flex; flex-direction: column; gap: 12px; }
        .expert-take-title { display: flex; align-items: center; gap: 8px; color: var(--primary); font-weight: 600; font-size: 0.95rem; }
        .expert-take p { font-style: italic; color: #334155; font-size: 1.05rem; line-height: 1.6; }

        /* Tags */
        .tags-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
        .tag { padding: 8px 16px; background-color: var(--bg-gray); border: 1px solid var(--border-color); border-radius: 100px; font-size: 0.85rem; font-weight: 500; color: var(--text-main); transition: var(--transition); cursor: default; }
        .tag:hover { border-color: var(--text-light); background-color: white; }

        /* === RIGHT COLUMN (STICKY SIDEBAR) === */
        
        .sidebar { position: sticky; top: 40px; display: flex; flex-direction: column; gap: 24px; }
        
        /* Main Info Card */
        .info-card { background: white; border: 1px solid var(--border-color); border-radius: var(--card-radius); padding: 32px; box-shadow: var(--shadow-md); transition: var(--transition); }
        .info-card:hover { box-shadow: var(--shadow-elevated); border-color: #d1d5db; }
        
        .card-header-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .game-main-title { font-size: 2rem; font-weight: 800; color: var(--text-main); line-height: 1.1; letter-spacing: -0.5px; }
        .rating-box { display: flex; flex-direction: column; align-items: flex-end; }
        .rating-score { display: flex; align-items: center; gap: 4px; font-size: 1.1rem; font-weight: 700; color: var(--text-main); }
        .rating-score svg { color: #f59e0b; fill: #f59e0b; }
        .reviews-count { font-size: 0.7rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
        .game-subtitle { font-size: 1rem; color: var(--text-muted); margin-bottom: 32px; }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 32px; }
        .stat-box { background-color: var(--bg-gray); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; text-align: center; transition: var(--transition); }
        .stat-box:hover { background-color: white; border-color: var(--primary-muted); transform: translateY(-2px); box-shadow: var(--shadow-sm); }
        .stat-icon { color: var(--primary); margin-bottom: 8px; }
        .stat-label { font-size: 0.75rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .stat-value { font-size: 0.95rem; font-weight: 700; color: var(--text-main); }

        /* Booking Section */
        .booking-section { border-top: 1px solid var(--border-color); padding-top: 24px; }
        .booking-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .booking-label { font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; }
        .booking-badge { background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 100px; display: flex; align-items: center; gap: 4px; }
        .booking-price { font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 24px; display: flex; align-items: baseline; gap: 6px; }
        .booking-price span { font-size: 0.9rem; font-weight: 500; color: var(--text-muted); }
        
        .btn-reserve { width: 100%; background: linear-gradient(135deg, var(--primary), #818cf8); color: white; border: none; padding: 16px; border-radius: 12px; font-size: 1.1rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.3); transition: var(--transition); display: flex; justify-content: center; align-items: center; gap: 8px; }
        .btn-reserve:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }
        .btn-reserve:active { transform: translateY(0); }
        
        .booking-guarantee { text-align: center; font-size: 0.7rem; color: var(--text-light); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 16px; }

        /* Feedback Section */
        .feedback-card { background: white; border: 1px solid var(--border-color); border-radius: var(--card-radius); padding: 24px; box-shadow: var(--shadow-sm); }
        .feedback-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .feedback-title { font-size: 1.1rem; font-weight: 700; color: var(--text-main); }
        .feedback-view-all { font-size: 0.85rem; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 4px; transition: var(--transition); }
        .feedback-view-all:hover { color: var(--primary-hover); gap: 6px; }
        
        .review-item { border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 16px; }
        .review-item:last-of-type { border-bottom: none; margin-bottom: 0; padding-bottom: 20px; }
        .reviewer-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .reviewer-name { font-weight: 700; font-size: 0.9rem; color: var(--text-main); }
        .review-date { font-size: 0.75rem; color: var(--text-light); font-weight: 500; }
        .review-stars { display: flex; gap: 2px; margin-bottom: 8px; }
        .review-stars svg { width: 12px; height: 12px; color: #f59e0b; fill: #f59e0b;}
        .review-stars svg.empty { color: #e5e7eb; fill: #e5e7eb; }
        .review-text { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; }
        
        .share-experience { display: block; text-align: center; width: 100%; font-size: 0.9rem; font-weight: 600; color: var(--text-main); padding-top: 16px; border-top: 1px solid var(--border-color); cursor: pointer; transition: color 0.2s; }
        .share-experience:hover { color: var(--primary); }

        /* Related Games */
        .related-card { background: var(--bg-gray); border: 1px solid var(--border-color); border-radius: var(--card-radius); padding: 24px; }
        .related-title { font-size: 0.85rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; }
        .related-list { display: flex; flex-direction: column; gap: 16px; }
        .related-item { display: flex; align-items: center; gap: 16px; cursor: pointer; padding: 8px; border-radius: 12px; transition: var(--transition); margin: -8px; }
        .related-item:hover { background-color: white; box-shadow: var(--shadow-sm); }
        .related-img { width: 48px; height: 48px; border-radius: 8px; background-color: #ddd; overflow: hidden; flex-shrink: 0; }
        .related-img img { width: 100%; height: 100%; object-fit: cover; }
        .related-info h4 { font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px; }
        .related-info p { font-size: 0.7rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; letter-spacing: 0.5px; }

        /* --- FOOTER --- */
        footer { background-color: var(--bg-card); padding: 40px 0 20px 0; border-top: 1px solid var(--border-color); margin-top: 60px; }
        .footer-top { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .footer-logo { margin-bottom: 15px; font-size: 1.1rem;}
        .footer-text { color: var(--text-muted); font-size: 0.85rem; max-width: 280px; line-height: 1.6; }
        .footer-col-title { font-weight: 700; margin-bottom: 16px; color: var(--text-main); font-size: 0.95rem; }
        .footer-links li { margin-bottom: 12px; }
        .footer-links a { color: var(--text-muted); font-size: 0.85rem; transition: color 0.2s; font-weight: 500; }
        .footer-links a:hover { color: var(--primary); }
        .social-links { display: flex; gap: 16px; }
        .social-link { color: var(--text-muted); transition: color 0.2s; }
        .social-link:hover { color: var(--primary); }
        .footer-bottom { padding-top: 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--text-muted); }
        .footer-legal-links { display: flex; gap: 24px; }
        .footer-legal-links a { font-weight: 500;}

        /* === DARK MODE === */
        [data-theme="dark"] {
            --primary-light: rgba(99,102,241,0.18);
            --primary-muted: rgba(99,102,241,0.18);
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --text-light: #64748b;
            --bg-body: #0f172a;
            --bg-gray: #1e293b;
            --bg-card: #1e293b;
            --bg-nav: rgba(15,23,42,0.9);
            --border-color: #334155;
            --border-light: #1e293b;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.3);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.4);
            --shadow-elevated: 0 20px 40px -5px rgba(0,0,0,0.5);
        }
        [data-theme="dark"] .expert-take { background: linear-gradient(145deg, #1e293b, #172033); }
        [data-theme="dark"] .expert-take p { color: #cbd5e1; }
        [data-theme="dark"] .info-card { background: var(--bg-card); }
        [data-theme="dark"] .feedback-card { background: var(--bg-card); }
        [data-theme="dark"] .tag:hover { background-color: var(--bg-gray); }
        [data-theme="dark"] .related-item:hover { background-color: #0f172a; }
        [data-theme="dark"] .booking-badge { background-color: rgba(251,191,36,0.1); color: #fbbf24; border-color: rgba(251,191,36,0.3); }
        [data-theme="dark"] #theme-toggle { background: var(--bg-gray); border-color: var(--border-color); }
        [data-theme="dark"] #theme-toggle:hover { background: var(--primary); color: white; border-color: var(--primary); }

    </style>
</head>
<body>

    <!-- NAVBAR -->
    <?php require dirname(__DIR__) . '/layout/header.php'; ?>

    <!-- BREADCRUMB -->
    <div class="container breadcrumb">
        <a href="<?= $baseUrl ?>/dashboard">
            <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            Back to Catalogue
        </a>
        /
        <a href="<?= $baseUrl ?>/dashboard?category=<?= urlencode($category) ?>"><?= $category ?> Games</a>
        /
        <span><?= $title ?></span>
    </div>

    <!-- MAIN LAYOUT -->
    <main class="container main-layout">
        
        <!-- === LEFT COLUMN === -->
        <div class="left-column">
            
            <!-- Gallery -->
            <div class="gallery-main" id="gallery-main">
                <div class="gallery-badge"><?= $category ?></div>
                <img id="main-img" src="<?= $gameImage ?>" alt="<?= $title ?>">
            </div>
            <div class="thumbnail-strip">
                <div class="thumbnail active" onclick="switchImg(this, '<?= $gameImage ?>')"><img src="<?= $gameImage ?>" alt="<?= $title ?>"></div>
                <div class="thumbnail" onclick="switchImg(this, '<?= $gameImage ?>')"><img src="<?= $gameImage ?>" alt="<?= $title ?>" style="filter:brightness(0.7) saturate(1.3)"></div>
                <div class="thumbnail" onclick="switchImg(this, '<?= $gameImage ?>')"><img src="<?= $gameImage ?>" alt="<?= $title ?>" style="filter:brightness(1.15) contrast(1.1)"></div>
                <div class="thumbnail" onclick="switchImg(this, '<?= $gameImage ?>')"><img src="<?= $gameImage ?>" alt="<?= $title ?>" style="filter:saturate(0.5)"></div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active">Details</div>
                <div class="tab">Rules</div>
                <div class="tab">Components</div>
            </div>

            <!-- About Section -->
            <div class="content-section">
                <h2 class="section-title">
                    <svg class="icon icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    About the Game
                </h2>
                <div class="description">
                    <p><?= nl2br($description) ?></p><br>
                    <p>In Terraforming Mars, you play one of those corporations and work together in the terraforming process, but compete for getting victory points that are awarded not only for your contribution to the terraforming, but also for advancing human infrastructure throughout the solar system.</p>
                </div>
                
                <div class="expert-take">
                    <div class="expert-take-title">
                        <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        The Expert's Take
                    </div>
                    <p>"A modern masterpiece of engine building. The depth of strategy combined with the thematic integration of science makes every session unique. Highly recommended for experienced groups."</p>
                </div>
            </div>

            <!-- Tags -->
            <div class="content-section">
                <h3 class="section-title" style="font-size: 1.2rem; margin-bottom: 12px;">Tags & Mechanics</h3>
                <div class="tags-container">
                    <span class="tag">Engine Building</span>
                    <span class="tag">Space Exploration</span>
                    <span class="tag">Drafting</span>
                    <span class="tag">Economic</span>
                </div>
            </div>

        </div> <!-- End Left Column -->

        <!-- === RIGHT COLUMN (STICKY) === -->
        <div class="sidebar">
            
            <!-- Main Info Card -->
            <div class="info-card">
                <div class="card-header-top">
                    <h1 class="game-main-title"><?= $title ?></h1>
                    <div class="rating-box">
                        <div class="rating-score">
                            <svg class="icon-md" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            <?= number_format($gameStats['rating'], 1) ?>
                        </div>
                        <div class="reviews-count"><?= $gameStats['reviews'] ?> VERIFIED REVIEWS</div>
                    </div>
                </div>
                <p class="game-subtitle">The Race to Tame the Red Planet.</p>

                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-icon"><svg class="icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                        <div class="stat-label">Players</div>
                        <div class="stat-value"><?= $players ?> Players</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon"><svg class="icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                        <div class="stat-label">Duration</div>
                        <div class="stat-value"><?= $duration ?> Min</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon"><svg class="icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg></div>
                        <div class="stat-label">Complexity</div>
                        <div class="stat-value"><?= $difficulty ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon"><svg class="icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                        <div class="stat-label">Age</div>
                        <div class="stat-value"><?= $gameStats['age'] ?> Years</div>
                    </div>
                </div>

                <div class="booking-section">
                    <div class="booking-header">
                        <span class="booking-label">Table Booking</span>
                        <span class="booking-badge">Limited Availability</span>
                    </div>
                    <div class="booking-price">
                        $5.00 <span>/ player / hr</span>
                    </div>
                    <button class="btn-reserve" onclick="window.location.href='<?= $baseUrl ?>/reservations/create?game_id=<?= $game['id'] ?? '' ?>'">
                        Reserve a Table
                    </button>
                    <div class="booking-guarantee">INSTANT CONFIRMATION + FLEXIBLE CANCELLATION</div>
                </div>
            </div>

            <!-- Feedback -->
            <div class="feedback-card">
                <div class="feedback-header">
                    <h3 class="feedback-title">Community Feedback</h3>
                    <a href="#" class="feedback-view-all">View All <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg></a>
                </div>
                
                <?php foreach($gameReviews as $review): ?>
                <div class="review-item">
                    <div class="reviewer-info">
                        <span class="reviewer-name"><?= htmlspecialchars($review['user']) ?></span>
                        <span class="review-date"><?= htmlspecialchars($review['date']) ?></span>
                    </div>
                    <div class="review-stars">
                        <?php 
                        for($i=1; $i<=5; $i++) {
                            echo ($i <= $review['rating']) 
                                ? '<svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>' 
                                : '<svg class="empty" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>';
                        }
                        ?>
                    </div>
                    <p class="review-text"><?= htmlspecialchars($review['comment']) ?></p>
                </div>
                <?php endforeach; ?>

                <div class="share-experience">Share Your Experience</div>
            </div>

            <!-- Related -->
            <div class="related-card">
                <h3 class="related-title">You Might Also Like</h3>
                <div class="related-list">
                    <?php foreach($relatedGames as $related):
                        $relatedKey = strtolower($related['name'] ?? '');
                        $relatedImg = isset($imageMap[$relatedKey])
                            ? $baseUrl . '/app/views/img/game/' . $imageMap[$relatedKey]
                            : null;
                    ?>
                    <div class="related-item" onclick="window.location.href='<?= $baseUrl ?>/games/<?= $related['id'] ?>'">
                        <div class="related-img">
                            <?php if ($relatedImg): ?>
                                <img src="<?= $relatedImg ?>" alt="<?= htmlspecialchars($related['name']) ?>">
                            <?php else: ?>
                                <div style="width:100%;height:100%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></div>
                            <?php endif; ?>
                        </div>
                        <div class="related-info">
                            <h4><?= htmlspecialchars($related['name']) ?></h4>
                            <p><?= htmlspecialchars($related['category_name']) ?> • <?= htmlspecialchars($related['nb_players']) ?> PLAYERS</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div> <!-- End Right Column -->

    </main>

    <!-- MAIN FOOTER -->
    <footer>
        <div class="container">
            <div class="footer-top">
                <div class="footer-col" style="flex:2;">
                    <div class="brand footer-logo">
                        <div class="brand-icon" style="width:30px;height:30px;">
                            <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"></circle>
                                <circle cx="15.5" cy="8.5" r="1.5" fill="currentColor"></circle>
                                <circle cx="8.5" cy="15.5" r="1.5" fill="currentColor"></circle>
                            </svg>
                        </div>
                        TableTop Hub
                    </div>
                    <p class="footer-text">
                        The ultimate destination for tabletop enthusiasts. Book tables, track sessions, and discover your next favorite board game.
                    </p>
                </div>
                <div class="footer-col">
                    <h4 class="footer-col-title">Platform</h4>
                    <ul class="footer-links">
                        <li><a href="#">Browse Games</a></li>
                        <li><a href="#">My Reservations</a></li>
                        <li><a href="#">Gaming History</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 class="footer-col-title">Support</h4>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Rules & Conduct</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4 class="footer-col-title">Connect</h4>
                    <div class="social-links">
                        <a href="#" class="social-link"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg></a>
                        <a href="#" class="social-link"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg></a>
                        <a href="#" class="social-link"><svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="copyright">
                    © 2024 TableTop Hub. All rights reserved.
                </div>
                <div class="footer-legal-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

<script>
function switchImg(thumb, src) {
    document.getElementById('main-img').src = src;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

const MOON_ICON = `<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>`;
const SUN_ICON  = `<circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>`;

function applyTheme(dark) {
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    const icon = document.getElementById('theme-icon');
    if (icon) icon.innerHTML = dark ? SUN_ICON : MOON_ICON;
}

(function() {
    const saved = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(saved ? saved === 'dark' : prefersDark);
})();

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('theme-toggle');
    if (btn) {
        btn.addEventListener('click', function() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            applyTheme(!isDark);
            localStorage.setItem('theme', !isDark ? 'dark' : 'light');
        });
    }
});
</script>
</body>
</html>
