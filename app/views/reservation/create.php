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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== VARIABLES ===== */
        :root {
            --primary:      #6366f1;
            --primary-hover:#4f46e5;
            --primary-light:#eff6ff;
            --primary-bg:   #6366f1;
            --bg:           #ffffff;
            --bg-card:      #ffffff;
            --bg-subcard:   #f8fafc;
            --white:        #ffffff;
            --gray-light:   #f3f4f6;
            --gray:         #e5e7eb;
            --gray-dark:    #d1d5db;
            --gray-text:    #6b7280;
            --text:         #111827;
            --border:       #e5e7eb;
            --green:        #10b981;
            --green-light:  #d1fae5;
            --red:          #ef4444;
            --red-light:    #fee2e2;
            --shadow:       0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md:    0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        /* ===== LAYOUT ===== */
        .page-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 16px 60px;
            min-height: 80vh;
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 32px;
            align-items: start;
            margin-top: 24px;
        }

        /* ===== STEPS HEADER ===== */
        .step-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 32px;
            margin-bottom: 16px;
        }
        .step-header:first-of-type { margin-top: 0; }
        .step-number {
            background: var(--primary);
            color: #fff;
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 600;
        }
        .step-title { font-size: 18px; font-weight: 700; color: var(--text); }

        /* ===== CARDS CONTAINERS ===== */
        .card-container {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }
        .card-container.gray-bg {
            background: var(--bg-subcard);
            padding: 24px 24px 0 24px;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
        }
        
        .section-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        /* ===== STEP 1: PREFERENCES ===== */
        .step1-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }

        /* Board Game Card */
        .board-game-card {
            background: var(--bg-subcard);
            border: 1px solid var(--border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
        }
        .board-game-img {
            width: 64px; height: 64px;
            background: var(--gray);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--gray-text);
        }
        .board-game-name { font-weight: 700; font-size: 16px; color: var(--text); margin-bottom: 2px;}
        .board-game-details { font-size: 13px; color: var(--gray-text); }

        /* Party Size */
        .party-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }
        .party-btn {
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text);
            border-radius: 8px;
            padding: 10px 0;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .party-btn:hover { border-color: var(--gray-dark); }
        .party-btn.active {
            background: var(--primary-bg);
            color: white;
            border-color: var(--primary-bg);
        }
        .party-note {
            display: flex; align-items: center; gap: 6px;
            font-size: 11px; color: var(--gray-text);
            margin-top: 12px;
        }

        /* ===== STEP 2: DATE & TIME ===== */
        .step2-content {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 32px;
            align-items: start;
        }

        /* Date Input */
        .date-input-wrapper {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            height: 108px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .date-input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid var(--bg-card); /* invisible border just for size */
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: transparent;
            color: var(--text);
            cursor: pointer;
            outline: none;
            font-weight: 500;
        }
        .date-icon {
            position: absolute;
            left: 24px;
            color: var(--gray-text);
            pointer-events: none;
        }

        /* Time Slots */
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }
        .slot-btn {
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text);
            border-radius: 12px;
            padding: 12px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .slot-btn svg { width: 16px; height: 16px; color: var(--gray-text); }
        .slot-btn:hover { border-color: var(--gray-dark); }
        .slot-btn.active {
            background: var(--primary-bg);
            color: white;
            border-color: var(--primary-bg);
        }
        .slot-btn.active svg { color: white; }
        
        /* Empty / hidden buttons to maintain grid if needed */
        .slot-btn.hidden { visibility: hidden; }

        /* ===== STEP 3: CHOOSE TABLE ===== */
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            padding-bottom: 16px;
        }
        .table-card {
            border: 1px solid var(--border);
            background: var(--bg-card);
            border-radius: 12px;
            padding: 24px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            display: flex; flex-direction: column; align-items: center;
        }
        .table-card:hover:not(.occupied) {
            border-color: var(--primary);
        }
        .table-card.selected {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .table-card.occupied {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--gray-light);
        }
        
        .table-icon {
            width: 24px; height: 24px;
            margin-bottom: 12px;
            color: var(--primary);
        }
        .table-card.occupied .table-icon { color: var(--gray-text); }
        
        .table-name { font-weight: 700; font-size: 14px; color: var(--text); }
        .table-seats { font-size: 10px; color: var(--gray-text); font-weight: 600; margin-top: 4px; letter-spacing: 0.5px;}

        /* Corner Status Text */
        .status-corner {
            position: absolute;
            top: 10px; right: 12px;
            font-size: 10px;
            color: var(--gray-text);
        }
        
        /* Main entrance pill */
        .main-entrance-wrapper {
            display: flex; justify-content: center; padding-bottom: 16px; margin-top: -8px;
        }
        .main-entrance {
            background: var(--gray);
            color: var(--text);
            font-size: 10px;
            font-weight: 700;
            padding: 4px 16px;
            border-radius: 12px;
            letter-spacing: 0.5px;
        }

        /* Selected Table Footer */
        .selected-table-footer {
            border: 1px solid var(--border);
            background: var(--bg-card);
            border-radius: 0 0 12px 12px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            border-top: none;
        }
        .selected-icon {
            width: 32px; height: 32px;
            background: var(--primary-light);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--primary);
        }
        .selected-info { flex: 1; line-height: 1.4; }
        .selected-info-label { font-size: 10px; font-weight: 700; color: var(--gray-text); letter-spacing: 0.5px; }
        .selected-info-val { font-size: 14px; font-weight: 700; color: var(--text); }
        .selected-note { font-size: 11px; color: var(--gray-text); text-align: right; max-width: 280px; }


        /* ===== SIDEBAR (Retained for functionality) ===== */
        .sidebar { position: sticky; top: 24px; }
        .summary-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }
        .summary-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; }
        .summary-row {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 12px; font-size: 13px;
        }
        .summary-row-label { color: var(--gray-text); flex: 1; }
        .summary-row-value { font-weight: 600; color: var(--text); }
        
        .summary-total {
            background: var(--bg-subcard);
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0 16px 0;
        }
        .summary-total-row { display: flex; justify-content: space-between; align-items: center; }
        .summary-total-label{ color: var(--gray-text); font-size: 13px; }
        .summary-total-price{ font-size: 20px; font-weight: 800; color: var(--primary); }
        
        .btn-confirm {
            width: 100%;
            background: var(--primary-bg);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-confirm:hover { background: var(--primary-hover); }

        /* ===== DARK MODE OVERRIDES ===== */
        [data-theme="dark"] body {
            --bg: #0f172a;
            --bg-card: #1e293b;
            --bg-subcard: #0f172a;
            --text: #f8fafc;
            --gray: #334155;
            --gray-light: #1e293b;
            --gray-text: #94a3b8;
            --border: #334155;
            --gray-dark: #475569;
            --primary-light: rgba(99,102,241,0.15);
        }
        [data-theme="dark"] .table-card.occupied { opacity: 0.4; }
        [data-theme="dark"] .main-entrance { background: #334155; color: #cbd5e1; }
        [data-theme="dark"] .step-card { border-color: #334155; }
        [data-theme="dark"] .date-input-wrapper { background: #1e293b; }
        [data-theme="dark"] .party-btn, [data-theme="dark"] .slot-btn, [data-theme="dark"] .table-card { background: #1e293b; }
        [data-theme="dark"] .party-btn:hover, [data-theme="dark"] .slot-btn:hover { border-color: #475569; background: #334155; }
        [data-theme="dark"] input::-webkit-calendar-picker-indicator { filter: invert(1); }

    </style>
</head>
<body>

<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>

<div class="page-wrapper">

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div style="background:var(--red-light); color:var(--red); padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; font-weight:500;">
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

        <div class="layout">
            <!-- MAIN STEPS -->
            <div class="main-steps-container">

                <!-- STEP 1 -->
                <div class="step-header">
                    <div class="step-number">1</div>
                    <div class="step-title">General Preferences</div>
                </div>
                <div class="card-container">
                    <div class="step1-content">
                        <!-- GAME MODULE -->
                        <div>
                            <div class="section-label">BOARD GAME</div>
                            <div class="board-game-card">
                                <div class="board-game-img">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path><path d="M12 12v9"></path><path d="m8 17 4 4 4-4"></path></svg>
                                </div>
                                <div>
                                    <div class="board-game-name"><?= htmlspecialchars($game['name']) ?></div>
                                    <div class="board-game-details"><?= htmlspecialchars($game['category_name']) ?> • <?= htmlspecialchars($game['nb_players']) ?> Players</div>
                                </div>
                            </div>
                        </div>

                        <!-- PARTY SIZE MODULE -->
                        <div>
                            <div class="section-label">PARTY SIZE</div>
                            <div class="party-grid">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <button type="button" class="party-btn <?= ($i === 4) ? 'active' : '' ?>" onclick="selectParty(<?= $i ?>, this)"><?= $i ?></button>
                                <?php endfor; ?>
                            </div>
                            <div class="party-note">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                Minimum 2 players for <?= htmlspecialchars($game['name']) ?> recommended.
                            </div>
                        </div>
                    </div>
                </div>


                <!-- STEP 2 -->
                <div class="step-header">
                    <div class="step-number">2</div>
                    <div class="step-title">Select Date & Time</div>
                </div>
                <div class="card-container">
                    <div class="step2-content">
                        
                        <!-- DATE MODULE -->
                        <div>
                            <div class="section-label">CHOOSE DATE</div>
                            <div class="date-input-wrapper">
                                <svg class="date-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <input type="date" class="date-input" name="reservation_date" id="reservationDate" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required onchange="updateSummaryDate(this.value)">
                            </div>
                        </div>

                        <!-- TIME MODULE -->
                        <div>
                            <div class="section-label">AVAILABLE SLOTS</div>
                            <div class="slots-grid">
                                <?php foreach ($timeSlots as $slot): ?>
                                    <button type="button" class="slot-btn <?= ($slot === '06:00 PM') ? 'active' : '' ?>" onclick="selectSlot('<?= $slot ?>', this)">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <?= $slot ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- STEP 3 -->
                <div class="step-header">
                    <div class="step-number">3</div>
                    <div class="step-title">Choose Your Table</div>
                </div>
                
                <div class="card-container gray-bg">
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
                                <svg class="table-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 10h18"></path>
                                    <path d="M5 10v7"></path>
                                    <path d="M19 10v7"></path>
                                    <path d="M2 10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2"></path>
                                </svg>
                                <div class="table-name"><?= htmlspecialchars($tableName) ?></div>
                                <div class="table-seats">SEATS <?= $table['capacity'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="main-entrance-wrapper">
                        <div class="main-entrance">MAIN ENTRANCE</div>
                    </div>
                </div>

                <!-- SELECTED TABLE FOOTER -->
                <div class="selected-table-footer">
                    <div class="selected-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div class="selected-info">
                        <div class="selected-info-label">SELECTED TABLE</div>
                        <div class="selected-info-val" id="selectedTableName">Table 1 (4 Seats)</div>
                    </div>
                    <div class="selected-note">
                        You can change your table at the venue if alternatives are available upon arrival.
                    </div>
                </div>

            </div> <!-- End Main Steps Container -->

            <!-- SIDEBAR -->
            <div class="sidebar">
                <div class="summary-card">
                    <h3 class="summary-title">Reservation Summary</h3>
                    
                    <div class="summary-row">
                        <span class="summary-row-label">Date</span>
                        <span class="summary-row-value" id="sumDate"><?= date('M d, Y') ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-label">Time</span>
                        <span class="summary-row-value" id="sumTime">06:00 PM</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-label">Party Size</span>
                        <span class="summary-row-value" id="sumPlayers">4 Guests</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-label">Table</span>
                        <span class="summary-row-value" id="sumTable">Table 1</span>
                    </div>

                    <div class="summary-total">
                        <div class="summary-total-row">
                            <span class="summary-total-label">Estimated Total</span>
                            <span class="summary-total-price" id="sumPrice">$20.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-confirm">Confirm Reservation</button>
                    <div style="text-align:center; margin-top: 16px;">
                        <a href="<?= $baseUrl ?>/dashboard" style="color:var(--gray-text); font-size:13px; text-decoration:none; font-weight:500;">Cancel</a>
                    </div>
                </div>
            </div>

        </div> <!-- End Layout Grid -->
    </form>
</div>

<script>
    var pricePerPlayer = 5;

    function selectParty(number, btn) {
        document.querySelectorAll('.party-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('input_players').value = number;
        document.getElementById('sumPlayers').textContent = number + ' Guests';
        document.getElementById('sumPrice').textContent = '$' + (number * pricePerPlayer).toFixed(2);
    }

    function selectSlot(slot, btn) {
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('input_time').value = slot;
        document.getElementById('sumTime').textContent = slot;
    }

    function selectTable(tableId, tableName, capacity, card) {
        document.querySelectorAll('.table-card:not(.occupied)').forEach(c => {
            c.classList.remove('selected');
            var status = c.querySelector('.status-corner');
            if(status) status.textContent = 'Available';
        });

        card.classList.add('selected');
        var status = card.querySelector('.status-corner');
        if(status) status.textContent = 'Selected';

        document.getElementById('input_table_id').value = tableId;
        document.getElementById('sumTable').textContent = tableName;
        document.getElementById('selectedTableName').textContent = tableName + ' (' + capacity + ' Seats)';
    }

    function updateSummaryDate(dateValue) {
        var d = new Date(dateValue + 'T00:00:00');
        var options = { year: 'numeric', month: 'short', day: 'numeric' };
        document.getElementById('sumDate').textContent = d.toLocaleDateString('en-US', options);
    }
</script>

</body>
</html>
