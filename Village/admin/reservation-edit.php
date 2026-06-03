<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$reservation = null;

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE id = ?');
    $stmt->execute([$id]);
    $reservation = $stmt->fetch();
    if (!$reservation) {
        flash('error', 'Reservation not found.');
        redirect(base_url('admin/reservations.php'));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $data = [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'reservation_date' => $_POST['reservation_date'] ?? '',
        'reservation_time' => $_POST['reservation_time'] ?? '',
        'num_guests' => (int) ($_POST['num_guests'] ?? 1),
        'special_requests' => trim($_POST['special_requests'] ?? ''),
        'status' => $_POST['status'] ?? 'pending',
        'notes' => trim($_POST['notes'] ?? ''),
    ];
    $oldStatus = $reservation['status'] ?? null;

    if ($id > 0) {
        $pdo->prepare(
            'UPDATE reservations SET full_name=?, email=?, phone_number=?, reservation_date=?, reservation_time=?,
             num_guests=?, special_requests=?, status=?, notes=? WHERE id=?'
        )->execute([
            $data['full_name'], $data['email'], $data['phone_number'], $data['reservation_date'],
            $data['reservation_time'], $data['num_guests'], $data['special_requests'],
            $data['status'], $data['notes'], $id,
        ]);
        $data['id'] = $id;
        if ($oldStatus !== $data['status'] && isset($_POST['notify_customer'])) {
            Mailer::reservationStatusUpdate(array_merge($reservation, $data));
        }
        flash('success', 'Reservation updated.');
    } else {
        $pdo->prepare(
            'INSERT INTO reservations (full_name, email, phone_number, reservation_date, reservation_time, num_guests, special_requests, status, notes)
             VALUES (?,?,?,?,?,?,?,?,?)'
        )->execute([
            $data['full_name'], $data['email'], $data['phone_number'], $data['reservation_date'],
            $data['reservation_time'], $data['num_guests'], $data['special_requests'],
            $data['status'], $data['notes'],
        ]);
        flash('success', 'Reservation created.');
    }
    redirect(base_url('admin/reservations.php'));
}

$r = $reservation ?: [
    'full_name' => '', 'email' => '', 'phone_number' => '', 'reservation_date' => date('Y-m-d'),
    'reservation_time' => '18:00', 'num_guests' => 2, 'special_requests' => '', 'status' => 'pending', 'notes' => '',
];
$timeSlots = reservation_time_slots();
$adminTitle = $id ? 'Edit Reservation' : 'Add Reservation';
require __DIR__ . '/includes/layout.php';
?>
<form method="post" class="admin-form-card max-w-2xl space-y-4">
    <?= csrf_field() ?>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="block text-sm font-medium mb-1">Full Name</label><input name="full_name" required value="<?= e($r['full_name']) ?>" class="w-full border rounded-lg px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="email" required value="<?= e($r['email']) ?>" class="w-full border rounded-lg px-3 py-2"></div>
    </div>
    <div><label class="block text-sm font-medium mb-1">Phone</label><input name="phone_number" value="<?= e($r['phone_number'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2"></div>
    <div class="grid sm:grid-cols-3 gap-4">
        <div><label class="block text-sm font-medium mb-1">Date</label><input type="date" name="reservation_date" required value="<?= e($r['reservation_date']) ?>" class="w-full border rounded-lg px-3 py-2"></div>
        <div><label class="block text-sm font-medium mb-1">Time</label>
            <select name="reservation_time" class="w-full border rounded-lg px-3 py-2">
                <?php foreach ($timeSlots as $slot): ?>
                <option value="<?= e($slot) ?>" <?= ($r['reservation_time'] === $slot || substr((string)$r['reservation_time'], 0, 5) === $slot) ? 'selected' : '' ?>><?= e(format_time($slot)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label class="block text-sm font-medium mb-1">Guests</label><input type="number" name="num_guests" min="1" max="20" value="<?= (int) $r['num_guests'] ?>" class="w-full border rounded-lg px-3 py-2"></div>
    </div>
    <div><label class="block text-sm font-medium mb-1">Special Requests</label><textarea name="special_requests" rows="2" class="w-full border rounded-lg px-3 py-2"><?= e($r['special_requests'] ?? '') ?></textarea></div>
    <div><label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full border rounded-lg px-3 py-2">
            <?php foreach (['pending', 'confirmed', 'declined', 'seated', 'cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $r['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($id): ?>
    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="notify_customer" value="1"> Email customer about status change</label>
    <?php endif; ?>
    <div><label class="block text-sm font-medium mb-1">Internal Notes</label><textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2"><?= e($r['notes'] ?? '') ?></textarea></div>
    <div class="flex gap-3">
        <button type="submit" class="admin-btn admin-btn--primary">Save</button>
        <a href="<?= base_url('admin/reservations.php') ?>" class="admin-btn admin-btn--secondary">Cancel</a>
    </div>
</form>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
