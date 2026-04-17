<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:      #6366f1;
            --bg:           #0f172a;
            --bg-card:      #1e293b;
            --text:         #f8fafc;
            --gray-text:    #94a3b8;
            --border:       #334155;
            --orange:       #ea580c;
            --orange-bg:    #78350f;
            --green:        #10b981;
            --green-bg:     #064e3b;
            --shadow:       0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding-bottom: 60px; }

        .page-wrapper { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
        .page-title { font-size: 28px; font-weight: 700; margin-bottom: 8px; color: #fff; }
        .page-subtitle { color: var(--gray-text); font-size: 15px; margin-bottom: 32px; }

        .reservation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
        }

        .reservation-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border);
            text-decoration: none;
            color: var(--text);
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: var(--shadow);
        }

        .reservation-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: fit-content;
        }
        .status-badge.confirmed { background: var(--green-bg); color: var(--green); }
        .status-badge.pending { background: var(--orange-bg); color: #f97316; }
        .status-badge.cancelled { background: #7f1d1d; color: #fca5a5; }

        .reservation-info { margin-bottom: 24px; flex-grow: 1; }
        .reservation-info strong { font-size: 20px; display: block; margin-bottom: 12px; color: #fff; }
        .reservation-info p { 
            font-size: 14px; 
            color: var(--gray-text); 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            margin-bottom: 8px; 
        }

        .timer {
            font-size: 12px;
            color: #60a5fa;
            background: rgba(96,165,250,0.1);
            padding: 4px 8px;
            border-radius: 4px;
            margin-top: 12px;
            display: inline-block;
            font-weight: 600;
        }

        .btn-view {
            background: rgba(255,255,255,0.05);
            color: var(--text);
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            width: 100%;
            transition: background 0.2s;
        }
        .reservation-card:hover .btn-view {
            background: rgba(255,255,255,0.1);
        }

        .empty-state { text-align: center; padding: 80px 20px; background: var(--bg-card); border-radius: 12px; border: 1px dashed var(--border); }
        .empty-state h3 { margin-bottom: 12px; font-size: 20px; color: #fff; }
        .empty-state p { color: var(--gray-text); font-size: 15px; margin-bottom: 24px; }
        .btn-primary { background: var(--primary); color: #fff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-size: 15px; font-weight: 600; display: inline-block; transition: background 0.2s; }
        .btn-primary:hover { background: #4f46e5; }
    </style>
</head>
<body>

<?php require_once dirname(__DIR__) . '/layout/header.php'; ?>

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
                $status = $res['status'] ?? 'pending';
                $statusClass = strtolower($status);
                ?>
                <a href="<?= $baseUrl ?>/reservations/<?= htmlspecialchars($res['id']) ?>" class="reservation-card">
                    <span class="status-badge <?= $statusClass ?>">
                        <?= htmlspecialchars(ucfirst($status)) ?>
                    </span>
                    <div class="reservation-info">
                        <strong>Table <?= htmlspecialchars($res['table_id'] ?? $res['id']) ?> Booking</strong>
                        <p>📅 <?= date('M d, Y', strtotime($res['reservation_date'])) ?></p>
                        <p>🕐 <?= htmlspecialchars($res['reservation_time']) ?></p>
                        <p>👥 <?= htmlspecialchars($res['number_of_people']) ?> Guests</p>
                        
                        <?php 
                            $datetime = $res['reservation_date'] . ' ' . $res['reservation_time'];
                        ?>
                        <div class="timer" data-time="<?= htmlspecialchars($datetime) ?>">Calculating...</div>
                    </div>
                    <div class="btn-view">View Details</div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function updateTimers() {
        document.querySelectorAll('.timer').forEach(function(el) {
            const timeString = el.getAttribute('data-time');
            if(!timeString) return;

            const target = new Date(timeString).getTime();
            const now = new Date().getTime();
            const diff = target - now;

            if(diff <= 0) {
                el.innerText = '✅ Session is active';
                el.style.color = '#34d399';
                el.style.background = 'rgba(52, 211, 153, 0.1)';
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((diff % (1000 * 60)) / 1000);

            let txt = '⏳ ';
            if(days > 0) txt += days + 'd ';
            if(hours > 0 || days > 0) txt += hours + 'h ';
            txt += mins + 'm ' + secs + 's';

            el.innerText = txt;
        });
    }

    setInterval(updateTimers, 1000);
    updateTimers();
</script>

</body>
</html>
