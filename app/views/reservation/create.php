<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

// Si l'utilisateur n'est pas connecté, redirige vers login
if (!isset($_SESSION['user_id'])) {
    header("Location: {$baseUrl}/login");
    exit;
}

// Info du jeu (passé par le controller, sinon valeurs par défaut)
$game = $game ?? [
    'id'            => 1,
    'name'          => 'Board Game',
    'category_name' => 'Strategy',
    'nb_players'    => '2-4',
];

// Créneaux horaires disponibles
$timeSlots = ['10:00 AM', '12:00 PM', '02:00 PM', '04:00 PM', '06:00 PM', '08:00 PM', '10:00 PM'];

// Tables (normalement vient de la base de données via le controller)
$tables = $tables ?? [
    ['id' => 1, 'name' => 'Table 1',    'capacity' => 4, 'status' => 'available'],
    ['id' => 2, 'name' => 'Table 2',    'capacity' => 2, 'status' => 'available'],
    ['id' => 3, 'name' => 'Table 3',    'capacity' => 6, 'status' => 'occupied'],
    ['id' => 4, 'name' => 'The Booth',  'capacity' => 2, 'status' => 'available'],
    ['id' => 5, 'name' => 'Table 5',    'capacity' => 4, 'status' => 'available'],
    ['id' => 6, 'name' => 'VIP Lounge', 'capacity' => 8, 'status' => 'available'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une Table – TableTop Hub</title>
    <style>
        /* ===== VARIABLES DE COULEURS ===== */
        :root {
            --primary:      #6366f1;
            --primary-dark: #4f46e5;
            --bg:           #f8f9fc;
            --white:        #ffffff;
            --gray-light:   #f1f3f9;
            --gray:         #e5e7eb;
            --gray-text:    #6b7280;
            --text:         #1e1e2e;
            --border:       #e5e7eb;
            --green:        #10b981;
            --red:          #ef4444;
            --shadow:       0 2px 12px rgba(99,102,241,.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* ===== MISE EN PAGE PRINCIPALE ===== */
        .page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 60px;
        }

        /* Breadcrumb (chemin de navigation) */
        .breadcrumb { font-size: 13px; color: var(--gray-text); margin-bottom: 12px; }
        .breadcrumb a { color: var(--gray-text); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb span { margin: 0 6px; }

        /* Titre de la page */
        .page-title    { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .page-subtitle { color: var(--gray-text); font-size: 14px; margin-bottom: 28px; }

        /* Grille : contenu à gauche + sidebar à droite */
        .layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }

        /* ===== ÉTAPES (CARDS) ===== */
        .step-card {
            background: var(--white);
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        /* Numéro + titre d'étape */
        .step-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .step-number {
            background: var(--primary);
            color: #fff;
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            flex-shrink: 0;
        }
        .step-title { font-size: 16px; font-weight: 600; }

        /* ===== ÉTAPE 1 : Jeu + Nombre de joueurs ===== */
        .step1-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .game-card {
            border: 2px solid var(--primary);
            border-radius: 10px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .game-img {
            width: 56px; height: 56px;
            border-radius: 8px;
            background: var(--gray-light);
            object-fit: cover;
            flex-shrink: 0;
        }
        .game-name { font-weight: 600; font-size: 15px; }
        .game-info { font-size: 12px; color: var(--gray-text); margin-top: 2px; }

        /* Sélecteur de nombre de joueurs */
        .section-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-text);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 10px;
        }
        .party-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        .party-btn {
            border: 1px solid var(--border);
            background: var(--white);
            border-radius: 8px;
            padding: 8px 0;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
        }
        .party-btn:hover, .party-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
        .party-note { font-size: 11px; color: var(--gray-text); margin-top: 8px; }

        /* ===== ÉTAPE 2 : Date & Horaires ===== */
        .step2-content {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 20px;
            align-items: start;
        }
        .date-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            background: var(--white);
            color: var(--text);
            cursor: pointer;
        }
        .date-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        .slot-btn {
            border: 1px solid var(--border);
            background: var(--white);
            border-radius: 8px;
            padding: 9px 0;
            text-align: center;
            font-size: 13px;
            cursor: pointer;
            transition: all .2s;
        }
        .slot-btn:hover, .slot-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ===== ÉTAPE 3 : Choix de la table ===== */
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .table-card {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            position: relative;
        }
        .table-card:hover:not(.occupied) {
            border-color: var(--primary);
            background: rgba(99,102,241,.04);
        }
        .table-card.selected {
            border-color: var(--primary);
            background: rgba(99,102,241,.08);
        }
        .table-card.occupied {
            opacity: .55;
            cursor: not-allowed;
        }
        .table-icon     { font-size: 28px; margin-bottom: 6px; }
        .table-name     { font-weight: 600; font-size: 14px; }
        .table-capacity { font-size: 12px; color: var(--gray-text); margin-top: 2px; }

        /* Badge de statut */
        .status-badge {
            position: absolute;
            top: 8px; right: 8px;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 20px;
        }
        .status-badge.occupied  { background: #fee2e2; color: var(--red); }
        .status-badge.selected  { background: #ede9fe; color: var(--primary); }
        .status-badge.available { background: #d1fae5; color: var(--green); }

        /* Pied de sélection de table */
        .table-footer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            font-size: 13px;
            color: var(--gray-text);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .table-footer strong { color: var(--text); }

        /* ===== SIDEBAR : Résumé ===== */
        .sidebar { position: sticky; top: 20px; }

        .summary-card {
            background: var(--white);
            border-radius: 14px;
            padding: 20px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 16px;
        }
        .summary-title { font-size: 16px; font-weight: 700; margin-bottom: 16px; }

        /* Ligne du jeu */
        .summary-game {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 14px;
        }
        .summary-game-icon {
            width: 36px; height: 36px;
            background: var(--gray-light);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .summary-game-name { font-weight: 600; font-size: 14px; }
        .summary-game-sub  { font-size: 12px; color: var(--gray-text); }
        .confirmed-badge {
            margin-left: auto;
            background: #d1fae5;
            color: var(--green);
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 20px;
        }

        /* Lignes d'infos */
        .summary-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .summary-row-icon  { font-size: 15px; color: var(--gray-text); width: 20px; text-align: center; }
        .summary-row-label { color: var(--gray-text); flex: 1; }
        .summary-row-value { font-weight: 500; }

        /* Total estimé */
        .summary-total {
            background: var(--gray-light);
            border-radius: 10px;
            padding: 12px;
            margin: 16px 0;
        }
        .summary-total-row  { display: flex; justify-content: space-between; align-items: center; font-size: 13px; }
        .summary-total-label{ color: var(--gray-text); }
        .summary-total-price{ font-size: 20px; font-weight: 700; color: var(--primary); }
        .summary-total-note { font-size: 11px; color: var(--gray-text); margin-top: 4px; }

        /* Boutons */
        .btn-confirm {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .2s;
            margin-bottom: 10px;
        }
        .btn-confirm:hover { opacity: .9; }
        .btn-back {
            display: block;
            text-align: center;
            color: var(--gray-text);
            font-size: 13px;
            text-decoration: none;
            margin-top: 4px;
        }
        .btn-back:hover { color: var(--primary); }

        /* "Pourquoi nous choisir ?" */
        .why-card {
            background: var(--white);
            border-radius: 14px;
            padding: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        .why-title { font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 10px; }
        .why-item  { display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: var(--gray-text); margin-bottom: 7px; }
        .why-item::before { content: "✓"; color: var(--green); font-weight: 700; flex-shrink: 0; }

        /* ===== FOOTER ===== */
        .page-footer {
            background: #1e1e2e;
            color: #9ca3af;
            padding: 48px 0 24px;
            margin-top: 60px;
        }
        .footer-inner { max-width: 1100px; margin: 0 auto; padding: 0 16px; }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }
        .footer-brand { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .footer-desc  { font-size: 13px; line-height: 1.6; }
        .footer-col-title { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 12px; }
        .footer-col a { display: block; font-size: 13px; color: #9ca3af; text-decoration: none; margin-bottom: 7px; }
        .footer-col a:hover { color: #fff; }
        .footer-bottom {
            border-top: 1px solid #374151;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .footer-bottom-links a { color: #9ca3af; text-decoration: none; margin-left: 16px; }
        .footer-bottom-links a:hover { color: #fff; }

        /* Responsive mobile */
        @media (max-width: 768px) {
            .layout, .step1-content, .step2-content { grid-template-columns: 1fr; }
            .tables-grid { grid-template-columns: repeat(2, 1fr); }
            .slots-grid  { grid-template-columns: repeat(3, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<!-- ===== HEADER (menu de navigation) ===== -->
<?php require_once __DIR__ . '/../layout/header.php'; ?>

<!-- ===== CONTENU PRINCIPAL ===== -->
<div class="page-wrapper">

    <!-- Breadcrumb (chemin cliquable) -->
    <div class="breadcrumb">
        <a href="<?= $baseUrl ?>/dashboard">Catalogue</a>
        <span>›</span>
        <a href="<?= $baseUrl ?>/games/<?= htmlspecialchars($game['id']) ?>"><?= htmlspecialchars($game['name']) ?></a>
        <span>›</span>
        Créer une réservation
    </div>

    <h1 class="page-title">Secure Your Table</h1>
    <p class="page-subtitle">
        Ready to settle into your next adventure? Choose your preferred date, time, and table to
        guarantee a spot for your gaming session.
    </p>

    <!-- Formulaire qui envoie les données au controller -->
    <form method="POST" action="<?= $baseUrl ?>/reservations">

        <!-- Champs cachés (remplis automatiquement par JavaScript) -->
        <input type="hidden" name="user_id"          value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
        <input type="hidden" name="client_name"      value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
        <input type="hidden" name="phone"            value="<?= htmlspecialchars($_SESSION['phone'] ?? '0600000000') ?>">
        <input type="hidden" name="number_of_people" id="input_players"  value="4">
        <input type="hidden" name="table_id"         id="input_table_id" value="1">
        <input type="hidden" name="reservation_time" id="input_time"     value="06:00 PM">

        <div class="layout">

            <!-- ===== COLONNE GAUCHE : Les 3 étapes ===== -->
            <div>

                <!-- ÉTAPE 1 : Jeu sélectionné + Nombre de joueurs -->
                <div class="step-card">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <div class="step-title">General Preferences</div>
                    </div>

                    <div class="step1-content">

                        <!-- Carte du jeu -->
                        <div>
                            <div class="section-label">Board Game</div>
                            <div class="game-card">
                                <img class="game-img"
                                     src="<?= $baseUrl ?>/app/views/img/game/catan.jpg"
                                     onerror="this.style.background='#e0e7ff'"
                                     alt="<?= htmlspecialchars($game['name']) ?>">
                                <div>
                                    <div class="game-name"><?= htmlspecialchars($game['name']) ?></div>
                                    <div class="game-info">
                                        <?= htmlspecialchars($game['category_name']) ?> • <?= htmlspecialchars($game['nb_players']) ?> Players
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sélecteur du nombre de joueurs -->
                        <div>
                            <div class="section-label">Party Size</div>
                            <div class="party-grid">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <button type="button"
                                            class="party-btn <?= ($i === 4) ? 'active' : '' ?>"
                                            onclick="selectParty(<?= $i ?>, this)">
                                        <?= $i ?>
                                    </button>
                                <?php endfor; ?>
                            </div>
                            <p class="party-note">
                                ⚠ Minimum 2 players for <?= htmlspecialchars($game['name']) ?> recommended.
                            </p>
                        </div>

                    </div>
                </div><!-- fin étape 1 -->

                <!-- ÉTAPE 2 : Date & Horaire -->
                <div class="step-card">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <div class="step-title">Select Date & Time</div>
                    </div>

                    <div class="step2-content">

                        <!-- Sélecteur de date -->
                        <div>
                            <div class="section-label">Choose Date</div>
                            <input type="date"
                                   class="date-input"
                                   name="reservation_date"
                                   id="reservationDate"
                                   value="<?= date('Y-m-d') ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   required
                                   onchange="updateSummaryDate(this.value)">
                        </div>

                        <!-- Créneaux horaires -->
                        <div>
                            <div class="section-label">Available Slots</div>
                            <div class="slots-grid">
                                <?php foreach ($timeSlots as $slot): ?>
                                    <button type="button"
                                            class="slot-btn <?= ($slot === '06:00 PM') ? 'active' : '' ?>"
                                            onclick="selectSlot('<?= $slot ?>', this)">
                                        <?= $slot ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </div><!-- fin étape 2 -->

                <!-- ÉTAPE 3 : Choix de la table -->
                <div class="step-card">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <div class="step-title">Choose Your Table</div>
                    </div>

                    <div class="tables-grid">
                        <?php foreach ($tables as $table): ?>
                            <?php
                            // Détermine le style de chaque table
                            $isOccupied = ($table['status'] === 'occupied');
                            $isSelected = ($table['id'] === 1); // Table 1 par défaut
                            $cardClass  = $isOccupied ? 'occupied' : ($isSelected ? 'selected' : '');
                            $badgeClass = $isOccupied ? 'occupied' : ($isSelected ? 'selected' : 'available');
                            $badgeLabel = $isOccupied ? 'Occupied' : ($isSelected ? 'Selected' : 'Available');
                            ?>
                            <div class="table-card <?= $cardClass ?>"
                                 id="table-<?= $table['id'] ?>"
                                 <?php if (!$isOccupied): ?>
                                     onclick="selectTable(<?= $table['id'] ?>, '<?= htmlspecialchars($table['name']) ?>', <?= $table['capacity'] ?>, this)"
                                 <?php endif; ?>>

                                <span class="status-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span>
                                <div class="table-icon">🪑</div>
                                <div class="table-name"><?= htmlspecialchars($table['name']) ?></div>
                                <div class="table-capacity">Máx <?= $table['capacity'] ?> seats</div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Table actuellement sélectionnée -->
                    <div class="table-footer">
                        📍 <strong>Selected Table:</strong>&nbsp;
                        <span id="selectedTableName">Table 1 (4 Seats)</span>
                        <span style="margin-left:auto; font-size:12px;">
                            You can change your table at the venue if alternatives are available upon arrival.
                        </span>
                    </div>

                </div><!-- fin étape 3 -->

            </div><!-- fin colonne gauche -->

            <!-- ===== SIDEBAR : Résumé de réservation ===== -->
            <div class="sidebar">

                <div class="summary-card">
                    <div class="summary-title">Reservation Summary</div>

                    <!-- Jeu sélectionné -->
                    <div class="summary-game">
                        <div class="summary-game-icon">🎲</div>
                        <div>
                            <div class="summary-game-name"><?= htmlspecialchars($game['name']) ?> Session</div>
                            <div class="summary-game-sub">Save Game • <?= htmlspecialchars($game['name']) ?></div>
                        </div>
                        <span class="confirmed-badge">Confirmed</span>
                    </div>

                    <!-- Infos de la réservation -->
                    <div class="summary-row">
                        <span class="summary-row-icon">📅</span>
                        <span class="summary-row-label">Date</span>
                        <span class="summary-row-value" id="sumDate"><?= date('M d, Y') ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-icon">🕐</span>
                        <span class="summary-row-label">Time</span>
                        <span class="summary-row-value" id="sumTime">06:00 PM</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-icon">👥</span>
                        <span class="summary-row-label">Players</span>
                        <span class="summary-row-value" id="sumPlayers">4 Guests</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-icon">🪑</span>
                        <span class="summary-row-label">Table</span>
                        <span class="summary-row-value" id="sumTable">Table 1</span>
                    </div>

                    <!-- Prix estimé -->
                    <div class="summary-total">
                        <div class="summary-total-row">
                            <span class="summary-total-label">Estimated Total</span>
                            <span class="summary-total-price" id="sumPrice">$15.00</span>
                        </div>
                        <div class="summary-total-note">
                            Includes table fee and initial 3-hour food and drinks billed separately.
                        </div>
                    </div>

                    <!-- Bouton de confirmation -->
                    <button type="submit" class="btn-confirm">
                        Confirm Reservation
                    </button>

                    <!-- Lien retour -->
                    <a href="<?= $baseUrl ?>/games/<?= (int)$game['id'] ?>" class="btn-back">
                        ← Go Back
                    </a>
                </div>

                <!-- Section "Pourquoi nous choisir ?" -->
                <div class="why-card">
                    <div class="why-title">🔵 Why Book With Us?</div>
                    <div class="why-item">Exclusive access to the VIP library</div>
                    <div class="why-item">Premium ergonomic gaming chairs</div>
                    <div class="why-item">Dedicated table service for snacks</div>
                    <div class="why-item">Climate-controlled gaming zones</div>
                </div>

            </div><!-- fin sidebar -->

        </div><!-- fin layout -->
    </form>

</div><!-- fin page-wrapper -->

<!-- ===== FOOTER ===== -->
<footer class="page-footer">
    <div class="footer-inner">
        <div class="footer-grid">

            <div>
                <div class="footer-brand">🎲 TableTop Hub</div>
                <p class="footer-desc">
                    The ultimate destination for tabletop enthusiasts. Book tables,
                    track sessions, and discover your next favorite board game.
                </p>
            </div>

            <div class="footer-col">
                <div class="footer-col-title">Platform</div>
                <a href="<?= $baseUrl ?>/dashboard">Browse Games</a>
                <a href="<?= $baseUrl ?>/reservations">My Reservations</a>
                <a href="#">Gaming History</a>
            </div>

            <div class="footer-col">
                <div class="footer-col-title">Support</div>
                <a href="#">Help Center</a>
                <a href="#">Rules & Conduct</a>
                <a href="#">Contact Us</a>
            </div>

            <div class="footer-col">
                <div class="footer-col-title">Connect</div>
                <a href="#">Instagram</a>
                <a href="#">Twitter / X</a>
                <a href="#">Discord</a>
            </div>

        </div>

        <div class="footer-bottom">
            <span>© 2024 TableTop Hub. All rights reserved.</span>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- ===== JAVASCRIPT : interactions ===== -->
<script>
    var pricePerPlayer = 5; // 5$ par joueur

    // --- 1. Sélection du nombre de joueurs ---
    function selectParty(number, btn) {
        // Enlève "active" de tous les boutons
        document.querySelectorAll('.party-btn').forEach(function(b) {
            b.classList.remove('active');
        });
        btn.classList.add('active');

        // Met à jour le champ caché et le résumé
        document.getElementById('input_players').value  = number;
        document.getElementById('sumPlayers').textContent = number + ' Guests';
        document.getElementById('sumPrice').textContent   = '$' + (number * pricePerPlayer).toFixed(2);
    }

    // --- 2. Sélection d'un créneau horaire ---
    function selectSlot(slot, btn) {
        document.querySelectorAll('.slot-btn').forEach(function(b) {
            b.classList.remove('active');
        });
        btn.classList.add('active');

        document.getElementById('input_time').value      = slot;
        document.getElementById('sumTime').textContent   = slot;
    }

    // --- 3. Sélection d'une table ---
    function selectTable(tableId, tableName, capacity, card) {
        // Remet toutes les tables non-occupées en "available"
        document.querySelectorAll('.table-card:not(.occupied)').forEach(function(c) {
            c.classList.remove('selected');
            var badge = c.querySelector('.status-badge');
            if (badge) {
                badge.className   = 'status-badge available';
                badge.textContent = 'Available';
            }
        });

        // Marque la table cliquée comme sélectionnée
        card.classList.add('selected');
        var badge = card.querySelector('.status-badge');
        badge.className   = 'status-badge selected';
        badge.textContent = 'Selected';

        // Met à jour les champs et le résumé
        document.getElementById('input_table_id').value          = tableId;
        document.getElementById('sumTable').textContent          = tableName;
        document.getElementById('selectedTableName').textContent = tableName + ' (' + capacity + ' Seats)';
    }

    // --- 4. Mise à jour de la date dans le résumé ---
    function updateSummaryDate(dateValue) {
        // Transforme "2024-05-24" en "May 24, 2024"
        var d = new Date(dateValue + 'T00:00:00');
        var options = { year: 'numeric', month: 'short', day: 'numeric' };
        document.getElementById('sumDate').textContent = d.toLocaleDateString('en-US', options);
    }
</script>

</body>
</html>
