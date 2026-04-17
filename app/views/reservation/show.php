<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$baseUrl}/login");
    exit;
}

if (!$reservation) {
    header("Location: {$baseUrl}/reservations");
    exit;
}

$status      = $reservation['status'] ?? 'pending';
$statusClass = strtolower($status);
$tableNum    = $reservation['table_number'] ?? $reservation['table_id'] ?? '—';
$resId       = $reservation['id'] ?? '';
$date        = $reservation['reservation_date'] ?? '';
$time        = $reservation['reservation_time'] ?? '';
$people      = $reservation['number_of_people'] ?? '—';
$client      = $reservation['client_name'] ?? '—';
$phone       = $reservation['phone'] ?? '—';
$createdAt   = $reservation['created_at'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation #<?= str_pad($resId, 5, '0', STR_PAD_LEFT) ?> – TableTop Hub</title>
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
        .container { max-width: 900px; margin: 0 auto; padding: 0 24px; }

        /* ── HERO ── */
        .page-hero {
            position: relative; overflow: hidden;
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
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(99,102,241,.22), transparent 70%);
            top: -100px; right: -40px;
        }
        .hero-orb-2 {
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(236,72,153,.15), transparent 70%);
            bottom: -50px; left: 4%;
        }
        .hero-inner { position: relative; z-index: 1; }

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: .85rem; color: var(--text-muted); font-weight: 500; margin-bottom: 20px;
            animation: fadeInLeft .4s ease both;
        }
        .breadcrumb a { color: var(--text-light); display: flex; align-items: center; gap: 5px; transition: var(--transition); }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb .current { color: var(--text-main); font-weight: 700; }

        .hero-title-row {
            display: flex; align-items: center; gap: 18px;
            animation: fadeInUp .5s .05s ease both; opacity: 0;
        }
        .hero-icon {
            width: 58px; height: 58px;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; box-shadow: 0 8px 24px rgba(99,102,241,.35); flex-shrink: 0;
            animation: bounce-in .7s .1s cubic-bezier(.34,1.56,.64,1) both;
        }
        .hero-icon svg { width: 28px; height: 28px; }
        .hero-title { font-size: 1.9rem; font-weight: 900; letter-spacing: -.8px; }
        .hero-title .grad {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 60%, var(--accent) 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            animation: grad-shift 4s ease infinite;
        }
        @keyframes grad-shift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .hero-sub { color: var(--text-muted); font-size: .9rem; margin-top: 4px; }

        /* ── MAIN LAYOUT ── */
        .main-wrap {
            padding-bottom: 80px;
            display: flex; flex-direction: column; gap: 24px;
        }

        /* ── STATUS BANNER ── */
        .status-banner {
            border-radius: var(--card-radius);
            padding: 20px 26px;
            display: flex; align-items: center; gap: 16px;
            animation: fadeInUp .5s .1s ease both; opacity: 0;
        }
        .status-banner.pending   { background: var(--amber-light); border: 1.5px solid rgba(245,158,11,.3); }
        .status-banner.confirmed { background: var(--green-light);  border: 1.5px solid rgba(16,185,129,.3); }
        .status-banner.cancelled { background: var(--red-light);    border: 1.5px solid rgba(239,68,68,.3); }

        .sb-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .status-banner.pending   .sb-icon { background: rgba(245,158,11,.2); }
        .status-banner.confirmed .sb-icon { background: rgba(16,185,129,.2); }
        .status-banner.cancelled .sb-icon { background: rgba(239,68,68,.2); }
        .sb-icon svg { width: 24px; height: 24px; }
        .status-banner.pending   .sb-icon svg { color: var(--amber); }
        .status-banner.confirmed .sb-icon svg { color: var(--green); }
        .status-banner.cancelled .sb-icon svg { color: var(--red); }

        .sb-texts { flex: 1; }
        .sb-title { font-size: 1rem; font-weight: 800; }
        .status-banner.pending   .sb-title { color: var(--amber); }
        .status-banner.confirmed .sb-title { color: var(--green); }
        .status-banner.cancelled .sb-title { color: var(--red); }
        .sb-sub { font-size: .82rem; color: var(--text-muted); margin-top: 2px; }

        .sb-badge {
            font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .6px;
            padding: 5px 14px; border-radius: 100px;
        }
        .status-banner.pending   .sb-badge { background: rgba(245,158,11,.2); color: var(--amber); }
        .status-banner.confirmed .sb-badge { background: rgba(16,185,129,.2); color: var(--green); }
        .status-banner.cancelled .sb-badge { background: rgba(239,68,68,.2);  color: var(--red); }

        /* ── CARD ── */
        .detail-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: fadeInUp .5s ease both; opacity: 0;
            position: relative;
        }
        .detail-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent2), var(--accent));
        }
        .detail-card:nth-of-type(1) { animation-delay: .15s; }
        .detail-card:nth-of-type(2) { animation-delay: .25s; }

        .card-head {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-gray);
        }
        [data-theme="dark"] .card-head { background: rgba(255,255,255,.02); }
        .card-head-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--primary-light);
            display: flex; align-items: center; justify-content: center;
        }
        .card-head-icon svg { width: 18px; height: 18px; color: var(--primary); }
        .card-head-title { font-size: .95rem; font-weight: 800; }
        .card-head-sub   { font-size: .75rem; color: var(--text-muted); margin-top: 1px; }

        /* Details grid inside card */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0;
        }
        @media (max-width: 600px) { .details-grid { grid-template-columns: 1fr; } }

        .detail-cell {
            padding: 20px 24px;
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            transition: var(--transition);
        }
        .detail-cell:nth-child(even) { border-right: none; }
        .detail-cell:nth-last-child(-n+2) { border-bottom: none; }
        @media (max-width: 600px) {
            .detail-cell { border-right: none; }
            .detail-cell:last-child { border-bottom: none; }
        }
        .detail-cell:hover { background: var(--bg-gray); }

        .dc-label {
            display: flex; align-items: center; gap: 6px;
            font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
            color: var(--text-light); margin-bottom: 8px;
        }
        .dc-label svg { width: 13px; height: 13px; color: var(--primary); }
        .dc-val { font-size: 1.05rem; font-weight: 700; color: var(--text-main); }
        .dc-val-sub { font-size: .78rem; color: var(--text-muted); margin-top: 2px; }

        /* ── HIGHLIGHT ROW ── */
        .highlight-row {
            display: flex; gap: 16px; flex-wrap: wrap;
        }
        .highlight-chip {
            display: flex; align-items: center; gap: 10px;
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: 14px; padding: 14px 20px;
            flex: 1; min-width: 160px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            animation: fadeInUp .5s ease both; opacity: 0;
        }
        .highlight-chip:nth-child(1) { animation-delay: .2s; }
        .highlight-chip:nth-child(2) { animation-delay: .28s; }
        .highlight-chip:nth-child(3) { animation-delay: .36s; }
        .highlight-chip:hover { border-color: var(--primary); box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .hc-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(99,102,241,.25);
        }
        .hc-icon svg { width: 18px; height: 18px; color: #fff; }
        .hc-label { font-size: .7rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; letter-spacing: .6px; }
        .hc-val { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-top: 2px; }

        /* ── ACTIONS ── */
        .actions-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            padding: 24px 28px;
            display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
            box-shadow: var(--shadow-sm);
            animation: fadeInUp .5s .35s ease both; opacity: 0;
        }

        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--bg-gray);
            color: var(--text-main);
            border: 1.5px solid var(--border-color);
            border-radius: 12px; padding: 12px 22px;
            font-size: .88rem; font-weight: 700; cursor: pointer;
            transition: var(--transition); font-family: inherit;
        }
        .btn-back:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
        .btn-back svg { width: 16px; height: 16px; }

        .btn-cancel {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--red-light);
            color: var(--red);
            border: 1.5px solid rgba(239,68,68,.25);
            border-radius: 12px; padding: 12px 22px;
            font-size: .88rem; font-weight: 700; cursor: pointer;
            transition: var(--transition); font-family: inherit;
        }
        .btn-cancel:hover { background: var(--red); color: #fff; border-color: var(--red); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(239,68,68,.3); }
        .btn-cancel svg { width: 16px; height: 16px; }

        .actions-note {
            flex: 1; font-size: .78rem; color: var(--text-light);
            display: flex; align-items: center; gap: 6px;
        }
        .actions-note svg { width: 14px; height: 14px; color: var(--amber); flex-shrink: 0; }

        /* ── CANCEL MODAL ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.6); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
            z-index: 9999; opacity: 0; visibility: hidden;
            transition: all .3s ease;
        }
        .modal-overlay.show { opacity: 1; visibility: visible; }

        .modal-box {
            background: var(--bg-card);
            border-radius: 24px; padding: 40px 36px;
            max-width: 380px; width: 90%;
            box-shadow: 0 32px 64px rgba(0,0,0,.25);
            border: 1.5px solid var(--border-color);
            text-align: center;
            transform: scale(.85);
            transition: transform .4s cubic-bezier(.34,1.56,.64,1);
        }
        .modal-overlay.show .modal-box { transform: scale(1); }

        .modal-icon-wrap {
            width: 68px; height: 68px;
            background: var(--red-light);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .modal-icon-wrap svg { width: 32px; height: 32px; color: var(--red); }
        .modal-title { font-size: 1.3rem; font-weight: 900; margin-bottom: 10px; }
        .modal-sub { font-size: .88rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 28px; }
        .modal-actions { display: flex; gap: 12px; }
        .modal-btn-keep {
            flex: 1; padding: 12px; border-radius: 12px;
            background: var(--bg-gray); color: var(--text-main);
            border: 1.5px solid var(--border-color);
            font-size: .88rem; font-weight: 700; cursor: pointer;
            transition: var(--transition); font-family: inherit;
        }
        .modal-btn-keep:hover { border-color: var(--primary); color: var(--primary); }
        .modal-btn-cancel {
            flex: 1; padding: 12px; border-radius: 12px;
            background: var(--red); color: #fff; border: none;
            font-size: .88rem; font-weight: 700; cursor: pointer;
            transition: var(--transition); font-family: inherit;
            box-shadow: 0 4px 14px rgba(239,68,68,.3);
        }
        .modal-btn-cancel:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(239,68,68,.4); }

        /* ── FOOTER ── */
        .page-footer { background: #13131f; color: #9ca3af; padding: 40px 0 20px; margin-top: 20px; }
        [data-theme="dark"] .page-footer { background: #09090f; border-top: 1px solid #1e1e36; }
        .footer-inner { max-width: 1220px; margin: 0 auto; padding: 0 24px; text-align: center; font-size: .8rem; }

        /* ── ANIMATIONS ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-16px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes bounce-in {
            0% { transform: scale(.6); opacity: 0; }
            60% { transform: scale(1.1); }
            80% { transform: scale(.95); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- CANCEL MODAL -->
<div class="modal-overlay" id="cancelModal">
    <div class="modal-box">
        <div class="modal-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <div class="modal-title">Cancel Reservation?</div>
        <p class="modal-sub">This will permanently cancel your booking for Table <?= htmlspecialchars((string)$tableNum) ?>. This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="modal-btn-keep" onclick="closeModal()">Keep Booking</button>
            <form action="<?= $baseUrl ?>/reservations/<?= $resId ?>/cancel" method="POST" style="flex:1">
                <button type="submit" class="modal-btn-cancel" style="width:100%">Yes, Cancel</button>
            </form>
        </div>
    </div>
</div>

<!-- HERO -->
<section class="page-hero">
    <div class="page-hero-bg"></div>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="container hero-inner">
        <div class="breadcrumb">
            <a href="<?= $baseUrl ?>/dashboard">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Home
            </a>
            <span style="color:var(--text-light)">›</span>
            <a href="<?= $baseUrl ?>/reservations">My Reservations</a>
            <span style="color:var(--text-light)">›</span>
            <span class="current">#<?= str_pad($resId, 5, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="hero-title-row">
            <div class="hero-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div>
                <h1 class="hero-title">Booking <span class="grad">#<?= str_pad($resId, 5, '0', STR_PAD_LEFT) ?></span></h1>
                <p class="hero-sub">Table <?= htmlspecialchars((string)$tableNum) ?> · <?= $date ? date('M d, Y', strtotime($date)) : '—' ?><?= $time ? ' at ' . htmlspecialchars($time) : '' ?></p>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="main-wrap">

        <!-- STATUS BANNER -->
        <div class="status-banner <?= $statusClass ?>">
            <div class="sb-icon">
                <?php if ($statusClass === 'confirmed'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <?php elseif ($statusClass === 'pending'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <?php endif; ?>
            </div>
            <div class="sb-texts">
                <div class="sb-title">
                    <?php if ($statusClass === 'confirmed'): ?>Reservation Confirmed
                    <?php elseif ($statusClass === 'pending'): ?>Awaiting Confirmation
                    <?php else: ?>Reservation Cancelled<?php endif; ?>
                </div>
                <div class="sb-sub">
                    <?php if ($statusClass === 'confirmed'): ?>Your table is secured. See you at the café!
                    <?php elseif ($statusClass === 'pending'): ?>Your booking is being reviewed by our team.
                    <?php else: ?>This reservation has been cancelled.<?php endif; ?>
                </div>
            </div>
            <span class="sb-badge"><?= ucfirst($statusClass) ?></span>
        </div>

        <!-- HIGHLIGHT CHIPS -->
        <div class="highlight-row">
            <div class="highlight-chip">
                <div class="hc-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <div>
                    <div class="hc-label">Date</div>
                    <div class="hc-val"><?= $date ? date('M d, Y', strtotime($date)) : '—' ?></div>
                </div>
            </div>
            <div class="highlight-chip">
                <div class="hc-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <div class="hc-label">Time</div>
                    <div class="hc-val"><?= htmlspecialchars($time) ?></div>
                </div>
            </div>
            <div class="highlight-chip">
                <div class="hc-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="hc-label">Party Size</div>
                    <div class="hc-val"><?= htmlspecialchars((string)$people) ?> Guests</div>
                </div>
            </div>
        </div>

        <!-- DETAILS CARD -->
        <div class="detail-card">
            <div class="card-head">
                <div class="card-head-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="card-head-title">Booking Details</div>
                    <div class="card-head-sub">All information about this reservation</div>
                </div>
            </div>
            <div class="details-grid">
                <div class="detail-cell">
                    <div class="dc-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h18"/><path d="M5 10v7"/><path d="M19 10v7"/><path d="M2 10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2"/></svg>
                        Table
                    </div>
                    <div class="dc-val">Table <?= htmlspecialchars((string)$tableNum) ?></div>
                    <div class="dc-val-sub">Assigned table</div>
                </div>
                <div class="detail-cell">
                    <div class="dc-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Date
                    </div>
                    <div class="dc-val"><?= $date ? date('l, M d, Y', strtotime($date)) : '—' ?></div>
                    <div class="dc-val-sub">Reservation date</div>
                </div>
                <div class="detail-cell">
                    <div class="dc-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Time
                    </div>
                    <div class="dc-val"><?= htmlspecialchars($time) ?></div>
                    <div class="dc-val-sub">Session start time</div>
                </div>
                <div class="detail-cell">
                    <div class="dc-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        Party Size
                    </div>
                    <div class="dc-val"><?= htmlspecialchars((string)$people) ?> Guests</div>
                    <div class="dc-val-sub">Number of players</div>
                </div>
                <div class="detail-cell">
                    <div class="dc-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Client Name
                    </div>
                    <div class="dc-val"><?= htmlspecialchars($client) ?></div>
                    <div class="dc-val-sub">Account holder</div>
                </div>
                <div class="detail-cell">
                    <div class="dc-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.56 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Phone
                    </div>
                    <div class="dc-val"><?= htmlspecialchars($phone) ?></div>
                    <div class="dc-val-sub">Contact number</div>
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="actions-card">
            <a href="<?= $baseUrl ?>/reservations" class="btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
                Back to Reservations
            </a>
            <?php if ($statusClass !== 'cancelled'): ?>
                <button type="button" class="btn-cancel" onclick="openModal()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    Cancel Booking
                </button>
            <?php endif; ?>
            <div class="actions-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <?php if ($statusClass !== 'cancelled'): ?>
                    Cancellations are free up to 2 hours before your session.
                <?php else: ?>
                    This reservation has been cancelled.
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<footer class="page-footer">
    <div class="footer-inner">
        <span>© <?= date('Y') ?> TableTop Hub. All rights reserved.</span>
    </div>
</footer>

<script>
function openModal()  { document.getElementById('cancelModal').classList.add('show'); }
function closeModal() { document.getElementById('cancelModal').classList.remove('show'); }
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</body>
</html>
