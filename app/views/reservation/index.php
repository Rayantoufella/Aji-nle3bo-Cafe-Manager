<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

// Si l'utilisateur n'est pas connecté, redirige vers login
if (!isset($_SESSION['user_id'])) {
    header("Location: {$baseUrl}/login");
    exit;
}

$reservations = $reservations ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations – TableTop Hub</title>
    <style>
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
            --shadow:       0 2px 12px rgba(99,102,241,.12);
        }

        [data-theme="dark"] body {
            --bg: #111827;
            --white: #1f2937;
            --gray-light: #374151;
            --gray: #4b5563;
            --border: #374151;
            --text: #f9fafb;
            --gray-text: #9ca3af;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); }

        .page-wrapper { max-width: 1200px; margin: 0 auto; padding: 24px 16px 60px; min-height: 80vh; }
        .page-title { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .page-subtitle { color: var(--gray-text); font-size: 14px; margin-bottom: 28px; }

        .reservation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .reservation-card {
            background: var(--white);
            border-radius: 14px;
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: transform .2s, box-shadow .2s;
            text-decoration: none;
            color: var(--text);
            display: block;
        }

        .reservation-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(99,102,241,.15);
            border-color: var(--primary);
        }

        /* Badge status */
        .status-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .status-badge.confirmed { background: #d1fae5; color: #059669; }
        .status-badge.pending { background: #fef3c7; color: #d97706; }
        .status-badge.cancelled { background: #fee2e2; color: #dc2626; }
        [data-theme="dark"] .status-badge.confirmed { background: #064e3b; color: #6ee7b7; }
        [data-theme="dark"] .status-badge.pending { background: #78350f; color: #fcd34d; }
        [data-theme="dark"] .status-badge.cancelled { background: #7f1d1d; color: #fca5a5; }

        .reservation-info { margin-bottom: 16px; }
        .reservation-info strong { font-size: 18px; display: block; margin-bottom: 4px; }
        .reservation-info p { font-size: 13px; color: var(--gray-text); display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }

        .btn-view {
            display: inline-block;
            background: var(--gray-light);
            color: var(--text);
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            width: 100%;
            transition: background .2s, color .2s;
        }
        .reservation-card:hover .btn-view {
            background: var(--primary);
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 14px;
            border: 1px dashed var(--border);
        }
        .empty-state h3 { margin-bottom: 8px; }
        .empty-state p { color: var(--gray-text); font-size: 14px; margin-bottom: 16px; }
        .btn-primary { background: var(--primary); color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500; display: inline-block; }

        /* ===== FOOTER ===== */
        .page-footer { background: #1e1e2e; color: #9ca3af; padding: 48px 0 24px; margin-top: 60px; }
        [data-theme="dark"] .page-footer { background: #0f172a; border-top: 1px solid #1e293b; }
        .footer-inner { max-width: 1200px; margin: 0 auto; padding: 0 16px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 32px; margin-bottom: 32px; }
        .footer-brand { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .footer-desc { font-size: 13px; line-height: 1.6; }
        .footer-col-title { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 12px; }
        .footer-col a { display: block; font-size: 13px; color: #9ca3af; text-decoration: none; margin-bottom: 7px; }
        .footer-col a:hover { color: #fff; }
        .footer-bottom { border-top: 1px solid #374151; padding-top: 20px; display: flex; justify-content: space-between; font-size: 12px; }
        .footer-bottom-links a { color: #9ca3af; text-decoration: none; margin-left: 16px; }
        .footer-bottom-links a:hover { color: #fff; }

        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .footer-bottom { flex-direction: column; align-items: center; gap: 10px; }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-wrapper">
    <h1 class="page-title">My Reservations</h1>
    <p class="page-subtitle">Track and manage your upcoming and past bookings.</p>

    <?php if (empty($reservations)): ?>
        <div class="empty-state">
            <h3>No Reservations Found</h3>
            <p>You haven't booked any tables yet. Start planning your next game night!</p>
            <a href="<?= $baseUrl ?>/dashboard" class="btn-primary">Browse Games</a>
        </div>
    <?php else: ?>
        <div class="reservation-grid">
            <?php foreach ($reservations as $res): ?>
                <?php
                // Using dynamic status or falling back to a default confirmed because the model sets confirmed by default.
                $status = $res['status'] ?? 'confirmed';
                $statusClass = strtolower($status);
                ?>
                <a href="<?= $baseUrl ?>/reservations/<?= htmlspecialchars($res['id']) ?>" class="reservation-card">
                    <span class="status-badge <?= $statusClass ?>">
                        <?= htmlspecialchars(ucfirst($status)) ?>
                    </span>
                    <div class="reservation-info">
                        <strong>Table <?= htmlspecialchars($res['table_id']) ?> Booking</strong>
                        <p>📅 <?= date('M d, Y', strtotime($res['reservation_date'])) ?></p>
                        <p>🕐 <?= htmlspecialchars($res['reservation_time']) ?></p>
                        <p>👥 <?= htmlspecialchars($res['number_of_people']) ?> Guests</p>
                    </div>
                    <div class="btn-view">View Details</div>
                </a>
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

</body>
</html>
