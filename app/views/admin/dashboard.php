<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: {$baseUrl}/login");
    exit;
}

$users = $users ?? [];
$games = $games ?? [];
$reservations = $reservations ?? [];
$tables = $tables ?? [];

$totalUsers = count($users);
$totalGames = count($games);
$totalReservations = count($reservations);

// Calculate Category Popularity
$categoriesData = [];
foreach($games as $g) {
    if(!empty($g['category_name'])) {
        $cat = $g['category_name'];
        if(isset($categoriesData[$cat])) {
            $categoriesData[$cat]++;
        } else {
            $categoriesData[$cat] = 1;
        }
    }
}
$catLabels = json_encode(array_keys($categoriesData));
$catValues = json_encode(array_values($categoriesData));

// Revenue Mock (Assume $15 per person per reservation)
$revenueTrends = [4000, 3800, 5000, 5100, 6200, 6100, 7500]; // Mocks for graph

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabletop HQ - Operational Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary:      #5a67d8;
            --primary-light:#c3dafe;
            --primary-fade: rgba(90, 103, 216, 0.1);
            --bg:           #f8fafc;
            --sidebar:      #ffffff;
            --card-bg:      #ffffff;
            --text-main:    #1a202c;
            --text-muted:   #718096;
            --border:       #e2e8f0;
            --shadow-sm:    0 1px 3px rgba(0,0,0,0.05);
            --shadow-md:    0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            --red-bg:       #fed7d7;
            --red-text:     #e53e3e;
            --green-bg:     #c6f6d5;
            --green-text:   #38a169;
            --orange-bg:    #feebc8;
            --orange-text:  #dd6b20;
        }

        /* ── DARK MODE ── */
        body.dark-mode {
            --bg:           #0f172a;
            --sidebar:      #1e293b;
            --card-bg:      #1e293b;
            --text-main:    #f8fafc;
            --text-muted:   #94a3b8;
            --border:       #334155;
            --shadow-sm:    0 4px 6px -1px rgba(0, 0, 0, 0.5);
            --bg-hover:     #334155;
        }
        body.dark-mode th { background: #0f172a; }
        body.dark-mode .nav-link:hover { background: rgba(255,255,255,0.05); }
        body.dark-mode .topbar { background: var(--sidebar); }
        body.dark-mode .search-area input { background: #0f172a; color: #fff; }
        body.dark-mode .btn-outline { background: transparent; color: var(--text-main); }
        body.dark-mode .badge.empty { background: rgba(255,255,255,0.05); color: #fff; border-color: #334155; }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; transition: background-color 0.3s, color 0.3s; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 250px;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 24px 0;
            z-index: 10;
        }
        .sidebar-brand { font-size: 18px; font-weight: 700; color: var(--primary); padding: 0 24px; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
        .sidebar-brand .icon { background: var(--primary); color: #fff; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px; }

        .nav-link {
            padding: 12px 24px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            border-left: 3px solid transparent;
        }
        .nav-link:hover { color: var(--primary); background: var(--bg); }
        .nav-link.active { color: var(--primary); background: var(--primary-fade); border-left-color: var(--primary); font-weight: 600; }
        
        .sidebar-bottom { margin-top: auto; padding: 0 24px; border-top: 1px solid var(--border); padding-top: 20px;}
        .sidebar-bottom a {
            display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--text-muted); text-decoration: none; padding: 8px 0;
        }
        .sidebar-bottom a.logout { color: var(--red-text); }

        /* ── MAIN CONTENT ── */
        .main-wrapper { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        
        .topbar {
            height: 70px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            background: #fff;
        }
        .topbar-title { font-size: 16px; font-weight: 600; color: var(--text-main); }
        .search-area { position: relative; }
        .search-area input { border: 1px solid var(--border); border-radius: 6px; background: var(--bg); padding: 8px 16px; width: 300px; font-size: 13px; outline: none; transition: 0.2s; }
        .search-area input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px var(--primary-fade); }

        .content-area { flex: 1; overflow-y: auto; padding: 32px 40px; }

        /* ── TABS ── */
        .tab-content { display: none; animation: fadeIn 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* ── HEADERS ── */
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
        .page-header h2 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .page-header p { font-size: 14px; color: var(--text-muted); }

        .btn-main { background: var(--primary); color: #fff; padding: 10px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; border: none; cursor: pointer; }
        .btn-outline { background: #fff; color: var(--text-main); border: 1px solid var(--border); padding: 10px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }

        /* ── CARDS & GRIDS ── */
        .kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
        .kpi-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 20px; box-shadow: var(--shadow-sm); }
        .kpi-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .kpi-icon { width: 32px; height: 32px; background: var(--primary-fade); color: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .kpi-trend { font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 12px; }
        .kpi-trend.up { background: var(--green-bg); color: var(--green-text); }
        .kpi-trend.down { background: var(--red-bg); color: var(--red-text); }
        .kpi-title { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 4px; }
        .kpi-value { font-size: 24px; font-weight: 700; color: var(--text-main); margin-bottom: 8px; }
        .kpi-sub { font-size: 12px; color: var(--text-muted); }

        .chart-row { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px; }
        .chart-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 24px; box-shadow: var(--shadow-sm); }
        .chart-card h3 { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        .chart-card p { font-size: 13px; color: var(--text-muted); margin-bottom: 20px; }
        
        .activity-list { margin-top: 10px; }
        .activity-item { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; padding: 16px; border-bottom: 1px solid var(--border); font-size: 13px; align-items: center; }
        .activity-item:last-child { border-bottom: none; }
        .activity-header { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: capitalize; }

        /* ── TABLES ── */
        .data-table-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow-sm); overflow: hidden; }
        .data-table-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 16px 24px; font-size: 12px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid var(--border); background: #f9fafb; text-transform: capitalize; }
        td { padding: 16px 24px; font-size: 13px; color: var(--text-main); border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .badge.active, .badge.confirmed { background: var(--primary-light); color: var(--primary); }
        .badge.cancelled { background: var(--red-bg); color: var(--red-text); }
        .badge.pending, .badge.maintenance { background: var(--orange-bg); color: var(--orange-text); }
        .badge.completed { background: var(--green-bg); color: var(--green-text); }
        .badge.empty { background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }

        .game-block { display: flex; align-items: center; gap: 12px; }
        .game-img-placeholder { width: 40px; height: 40px; border-radius: 8px; background: var(--bg); display: flex; align-items: center; justify-content: center; font-size: 16px; color: var(--text-muted); }
        
        .action-btns button { border: none; background: none; color: var(--text-muted); cursor: pointer; padding: 4px; margin-right: 4px;}
        .action-btns button:hover { color: var(--primary); }
        .action-btns .btn-del:hover { color: var(--red-text); }
        .inline-form { display: inline-block; }

        /* ── LIVE SESSIONS GRID ── */
        .live-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .table-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: var(--shadow-sm); }
        .table-card.active { border-top: 3px solid var(--primary); }
        .table-card.cleaning { border-top: 3px solid var(--orange-text); }
        .table-card.empty { border-top: 3px solid var(--border); opacity: 0.8; }
        
        .tc-header { padding: 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .tc-title { font-weight: 600; font-size: 15px;}
        .tc-body { padding: 16px; flex-grow: 1; display:flex; flex-direction: column; gap:12px; }
        .tc-empty-state { text-align: center; color: var(--text-muted); font-size: 13px; font-style: italic; padding: 20px 0; }
        .tc-footer { padding: 12px 16px; background: var(--bg); border-top: 1px solid var(--border); }
        .btn-block { width: 100%; text-align: center; display: block; border-radius: 6px; padding: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-end { background: var(--red-bg); color: var(--red-text); border: none; }
        .btn-start { background: #fff; border: 1px solid var(--border); color: var(--text-main); }
        
        .flash { background: var(--green-bg); color: var(--green-text); padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
    </style>
</head>
<body>

    <!-- ── SIDEBAR ── -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="icon">⚄</div>
            Tabletop HQ
        </div>
        <nav>
            <div class="nav-link active" onclick="switchTab('dashboard', this)">🖥️ Dashboard</div>
            <div class="nav-link" onclick="switchTab('inventory', this)">📚 Games Inventory</div>
            <div class="nav-link" onclick="switchTab('reservations', this)">📅 Reservations</div>
            <div class="nav-link" onclick="switchTab('live', this)">🔵 Live Sessions</div>
            <div class="nav-link" onclick="switchTab('stats', this)">📊 Statistics</div>
        </nav>
        <div class="sidebar-bottom">
            <a href="<?= $baseUrl ?>/dashboard" class="logout">⏏️ Logout</a>
        </div>
    </aside>

    <!-- ── MAIN WRAPPER ── -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-title" id="topbarTitle">Operational Dashboard</div>
            <div style="display: flex; gap: 16px; align-items: center;">
                <div class="search-area">
                    <input type="text" id="searchInput" placeholder="Quick search..." onkeyup="filterTables()">
                </div>
                <button onclick="toggleDarkMode()" style="background:none; border:none; font-size:20px; cursor:pointer;" id="themeIcon">🌙</button>
            </div>
        </header>
        
        <main class="content-area">
            <?php if(isset($_SESSION['flash_success'])): ?>
                <div class="flash">✓ <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <!-- ======================= 1. DASHBOARD OVERVIEW ======================= -->
            <div id="tab-dashboard" class="tab-content active">
                <div class="page-header">
                    <div>
                        <h2>Welcome back, Admin</h2>
                        <p>Here's what's happening at the venue today.</p>
                    </div>
                </div>

                <div class="kpi-row">
                    <div class="kpi-card">
                        <div class="kpi-header">
                            <div class="kpi-icon">🎲</div>
                            <div class="kpi-trend up">↗ +2.5%</div>
                        </div>
                        <div class="kpi-title">Total Games</div>
                        <div class="kpi-value"><?= $totalGames ?></div>
                        <div class="kpi-sub">12 added this week</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-header">
                            <div class="kpi-icon">📅</div>
                            <div class="kpi-trend up">↗ +18%</div>
                        </div>
                        <div class="kpi-title">Reservations Today</div>
                        <div class="kpi-value"><?= $totalReservations ?></div>
                        <div class="kpi-sub">8 groups pending arrival</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-header">
                            <div class="kpi-icon">👥</div>
                            <div class="kpi-trend down">↘ -4%</div>
                        </div>
                        <div class="kpi-title">Active Sessions</div>
                        <div class="kpi-value">14 / 20</div>
                        <div class="kpi-sub">70% capacity utilized</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-header">
                            <div class="kpi-icon">📈</div>
                            <div class="kpi-trend up">↗ +12%</div>
                        </div>
                        <div class="kpi-title">Occupancy Rate</div>
                        <div class="kpi-value">85%</div>
                        <div class="kpi-sub">Peak reached at 7:00 PM</div>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-card">
                        <h3>Peak Hours (Today)</h3>
                        <p>Live occupancy levels across active tables</p>
                        <canvas id="peakChart" height="200"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3>Category Popularity</h3>
                        <p>Distribution of games played today</p>
                        <canvas id="categoryChart" height="200" style="margin-top:20px;"></canvas>
                    </div>
                </div>

                <div class="data-table-card" style="margin-bottom: 40px;">
                    <div class="data-table-header">
                        <div>
                            <h3 style="font-size: 16px; font-weight: 600;">Recent Activity</h3>
                            <p style="font-size: 13px; color: var(--text-muted);">Latest events and updates from the venue floor</p>
                        </div>
                    </div>
                    <div class="activity-header">
                        <div>Event Details</div><div>Time</div><div>Type</div><div>Status</div>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div>Table 4: Started 'Gloomhaven' session</div>
                            <div>2 mins ago</div>
                            <div><span class="badge empty">Session</span></div>
                            <div><strong style="color:var(--text-main);">Active</strong></div>
                        </div>
                        <div class="activity-item">
                            <div>Group 'Meeple Friends': Reservation Confirmed</div>
                            <div>15 mins ago</div>
                            <div><span class="badge empty">Booking</span></div>
                            <div><span class="badge confirmed">Confirmed</span></div>
                        </div>
                        <div class="activity-item">
                            <div>Game 'Catan' returned and checked in</div>
                            <div>34 mins ago</div>
                            <div><span class="badge empty">Inventory</span></div>
                            <div><span class="badge completed">Completed</span></div>
                        </div>
                        <div class="activity-item">
                            <div>Table 2: Reservation No-Show</div>
                            <div>1.5 hours ago</div>
                            <div><span class="badge empty">Booking</span></div>
                            <div><span class="badge cancelled">Cancelled</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ======================= 2. GAMES INVENTORY ======================= -->
            <div id="tab-inventory" class="tab-content">
                <div class="page-header">
                    <div>
                        <h2>Games Inventory</h2>
                        <p>Detailed listing of all <?= $totalGames ?> games in your collection</p>
                    </div>
                    <a href="<?= $baseUrl ?>/games" class="btn-main" style="text-decoration:none;">+ Add New Game</a>
                </div>
                
                <div class="data-table-card">
                    <table id="gamesTable">
                        <thead>
                            <tr>
                                <th>Game Details</th>
                                <th>Category</th>
                                <th>Players</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($games as $g): ?>
                            <tr>
                                <td>
                                    <div class="game-block">
                                        <div class="game-img-placeholder">🎲</div>
                                        <div>
                                            <div style="font-weight: 600;"><?= htmlspecialchars($g['name']) ?></div>
                                            <div style="font-size: 12px; color: var(--text-muted);">Difficulty: <?= htmlspecialchars($g['difficulty']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge empty"><?= htmlspecialchars($g['category_name'] ?? 'N/A') ?></span></td>
                                <td><?= htmlspecialchars($g['nb_players']) ?></td>
                                <td><?= htmlspecialchars($g['duration']) ?> min</td>
                                <td>
                                    <?php 
                                        $bgStatus = strtolower($g['status']) == 'active' ? 'confirmed' : 'maintenance';
                                        $txtStatus = strtolower($g['status']) == 'active' ? 'Available' : 'Unavailable';
                                    ?>
                                    <span class="badge <?= $bgStatus ?>"><?= $txtStatus ?></span>
                                </td>
                                <td class="action-btns">
                                    <button title="Edit">✎</button>
                                    <form method="POST" action="<?= $baseUrl ?>/admin/deleteGame" class="inline-form" onsubmit="return confirm('Delete this game?');">
                                        <input type="hidden" name="game_id" value="<?= $g['id'] ?>">
                                        <button type="submit" class="btn-del" title="Delete">🗑</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ======================= 3. RESERVATIONS ======================= -->
            <div id="tab-reservations" class="tab-content">
                <div class="page-header">
                    <div>
                        <h2>Today's Reservations</h2>
                        <p>Manage your table bookings and customer group flow</p>
                    </div>
                    <a href="<?= $baseUrl ?>/reservations/create" class="btn-main" style="text-decoration:none;">+ New Reservation</a>
                </div>

                <div class="data-table-card" style="margin-bottom: 24px;">
                    <table id="resTable">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Party</th>
                                <th>Date & Time</th>
                                <th>Table</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($reservations as $r): ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600;"><?= htmlspecialchars($r['client_name']) ?></div>
                                    <div style="font-size:12px; color:var(--text-muted);"><?= htmlspecialchars($r['client_email'] ?? 'No email mapped') ?></div>
                                </td>
                                <td>👥 <?= htmlspecialchars($r['number_of_people']) ?> Players</td>
                                <td>
                                    <?= date('M d, Y', strtotime($r['reservation_date'])) ?> • <?= htmlspecialchars($r['reservation_time']) ?>
                                </td>
                                <td style="font-weight:600;">T-<?= str_pad($r['table_id'], 2, '0', STR_PAD_LEFT) ?></td>
                                <td><span class="badge <?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                                <td class="action-btns">
                                    <?php if($r['status'] !== 'cancelled'): ?>
                                    <form method="POST" action="<?= $baseUrl ?>/admin/cancelReservation" class="inline-form" onsubmit="return confirm('Cancel reservation?');">
                                        <input type="hidden" name="reservation_id" value="<?= $r['id'] ?>">
                                        <button type="submit" class="btn-del">Cancel</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="chart-row">
                    <div class="chart-card">
                        <h3>Evening Peak Forecast</h3>
                        <p>Expected peak occupancy at 19:30</p>
                        <canvas id="forecastChart" height="150" style="margin-top:20px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- ======================= 4. LIVE SESSIONS ======================= -->
            <div id="tab-live" class="tab-content">
                <div class="page-header">
                    <div>
                        <h2>Live Sessions</h2>
                        <p>Real-time floor plan view</p>
                    </div>
                </div>
                
                <div class="live-grid">
                    <?php 
                    // Generate Mock session floor plan if tables count < 12
                    $tableCount = count($tables);
                    $displayCards = $tableCount > 0 ? $tables : array_fill(0, 12, ['id' => rand(1,20)]);
                    foreach(array_slice($displayCards, 0, 12) as $index => $t): 
                        // Mock varying states to match mockup (Active, Cleaning, Empty)
                        $tid = str_pad($index+1, 2, '0', STR_PAD_LEFT);
                        $modulo = $index % 4;
                        if($modulo == 0 || $modulo == 3) {
                            $state = 'active'; $badge = 'ACTIVE';
                        } elseif ($modulo == 1) {
                            $state = 'cleaning'; $badge = 'CLEANING';
                        } else {
                            $state = 'empty'; $badge = 'EMPTY';
                        }
                    ?>
                    <div class="table-card <?= $state ?>">
                        <div class="tc-header">
                            <span class="tc-title">● T<?= $tid ?></span>
                            <span class="badge <?= $state ?>"><?= $badge ?></span>
                        </div>
                        <div class="tc-body">
                            <?php if($state == 'active'): ?>
                                <div style="font-weight:600;">Gloomhaven</div>
                                <div style="font-size:12px; color:var(--text-muted);">👥 4 Players <br> 🕒 Started 14:15</div>
                            <?php elseif($state == 'cleaning'): ?>
                                <div class="tc-empty-state">🔄<br>Staff is sanitizing the table</div>
                            <?php else: ?>
                                <div class="tc-empty-state">🎮<br>Table available for players</div>
                            <?php endif; ?>
                        </div>
                        <div class="tc-footer">
                            <?php if($state == 'active'): ?>
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--primary);">01:42:08</span>
                                    <a href="<?= $baseUrl ?>/sessions" class="btn-block btn-end" style="width: auto; padding: 4px 12px; text-decoration:none;">Stop</a>
                                </div>
                            <?php elseif($state == 'cleaning'): ?>
                                <button class="btn-block btn-start" style="color:var(--text-muted);">In Progress...</button>
                            <?php else: ?>
                                <a href="<?= $baseUrl ?>/sessions" class="btn-block btn-start" style="text-decoration:none;">▷ Start Session</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ======================= 5. STATISTICS ======================= -->
            <div id="tab-stats" class="tab-content">
                <div class="page-header">
                    <div>
                        <h2>Venue Performance Statistics</h2>
                        <p>Comprehensive analytics of your venue operations</p>
                    </div>
                    <button class="btn-main">Export PDF Report</button>
                </div>
                
                <div class="kpi-row">
                    <div class="kpi-card" style="grid-column: span 2;">
                        <h3 style="font-size:14px; margin-bottom: 10px;">Avg. Session Length</h3>
                        <div class="kpi-value">2.4 Hours</div>
                        <div class="kpi-sub">12% increase from last month</div>
                    </div>
                    <div class="kpi-card">
                        <h3 style="font-size:14px; margin-bottom: 10px;">Most Popular</h3>
                        <div class="kpi-value">Strategy</div>
                        <div class="kpi-sub">42% of total bookings</div>
                    </div>
                    <div class="kpi-card">
                        <h3 style="font-size:14px; margin-bottom: 10px;">Busiest Day</h3>
                        <div class="kpi-value">Saturday</div>
                        <div class="kpi-sub">Avg. 94% occupancy</div>
                    </div>
                </div>

                <div class="chart-card" style="margin-bottom: 24px;">
                    <h3>Revenue & Bookings Trend</h3>
                    <p>Daily financial and operational throughput</p>
                    <canvas id="revChart" height="100"></canvas>
                </div>
                
                <div class="chart-row">
                    <div class="chart-card">
                        <h3>Top Most Played Games</h3>
                        <p>Frequency of sessions by game title</p>
                        <canvas id="topGamesChart" height="200"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3>Customer Loyalty</h3>
                        <p>New vs returning visitors</p>
                        <canvas id="loyaltyChart" height="200" style="margin-top:20px;"></canvas>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- ── SCRIPTS ── -->
    <script>
        // UI Navigation Logic
        function switchTab(tabId, element) {
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            element.classList.add('active');
            
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.add('active');

            const titles = {
                'dashboard': 'Operational Dashboard',
                'inventory': 'Games Inventory',
                'reservations': 'Reservations Management',
                'live': 'Live Sessions',
                'stats': 'Venue Performance Statistics'
            };
            document.getElementById('topbarTitle').innerText = titles[tabId];
            
            document.getElementById('searchInput').value = "";
            filterTables();
        }

        // Live Search Filter
        function filterTables() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let table = document.querySelector('.tab-content.active table');
            if(!table) return;

            let rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                let rowText = rows[i].innerText.toLowerCase();
                rows[i].style.display = rowText.includes(input) ? "" : "none";
            }
        }

        // Dark Mode Logic
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('admin_theme', isDark ? 'dark' : 'light');
            document.getElementById('themeIcon').innerText = isDark ? '☀️' : '🌙';
            
            // Update chart defaults to look good on dark mode
            Chart.defaults.color = isDark ? "#94a3b8" : "#718096";
            for (let id in Chart.instances) {
                Chart.instances[id].update();
            }
        }

        // Initialize Theme
        if (localStorage.getItem('admin_theme') === 'dark') {
            document.body.classList.add('dark-mode');
            document.getElementById('themeIcon').innerText = '☀️';
            Chart.defaults.color = "#94a3b8";
        }

        // ── CHART.JS INITIALIZATION ──
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = "#718096";

        // Tab 1: Peak Hours Chart
        new Chart(document.getElementById('peakChart'), {
            type: 'bar',
            data: {
                labels: ['12pm', '2pm', '4pm', '6pm', '8pm', '10pm'],
                datasets: [{
                    label: 'Occupancy',
                    data: [45, 52, 78, 95, 88, 64],
                    backgroundColor: '#5a67d8',
                    borderRadius: 4
                }]
            },
            options: { responsive: true, plugins: { legend: { display:false } }, scales: { y: { beginAtZero: true, max: 100 } } }
        });

        // Tab 1: Category Doughnut Chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: <?= $catLabels ?>,
                datasets: [{
                    data: <?= $catValues ?>,
                    backgroundColor: ['#5a67d8', '#f56565', '#38b2ac', '#805ad5', '#ed8936'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } } }
        });

        // Tab 5: Revenue Line Chart
        new Chart(document.getElementById('revChart'), {
            type: 'line',
            data: {
                labels: ['Oct 01', 'Oct 05', 'Oct 10', 'Oct 15', 'Oct 20', 'Oct 25', 'Oct 30'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: <?= json_encode($revenueTrends) ?>,
                    borderColor: '#5a67d8',
                    backgroundColor: 'rgba(90, 103, 216, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // Tab 5: Top Games Horizontal Bar
        new Chart(document.getElementById('topGamesChart'), {
            type: 'bar',
            data: {
                labels: ['Catan', 'Wingspan', 'Ticket to Ride', '7 Wonders', 'Gloomhaven', 'Azul'],
                datasets: [{
                    label: 'Sessions',
                    data: [142, 120, 95, 85, 78, 65],
                    backgroundColor: '#5a67d8',
                    borderRadius: 4
                }]
            },
            options: { indexAxis: 'y', responsive: true, plugins: { legend: { display:false } }, scales: { x: { display: false } } }
        });

        // Tab 5: Customer Loyalty
        new Chart(document.getElementById('loyaltyChart'), {
            type: 'doughnut',
            data: {
                labels: ['Returning', 'First-time'],
                datasets: [{
                    data: [65, 35],
                    backgroundColor: ['#5a67d8', '#f56565'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } } }
        });
        
        // Tab 3: Forecast Chart
        new Chart(document.getElementById('forecastChart'), {
            type: 'bar',
            data: {
                labels: ['17:00','18:00','19:00','20:00','21:00','22:00'],
                datasets: [{
                    data: [10, 45, 60, 40, 20, 5],
                    backgroundColor: ['#c3dafe','#7f9cf5','#5a67d8','#c3dafe','#e2e8f0','#e2e8f0'],
                    borderRadius: 4
                }]
            },
            options: { responsive: true, plugins: { legend: { display:false } }, scales: { x: { grid: {display:false} }, y: { display: false } } }
        });
    </script>
</body>
</html>
