<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/reservations" class="btn btn-secondary btn-icon sm" title="Back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <h1>Reservation #<?= $reservation['id'] ?></h1>
        </div>
        <p>Reservation details</p>
    </div>
    <div class="page-header-right">
        <?php if (in_array($reservation['status'], ['pending', 'confirmed'])): ?>
            <form method="POST" action="<?= BASE_URL ?>/reservations/<?= $reservation['id'] ?>/cancel" onsubmit="return confirm('Cancel this reservation?');">
                <button type="submit" class="btn btn-danger">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    Cancel Reservation
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <?php 
        $sc = match($reservation['status']) {
            'confirmed' => 'badge-success', 'pending' => 'badge-warning',
            'cancelled' => 'badge-danger', 'completed' => 'badge-info',
            default => 'badge-info'
        };
        ?>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
            <h2 style="font-size:20px;font-weight:800;"><?= htmlspecialchars($reservation['client_name']) ?></h2>
            <span class="badge badge-dot <?= $sc ?>" style="font-size:13px;padding:6px 14px;"><?= ucfirst($reservation['status']) ?></span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:18px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Date</div>
                <div style="font-size:16px;font-weight:700;"><?= date('l, M d, Y', strtotime($reservation['reservation_date'])) ?></div>
            </div>
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:18px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Time</div>
                <div style="font-size:16px;font-weight:700;"><?= date('H:i', strtotime($reservation['reservation_time'])) ?></div>
            </div>
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:18px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Guests</div>
                <div style="font-size:16px;font-weight:700;"><?= $reservation['number_of_people'] ?> persons</div>
            </div>
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:18px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Table</div>
                <div style="font-size:16px;font-weight:700;">Table #<?= $reservation['table_number'] ?? 'N/A' ?></div>
            </div>
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:18px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Phone</div>
                <div style="font-size:16px;font-weight:700;"><?= htmlspecialchars($reservation['phone'] ?: 'N/A') ?></div>
            </div>
            <div style="background:var(--bg);border-radius:var(--radius-sm);padding:18px;">
                <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Booked On</div>
                <div style="font-size:16px;font-weight:700;"><?= date('M d, Y H:i', strtotime($reservation['created_at'])) ?></div>
            </div>
        </div>

        <?php if ($reservation['status'] === 'pending'): ?>
        <div style="display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
            <form method="POST" action="<?= BASE_URL ?>/reservations/<?= $reservation['id'] ?>/update" style="flex:1;">
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn btn-success" style="width:100%;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Confirm Reservation
                </button>
            </form>
            <form method="POST" action="<?= BASE_URL ?>/reservations/<?= $reservation['id'] ?>/cancel" style="flex:1;">
                <button type="submit" class="btn btn-danger" style="width:100%;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    Cancel
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
