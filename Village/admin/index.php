<?php



declare(strict_types=1);



require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();
$stats = admin_dashboard_stats();

$recent = $pdo->query(

    'SELECT * FROM reservations ORDER BY created_at DESC LIMIT 6'

)->fetchAll();

$upcoming = admin_dashboard_upcoming(5);

$recentMessages = admin_dashboard_recent_messages(4);



$user = current_user();

$restaurantName = Settings::get('restaurant_name', 'Hargeisa Village Restaurant');

$needsAttention = $stats['pending'] > 0 || $stats['unread_messages'] > 0;



$adminTitle = 'Dashboard';

$adminSubtitle = null;

ob_start();

?>

<a href="<?= base_url('admin/reservation-edit.php') ?>" class="admin-btn admin-btn--primary">+ New Reservation</a>

<?php

$adminActions = ob_get_clean();

require __DIR__ . '/includes/layout.php';



$statCards = [

    [

        'label' => 'Today',

        'value' => $stats['today_reservations'],

        'hint' => 'Reservations today',

        'href' => base_url('admin/reservations.php?date=' . date('Y-m-d')),

        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',

        'bg' => 'bg-orange-100 text-accent',

        'ring' => 'hover:ring-orange-200',

    ],

    [

        'label' => 'Pending',

        'value' => $stats['pending'],

        'hint' => 'Awaiting confirmation',

        'href' => base_url('admin/reservations.php?status=pending'),

        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',

        'bg' => 'bg-amber-100 text-amber-700',

        'ring' => 'hover:ring-amber-200',

        'alert' => $stats['pending'] > 0,

    ],

    [

        'label' => 'This week',

        'value' => $stats['week_reservations'],

        'hint' => $stats['week_confirmed'] . ' confirmed',

        'href' => base_url('admin/reservations.php'),

        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',

        'bg' => 'bg-emerald-100 text-emerald-700',

        'ring' => 'hover:ring-emerald-200',

    ],

    [

        'label' => 'Messages',

        'value' => $stats['unread_messages'],

        'hint' => 'Unread inquiries',

        'href' => base_url('admin/messages.php'),

        'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',

        'bg' => 'bg-blue-100 text-blue-700',

        'ring' => 'hover:ring-blue-200',

        'alert' => $stats['unread_messages'] > 0,

    ],

];



$quickLinks = [

    ['label' => 'Reservations', 'desc' => 'Manage bookings', 'href' => base_url('admin/reservations.php'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'bg-brand-900'],

    ['label' => 'Add menu item', 'desc' => 'Update your menu', 'href' => base_url('admin/menu-items.php?action=add'), 'icon' => 'M12 4v16m8-8H4', 'color' => 'bg-accent'],

    ['label' => 'Upload photo', 'desc' => 'Gallery images', 'href' => base_url('admin/gallery.php?action=add'), 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'bg-stone-700'],

    ['label' => 'Site settings', 'desc' => 'Hours & contact', 'href' => base_url('admin/settings.php'), 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'bg-stone-600'],

];

?>



<!-- Welcome banner -->

<section class="mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-brand-900 via-stone-800 to-stone-900 p-6 text-white shadow-lg sm:p-8">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <p class="text-xs font-semibold uppercase tracking-widest text-accent-light/90">Admin dashboard</p>

            <h2 class="mt-1 font-display text-2xl font-bold sm:text-3xl">Welcome back, <?= e($user['username'] ?? 'Admin') ?></h2>

            <p class="mt-2 max-w-xl text-sm text-stone-300"><?= e($restaurantName) ?> — <?= e(date('l, F j, Y')) ?></p>

        </div>

        <div class="flex flex-wrap gap-3 text-sm">

            <div class="rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">

                <p class="text-stone-400">Menu items</p>

                <p class="text-xl font-bold"><?= $stats['menu_items'] ?></p>

            </div>

            <div class="rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">

                <p class="text-stone-400">Gallery</p>

                <p class="text-xl font-bold"><?= $stats['gallery_images'] ?></p>

            </div>

            <div class="rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">

                <p class="text-stone-400">All bookings</p>

                <p class="text-xl font-bold"><?= $stats['total_reservations'] ?></p>

            </div>

        </div>

    </div>

</section>



<?php if ($needsAttention): ?>

<div class="mb-6 flex flex-wrap items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">

    <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>

    <span class="flex-1">

        <?php if ($stats['pending'] > 0): ?><strong><?= $stats['pending'] ?></strong> reservation<?= $stats['pending'] !== 1 ? 's' : '' ?> need confirmation.<?php endif; ?>

        <?php if ($stats['pending'] > 0 && $stats['unread_messages'] > 0): ?> <?php endif; ?>

        <?php if ($stats['unread_messages'] > 0): ?><strong><?= $stats['unread_messages'] ?></strong> unread message<?= $stats['unread_messages'] !== 1 ? 's' : '' ?>.<?php endif; ?>

    </span>

    <?php if ($stats['pending'] > 0): ?>

    <a href="<?= base_url('admin/reservations.php?status=pending') ?>" class="admin-btn admin-btn--primary text-xs">Review reservations</a>

    <?php endif; ?>

</div>

<?php endif; ?>



<!-- Stat cards -->

<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

    <?php foreach ($statCards as $card): ?>

    <a href="<?= e($card['href']) ?>"

       class="group relative flex flex-col rounded-2xl border border-stone-200 bg-white p-5 shadow-sm ring-1 ring-transparent transition hover:-translate-y-0.5 hover:shadow-md <?= e($card['ring']) ?>">

        <?php if (!empty($card['alert'])): ?>

        <span class="absolute right-4 top-4 flex h-2.5 w-2.5">

            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-accent opacity-75"></span>

            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-accent"></span>

        </span>

        <?php endif; ?>

        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl <?= e($card['bg']) ?>">

            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($card['icon']) ?>"/></svg>

        </span>

        <p class="mt-4 text-xs font-semibold uppercase tracking-wider text-stone-500"><?= e($card['label']) ?></p>

        <p class="mt-1 font-display text-3xl font-bold text-brand-900"><?= (int) $card['value'] ?></p>

        <p class="mt-1 text-sm text-stone-500"><?= e($card['hint']) ?></p>

    </a>

    <?php endforeach; ?>

</div>



<!-- Quick actions -->

<div class="mb-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

    <?php foreach ($quickLinks as $link): ?>

    <a href="<?= e($link['href']) ?>"

       class="flex items-center gap-4 rounded-2xl border border-stone-200 bg-white p-4 no-underline shadow-sm transition hover:border-accent/30 hover:shadow-md">

        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-white <?= e($link['color']) ?>">

            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= e($link['icon']) ?>"/></svg>

        </span>

        <span>

            <span class="block font-semibold text-stone-800"><?= e($link['label']) ?></span>

            <span class="block text-xs text-stone-500"><?= e($link['desc']) ?></span>

        </span>

    </a>

    <?php endforeach; ?>

</div>



<div class="grid gap-6 xl:grid-cols-3">

    <!-- Recent reservations -->

    <div class="xl:col-span-2">

        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-100 px-5 py-4">

                <h2 class="font-display text-lg font-bold text-brand-900">Recent reservations</h2>

                <a href="<?= base_url('admin/reservations.php') ?>" class="text-sm font-semibold text-accent hover:underline">View all</a>

            </div>

            <div class="overflow-x-auto">

                <table class="admin-table w-full">

                    <thead>

                        <tr>

                            <th>Guest</th>

                            <th class="hidden sm:table-cell">When</th>

                            <th>Guests</th>

                            <th>Status</th>

                            <th></th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($recent as $r): ?>

                    <tr>

                        <td>

                            <strong class="text-stone-800"><?= e($r['full_name']) ?></strong>

                            <span class="block text-xs text-stone-400 sm:hidden"><?= e(format_date($r['reservation_date'])) ?> · <?= e(format_time($r['reservation_time'])) ?></span>

                        </td>

                        <td class="hidden sm:table-cell">

                            <?= e(format_date($r['reservation_date'])) ?>

                            <span class="block text-xs text-stone-500"><?= e(format_time($r['reservation_time'])) ?></span>

                        </td>

                        <td><?= (int) $r['num_guests'] ?></td>

                        <td><?= admin_status_badge($r['status']) ?></td>

                        <td><a href="<?= base_url('admin/reservation-edit.php?id=' . (int) $r['id']) ?>" class="admin-table-link">Edit</a></td>

                    </tr>

                    <?php endforeach; ?>

                    <?php if (empty($recent)): ?>

                    <tr><td colspan="5" class="py-12 text-center text-stone-400">No reservations yet.</td></tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- Sidebar column -->

    <div class="space-y-6">

        <!-- Upcoming -->

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">

            <div class="mb-4 flex items-center justify-between">

                <h2 class="font-display text-lg font-bold text-brand-900">Upcoming</h2>

                <a href="<?= base_url('admin/reservations.php') ?>" class="text-xs font-semibold text-accent hover:underline">See all</a>

            </div>

            <ul class="space-y-3">

                <?php foreach ($upcoming as $u): ?>

                <li class="rounded-xl border border-stone-100 bg-stone-50/80 p-3">

                    <div class="flex items-start justify-between gap-2">

                        <div class="min-w-0">

                            <p class="truncate font-semibold text-stone-800"><?= e($u['full_name']) ?></p>

                            <p class="text-xs text-stone-500"><?= e(format_date($u['reservation_date'])) ?> · <?= e(format_time($u['reservation_time'])) ?></p>

                            <p class="mt-0.5 text-xs text-stone-400"><?= (int) $u['num_guests'] ?> guest<?= (int) $u['num_guests'] !== 1 ? 's' : '' ?></p>

                        </div>

                        <?= admin_status_badge($u['status']) ?>

                    </div>

                    <a href="<?= base_url('admin/reservation-edit.php?id=' . (int) $u['id']) ?>" class="mt-2 inline-block text-xs font-semibold text-accent hover:underline">Manage</a>

                </li>

                <?php endforeach; ?>

                <?php if (empty($upcoming)): ?>

                <li class="py-6 text-center text-sm text-stone-400">No upcoming bookings.</li>

                <?php endif; ?>

            </ul>

        </div>



        <!-- Recent messages -->

        <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">

            <div class="mb-4 flex items-center justify-between">

                <h2 class="font-display text-lg font-bold text-brand-900">Messages</h2>

                <a href="<?= base_url('admin/messages.php') ?>" class="text-xs font-semibold text-accent hover:underline">Inbox</a>

            </div>

            <ul class="space-y-3">

                <?php foreach ($recentMessages as $m): ?>

                <li class="rounded-xl border p-3 <?= $m['is_read'] ? 'border-stone-100 bg-white' : 'border-orange-200 bg-orange-50/50' ?>">

                    <div class="flex items-center justify-between gap-2">

                        <p class="truncate font-semibold text-sm text-stone-800"><?= e($m['full_name']) ?></p>

                        <?php if (!$m['is_read']): ?>

                        <span class="shrink-0 rounded-full bg-accent px-2 py-0.5 text-[10px] font-bold text-white">New</span>

                        <?php endif; ?>

                    </div>

                    <?php if (!empty($m['subject'])): ?>

                    <p class="truncate text-xs text-stone-600"><?= e($m['subject']) ?></p>

                    <?php endif; ?>

                    <p class="mt-1 line-clamp-2 text-xs text-stone-500"><?= e(strlen($m['message']) > 80 ? substr($m['message'], 0, 80) . '…' : $m['message']) ?></p>

                    <a href="<?= base_url('admin/messages.php') ?>" class="mt-2 inline-block text-xs font-semibold text-accent hover:underline">Open</a>

                </li>

                <?php endforeach; ?>

                <?php if (empty($recentMessages)): ?>

                <li class="py-6 text-center text-sm text-stone-400">No messages yet.</li>

                <?php endif; ?>

            </ul>

        </div>



        <!-- Menu snapshot -->

        <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50 p-5">

            <h3 class="text-sm font-semibold text-stone-700">Menu overview</h3>

            <dl class="mt-3 space-y-2 text-sm">

                <div class="flex justify-between"><dt class="text-stone-500">Active items</dt><dd class="font-semibold text-stone-800"><?= $stats['menu_items'] - $stats['menu_unavailable'] ?></dd></div>

                <div class="flex justify-between"><dt class="text-stone-500">Unavailable</dt><dd class="font-semibold <?= $stats['menu_unavailable'] > 0 ? 'text-amber-700' : 'text-stone-800' ?>"><?= $stats['menu_unavailable'] ?></dd></div>

            </dl>

            <a href="<?= base_url('admin/menu-items.php') ?>" class="admin-btn admin-btn--secondary mt-4 w-full text-center text-sm">Manage menu</a>

        </div>

    </div>

</div>



<?php require __DIR__ . '/includes/layout-end.php'; ?>

