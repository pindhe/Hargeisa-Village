<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

$sql = 'SELECT * FROM reservations WHERE 1=1';
$params = [];
if ($statusFilter !== '' && in_array($statusFilter, ['pending', 'confirmed', 'declined', 'seated', 'cancelled'], true)) {
    $sql .= ' AND status = ?';
    $params[] = $statusFilter;
}
if ($dateFilter !== '') {
    $sql .= ' AND reservation_date = ?';
    $params[] = $dateFilter;
}
$sql .= ' ORDER BY reservation_date DESC, reservation_time DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();

$adminTitle = 'Reservations';
$adminSubtitle = 'Manage booking requests and table reservations';
ob_start();
?>
<a href="<?= base_url('admin/reservation-edit.php') ?>" class="admin-btn admin-btn--primary">+ Add Reservation</a>
<?php
$adminActions = ob_get_clean();
require __DIR__ . '/includes/layout.php';
?>

<form method="get" class="admin-filter-bar">
    <select name="status">
        <option value="">All statuses</option>
        <?php foreach (['pending', 'confirmed', 'declined', 'seated', 'cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="date" value="<?= e($dateFilter) ?>">
    <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
    <a href="<?= base_url('admin/reservations.php') ?>" class="admin-btn admin-btn--secondary">Clear</a>
</form>

<div class="admin-panel">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Guest</th>
                    <th>Contact</th>
                    <th>Date / time</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reservations as $r): ?>
            <tr>
                <td>#<?= (int) $r['id'] ?></td>
                <td><strong><?= e($r['full_name']) ?></strong></td>
                <td class="text-xs text-stone-500"><?= e($r['email']) ?><br><?= e($r['phone_number'] ?? '') ?></td>
                <td><?= e(format_date($r['reservation_date'])) ?><br><?= e(format_time($r['reservation_time'])) ?></td>
                <td><?= (int) $r['num_guests'] ?></td>
                <td><?= admin_status_badge($r['status']) ?></td>
                <td><a href="<?= base_url('admin/reservation-edit.php?id=' . (int) $r['id']) ?>" class="admin-table-link">Edit</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($reservations)): ?>
            <tr><td colspan="7" class="text-center text-stone-400 py-10">No reservations found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/layout-end.php'; ?>
