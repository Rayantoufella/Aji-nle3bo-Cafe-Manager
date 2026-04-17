<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$baseUrl}/login");
    exit;
}

// Redirect if practically empty or not found
if (!$reservation) {
    header("Location: {$baseUrl}/reservations");
    exit;
}

$status = $reservation['status'] ?? 'confirmed';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details – TableTop Hub</title>
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

        .page-wrapper { max-width: 800px; margin: 0 auto; padding: 24px 16px 60px; min-height: 80vh; }
        .breadcrumb { font-size: 13px; color: var(--gray-text); margin-bottom: 20px; }
        .breadcrumb a { color: var(--gray-text); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb span { margin: 0 6px; }

        .card {
            background: var(--white);
            border-radius: 14px;
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            text-align: center;
        }

        .icon-lg {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .res-title { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .res-status {
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            margin-bottom: 24px;
        }
        .res-status.confirmed { background: #d1fae5; color: #059669; }
        .res-status.pending { background: #fef3c7; color: #d97706; }
        .res-status.cancelled { background: #fee2e2; color: #dc2626; }
        [data-theme="dark"] .res-status.confirmed { background: #064e3b; color: #6ee7b7; }
        [data-theme="dark"] .res-status.pending { background: #78350f; color: #fcd34d; }
        [data-theme="dark"] .res-status.cancelled { background: #7f1d1d; color: #fca5a5; }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            text-align: left;
            margin-bottom: 30px;
            background: var(--gray-light);
            padding: 20px;
            border-radius: 12px;
        }

        .detail-item { font-size: 14px; }
        .detail-item span { display: block; color: var(--gray-text); font-size: 12px; margin-bottom: 4px; text-transform: uppercase; font-weight: 600; }
        .detail-item strong { display: block; font-size: 16px; font-weight: 600; }

        .actions {
            display: flex;
            gap: 16px;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: opacity .2s;
        }
        .btn:hover { opacity: .9; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-danger { background: #ef4444; color: #fff; }
        
        /* ===== FOOTER ===== */
        .page-footer { background: #1e1e2e; color: #9ca3af; padding: 48px 0 24px; margin-top: 60px; }
        [data-theme="dark"] .page-footer { background: #0f172a; border-top: 1px solid #1e293b; }
        .footer-inner { max-width: 1200px; margin: 0 auto; padding: 0 16px; text-align: center; font-size: 13px; }

        @media (max-width: 600px) {
            .details-grid { grid-template-columns: 1fr; }
            .actions { flex-direction: column; }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="page-wrapper">
    <div class="breadcrumb">
        <a href="<?= $baseUrl ?>/reservations">My Reservations</a>
        <span>›</span>
        Details
    </div>

    <div class="card">
        <div class="icon-lg">🎟️</div>
        <h1 class="res-title">Reservation for Table <?= htmlspecialchars($reservation['table_id']) ?></h1>
        <div class="res-status <?= strtolower($status) ?>"><?= htmlspecialchars($status) ?></div>

        <div class="details-grid">
            <div class="detail-item">
                <span>Date & Time</span>
                <strong><?= date('l, M d, Y', strtotime($reservation['reservation_date'])) ?> at <?= htmlspecialchars($reservation['reservation_time']) ?></strong>
            </div>
            <div class="detail-item">
                <span>Party Size</span>
                <strong><?= htmlspecialchars($reservation['number_of_people']) ?> Guests</strong>
            </div>
            <div class="detail-item">
                <span>Client Name</span>
                <strong><?= htmlspecialchars($reservation['client_name']) ?></strong>
            </div>
            <div class="detail-item">
                <span>Phone</span>
                <strong><?= htmlspecialchars($reservation['phone']) ?></strong>
            </div>
        </div>

        <div class="actions">
            <a href="<?= $baseUrl ?>/reservations" class="btn btn-primary">Back to Reservations</a>
            <?php if ($status !== 'cancelled' && $status !== 'completed'): ?>
                <form action="<?= $baseUrl ?>/reservations/<?= $reservation['id'] ?>/cancel" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                    <button type="submit" class="btn btn-danger">Cancel Booking</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="page-footer">
    <div class="footer-inner">
        <span>© <?= date('Y') ?> TableTop Hub. All rights reserved.</span>
    </div>
</footer>

</body>
</html>
