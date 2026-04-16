<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Reservations</h1>
        <p>Manage all café table reservations</p>
    </div>
    <div class="page-header-right">
        <a href="<?= BASE_URL ?>/reservations/create" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            New Reservation
        </a>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card">
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value"><?= $totalCount ?></div>
        </div>
        <div class="stat-icon blue">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Today</div>
            <div class="stat-value"><?= $todayCount ?></div>
        </div>
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Pending</div>
            <div class="stat-value"><?= $pendingCount ?></div>
        </div>
        <div class="stat-icon orange">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-label">Confirmed</div>
            <div class="stat-value"><?= $confirmedCount ?></div>
        </div>
        <div class="stat-icon purple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <?php 
    $filter = $_GET['filter'] ?? 'all';
    $filters = ['all' => 'All', 'today' => 'Today', 'upcoming' => 'Upcoming', 'mine' => 'My Reservations'];
    foreach ($filters as $key => $label): ?>
        <a href="<?= BASE_URL ?>/reservations?filter=<?= $key ?>" class="filter-btn <?= $filter === $key ? 'active' : '' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="card">
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>People</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="9" style="text-align:center;padding:50px;color:var(--muted);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.3;margin-bottom:8px;display:block;margin:0 auto 12px;"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        No reservations found
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($reservations as $i => $res): ?>
                    <tr>
                        <td style="color:var(--muted);font-weight:600;">#<?= $res['id'] ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="topbar-avatar" style="width:32px;height:32px;font-size:12px;"><?= strtoupper(substr($res['client_name'], 0, 1)) ?></div>
                                <div>
                                    <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($res['client_name']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--muted);font-size:13px;"><?= htmlspecialchars($res['phone'] ?? '-') ?></td>
                        <td style="font-weight:500;"><?= date('M d, Y', strtotime($res['reservation_date'])) ?></td>
                        <td style="font-weight:600;"><?= date('H:i', strtotime($res['reservation_time'])) ?></td>
                        <td>
                            <span class="badge badge-info"><?= $res['number_of_people'] ?> pers.</span>
                        </td>
                        <td><span class="badge badge-primary">Table <?= $res['table_number'] ?? 'N/A' ?></span></td>
                        <td>
                            <?php 
                            $sc = match($res['status']) {
                                'confirmed' => 'badge-success', 'pending' => 'badge-warning',
                                'cancelled' => 'badge-danger', 'completed' => 'badge-info',
                                default => 'badge-info'
                            };
                            ?>
                            <span class="badge badge-dot <?= $sc ?>"><?= ucfirst($res['status']) ?></span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <?php if ($res['status'] === 'pending'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/reservations/<?= $res['id'] ?>/update" style="display:inline;">
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="btn btn-success btn-sm" style="padding:5px 10px;font-size:11px;" title="Confirm">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if (in_array($res['status'], ['pending', 'confirmed'])): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/reservations/<?= $res['id'] ?>/cancel" style="display:inline;" onsubmit="return confirm('Cancel this reservation?');">
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding:5px 10px;font-size:11px;" title="Cancel">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
