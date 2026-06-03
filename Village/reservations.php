<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone_number' => trim($_POST['phone_number'] ?? ''),
            'reservation_date' => $_POST['reservation_date'] ?? '',
            'reservation_time' => $_POST['reservation_time'] ?? '',
            'num_guests' => (int) ($_POST['num_guests'] ?? 0),
            'special_requests' => trim($_POST['special_requests'] ?? ''),
        ];
        store_old_input($data);

        if ($data['full_name'] === '') $errors[] = 'Full name is required.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
        if ($data['reservation_date'] === '' || strtotime($data['reservation_date']) < strtotime('today')) {
            $errors[] = 'Please select a valid future date.';
        }
        if ($data['reservation_time'] === '') $errors[] = 'Please select a time.';
        if ($data['num_guests'] < 1 || $data['num_guests'] > 20) $errors[] = 'Number of guests must be between 1 and 20.';

        if (empty($errors)) {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare(
                'INSERT INTO reservations (full_name, email, phone_number, reservation_date, reservation_time, num_guests, special_requests)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['phone_number'],
                $data['reservation_date'],
                $data['reservation_time'],
                $data['num_guests'],
                $data['special_requests'],
            ]);
            $data['id'] = (int) $pdo->lastInsertId();
            $data['status'] = 'pending';
            Mailer::reservationConfirmation($data);
            clear_old_input();
            $success = true;
        }
    }
}

$timeSlots = reservation_time_slots();
$pageTitle = 'Reservations';
$currentPage = 'reservations.php';
require __DIR__ . '/includes/layout/header.php';

$bannerTitle = 'Reservations';
$bannerSubtitle = 'Book your table online. We will confirm your reservation by email shortly.';
$bannerEyebrow = 'Book Now';
require __DIR__ . '/includes/layout/page-banner.php';
?>

<section class="page-section bg-stone-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-5 gap-10 lg:gap-12">
            <aside class="lg:col-span-2 space-y-6">
                <div class="info-card">
                    <h2 class="font-display text-2xl text-brand-900 mb-4">Visit Us</h2>
                    <ul class="space-y-4 text-stone-600 text-sm">
                        <li class="flex gap-3">
                            <span class="text-accent font-bold shrink-0">01</span>
                            <span><strong class="text-stone-800 block">Address</strong><?= e(Settings::get('address')) ?></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-accent font-bold shrink-0">02</span>
                            <span><strong class="text-stone-800 block">Phone</strong>
                            <a href="tel:<?= e(preg_replace('/\s+/', '', Settings::get('phone'))) ?>" class="text-accent hover:underline"><?= e(Settings::get('phone')) ?></a></span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-accent font-bold shrink-0">03</span>
                            <span><strong class="text-stone-800 block">Hours</strong><?= e(hero_hours_summary()) ?></span>
                        </li>
                    </ul>
                </div>
                <div class="info-card bg-brand-900 text-white border-brand-800">
                    <h3 class="font-display text-lg mb-2">Same-day bookings?</h3>
                    <p class="text-stone-300 text-sm">Call us directly for the fastest confirmation on short notice.</p>
                </div>
            </aside>
            <div class="lg:col-span-3">
                <?php if ($success): ?>
                <div class="form-card text-center py-12" role="alert">
                    <div class="w-16 h-16 mx-auto rounded-full bg-green-100 flex items-center justify-center text-green-600 text-2xl mb-4">✓</div>
                    <h2 class="font-display text-2xl text-green-900">Reservation Submitted</h2>
                    <p class="text-green-800 mt-2 max-w-md mx-auto">Thank you! We have received your request and sent a confirmation email. Our team will confirm your booking soon.</p>
                    <a href="<?= e(home_url()) ?>" class="inline-block mt-6 text-accent font-semibold hover:underline">Return to Home</a>
                </div>
                <?php else: ?>
                <?php if ($errors): ?>
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-red-800 text-sm" role="alert">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <form method="post" class="form-card space-y-5" novalidate>
                    <?= csrf_field() ?>
                    <h2 class="font-display text-2xl text-brand-900">Reservation Details</h2>
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-stone-700 mb-1">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required value="<?= old_input('full_name') ?>" class="form-input">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-stone-700 mb-1">Email *</label>
                            <input type="email" id="email" name="email" required value="<?= old_input('email') ?>" class="form-input">
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-stone-700 mb-1">Phone</label>
                            <input type="tel" id="phone_number" name="phone_number" value="<?= old_input('phone_number') ?>" class="form-input">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="reservation_date" class="block text-sm font-medium text-stone-700 mb-1">Date *</label>
                            <input type="date" id="reservation_date" name="reservation_date" required min="<?= date('Y-m-d') ?>" value="<?= old_input('reservation_date') ?>" class="form-input">
                        </div>
                        <div>
                            <label for="reservation_time" class="block text-sm font-medium text-stone-700 mb-1">Time *</label>
                            <select id="reservation_time" name="reservation_time" required class="form-input">
                                <option value="">Select time</option>
                                <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?= e($slot) ?>" <?= (($_SESSION['old_input']['reservation_time'] ?? '') === $slot) ? 'selected' : '' ?>><?= e(format_time($slot)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="num_guests" class="block text-sm font-medium text-stone-700 mb-1">Number of Guests *</label>
                        <select id="num_guests" name="num_guests" required class="form-input">
                            <?php for ($g = 1; $g <= 20; $g++): ?>
                            <option value="<?= $g ?>" <?= ((int)($_SESSION['old_input']['num_guests'] ?? 2) === $g) ? 'selected' : '' ?>><?= $g ?> <?= $g === 1 ? 'guest' : 'guests' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label for="special_requests" class="block text-sm font-medium text-stone-700 mb-1">Special Requests</label>
                        <textarea id="special_requests" name="special_requests" rows="3" class="form-input" placeholder="High chair, accessibility needs, celebration, etc."><?= old_input('special_requests') ?></textarea>
                    </div>
                    <button type="submit" class="btn-accent w-full py-3.5">Submit Reservation</button>
                    <p class="text-xs text-stone-500 text-center">Reservations are subject to confirmation.</p>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
