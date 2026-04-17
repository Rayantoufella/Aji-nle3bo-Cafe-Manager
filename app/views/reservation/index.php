<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$baseUrl}/login");
    exit;
}

$reservations = $reservations ?? [];
$today = date('Y-m-d');

$upcoming = array_values(array_filter($reservations, function($r) use ($today) {
    return ($r['reservation_date'] ?? '') >= $today && ($r['status'] ?? '') !== 'cancelled';
}));
usort($upcoming, fn($a, $b) => strcmp($a['reservation_date'].$a['reservation_time'], $b['reservation_date'].$b['reservation_time']));

$history = array_values(array_filter($reservations, function($r) use ($today) {
    return ($r['reservation_date'] ?? '') < $today || ($r['status'] ?? '') === 'cancelled';
}));
usort($history, fn($a, $b) => strcmp($b['reservation_date'].$b['reservation_time'], $a['reservation_date'].$a['reservation_time']));

$nextSessionDays = null;
if (!empty($upcoming)) {
    $diff = (new DateTime($upcoming[0]['reservation_date']))->diff(new DateTime($today))->days;
    $nextSessionDays = $diff;
}

$cardGradients = [
    'linear-gradient(135deg,#6366f1,#8b5cf6)',
    'linear-gradient(135deg,#f59e0b,#f97316)',
    'linear-gradient(135deg,#10b981,#06b6d4)',
    'linear-gradient(135deg,#ec4899,#f43f5e)',
    'linear-gradient(135deg,#3b82f6,#06b6d4)',
    'linear-gradient(135deg,#8b5cf6,#ec4899)',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings – TableTop Hub</title>
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
            --border-color:  #e5e7eb;
            --green:         #10b981;
            --green-light:   #d1fae5;
            --amber:         #f59e0b;
            --amber-light:   #fef3c7;
            --red:           #ef4444;
            --red-light:     #fee2e2;
            --blue:          #3b82f6;
            --blue-light:    #dbeafe;
            --card-radius:   16px;
            --transition:    all 0.3s cubic-bezier(.4,0,.2,1);
            --shadow-sm:     0 2px 8px rgba(0,0,0,0.06);
            --shadow-md:     0 8px 24px rgba(0,0,0,0.09);
        }
        [data-theme="dark"] {
            --primary:       #818cf8;
            --primary-dark:  #6366f1;
            --primary-light: rgba(99,102,241,0.18);
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
            --blue-light:    rgba(59,130,246,0.15);
            --shadow-sm:     0 2px 8px rgba(0,0,0,0.3);
            --shadow-md:     0 8px 24px rgba(0,0,0,0.4);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-body); color: var(--text-main); line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

        /* ── PAGE HEADER ── */
        .page-top {
            padding: 36px 0 28px;
            display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 20px;
        }
        .page-top-left {}
        .page-top-title { font-size: 1.6rem; font-weight: 800; letter-spacing: -.5px; margin-bottom: 4px; }
        .page-top-sub { color: var(--text-muted); font-size: .88rem; max-width: 420px; line-height: 1.5; }
        .page-top-right { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .next-session-chip {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg-card); border: 1.5px solid var(--border-color);
            border-radius: 10px; padding: 9px 16px;
            font-size: .82rem; font-weight: 600; color: var(--text-muted);
            box-shadow: var(--shadow-sm);
        }
        .next-session-chip svg { width: 15px; height: 15px; color: var(--primary); }
        .next-session-chip .days { color: var(--primary); font-weight: 800; }
        .btn-book {
            display: flex; align-items: center; gap: 7px;
            background: var(--primary); color: #fff;
            border: none; border-radius: 10px;
            padding: 10px 18px; font-size: .85rem; font-weight: 700;
            cursor: pointer; font-family: inherit; transition: var(--transition);
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
        }
        .btn-book:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-book svg { width: 16px; height: 16px; }

        /* ── FLASH ── */
        .flash-success {
            display: flex; align-items: center; gap: 12px;
            background: linear-gradient(135deg, rgba(16,185,129,.08), rgba(16,185,129,.04));
            color: var(--green); border: 1.5px solid rgba(16,185,129,.25);
            padding: 14px 18px; border-radius: 12px; margin-bottom: 24px;
            font-size: .88rem; font-weight: 600; animation: fadeInUp .4s ease;
        }
        .flash-success svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ── SECTION HEADER ── */
        .section-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
        }
        .section-icon { color: var(--primary); display: flex; align-items: center; }
        .section-icon svg { width: 18px; height: 18px; }
        .section-title { font-size: 1rem; font-weight: 800; }
        .section-badge {
            display: inline-flex; align-items: center;
            background: var(--primary-light); color: var(--primary);
            font-size: .72rem; font-weight: 800; padding: 3px 10px; border-radius: 100px;
            margin-left: 4px;
        }
        [data-theme="dark"] .section-badge { background: rgba(99,102,241,.2); }

        /* ── UPCOMING CARDS ── */
        .upcoming-section { margin-bottom: 40px; }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        @media (max-width: 900px) { .cards-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 560px) { .cards-grid { grid-template-columns: 1fr; } }

        .booking-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            animation: fadeInUp .5s ease both;
        }
        .booking-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); border-color: var(--primary); }

        .card-img {
            position: relative; height: 140px; overflow: hidden;
        }
        .card-img-bg {
            width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
        }
        .card-img-bg svg { width: 52px; height: 52px; opacity: .35; color: #fff; }
        .card-status-badge {
            position: absolute; top: 10px; left: 10px;
            font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
            padding: 4px 10px; border-radius: 6px;
        }
        .card-status-badge.confirmed { background: rgba(16,185,129,.9); color: #fff; }
        .card-status-badge.pending   { background: rgba(245,158,11,.9); color: #fff; }

        .card-body { padding: 16px 18px; }
        .card-game-name { font-size: .97rem; font-weight: 800; margin-bottom: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .card-meta { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        .card-meta-row { display: flex; align-items: center; gap: 7px; font-size: .8rem; color: var(--text-muted); }
        .card-meta-row svg { width: 13px; height: 13px; flex-shrink: 0; color: var(--text-light); }
        .card-meta-row strong { color: var(--text-main); font-weight: 600; margin-left: 2px; }
        .card-location {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--bg-gray); border: 1px solid var(--border-color);
            border-radius: 7px; padding: 5px 10px;
            font-size: .78rem; font-weight: 600; color: var(--text-muted);
            margin-bottom: 14px;
        }
        [data-theme="dark"] .card-location { background: rgba(255,255,255,.04); }
        .card-location svg { width: 12px; height: 12px; color: var(--primary); }
        .card-actions { display: flex; gap: 10px; }
        .btn-card-view {
            flex: 1; text-align: center;
            padding: 8px 0; border-radius: 8px;
            font-size: .8rem; font-weight: 700;
            color: var(--primary); border: 1.5px solid var(--primary);
            background: transparent; cursor: pointer; font-family: inherit;
            transition: var(--transition);
        }
        .btn-card-view:hover { background: var(--primary); color: #fff; }
        .btn-card-cancel {
            flex: 1; text-align: center;
            padding: 8px 0; border-radius: 8px;
            font-size: .8rem; font-weight: 700;
            color: var(--red); border: 1.5px solid var(--red-light);
            background: transparent; cursor: pointer; font-family: inherit;
            transition: var(--transition);
        }
        .btn-card-cancel:hover { background: var(--red-light); }

        /* ── HISTORY TABLE ── */
        .history-section { margin-bottom: 40px; }
        .history-table-wrap {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        table.history-table { width: 100%; border-collapse: collapse; }
        .history-table thead tr {
            background: var(--bg-gray); border-bottom: 1.5px solid var(--border-color);
        }
        [data-theme="dark"] .history-table thead tr { background: rgba(255,255,255,.03); }
        .history-table th {
            padding: 13px 18px; text-align: left;
            font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .7px;
            color: var(--text-light);
        }
        .history-table th:last-child { text-align: right; }
        .history-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background .15s;
        }
        .history-table tbody tr:last-child { border-bottom: none; }
        .history-table tbody tr:hover { background: var(--bg-gray); }
        [data-theme="dark"] .history-table tbody tr:hover { background: rgba(255,255,255,.02); }
        .history-table td {
            padding: 14px 18px; font-size: .85rem; color: var(--text-muted);
        }
        .history-table td:first-child { color: var(--text-main); font-weight: 600; }
        .history-table td.game-cell { color: var(--text-main); font-weight: 600; }

        .status-pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
            padding: 4px 10px; border-radius: 100px;
        }
        .status-pill::before { content: ''; width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        .status-pill.pending   { background: var(--amber-light); color: var(--amber); }
        .status-pill.pending::before { background: var(--amber); }
        .status-pill.confirmed { background: var(--green-light); color: var(--green); }
        .status-pill.confirmed::before { background: var(--green); }
        .status-pill.cancelled { background: var(--red-light); color: var(--red); }
        .status-pill.cancelled::before { background: var(--red); }
        .status-pill.completed { background: var(--blue-light); color: var(--blue); }
        .status-pill.completed::before { background: var(--blue); }

        .action-cell { display: flex; align-items: center; justify-content: flex-end; gap: 8px; }
        .btn-rate {
            display: flex; align-items: center; gap: 5px;
            font-size: .78rem; font-weight: 700; color: var(--primary);
            background: var(--primary-light); border: none; border-radius: 8px;
            padding: 5px 12px; cursor: pointer; font-family: inherit; transition: var(--transition);
        }
        [data-theme="dark"] .btn-rate { background: rgba(99,102,241,.15); }
        .btn-rate:hover { background: var(--primary); color: #fff; }
        .btn-rate svg { width: 13px; height: 13px; }
        .btn-menu {
            width: 30px; height: 30px; border-radius: 7px;
            border: 1.5px solid var(--border-color); background: transparent;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: var(--transition); color: var(--text-muted);
        }
        .btn-menu:hover { border-color: var(--primary); color: var(--primary); }
        .btn-menu svg { width: 15px; height: 15px; }

        /* history pagination */
        .history-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 18px; border-top: 1.5px solid var(--border-color);
            font-size: .8rem; color: var(--text-muted); background: var(--bg-card);
        }
        .pagination-btns { display: flex; gap: 8px; }
        .btn-page {
            padding: 6px 16px; border-radius: 8px; font-size: .8rem; font-weight: 700;
            border: 1.5px solid var(--border-color); background: var(--bg-card);
            color: var(--text-muted); cursor: pointer; font-family: inherit; transition: var(--transition);
        }
        .btn-page:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); }
        .btn-page:disabled { opacity: .4; cursor: default; }

        /* ── PROMO BANNER ── */
        .promo-banner {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            padding: 22px 28px;
            display: flex; align-items: center; justify-content: space-between; gap: 20px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-sm);
        }
        .promo-left { display: flex; align-items: center; gap: 16px; }
        .promo-icon {
            width: 42px; height: 42px; border-radius: 10px;
            background: var(--primary-light); color: var(--primary);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        [data-theme="dark"] .promo-icon { background: rgba(99,102,241,.18); }
        .promo-icon svg { width: 20px; height: 20px; }
        .promo-title { font-size: .95rem; font-weight: 800; margin-bottom: 2px; }
        .promo-sub { font-size: .8rem; color: var(--text-muted); line-height: 1.5; }
        .btn-explore {
            display: flex; align-items: center; gap: 6px;
            color: var(--primary); font-size: .83rem; font-weight: 700;
            white-space: nowrap; transition: var(--transition); flex-shrink: 0;
        }
        .btn-explore:hover { color: var(--primary-dark); }
        .btn-explore svg { width: 14px; height: 14px; }

        /* ── EMPTY ── */
        .empty-wrap { display: flex; align-items: center; justify-content: center; padding: 50px 0 60px; }
        .empty-box {
            background: var(--bg-card); border: 1.5px dashed var(--border-color);
            border-radius: 24px; padding: 56px 48px; text-align: center;
            max-width: 460px; width: 100%; animation: fadeInUp .5s ease both;
        }
        .empty-icon-wrap {
            width: 72px; height: 72px; background: var(--primary-light); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;
        }
        .empty-icon-wrap svg { width: 34px; height: 34px; color: var(--primary); }
        .empty-title { font-size: 1.3rem; font-weight: 800; margin-bottom: 8px; }
        .empty-sub { color: var(--text-muted); font-size: .87rem; line-height: 1.6; margin-bottom: 24px; }
        .btn-start {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--primary); color: #fff; padding: 12px 26px;
            border-radius: 10px; font-size: .87rem; font-weight: 700;
            box-shadow: 0 6px 18px rgba(99,102,241,.3); transition: var(--transition);
        }
        .btn-start:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(99,102,241,.4); }

        /* ── FOOTER ── */
        .page-footer { background: #13131f; color: #9ca3af; padding: 48px 0 24px; margin-top: 10px; }
        [data-theme="dark"] .page-footer { background: #09090f; border-top: 1px solid #1e1e36; }
        .footer-inner { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 32px; margin-bottom: 32px; }
        @media (max-width: 768px) { .footer-grid { grid-template-columns: 1fr 1fr; } }
        .footer-brand { display: flex; align-items: center; gap: 8px; font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 10px; }
        .footer-brand-icon {
            width: 32px; height: 32px; background: var(--primary);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }
        .footer-brand-icon svg { width: 16px; height: 16px; color: #fff; }
        .footer-desc { font-size: .81rem; line-height: 1.7; }
        .footer-col-title { font-size: .75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff; margin-bottom: 14px; }
        .footer-col a { display: block; font-size: .81rem; color: #9ca3af; margin-bottom: 8px; transition: color .2s; }
        .footer-col a:hover { color: #fff; }
        .footer-social { display: flex; gap: 10px; margin-top: 4px; }
        .social-btn {
            width: 34px; height: 34px; border-radius: 8px;
            background: rgba(255,255,255,.08); display: flex; align-items: center; justify-content: center;
            transition: background .2s; color: #9ca3af;
        }
        .social-btn:hover { background: var(--primary); color: #fff; }
        .social-btn svg { width: 15px; height: 15px; }
        .footer-bottom { border-top: 1px solid #252540; padding-top: 20px; display: flex; justify-content: space-between; font-size: .74rem; flex-wrap: wrap; gap: 8px; }
        .footer-bottom-links a { color: #9ca3af; margin-left: 16px; transition: color .2s; }
        .footer-bottom-links a:hover { color: #fff; }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .booking-card:nth-child(1) { animation-delay: .05s; }
        .booking-card:nth-child(2) { animation-delay: .12s; }
        .booking-card:nth-child(3) { animation-delay: .19s; }
        .booking-card:nth-child(4) { animation-delay: .26s; }
        .booking-card:nth-child(5) { animation-delay: .33s; }
        .booking-card:nth-child(6) { animation-delay: .40s; }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">

    <!-- PAGE HEADER -->
    <div class="page-top">
        <div class="page-top-left">
            <h1 class="page-top-title">My Reservations</h1>
            <p class="page-top-sub">Manage your upcoming tabletop sessions and view your gaming history at the Hub.</p>
        </div>
        <div class="page-top-right">
            <?php if ($nextSessionDays !== null): ?>
            <div class="next-session-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>NEXT SESSION</span>
                <span class="days">
                    <?php if ($nextSessionDays === 0): ?>Today
                    <?php elseif ($nextSessionDays === 1): ?>Tomorrow
                    <?php else: ?>In <?= $nextSessionDays ?> Days
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>
            <a href="<?= $baseUrl ?>/dashboard" class="btn-book">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Book New Session
            </a>
        </div>
    </div>

    <!-- FLASH -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <?php unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <!-- UPCOMING SESSIONS -->
    <section class="upcoming-section">
        <div class="section-header">
            <span class="section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </span>
            <span class="section-title">Upcoming Sessions</span>
            <?php if (!empty($upcoming)): ?>
                <span class="section-badge"><?= count($upcoming) ?> Reservation<?= count($upcoming) !== 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </div>

        <?php if (empty($upcoming)): ?>
            <div class="empty-wrap" style="padding:30px 0 20px;">
                <div class="empty-box">
                    <div class="empty-icon-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="empty-title">No Upcoming Sessions</div>
                    <p class="empty-sub">You have no upcoming reservations. Book a table to reserve your next game night!</p>
                    <a href="<?= $baseUrl ?>/dashboard" class="btn-start">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Browse Games
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($upcoming as $idx => $res):
                    $status     = $res['status'] ?? 'pending';
                    $tableNum   = $res['table_number'] ?? $res['table_id'] ?? '?';
                    $date       = $res['reservation_date'] ?? '';
                    $time       = $res['reservation_time'] ?? '';
                    $people     = $res['number_of_people'] ?? '?';
                    $resId      = $res['id'] ?? '';
                    $gradient   = $cardGradients[$idx % count($cardGradients)];
                    $dateLabel  = $date ? date('l, M j', strtotime($date)) : '—';
                    $timeLabel  = $time ? date('g:i A', strtotime($time)) : '—';
                    $tableLabel = 'Table ' . $tableNum;
                    $capacity   = $res['table_capacity'] ?? '';
                ?>
                <div class="booking-card">
                    <div class="card-img">
                        <div class="card-img-bg" style="background:<?= $gradient ?>;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M3 10h18"/><path d="M5 10v7"/><path d="M19 10v7"/>
                                <path d="M2 10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2"/>
                                <circle cx="12" cy="6" r="2"/>
                            </svg>
                        </div>
                        <span class="card-status-badge <?= htmlspecialchars($status) ?>"><?= ucfirst($status) ?></span>
                    </div>
                    <div class="card-body">
                        <div class="card-game-name">Table <?= htmlspecialchars((string)$tableNum) ?> Booking</div>
                        <div class="card-meta">
                            <div class="card-meta-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <?= htmlspecialchars($dateLabel) ?>
                            </div>
                            <div class="card-meta-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= htmlspecialchars($timeLabel) ?>
                                &nbsp;·&nbsp;
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <?= htmlspecialchars((string)$people) ?> Players
                            </div>
                        </div>
                        <div class="card-location">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?= htmlspecialchars($tableLabel) ?><?= $capacity ? ' (Cap. '.$capacity.')' : '' ?>
                        </div>
                        <div class="card-actions">
                            <a href="<?= $baseUrl ?>/reservations/<?= htmlspecialchars((string)$resId) ?>" class="btn-card-view">View Details</a>
                            <form method="POST" action="<?= $baseUrl ?>/reservations/<?= htmlspecialchars((string)$resId) ?>/cancel" style="flex:1;" onsubmit="return confirm('Cancel this reservation?')">
                                <button type="submit" class="btn-card-cancel" style="width:100%">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- RESERVATION HISTORY -->
    <section class="history-section">
        <div class="section-header">
            <span class="section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="12 8 12 12 14 14"/><path d="M3.05 11a9 9 0 1 0 .5-4.5"/><polyline points="3 3 3 7 7 7"/></svg>
            </span>
            <span class="section-title">Reservation History</span>
        </div>

        <?php if (empty($history)): ?>
            <p style="color:var(--text-muted);font-size:.87rem;padding:12px 0;">No past reservations yet.</p>
        <?php else: ?>
            <div class="history-table-wrap">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Board Game</th>
                            <th>Table</th>
                            <th>Players</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <?php foreach ($history as $i => $res):
                            $status    = $res['status'] ?? 'cancelled';
                            $tableNum  = $res['table_number'] ?? $res['table_id'] ?? '?';
                            $date      = $res['reservation_date'] ?? '';
                            $people    = $res['number_of_people'] ?? '?';
                            $resId     = $res['id'] ?? '';
                            $dateLabel = $date ? date('F j, Y', strtotime($date)) : '—';
                            $statusClass = strtolower($status);
                            if ($statusClass === 'pending' || $statusClass === 'confirmed') $statusClass = 'completed';
                        ?>
                        <tr class="history-row" data-page="<?= floor($i / 5) + 1 ?>" <?= $i >= 5 ? 'style="display:none"' : '' ?>>
                            <td><?= htmlspecialchars($dateLabel) ?></td>
                            <td class="game-cell">Table <?= htmlspecialchars((string)$tableNum) ?> Booking</td>
                            <td><?= htmlspecialchars((string)$tableNum) ?></td>
                            <td><?= htmlspecialchars((string)$people) ?> Players</td>
                            <td><span class="status-pill <?= $statusClass ?>"><?= ucfirst($statusClass) ?></span></td>
                            <td>
                                <div class="action-cell">
                                    <a href="<?= $baseUrl ?>/reservations/<?= htmlspecialchars((string)$resId) ?>" class="btn-rate">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                                        View
                                    </a>
                                    <button class="btn-menu" title="More options">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="history-footer">
                    <span id="historyCount">Showing <?= min(5, count($history)) ?> of <?= count($history) ?> past reservations</span>
                    <div class="pagination-btns">
                        <button class="btn-page" id="btnPrev" onclick="historyPage(-1)" disabled>Previous</button>
                        <button class="btn-page" id="btnNext" onclick="historyPage(1)" <?= count($history) <= 5 ? 'disabled' : '' ?>>Next</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- PROMO BANNER -->
    <div class="promo-banner">
        <div class="promo-left">
            <div class="promo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="promo-title">Looking for something new?</div>
                <p class="promo-sub">Our library just added 12 new strategic games. Check out the latest arrivals and book your next adventure.</p>
            </div>
        </div>
        <a href="<?= $baseUrl ?>/dashboard" class="btn-explore">
            Explore Catalogue
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

</div>

<!-- FOOTER -->
<footer class="page-footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <div class="footer-brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                    </div>
                    TableTop Hub
                </div>
                <p class="footer-desc">The ultimate destination for tabletop enthusiasts. Book tables, track sessions, and discover your next favorite board game.</p>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Platform</div>
                <a href="<?= $baseUrl ?>/dashboard">Browse Games</a>
                <a href="<?= $baseUrl ?>/reservations">My Reservations</a>
                <a href="<?= $baseUrl ?>/reservations">Gaming History</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Support</div>
                <a href="#">Help Center</a>
                <a href="#">Rules &amp; Conduct</a>
                <a href="#">Contact Us</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Connect</div>
                <div class="footer-social">
                    <a href="#" class="social-btn" title="Facebook">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" class="social-btn" title="Twitter">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                    </a>
                    <a href="#" class="social-btn" title="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="#" class="social-btn" title="YouTube">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
                    </a>
                    <a href="#" class="social-btn" title="Discord">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© <?= date('Y') ?> TableTop Hub. All rights reserved.</span>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

<script>
var historyCurrentPage = 1;
var historyPerPage = 5;
var historyRows = document.querySelectorAll('.history-row');
var historyTotal = historyRows.length;
var historyTotalPages = Math.ceil(historyTotal / historyPerPage);

function historyPage(dir) {
    historyCurrentPage = Math.max(1, Math.min(historyTotalPages, historyCurrentPage + dir));
    historyRows.forEach(function(row, i) {
        var page = Math.floor(i / historyPerPage) + 1;
        row.style.display = page === historyCurrentPage ? '' : 'none';
    });
    var start = (historyCurrentPage - 1) * historyPerPage + 1;
    var end = Math.min(historyCurrentPage * historyPerPage, historyTotal);
    var countEl = document.getElementById('historyCount');
    if (countEl) countEl.textContent = 'Showing ' + start + '–' + end + ' of ' + historyTotal + ' past reservations';
    document.getElementById('btnPrev').disabled = historyCurrentPage === 1;
    document.getElementById('btnNext').disabled = historyCurrentPage === historyTotalPages;
}
</script>

</body>
</html>
