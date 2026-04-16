<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <div class="page-header-left">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/reservations" class="btn btn-secondary btn-icon sm" title="Back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </a>
            <h1>New Reservation</h1>
        </div>
        <p>Book a table for your gaming session</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:28px;">
    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/reservations/store" id="reservationForm">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="client_name">Customer Name *</label>
                        <input type="text" class="form-input" id="client_name" name="client_name" placeholder="Full name" required value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" class="form-input" id="phone" name="phone" placeholder="+212 600...">
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label" for="reservation_date">Date *</label>
                        <input type="date" class="form-input" id="reservation_date" name="reservation_date" min="<?= date('Y-m-d') ?>" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="reservation_time">Time *</label>
                        <input type="time" class="form-input" id="reservation_time" name="reservation_time" required value="18:00">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="number_of_people">Number of People *</label>
                        <input type="number" class="form-input" id="number_of_people" name="number_of_people" min="1" max="20" value="2" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="table_id">Select Table *</label>
                    <select class="form-select" id="table_id" name="table_id" required>
                        <option value="">Choose an available table</option>
                        <?php foreach ($tables as $table): ?>
                            <option value="<?= $table['id'] ?>">Table #<?= $table['number'] ?> — Capacity: <?= $table['capacity'] ?> persons</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border);">
                    <a href="<?= BASE_URL ?>/reservations" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Create Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Card -->
    <div>
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body" style="text-align:center;">
                <div style="width:64px;height:64px;border-radius:16px;background:var(--primary-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;">📅</div>
                <h3 style="font-size:16px;font-weight:700;margin-bottom:8px;">Booking Tips</h3>
                <ul style="text-align:left;font-size:13px;color:var(--muted);list-style:none;display:flex;flex-direction:column;gap:10px;">
                    <li style="display:flex;gap:8px;align-items:flex-start;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Select a date at least 1 hour ahead
                    </li>
                    <li style="display:flex;gap:8px;align-items:flex-start;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Choose a table matching your group size
                    </li>
                    <li style="display:flex;gap:8px;align-items:flex-start;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Reservations are confirmed by admin
                    </li>
                    <li style="display:flex;gap:8px;align-items:flex-start;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        You can cancel anytime before the session
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding:16px;">
                <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;">Available Tables</h4>
                <?php if (empty($tables)): ?>
                    <p style="color:var(--muted);font-size:13px;">No tables available right now.</p>
                <?php else: ?>
                    <?php foreach (array_slice($tables, 0, 5) as $t): ?>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border-light);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:8px;background:var(--success-bg);color:var(--success);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">#<?= $t['number'] ?></div>
                            <span style="font-size:13px;font-weight:500;">Table <?= $t['number'] ?></span>
                        </div>
                        <span class="badge badge-info"><?= $t['capacity'] ?> seats</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
