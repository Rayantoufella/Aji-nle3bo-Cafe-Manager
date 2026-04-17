<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

// Si l'utilisateur n'est pas connecté, redirige vers login
if (!isset($_SESSION['user_id'])) {
    header("Location: {$baseUrl}/login");
    exit;
}

// Info du jeu (passée par le controller, sinon valeurs par défaut)
$game = $game ?? [
    'id'            => 1,
    'name'          => 'Catan',
    'category_name' => 'Strategy',
    'nb_players'    => '3-4',
];

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

// Créneaux horaires disponibles
$timeSlots = ['10:00 AM', '12:00 PM', '02:00 PM', '04:00 PM', '06:00 PM', '08:00 PM', '10:00 PM'];

// Tables (normalement vient de la base de données via le controller)
$tables = $tables ?? [
    ['id' => 1, 'name' => 'Table 1',    'capacity' => 4, 'status' => 'available'],
    ['id' => 2, 'name' => 'Table 2',    'capacity' => 4, 'status' => 'available'],
    ['id' => 3, 'name' => 'Table 3',    'capacity' => 6, 'status' => 'available'],
    ['id' => 4, 'name' => 'The Booth',  'capacity' => 2, 'status' => 'available'],
    ['id' => 5, 'name' => 'Table 5',    'capacity' => 4, 'status' => 'available'],
    ['id' => 6, 'name' => 'VIP Lounge', 'capacity' => 8, 'status' => 'available'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve a Table – TableTop Hub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ==================== VARIABLES ==================== */
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
            --border-light:  #f3f4f6;
            --green:         #10b981;
            --green-light:   #d1fae5;
            --red:           #ef4444;
            --red-light:     #fee2e2;
            --card-radius:   16px;
            --transition:    all 0.3s cubic-bezier(.4,0,.2,1);
            --shadow-sm:     0 2px 8px rgba(0,0,0,0.06);
            --shadow-md:     0 8px 24px rgba(0,0,0,0.09);
            --shadow-lg:     0 16px 48px rgba(99,102,241,0.18);
            --glow:          0 0 0 0 transparent;
        }

        [data-theme="dark"] {
            --primary:       #818cf8;
            --primary-dark:  #6366f1;
            --primary-light: rgba(99,102,241,0.18);
            --accent:        #fbbf24;
            --accent2:       #f472b6;
            --text-main:     #f1f5f9;
            --text-muted:    #94a3b8;
            --text-light:    #64748b;
            --bg-body:       #0d0d1a;
            --bg-gray:       #13131f;
            --bg-card:       #1a1a2e;
            --bg-nav:        rgba(13,13,26,0.85);
            --border-color:  #252540;
            --border-light:  #1e1e36;
            --shadow-sm:     0 2px 8px rgba(0,0,0,0.3);
            --shadow-md:     0 8px 24px rgba(0,0,0,0.4);
            --shadow-lg:     0 16px 48px rgba(99,102,241,0.25);
            --glow:          0 0 20px rgba(129,140,248,0.15);
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

        /* ==================== PAGE HERO / HEADER ==================== */
        .page-hero {
            position: relative;
            overflow: hidden;
            padding: 40px 0 32px;
        }

        .page-hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(99,102,241,0.07) 0%, rgba(168,85,247,0.05) 50%, rgba(236,72,153,0.04) 100%);
            z-index: 0;
        }

        [data-theme="dark"] .page-hero-bg {
            background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(168,85,247,0.08) 50%, rgba(236,72,153,0.06) 100%);
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        .hero-orb-1 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(99,102,241,0.25), transparent 70%);
            top: -120px; right: -80px;
        }

        .hero-orb-2 {
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(236,72,153,0.18), transparent 70%);
            bottom: -60px; left: 5%;
        }

        .page-hero-inner {
            position: relative;
            z-index: 1;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 20px;
        }
        .breadcrumb a {
            color: var(--text-light);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
        }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb .current { color: var(--text-main); font-weight: 700; }
        .breadcrumb .sep { color: var(--text-light); }

        /* Page Title */
        .page-title-row {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .page-title-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 100%);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
            flex-shrink: 0;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 900;
            letter-spacing: -0.8px;
            line-height: 1.2;
        }

        .page-title .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 60%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 4px;
        }

        /* ==================== MAIN LAYOUT ==================== */
        .main-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 36px;
            align-items: start;
            padding-bottom: 80px;
        }

        @media (max-width: 1024px) {
            .main-layout { grid-template-columns: 1fr; }
        }

        /* ==================== STEP SECTIONS ==================== */
        .step-section {
            margin-bottom: 28px;
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .step-number {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            color: #fff;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            font-weight: 800;
            box-shadow: 0 4px 14px rgba(99,102,241,0.35);
            flex-shrink: 0;
        }

        .step-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.3px;
        }

        .step-badge {
            margin-left: auto;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--primary);
            background: var(--primary-light);
            padding: 4px 12px;
            border-radius: 100px;
            border: 1px solid rgba(99,102,241,0.2);
        }

        /* ==================== CARDS ==================== */
        .card {
            background: var(--bg-card);
            border: 1.5px solid var(--border-color);
            border-radius: var(--card-radius);
            padding: 28px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent2), var(--accent));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover { box-shadow: var(--shadow-md); }
        .card:hover::before { opacity: 1; }

        .card-gray {
            background: var(--bg-gray);
            border-radius: var(--card-radius) var(--card-radius) 0 0;
            border-bottom: none;
        }

        .section-label {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-label svg { width: 14px; height: 14px; }

        /* ==================== STEP 1: PREFERENCES ==================== */
        .preferences-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
        }

        @media (max-width: 768px) {
            .preferences-grid { grid-template-columns: 1fr; }
        }

        /* Board Game Card */
        .game-card-display {
            background: linear-gradient(145deg, var(--bg-gray), var(--bg-card));
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
            cursor: default;
        }

        .game-card-display:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 24px rgba(99,102,241,0.12), var(--glow);
            transform: translateY(-2px);
        }

        .game-thumb {
            width: 80px; height: 80px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .game-thumb img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        .game-thumb-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, transparent 60%);
        }

        .game-meta { flex: 1; min-width: 0; }

        .game-meta-name {
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--text-main);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .game-meta-badge {
            background: linear-gradient(135deg, var(--primary), #818cf8);
            color: #fff;
            font-size: 0.62rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(99,102,241,0.3);
        }

        .game-meta-info {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .game-meta-info span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .game-meta-info svg {
            width: 14px; height: 14px;
            color: var(--primary);
        }

        /* Party Size */
        .party-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .party-btn {
            border: 1.5px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-main);
            border-radius: 10px;
            padding: 11px 0;
            text-align: center;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .party-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.1);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .party-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .party-btn.active {
            background: linear-gradient(135deg, var(--primary), #818cf8);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(99,102,241,0.35);
            transform: translateY(-2px);
        }

        .party-btn.active:hover::after { opacity: 1; }

        .party-note {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 14px;
            padding: 8px 12px;
            background: var(--bg-gray);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .party-note svg {
            width: 14px; height: 14px;
            color: var(--accent);
            flex-shrink: 0;
        }

        /* ==================== STEP 2: DATE & TIME ==================== */
        .datetime-grid {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 28px;
            align-items: start;
        }

        @media (max-width: 768px) {
            .datetime-grid { grid-template-columns: 1fr; }
        }

        .date-input-wrapper {
            background: linear-gradient(145deg, var(--bg-gray), var(--bg-card));
            border: 1.5px solid var(--border-color);
            border-radius: 14px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: var(--transition);
        }

        .date-input-wrapper:hover,
        .date-input-wrapper:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .date-input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: inherit;
            background: transparent;
            color: var(--text-main);
            cursor: pointer;
            outline: none;
            font-weight: 600;
        }

        .date-icon {
            position: absolute;
            left: 28px;
            color: var(--primary);
            pointer-events: none;
        }

        /* Time Slots */
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        @media (max-width: 768px) {
            .slots-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .slot-btn {
            border: 1.5px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-main);
            border-radius: 12px;
            padding: 14px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .slot-btn svg { width: 16px; height: 16px; color: var(--text-light); transition: var(--transition); }

        .slot-btn:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .slot-btn:hover svg { color: var(--primary); }

        .slot-btn.active {
            background: linear-gradient(135deg, var(--primary), #818cf8);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(99,102,241,0.35);
            transform: translateY(-2px);
        }

        .slot-btn.active svg { color: white; }

        /* ==================== STEP 3: CHOOSE TABLE ==================== */
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            padding-bottom: 16px;
        }

        @media (max-width: 768px) {
            .tables-grid { grid-template-columns: repeat(2, 1fr); }
        }

        .table-card {
            border: 1.5px solid var(--border-color);
            background: var(--bg-card);
            border-radius: 14px;
            padding: 22px 16px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
        }

        .table-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent2));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .table-card:hover:not(.occupied) {
            border-color: var(--primary);
            box-shadow: var(--shadow-md), var(--glow);
            transform: translateY(-4px);
        }

        .table-card:hover:not(.occupied)::before { opacity: 1; }

        .table-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 8px 24px rgba(99,102,241,0.2);
        }

        .table-card.selected::before { opacity: 1; }

        .table-card.occupied {
            opacity: 0.4;
            cursor: not-allowed;
            background: var(--bg-gray);
        }

        .table-icon-wrap {
            width: 44px; height: 44px;
            background: var(--primary-light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            transition: var(--transition);
        }

        .table-card:hover:not(.occupied) .table-icon-wrap {
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            box-shadow: 0 4px 12px rgba(99,102,241,0.3);
        }

        .table-card:hover:not(.occupied) .table-icon-wrap svg { color: white; }
        .table-card.selected .table-icon-wrap {
            background: linear-gradient(135deg, var(--primary), var(--accent2));
        }
        .table-card.selected .table-icon-wrap svg { color: white; }

        .table-icon-wrap svg {
            width: 22px; height: 22px;
            color: var(--primary);
            transition: var(--transition);
        }

        .table-card.occupied .table-icon-wrap { background: var(--border-color); }
        .table-card.occupied .table-icon-wrap svg { color: var(--text-light); }

        .table-name { font-weight: 700; font-size: 0.9rem; color: var(--text-main); }
        .table-seats {
            font-size: 0.68rem;
            color: var(--text-light);
            font-weight: 700;
            margin-top: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-corner {
            position: absolute;
            top: 8px; right: 10px;
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 2px 8px;
            border-radius: 100px;
        }

        .table-card:not(.occupied):not(.selected) .status-corner {
            color: var(--green);
            background: var(--green-light);
        }

        .table-card.selected .status-corner {
            color: var(--primary);
            background: var(--primary-light);
        }

        .table-card.occupied .status-corner {
            color: var(--red);
            background: var(--red-light);
        }

        /* Entrance */
        .main-entrance-wrapper {
            display: flex;
            justify-content: center;
            padding: 12px 0 16px;
        }

        .main-entrance {
            background: linear-gradient(135deg, var(--primary-light), rgba(168,85,247,0.1));
            color: var(--primary);
            font-size: 0.68rem;
            font-weight: 800;
            padding: 6px 20px;
            border-radius: 100px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid rgba(99,102,241,0.2);
        }

        /* Selected Table Footer */
        .selected-table-footer {
            border: 1.5px solid var(--border-color);
            background: var(--bg-card);
            border-radius: 0 0 var(--card-radius) var(--card-radius);
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-top: none;
            box-shadow: var(--shadow-sm);
        }

        .selected-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary-light), rgba(99,102,241,0.1));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
        }

        .selected-info { flex: 1; line-height: 1.4; }
        .selected-info-label {
            font-size: 0.65rem;
            font-weight: 800;
            color: var(--text-light);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .selected-info-val {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-main);
        }
        .selected-note {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: right;
            max-width: 260px;
            line-height: 1.5;
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar { position: sticky; top: 24px; }

        .summary-card {
            background: var(--bg-card);
            border-radius: var(--card-radius);
            padding: 28px;
            border: 1.5px solid var(--border-color);
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent2), var(--accent));
        }

        .summary-title {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.3px;
        }

        .summary-title svg { color: var(--primary); width: 20px; height: 20px; }

        .summary-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            font-size: 0.88rem;
            padding: 10px 14px;
            background: var(--bg-gray);
            border-radius: 10px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .summary-row:hover { border-color: var(--primary); }

        .summary-row-icon {
            width: 32px; height: 32px;
            background: var(--primary-light);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .summary-row-icon svg { width: 16px; height: 16px; color: var(--primary); }

        .summary-row-label { color: var(--text-muted); flex: 1; font-weight: 500; }
        .summary-row-value { font-weight: 700; color: var(--text-main); }

        .summary-divider {
            height: 1px;
            background: var(--border-color);
            margin: 20px 0;
        }

        .summary-total {
            background: linear-gradient(145deg, var(--bg-gray), var(--bg-card));
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }

        .summary-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-total-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .summary-total-price {
            font-size: 1.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-confirm {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 24px rgba(99,102,241,0.35);
            position: relative;
            overflow: hidden;
        }

        .btn-confirm::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 200%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }

        .btn-confirm:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(99,102,241,0.45);
        }

        .btn-confirm:hover::before { left: 100%; }

        .btn-confirm svg { width: 18px; height: 18px; }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .cancel-link:hover { color: var(--primary); }

        .booking-guarantee {
            text-align: center;
            font-size: 0.68rem;
            color: var(--text-light);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .booking-guarantee svg { width: 14px; height: 14px; color: var(--green); }

        /* ==================== FLASH MESSAGE ==================== */
        .flash-error {
            background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(239,68,68,0.04));
            color: var(--red);
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.88rem;
            font-weight: 600;
            border: 1.5px solid rgba(239,68,68,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-error svg { width: 18px; height: 18px; flex-shrink: 0; }

        /* ==================== DARK MODE ADJUSTMENTS ==================== */
        [data-theme="dark"] .date-input-wrapper { background: var(--bg-card); }
        [data-theme="dark"] .party-btn { background: var(--bg-card); }
        [data-theme="dark"] .slot-btn { background: var(--bg-card); }
        [data-theme="dark"] .table-card { background: var(--bg-card); }
        [data-theme="dark"] .game-card-display { background: linear-gradient(145deg, var(--bg-card), var(--bg-gray)); }
        [data-theme="dark"] .main-entrance { background: var(--border-color); color: var(--text-muted); border-color: var(--border-color); }
        [data-theme="dark"] input::-webkit-calendar-picker-indicator { filter: invert(1); }
        [data-theme="dark"] .table-card.occupied { opacity: 0.3; }

        /* ==================== PROGRESS TRACKER ==================== */
        .progress-tracker {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin: 28px 0 36px;
            padding: 0 8px;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
            z-index: 1;
        }

        .progress-dot {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            font-weight: 800;
            transition: all 0.5s cubic-bezier(.34,1.56,.64,1);
            border: 2px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-light);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .progress-step.active .progress-dot {
            background: linear-gradient(135deg, var(--primary), var(--accent2));
            border-color: transparent;
            color: #fff;
            box-shadow: 0 6px 20px rgba(99,102,241,0.4);
            transform: scale(1.15);
        }

        .progress-step.done .progress-dot {
            background: linear-gradient(135deg, var(--green), #34d399);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }

        .progress-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-light);
            transition: color 0.3s;
            white-space: nowrap;
        }

        .progress-step.active .progress-label { color: var(--primary); }
        .progress-step.done .progress-label { color: var(--green); }

        .progress-line {
            flex: 1;
            height: 2px;
            background: var(--border-color);
            margin: 0 12px;
            margin-bottom: 26px;
            border-radius: 1px;
            overflow: hidden;
            position: relative;
        }

        .progress-line-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent2));
            width: 0%;
            transition: width 0.6s cubic-bezier(.4,0,.2,1);
            border-radius: 1px;
        }

        /* ==================== FLOATING PARTICLES ==================== */
        .particles-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: float-particle linear infinite;
        }

        @keyframes float-particle {
            0% { transform: translateY(100%) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-120px) scale(1); opacity: 0; }
        }

        /* Dice floating animation */
        .hero-dice {
            position: absolute;
            right: 6%;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
            opacity: 0.08;
            pointer-events: none;
            animation: hero-dice-spin 12s linear infinite;
        }

        [data-theme="dark"] .hero-dice { opacity: 0.12; }

        @keyframes hero-dice-spin {
            0% { transform: translateY(-50%) rotate(0deg) scale(1); }
            50% { transform: translateY(calc(-50% - 10px)) rotate(180deg) scale(1.05); }
            100% { transform: translateY(-50%) rotate(360deg) scale(1); }
        }

        @media (max-width: 768px) { .hero-dice { display: none; } }

        /* ==================== ANIMATIONS ==================== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 8px 24px rgba(99,102,241,0.35); }
            50% { box-shadow: 0 8px 32px rgba(99,102,241,0.65), 0 0 0 8px rgba(99,102,241,0.08); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes bounce-in {
            0% { transform: scale(0.6); opacity: 0; }
            60% { transform: scale(1.1); }
            80% { transform: scale(0.95); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes ripple-effect {
            0% { transform: scale(0); opacity: 0.6; }
            100% { transform: scale(4); opacity: 0; }
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes confetti-fall {
            0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }

        @keyframes checkmark-draw {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }

        .step-section {
            animation: fadeInUp 0.65s cubic-bezier(.4,0,.2,1) both;
            opacity: 0;
        }
        .step-section:nth-child(1) { animation-delay: 0.05s; }
        .step-section:nth-child(2) { animation-delay: 0.18s; }
        .step-section:nth-child(3) { animation-delay: 0.31s; }

        .sidebar { animation: slideInRight 0.65s 0.25s cubic-bezier(.4,0,.2,1) both; }

        /* Pulse on confirm button */
        .btn-confirm {
            animation: pulse-glow 2.5s ease-in-out infinite;
        }

        .btn-confirm:hover {
            animation: none;
        }

        /* Shimmer on summary card top bar */
        .summary-card::before {
            background: linear-gradient(90deg, var(--primary), var(--accent2), var(--accent), var(--accent2), var(--primary));
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }

        /* Ripple on buttons */
        .ripple-host { position: relative; overflow: hidden; }
        .ripple-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
            pointer-events: none;
            animation: ripple-effect 0.55s ease-out forwards;
        }

        /* Active party-btn bounce */
        .party-btn.active { animation: bounce-in 0.35s cubic-bezier(.34,1.56,.64,1) forwards; }

        /* Active slot-btn bounce */
        .slot-btn.active { animation: bounce-in 0.35s cubic-bezier(.34,1.56,.64,1) forwards; }

        /* Selected table card */
        .table-card.selected {
            animation: bounce-in 0.35s cubic-bezier(.34,1.56,.64,1) forwards;
        }

        /* Animated gradient text on page title */
        .page-title .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent2) 50%, var(--accent) 100%);
            background-size: 200% 200%;
            animation: gradient-shift 4s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Step number pulse when entering view */
        .step-number {
            animation: pulse-glow 3s ease-in-out infinite;
        }

        /* Summary rows animate in */
        .summary-row {
            animation: fadeInLeft 0.4s ease both;
        }
        .summary-row:nth-child(1) { animation-delay: 0.4s; }
        .summary-row:nth-child(2) { animation-delay: 0.5s; }
        .summary-row:nth-child(3) { animation-delay: 0.6s; }
        .summary-row:nth-child(4) { animation-delay: 0.7s; }

        /* Value change highlight */
        .val-flash {
            animation: val-highlight 0.5s ease forwards;
        }
        @keyframes val-highlight {
            0% { color: var(--primary); transform: scale(1.15); }
            100% { color: var(--text-main); transform: scale(1); }
        }

        /* Page title icon spin on load */
        .page-title-icon {
            animation: bounce-in 0.7s 0.1s cubic-bezier(.34,1.56,.64,1) both;
        }

        /* Breadcrumb fade */
        .breadcrumb {
            animation: fadeInLeft 0.5s 0.05s ease both;
        }

        /* Table card stagger */
        .table-card:nth-child(1) { animation: fadeInUp 0.5s 0.35s ease both; opacity: 0; }
        .table-card:nth-child(2) { animation: fadeInUp 0.5s 0.42s ease both; opacity: 0; }
        .table-card:nth-child(3) { animation: fadeInUp 0.5s 0.49s ease both; opacity: 0; }
        .table-card:nth-child(4) { animation: fadeInUp 0.5s 0.56s ease both; opacity: 0; }
        .table-card:nth-child(5) { animation: fadeInUp 0.5s 0.63s ease both; opacity: 0; }
        .table-card:nth-child(6) { animation: fadeInUp 0.5s 0.70s ease both; opacity: 0; }

        /* Party buttons stagger */
        .party-btn:nth-child(1)  { animation: fadeInUp 0.4s 0.1s ease both; opacity: 0; }
        .party-btn:nth-child(2)  { animation: fadeInUp 0.4s 0.15s ease both; opacity: 0; }
        .party-btn:nth-child(3)  { animation: fadeInUp 0.4s 0.2s ease both; opacity: 0; }
        .party-btn:nth-child(4)  { animation: fadeInUp 0.4s 0.25s ease both; opacity: 0; }
        .party-btn:nth-child(5)  { animation: fadeInUp 0.4s 0.3s ease both; opacity: 0; }
        .party-btn:nth-child(6)  { animation: fadeInUp 0.4s 0.35s ease both; opacity: 0; }
        .party-btn:nth-child(7)  { animation: fadeInUp 0.4s 0.4s ease both; opacity: 0; }
        .party-btn:nth-child(8)  { animation: fadeInUp 0.4s 0.45s ease both; opacity: 0; }
        .party-btn:nth-child(9)  { animation: fadeInUp 0.4s 0.5s ease both; opacity: 0; }
        .party-btn:nth-child(10) { animation: fadeInUp 0.4s 0.55s ease both; opacity: 0; }

        /* Slot buttons stagger */
        .slot-btn:nth-child(1) { animation: fadeInUp 0.4s 0.2s ease both; opacity: 0; }
        .slot-btn:nth-child(2) { animation: fadeInUp 0.4s 0.25s ease both; opacity: 0; }
        .slot-btn:nth-child(3) { animation: fadeInUp 0.4s 0.3s ease both; opacity: 0; }
        .slot-btn:nth-child(4) { animation: fadeInUp 0.4s 0.35s ease both; opacity: 0; }
        .slot-btn:nth-child(5) { animation: fadeInUp 0.4s 0.4s ease both; opacity: 0; }
        .slot-btn:nth-child(6) { animation: fadeInUp 0.4s 0.45s ease both; opacity: 0; }
        .slot-btn:nth-child(7) { animation: fadeInUp 0.4s 0.5s ease both; opacity: 0; }

        /* Confetti overlay */
        #confetti-canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 9999;
            display: none;
        }

        /* Success overlay */
        .success-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: flex; align-items: center; justify-content: center;
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
            backdrop-filter: blur(8px);
        }

        .success-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .success-box {
            background: var(--bg-card);
            border-radius: 24px;
            padding: 48px 40px;
            text-align: center;
            max-width: 360px;
            width: 90%;
            box-shadow: 0 32px 64px rgba(0,0,0,0.3);
            transform: scale(0.8);
            transition: transform 0.5s cubic-bezier(.34,1.56,.64,1);
            border: 1.5px solid var(--border-color);
        }

        .success-overlay.show .success-box {
            transform: scale(1);
        }

        .success-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--green), #34d399);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 12px 32px rgba(16,185,129,0.4);
        }

        .success-icon svg {
            width: 36px; height: 36px;
            stroke: white;
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: checkmark-draw 0.6s 0.3s ease forwards;
        }

        .success-title {
            font-size: 1.4rem;
            font-weight: 900;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .success-sub {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

    </style>
</head>
<body>

<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>

<!-- CONFETTI CANVAS -->
<canvas id="confetti-canvas"></canvas>

<!-- SUCCESS OVERLAY -->
<div class="success-overlay" id="successOverlay">
    <div class="success-box">
        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <div class="success-title">Reservation Confirmed!</div>
        <div class="success-sub">Your table has been successfully booked. Get ready for an amazing gaming session!</div>
    </div>
</div>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="page-hero-bg"></div>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="particles-container" id="heroParticles"></div>

    <!-- Floating Dice SVG -->
    <svg class="hero-dice" width="220" height="220" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
        <rect x="2" y="2" width="20" height="20" rx="3" ry="3"/>
        <circle cx="7" cy="7" r="1.2" fill="currentColor" stroke="none"/>
        <circle cx="12" cy="12" r="1.2" fill="currentColor" stroke="none"/>
        <circle cx="17" cy="17" r="1.2" fill="currentColor" stroke="none"/>
        <circle cx="17" cy="7" r="1.2" fill="currentColor" stroke="none"/>
        <circle cx="7" cy="17" r="1.2" fill="currentColor" stroke="none"/>
    </svg>

    <div class="container page-hero-inner">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?= $baseUrl ?>/dashboard">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                Browse Games
            </a>
            <span class="sep">/</span>
            <a href="<?= $baseUrl ?>/games/<?= $game['id'] ?>"><?= htmlspecialchars($game['name']) ?></a>
            <span class="sep">/</span>
            <span class="current">Reserve</span>
        </div>

        <!-- Title -->
        <div class="page-title-row">
            <div class="page-title-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </div>
            <div>
                <h1 class="page-title">Reserve <span class="gradient-text">Your Table</span></h1>
                <p class="page-subtitle">Book a gaming session for <?= htmlspecialchars($game['name']) ?> — choose your time, party size, and table.</p>
            </div>
        </div>
    </div>
</section>

<div class="container">

    <!-- PROGRESS TRACKER -->
    <div class="progress-tracker" id="progressTracker">
        <div class="progress-step active" id="pStep1">
            <div class="progress-dot">1</div>
            <span class="progress-label">Preferences</span>
        </div>
        <div class="progress-line"><div class="progress-line-fill" id="pLine1"></div></div>
        <div class="progress-step" id="pStep2">
            <div class="progress-dot">2</div>
            <span class="progress-label">Date & Time</span>
        </div>
        <div class="progress-line"><div class="progress-line-fill" id="pLine2"></div></div>
        <div class="progress-step" id="pStep3">
            <div class="progress-dot">3</div>
            <span class="progress-label">Choose Table</span>
        </div>
    </div>

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="flash-error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <?php unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $baseUrl ?>/reservations">

        <!-- HIDDEN INPUTS -->
        <input type="hidden" name="user_id"          value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
        <input type="hidden" name="client_name"      value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
        <input type="hidden" name="phone"            value="<?= htmlspecialchars($_SESSION['phone'] ?? '0600000000') ?>">
        <input type="hidden" name="number_of_people" id="input_players"  value="4">
        <input type="hidden" name="table_id"         id="input_table_id" value="1">
        <input type="hidden" name="reservation_time" id="input_time"     value="06:00 PM">

        <div class="main-layout">
            <!-- ===== MAIN STEPS ===== -->
            <div class="main-steps-container">

                <!-- ── STEP 1: PREFERENCES ── -->
                <div class="step-section">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <div class="step-title">General Preferences</div>
                        <span class="step-badge">Required</span>
                    </div>
                    <div class="card">
                        <div class="preferences-grid">
                            <!-- GAME MODULE -->
                            <div>
                                <div class="section-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5" fill="currentColor" stroke="none"/><circle cx="15.5" cy="15.5" r="1.5" fill="currentColor" stroke="none"/></svg>
                                    Board Game
                                </div>
                                <div class="game-card-display">
                                    <div class="game-thumb">
                                        <img src="<?= $gameImage ?>" alt="<?= htmlspecialchars($game['name']) ?>">
                                        <div class="game-thumb-overlay"></div>
                                    </div>
                                    <div class="game-meta">
                                        <div class="game-meta-name">
                                            <?= htmlspecialchars($game['name']) ?>
                                            <span class="game-meta-badge"><?= htmlspecialchars($game['category_name']) ?></span>
                                        </div>
                                        <div class="game-meta-info">
                                            <span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                                <?= htmlspecialchars($game['nb_players']) ?> Players
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PARTY SIZE MODULE -->
                            <div>
                                <div class="section-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    Party Size
                                </div>
                                <div class="party-grid">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <button type="button" class="party-btn <?= ($i === 4) ? 'active' : '' ?>" onclick="selectParty(<?= $i ?>, this)"><?= $i ?></button>
                                    <?php endfor; ?>
                                </div>
                                <div class="party-note">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                    Minimum 2 players for <?= htmlspecialchars($game['name']) ?> recommended.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── STEP 2: DATE & TIME ── -->
                <div class="step-section">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <div class="step-title">Select Date & Time</div>
                        <span class="step-badge">Required</span>
                    </div>
                    <div class="card">
                        <div class="datetime-grid">
                            <!-- DATE MODULE -->
                            <div>
                                <div class="section-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Choose Date
                                </div>
                                <div class="date-input-wrapper">
                                    <svg class="date-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    <input type="date" class="date-input" name="reservation_date" id="reservationDate" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required onchange="updateSummaryDate(this.value)">
                                </div>
                            </div>

                            <!-- TIME MODULE -->
                            <div>
                                <div class="section-label">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    Available Slots
                                </div>
                                <div class="slots-grid">
                                    <?php foreach ($timeSlots as $slot): ?>
                                        <button type="button" class="slot-btn <?= ($slot === '06:00 PM') ? 'active' : '' ?>" onclick="selectSlot('<?= $slot ?>', this)">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            <?= $slot ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── STEP 3: CHOOSE TABLE ── -->
                <div class="step-section">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <div class="step-title">Choose Your Table</div>
                        <span class="step-badge">Required</span>
                    </div>

                    <div class="card card-gray">
                        <div class="section-label" style="margin-bottom:18px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 10h18"/><path d="M5 10v7"/><path d="M19 10v7"/><path d="M2 10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2"/></svg>
                            Floor Plan
                        </div>
                        <div class="tables-grid">
                            <?php foreach ($tables as $index => $table): ?>
                                <?php
                                $isOccupied = ($table['status'] === 'occupied');
                                $isSelected = ($table['id'] == 1);
                                $cardClass  = $isOccupied ? 'occupied' : ($isSelected ? 'selected' : '');
                                $statusTxt  = $isOccupied ? 'Occupied' : ($isSelected ? 'Selected' : 'Available');
                                $tableName = $table['name'] ?? 'Table ' . $table['number'];
                                ?>
                                <div class="table-card <?= $cardClass ?>" id="table-<?= $table['id'] ?>" <?php if (!$isOccupied): ?>onclick="selectTable(<?= $table['id'] ?>, '<?= htmlspecialchars($tableName) ?>', <?= $table['capacity'] ?>, this)"<?php endif; ?>>
                                    <span class="status-corner"><?= $statusTxt ?></span>
                                    <div class="table-icon-wrap">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 10h18"></path>
                                            <path d="M5 10v7"></path>
                                            <path d="M19 10v7"></path>
                                            <path d="M2 10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2"></path>
                                        </svg>
                                    </div>
                                    <div class="table-name"><?= htmlspecialchars($tableName) ?></div>
                                    <div class="table-seats">Seats <?= $table['capacity'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="main-entrance-wrapper">
                            <div class="main-entrance">Main Entrance</div>
                        </div>
                    </div>

                    <!-- SELECTED TABLE FOOTER -->
                    <div class="selected-table-footer">
                        <div class="selected-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="selected-info">
                            <div class="selected-info-label">Selected Table</div>
                            <div class="selected-info-val" id="selectedTableName">Table 1 (4 Seats)</div>
                        </div>
                        <div class="selected-note">
                            You can change your table at the venue if alternatives are available upon arrival.
                        </div>
                    </div>
                </div>

            </div> <!-- End Main Steps Container -->

            <!-- ===== SIDEBAR ===== -->
            <div class="sidebar">
                <div class="summary-card">
                    <h3 class="summary-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Reservation Summary
                    </h3>

                    <div class="summary-row">
                        <div class="summary-row-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <span class="summary-row-label">Date</span>
                        <span class="summary-row-value" id="sumDate"><?= date('M d, Y') ?></span>
                    </div>

                    <div class="summary-row">
                        <div class="summary-row-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <span class="summary-row-label">Time</span>
                        <span class="summary-row-value" id="sumTime">06:00 PM</span>
                    </div>

                    <div class="summary-row">
                        <div class="summary-row-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        </div>
                        <span class="summary-row-label">Party Size</span>
                        <span class="summary-row-value" id="sumPlayers">4 Guests</span>
                    </div>

                    <div class="summary-row">
                        <div class="summary-row-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h18"/><path d="M5 10v7"/><path d="M19 10v7"/><path d="M2 10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2"/></svg>
                        </div>
                        <span class="summary-row-label">Table</span>
                        <span class="summary-row-value" id="sumTable">Table 1</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <div class="summary-total-row">
                            <span class="summary-total-label">Estimated Total</span>
                            <span class="summary-total-price" id="sumPrice">$20.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-confirm">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Confirm Reservation
                    </button>

                    <a href="<?= $baseUrl ?>/dashboard" class="cancel-link">Cancel & Go Back</a>

                    <div class="booking-guarantee">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Instant Confirmation · Flexible Cancellation
                    </div>
                </div>
            </div>

        </div> <!-- End Layout Grid -->
    </form>
</div>

<script>
    var pricePerPlayer = 5;
    var stepsCompleted = { s1: false, s2: false, s3: false };

    /* ── RIPPLE EFFECT ── */
    function addRipple(e, el) {
        var rect = el.getBoundingClientRect();
        var r = document.createElement('span');
        r.className = 'ripple-circle';
        var size = Math.max(rect.width, rect.height);
        r.style.cssText = 'width:' + size + 'px;height:' + size + 'px;' +
            'top:' + (e.clientY - rect.top - size/2) + 'px;' +
            'left:' + (e.clientX - rect.left - size/2) + 'px;';
        el.classList.add('ripple-host');
        el.appendChild(r);
        setTimeout(() => r.remove(), 600);
    }

    /* ── FLASH VALUE ── */
    function flashVal(el) {
        el.classList.remove('val-flash');
        void el.offsetWidth;
        el.classList.add('val-flash');
        setTimeout(() => el.classList.remove('val-flash'), 500);
    }

    /* ── PROGRESS TRACKER ── */
    function updateProgress() {
        var done = Object.values(stepsCompleted).filter(Boolean).length;
        if (done >= 1) {
            document.getElementById('pStep1').className = 'progress-step done';
            document.getElementById('pLine1').style.width = '100%';
        }
        if (done >= 2) {
            document.getElementById('pStep2').className = 'progress-step done';
            document.getElementById('pLine2').style.width = '100%';
        }
        if (done >= 3) {
            document.getElementById('pStep3').className = 'progress-step done';
        }
        // Set current active
        if (done === 0) document.getElementById('pStep1').className = 'progress-step active';
        else if (done === 1) document.getElementById('pStep2').className = 'progress-step active';
        else if (done === 2) document.getElementById('pStep3').className = 'progress-step active';
    }

    /* ── PARTY SIZE ── */
    function selectParty(number, btn) {
        document.querySelectorAll('.party-btn').forEach(b => {
            b.classList.remove('active');
            b.style.animation = '';
        });
        btn.classList.add('active');
        addRipple(event, btn);
        document.getElementById('input_players').value = number;
        var el = document.getElementById('sumPlayers');
        el.textContent = number + ' Guests';
        flashVal(el);
        var priceEl = document.getElementById('sumPrice');
        priceEl.textContent = '$' + (number * pricePerPlayer).toFixed(2);
        flashVal(priceEl);
        stepsCompleted.s1 = true;
        updateProgress();
    }

    /* ── TIME SLOT ── */
    function selectSlot(slot, btn) {
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        addRipple(event, btn);
        document.getElementById('input_time').value = slot;
        var el = document.getElementById('sumTime');
        el.textContent = slot;
        flashVal(el);
        stepsCompleted.s2 = true;
        updateProgress();
        checkTableAvailability();
    }

    /* ── TABLE SELECT ── */
    function selectTable(tableId, tableName, capacity, card) {
        document.querySelectorAll('.table-card:not(.occupied)').forEach(c => {
            c.classList.remove('selected');
            var status = c.querySelector('.status-corner');
            if (status) status.textContent = 'Available';
        });
        card.classList.add('selected');
        addRipple(event, card);
        var status = card.querySelector('.status-corner');
        if (status) status.textContent = 'Selected';
        document.getElementById('input_table_id').value = tableId;
        var el = document.getElementById('sumTable');
        el.textContent = tableName;
        flashVal(el);
        document.getElementById('selectedTableName').textContent = tableName + ' (' + capacity + ' Seats)';
        stepsCompleted.s3 = true;
        updateProgress();
    }

    /* ── DATE UPDATE ── */
    function updateSummaryDate(dateValue) {
        var d = new Date(dateValue + 'T00:00:00');
        var options = { year: 'numeric', month: 'short', day: 'numeric' };
        var el = document.getElementById('sumDate');
        el.textContent = d.toLocaleDateString('en-US', options);
        flashVal(el);
        checkTableAvailability();
    }

    /* ── LIVE AVAILABILITY CHECK ── */
    function checkTableAvailability() {
        const dateStr = document.getElementById('reservationDate').value;
        const timeStr = document.getElementById('input_time').value;
        const cap = document.getElementById('input_players').value;
        
        // Convert timeStr like '06:00 PM' to '18:00' if possible, or pass directly
        const url = `<?= $baseUrl ?>/reservations/check-availability?date=${dateStr}&time=${timeStr}&capacity=${cap}`;
        
        fetch(url)
            .then(res => res.json())
            .then(availableTables => {
                const availableIds = availableTables.map(t => parseInt(t.id));
                
                document.querySelectorAll('.table-card').forEach(card => {
                    const tableId = parseInt(card.id.replace('table-', ''));
                    const statusBadge = card.querySelector('.status-corner');
                    
                    if (!availableIds.includes(tableId)) {
                        card.classList.add('occupied');
                        card.classList.remove('selected');
                        card.onclick = null;
                        if (statusBadge) statusBadge.textContent = 'Occupied';
                    } else {
                        card.classList.remove('occupied');
                        // Restore onclick behavior via closure or inline attribute
                        card.onclick = function(e) {
                            const nameEl = card.querySelector('.table-name');
                            const seatsEl = card.querySelector('.table-seats');
                            const seats = parseInt(seatsEl.textContent.replace(/\D/g,'') || 4);
                            selectTable(tableId, nameEl.textContent, seats, card);
                        };
                        if (!card.classList.contains('selected') && statusBadge) {
                            statusBadge.textContent = 'Available';
                        }
                    }
                });
            })
            .catch(err => console.error("Could not fetch availability", err));
    }
    
    // Call it immediately on load
    window.addEventListener('DOMContentLoaded', checkTableAvailability);

    /* ── HERO PARTICLES ── */
    (function spawnParticles() {
        var container = document.getElementById('heroParticles');
        if (!container) return;
        var colors = ['rgba(99,102,241,0.5)', 'rgba(236,72,153,0.4)', 'rgba(245,158,11,0.4)', 'rgba(129,140,248,0.45)'];
        for (var i = 0; i < 14; i++) {
            (function(idx) {
                setTimeout(function() {
                    var p = document.createElement('div');
                    p.className = 'particle';
                    var size = Math.random() * 8 + 4;
                    p.style.cssText = [
                        'width:' + size + 'px',
                        'height:' + size + 'px',
                        'left:' + (Math.random() * 90 + 5) + '%',
                        'bottom:0',
                        'background:' + colors[Math.floor(Math.random() * colors.length)],
                        'animation-duration:' + (Math.random() * 4 + 3) + 's',
                        'animation-delay:' + (Math.random() * 2) + 's',
                    ].join(';');
                    container.appendChild(p);
                    p.addEventListener('animationend', function() {
                        p.remove();
                        setTimeout(function() {
                            var p2 = p.cloneNode();
                            p2.style.left = (Math.random() * 90 + 5) + '%';
                            p2.style.animationDelay = '0s';
                            container.appendChild(p2);
                            p2.addEventListener('animationend', arguments.callee);
                        }, Math.random() * 2000);
                    });
                }, idx * 300);
            })(i);
        }
    })();

    /* ── CONFETTI ON SUBMIT ── */
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        launchConfetti();
        setTimeout(function() {
            document.getElementById('successOverlay').classList.add('show');
        }, 200);
        setTimeout(function() {
            form.submit();
        }, 2200);
    });

    function launchConfetti() {
        var canvas = document.getElementById('confetti-canvas');
        canvas.style.display = 'block';
        var ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        var pieces = [];
        var colors = ['#6366f1','#ec4899','#f59e0b','#10b981','#818cf8','#f472b6','#34d399'];
        for (var i = 0; i < 120; i++) {
            pieces.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                w: Math.random() * 10 + 6,
                h: Math.random() * 6 + 3,
                color: colors[Math.floor(Math.random() * colors.length)],
                vx: Math.random() * 4 - 2,
                vy: Math.random() * 4 + 2,
                rot: Math.random() * 360,
                vrot: Math.random() * 6 - 3,
                alpha: 1
            });
        }
        var frame;
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            var alive = false;
            pieces.forEach(function(p) {
                p.x += p.vx;
                p.y += p.vy;
                p.rot += p.vrot;
                if (p.y > canvas.height * 0.6) p.alpha -= 0.02;
                if (p.alpha <= 0) return;
                alive = true;
                ctx.save();
                ctx.globalAlpha = Math.max(0, p.alpha);
                ctx.translate(p.x, p.y);
                ctx.rotate(p.rot * Math.PI / 180);
                ctx.fillStyle = p.color;
                ctx.fillRect(-p.w/2, -p.h/2, p.w, p.h);
                ctx.restore();
            });
            if (alive) frame = requestAnimationFrame(draw);
            else canvas.style.display = 'none';
        }
        draw();
    }
</script>

</body>
</html>
