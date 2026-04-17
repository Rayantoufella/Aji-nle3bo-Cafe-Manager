<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$baseUrl}/login");
    exit;
}

$reservations = $reservations ?? [];

$countAll       = count($reservations);
$countPending   = count(array_filter($reservations, fn($r) => ($r['status'] ?? '') === 'pending'));
$countConfirmed = count(array_filter($reservations, fn($r) => ($r['status'] ?? '') === 'confirmed'));
$countCancelled = count(array_filter($reservations, fn($r) => ($r['status'] ?? '') === 'cancelled'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations – TableTop Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #6366f1;
            --primary-dark:  #4f46e5;
            --primary-light: #e0e7ff;
            --accent:        #f59e0b;
            --accent2:       #ec4899;
            --text-main:     #111827;
            --text-muted:    #6b7280;
            --text-light:    #9ca3af;
            --bg-body:       #f8faff;
            --bg-gray:       #f1f5f9;
            --bg-card:       #ffffff;
            --bg-nav:        rgba(255,255,255,0.85);
            --border-color:  #e5e7eb;
            --green:         #10b981;
            --green-light:   #d1fae5;
            --amber:         #f59e0b;
            --amber-light:   #fef3c7;
            --red:           #ef4444;
            --red-light:     #fee2e2;
            --card-radius:   16px;
            --transition:    all 0.3s cubic-bezier(.4,0,.2,1);
            --shadow-sm:     0 2px 8px rgba(0,0,0,0.06);
            --shadow-md:     0 8px 24px rgba(0,0,0,0.09);
            --shadow-lg:     0 16px 48px rgba(99,102,241,0.18);
        }

        [data-theme="dark"] {
            --primary:       #818cf8;
            --primary-dark:  #6366f1;
            --primary-light: rgba(99,102,241,0.18);
            --accent2:       #f472b6;
            --text-main:     #f1f5f9;
            --text-muted:    #94a3b8;
            --text-light:    #64748b;
            --bg-body:       #0d0d1a;
            --bg-gray:       #13131f;
            --bg-card:       #1a1a2e;
            --border-color:  #252540;
            --green-light:   rgba(16,185,129,0.15);
            --amber-light:   rgba(245,158,11,0.15);
            --red-light:     rgba(239,68,68,0.15);
            --shadow-sm:     0 2px 8px rgba(0,0,0,0.3);
            --shadow-md:     0 8px 24px rgba(0,0,0,0.4);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
        }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1220px; margin: 0 auto; padding: 0 24px; }

        /* ── HERO ── */
        .page-hero {
            position: relative;
            overflow: hidden;
            padding: 44px 0 36px;
        }
        .page-hero-bg {
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(99,102,241,.07) 0%, rgba(168,85,247,.05) 50%, rgba(236,72,153,.04) 100%);
        }
        [data-theme="dark"] .page-hero-bg {
            background: linear-gradient(135deg, rgba(99,102,241,.12) 0%, rgba(168,85,247,.08) 50%, rgba(236,72,153,.06) 100%);
        }
        .hero-orb {
            position: absolute; border-radius: 50%; filter: blur(80px); pointer-events: none;
        }
        .hero-orb-1 {
            width: 380px; height: 380px;
            background: radial-gradient(circle, rgba(99,102,241,.22), transparent 70%);
            top: -120px; right: -60px;
        }
        .hero-orb-2 {
            width: 220px; height: 220px;
            background: radial-gradient(circle, rgba(236,72,153,.15), transparent 70%);
            bottom: -50px; left: 4%;
        }
        .hero-inner { position: relative; z-index: 1; }

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: .85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 20px;
        }
        .breadcrumb a { color: var(--text-light); display: flex; align-items: center; gap: 5px; transition: var(--transition); }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb .current { color: var(--text-main); font-weight: 700; }

        .hero-title-row { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .hero-icon {
            width: 54px; height: 54px;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; box-shadow: 0 8px 24px rgba(99,102,241,.35); flex-shrink: 0;
        }
        .hero-icon svg { width: 26px; height: 26px; }
        .hero-texts {}
        .hero-title {
            font-size: 2rem; font-weight: 900; letter-spacing: -.8px; line-height: 1.2;
        }
        .hero-title .grad {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 60%, var(--accent) 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            animation: grad-shift 4s ease infinite;
        }
        @keyframes grad-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .hero-sub { color: var(--text-muted); font-size: .93rem; margin-top: 4px; }

        /* ── STATS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin: 32px 0 28px;
        }
        @media (max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }

        .stat-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            padding: 20px 22px;
            display: flex; align-items: center; gap: 14px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative; overflow: hidden;
            animation: fadeInUp .5s ease both;
        }
        .stat-card:nth-child(1) { animation-delay: .05s; }
        .stat-card:nth-child(2) { animation-delay: .12s; }
        .stat-card:nth-child(3) { animation-delay: .19s; }
        .stat-card:nth-child(4) { animation-delay: .26s; }
        .stat-card::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--sc1), var(--sc2));
            opacity: 0; transition: opacity .3s;
        }
        .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .stat-card:hover::after { opacity: 1; }

        .stat-card.all    { --sc1: #6366f1; --sc2: #818cf8; }
        .stat-card.pend   { --sc1: #f59e0b; --sc2: #fbbf24; }
        .stat-card.conf   { --sc1: #10b981; --sc2: #34d399; }
        .stat-card.canc   { --sc1: #ef4444; --sc2: #f87171; }

        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-icon svg { width: 20px; height: 20px; }
        .stat-card.all  .stat-icon { background: var(--primary-light); color: var(--primary); }
        .stat-card.pend .stat-icon { background: var(--amber-light);   color: var(--amber); }
        .stat-card.conf .stat-icon { background: var(--green-light);   color: var(--green); }
        .stat-card.canc .stat-icon { background: var(--red-light);     color: var(--red); }

        .stat-info { flex: 1; }
        .stat-num  { font-size: 1.7rem; font-weight: 900; line-height: 1; letter-spacing: -.5px; }
        .stat-label { font-size: .73rem; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--text-light); margin-top: 3px; }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .filter-btn {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 18px;
            border: 1.5px solid var(--border-color);
            border-radius: 100px;
            background: var(--bg-card);
            color: var(--text-muted);
            font-size: .82rem; font-weight: 600;
            cursor: pointer; transition: var(--transition);
            font-family: inherit;
        }
        .filter-btn:hover { border-color: var(--primary); color: var(--primary); }
        .filter-btn.active {
            background: var(--primary); color: #fff; border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(99,102,241,.3);
        }
        .filter-btn .dot {
            width: 7px; height: 7px; border-radius: 50%;
        }
        .filter-btn.active .dot { background: rgba(255,255,255,.7); }
        .filter-btn:not(.active) .dot-all    { background: var(--primary); }
        .filter-btn:not(.active) .dot-pend   { background: var(--amber); }
        .filter-btn:not(.active) .dot-conf   { background: var(--green); }
        .filter-btn:not(.active) .dot-canc   { background: var(--red); }

        .filter-spacer { flex: 1; }
        .new-res-btn {
            display: flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            color: #fff; border: none; border-radius: 12px;
            padding: 10px 20px; font-size: .88rem; font-weight: 700;
            cursor: pointer; transition: var(--transition); font-family: inherit;
            box-shadow: 0 6px 18px rgba(99,102,241,.35);
        }
        .new-res-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(99,102,241,.45); }
        .new-res-btn svg { width: 16px; height: 16px; }

        /* ── GRID ── */
        .res-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 22px;
            padding-bottom: 60px;
        }
        @media (max-width: 480px) { .res-grid { grid-template-columns: 1fr; } }

        /* ── CARD ── */
        .res-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            padding: 0;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex; flex-direction: column;
            overflow: hidden;
            position: relative;
            animation: fadeInUp .55s ease both;
        }
        .res-card:nth-child(1)  { animation-delay: .05s; }
        .res-card:nth-child(2)  { animation-delay: .12s; }
        .res-card:nth-child(3)  { animation-delay: .19s; }
        .res-card:nth-child(4)  { animation-delay: .26s; }
        .res-card:nth-child(5)  { animation-delay: .33s; }
        .res-card:nth-child(6)  { animation-delay: .40s; }

        .res-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .res-card-header {
            padding: 20px 22px 16px;
            display: flex; align-items: flex-start; gap: 14px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-gray);
        }
        [data-theme="dark"] .res-card-header { background: rgba(255,255,255,.02); }

        .res-card-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 16px rgba(99,102,241,.3);
        }
        .res-card-icon svg { width: 22px; height: 22px; color: #fff; }

        .res-card-head-info { flex: 1; min-width: 0; }
        .res-card-title {
            font-size: 1rem; font-weight: 800; color: var(--text-main);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .res-card-id { font-size: .72rem; color: var(--text-light); font-weight: 600; margin-top: 2px; }

        .status-pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .6px;
            padding: 4px 12px; border-radius: 100px;
        }
        .status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .status-pill.pending   { background: var(--amber-light); color: var(--amber); }
        .status-pill.pending::before { background: var(--amber); }
        .status-pill.confirmed { background: var(--green-light); color: var(--green); }
        .status-pill.confirmed::before { background: var(--green); }
        .status-pill.cancelled { background: var(--red-light); color: var(--red); }
        .status-pill.cancelled::before { background: var(--red); }

        .res-card-body { padding: 18px 22px; display: flex; flex-direction: column; gap: 10px; flex: 1; }

        .res-info-row {
            display: flex; align-items: center; gap: 10px;
            font-size: .85rem; color: var(--text-muted);
        }
        .res-info-row-icon {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--primary-light); display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .res-info-row-icon svg { width: 14px; height: 14px; color: var(--primary); }
        .res-info-label { flex: 1; font-weight: 500; color: var(--text-light); font-size: .75rem; }
        .res-info-val { font-weight: 700; color: var(--text-main); font-size: .88rem; }

        .res-card-footer {
            padding: 14px 22px;
            border-top: 1px solid var(--border-color);
            display: flex; gap: 10px;
        }

        .btn-view-detail {
            flex: 1;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            color: #fff; border: none; border-radius: 10px;
            padding: 10px; font-size: .83rem; font-weight: 700;
            cursor: pointer; transition: var(--transition); font-family: inherit;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            box-shadow: 0 4px 14px rgba(99,102,241,.25);
        }
        .btn-view-detail:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,.4); }
        .btn-view-detail svg { width: 15px; height: 15px; }

        /* ── EMPTY ── */
        .empty-wrap {
            display: flex; align-items: center; justify-content: center;
            padding-bottom: 60px;
        }
        .empty-box {
            background: var(--bg-card);
            border: 1.5px dashed var(--border-color);
            border-radius: 24px;
            padding: 64px 48px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            animation: fadeInUp .6s ease both;
        }
        .empty-icon-wrap {
            width: 80px; height: 80px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }
        .empty-icon-wrap svg { width: 38px; height: 38px; color: var(--primary); }
        .empty-title { font-size: 1.4rem; font-weight: 800; margin-bottom: 10px; }
        .empty-sub { color: var(--text-muted); font-size: .9rem; line-height: 1.6; margin-bottom: 28px; }
        .btn-start {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            color: #fff; padding: 13px 28px; border-radius: 12px;
            font-size: .9rem; font-weight: 700;
            box-shadow: 0 8px 20px rgba(99,102,241,.35);
            transition: var(--transition);
        }
        .btn-start:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(99,102,241,.45); }

        /* ── FLASH ── */
        .flash-success {
            display: flex; align-items: center; gap: 12px;
            background: linear-gradient(135deg, rgba(16,185,129,.08), rgba(16,185,129,.04));
            color: var(--green); border: 1.5px solid rgba(16,185,129,.2);
            padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
            font-size: .88rem; font-weight: 600;
            animation: fadeInUp .4s ease;
        }
        .flash-success svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ── FOOTER ── */
        .page-footer { background: #13131f; color: #9ca3af; padding: 48px 0 24px; margin-top: 20px; }
        [data-theme="dark"] .page-footer { background: #09090f; border-top: 1px solid #1e1e36; }
        .footer-inner { max-width: 1220px; margin: 0 auto; padding: 0 24px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 32px; margin-bottom: 32px; }
        .footer-brand { font-size: 1.1rem; font-weight: 800; color: #fff; margin-bottom: 8px; }
        .footer-desc { font-size: .82rem; line-height: 1.7; }
        .footer-col-title { font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff; margin-bottom: 14px; }
        .footer-col a { display: block; font-size: .82rem; color: #9ca3af; margin-bottom: 8px; transition: color .2s; }
        .footer-col a:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid #252540; padding-top: 20px; display: flex; justify-content: space-between; font-size: .75rem; }
        .footer-bottom-links a { color: #9ca3af; margin-left: 16px; transition: color .2s; }
        .footer-bottom-links a:hover { color: #fff; }
        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .footer-bottom { flex-direction: column; align-items: center; gap: 10px; }
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- HERO -->
<section class="page-hero">
    <div class="page-hero-bg"></div>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="container hero-inner">
        <div class="breadcrumb">
            <a href="<?= $baseUrl ?>/dashboard">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Home
            </a>
            <span style="color:var(--text-light)">›</span>
            <span class="current">My Reservations</span>
        </div>
        <div class="hero-title-row">
            <div class="hero-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="hero-texts">
                <h1 class="hero-title">My <span class="grad">Reservations</span></h1>
                <p class="hero-sub">Track and manage all your upcoming and past game sessions.</p>
            </div>
        </div>
    </div>
</section>

<div class="container">

    <!-- FLASH -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <!-- STATS -->
    <div class="stats-row">
        <div class="stat-card all">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-num"><?= $countAll ?></div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        <div class="stat-card pend">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-num"><?= $countPending ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="stat-card conf">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-num"><?= $countConfirmed ?></div>
                <div class="stat-label">Confirmed</div>
            </div>
        </div>
        <div class="stat-card canc">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-num"><?= $countCancelled ?></div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterCards('all', this)">
            <span class="dot dot-all"></span> All
        </button>
        <button class="filter-btn" onclick="filterCards('pending', this)">
            <span class="dot dot-pend"></span> Pending
        </button>
        <button class="filter-btn" onclick="filterCards('confirmed', this)">
            <span class="dot dot-conf"></span> Confirmed
        </button>
        <button class="filter-btn" onclick="filterCards('cancelled', this)">
            <span class="dot dot-canc"></span> Cancelled
        </button>
        <div class="filter-spacer"></div>
        <a href="<?= $baseUrl ?>/dashboard" class="new-res-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Reservation
        </a>
    </div>

    <!-- CARDS / EMPTY -->
    <?php if (empty($reservations)): ?>
        <div class="empty-wrap">
            <div class="empty-box">
                <div class="empty-icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div class="empty-title">No Reservations Yet</div>
                <p class="empty-sub">You haven't booked any tables yet. Start planning your next game night and reserve your spot!</p>
                <a href="<?= $baseUrl ?>/dashboard" class="btn-start">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Browse Games
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="res-grid" id="resGrid">
            <?php foreach ($reservations as $idx => $res): ?>
                <?php
                $status     = $res['status'] ?? 'pending';
                $statusClass = strtolower($status);
                $tableNum   = $res['table_number'] ?? $res['table_id'] ?? '—';
                $date       = $res['reservation_date'] ?? '';
                $time       = $res['reservation_time'] ?? '';
                $people     = $res['number_of_people'] ?? '—';
                $resId      = $res['id'] ?? '';
                ?>
                <div class="res-card" data-status="<?= $statusClass ?>">
                    <div class="res-card-header">
                        <div class="res-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 10h18"/><path d="M5 10v7"/><path d="M19 10v7"/><path d="M2 10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2"/>
                            </svg>
                        </div>
                        <div class="res-card-head-info">
                            <div class="res-card-title">Table <?= htmlspecialchars((string)$tableNum) ?> Booking</div>
                            <div class="res-card-id">#<?= str_pad($resId, 5, '0', STR_PAD_LEFT) ?></div>
                        </div>
                        <span class="status-pill <?= $statusClass ?>"><?= ucfirst($statusClass) ?></span>
                    </div>
                    <div class="res-card-body">
                        <div class="res-info-row">
                            <div class="res-info-row-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </div>
                            <span class="res-info-label">Date</span>
                            <span class="res-info-val"><?= $date ? date('M d, Y', strtotime($date)) : '—' ?></span>
                        </div>
                        <div class="res-info-row">
                            <div class="res-info-row-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <span class="res-info-label">Time</span>
                            <span class="res-info-val"><?= htmlspecialchars($time) ?></span>
                        </div>
                        <div class="res-info-row">
                            <div class="res-info-row-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            </div>
                            <span class="res-info-label">Party Size</span>
                            <span class="res-info-val"><?= htmlspecialchars((string)$people) ?> Guests</span>
                        </div>
                    </div>
                    <div class="res-card-footer">
                        <a href="<?= $baseUrl ?>/reservations/<?= htmlspecialchars((string)$resId) ?>" class="btn-view-detail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<footer class="page-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">🎲 TableTop Hub</div>
                <p class="footer-desc">The ultimate destination for tabletop enthusiasts. Book tables, track sessions, and discover your next favorite board game.</p>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Platform</div>
                <a href="<?= $baseUrl ?>/dashboard">Browse Games</a>
                <a href="<?= $baseUrl ?>/reservations">My Reservations</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Support</div>
                <a href="#">Help Center</a>
                <a href="#">Contact Us</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Connect</div>
                <a href="#">Instagram</a>
                <a href="#">Twitter</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© <?= date('Y') ?> TableTop Hub. All rights reserved.</span>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<script>
function filterCards(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.res-card').forEach(function(card) {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

</body>
</html>
