<?php

/** @var string $adminTitle */

/** @var string|null $adminSubtitle */

/** @var string|null $adminActions */

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

require_once __DIR__ . '/helpers.php';

require_login();



$siteName = Settings::get('restaurant_name');

$title = ($adminTitle ?? 'Dashboard') . ' | Admin';

$user = current_user();

$flashSuccess = flash('success');

$flashError = flash('error');

$navItems = admin_nav_items();

$unreadMessages = admin_unread_messages_count();

$pendingReservations = admin_pending_reservations_count();

?>

<!DOCTYPE html>

<html lang="en" class="h-full">

<head>
    <script>
    (function(){try{var t=localStorage.getItem('hv-theme');if(t==='dark'){document.documentElement.classList.add('dark');document.documentElement.style.colorScheme='dark';}}catch(e){}})();
    </script>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#f5f5f4">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= port_fix_script_tag() ?>
    <base href="<?= e(rtrim(site_base_url(), '/') . '/') ?>">
    <title><?= e($title) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>

        tailwind.config = {
            darkMode: 'class',
            theme: {

                extend: {

                    colors: {

                        brand: { 50: '#fdf8f3', 100: '#fef3e2', 700: '#78350f', 800: '#5c2a0c', 900: '#451a03' },

                        accent: { DEFAULT: '#e85d04', dark: '#dc2f02', light: '#f48c06' },

                    },

                    fontFamily: {

                        display: ['"Playfair Display"', 'Georgia', 'serif'],

                        sans: ['"DM Sans"', 'system-ui', 'sans-serif'],

                    },

                    width: { sidebar: '17rem', 'sidebar-collapsed': '5rem' },

                    transitionDuration: { sidebar: '300ms' },

                },

            },

        };

    </script>

    <link rel="stylesheet" href="<?= asset_url('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/admin-dark-mode.css') ?>">

</head>

<body class="admin-app min-h-full bg-stone-100 font-sans text-stone-900 antialiased">



<div id="admin-sidebar-backdrop"

     class="fixed inset-0 z-40 bg-stone-900/60 backdrop-blur-sm opacity-0 invisible pointer-events-none transition-all duration-sidebar ease-out lg:hidden"

     aria-hidden="true"></div>



<div class="flex min-h-screen">



    <aside id="admin-sidebar"

           class="group/sidebar fixed top-0 left-0 z-50 flex h-screen w-sidebar flex-col bg-gradient-to-b from-brand-900 via-stone-800 to-stone-900 text-stone-300 shadow-2xl -translate-x-full transition-[transform,width] duration-sidebar ease-out lg:sticky lg:translate-x-0 lg:shadow-none data-[collapsed=true]:lg:w-sidebar-collapsed"

           aria-label="Admin navigation"

           data-collapsed="false">



        <div class="flex shrink-0 items-center justify-between gap-2 border-b border-white/10 px-4 py-4">

            <a href="<?= e(base_url('admin/index.php')) ?>"

               class="flex min-w-0 items-center gap-3 no-underline text-inherit group-data-[collapsed=true]/sidebar:lg:justify-center group-data-[collapsed=true]/sidebar:lg:gap-0">

                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent font-display text-sm font-bold text-white shadow-lg shadow-accent/30">HV</span>

                <span class="min-w-0 flex flex-col leading-tight group-data-[collapsed=true]/sidebar:lg:hidden">

                    <strong class="truncate font-display text-base text-white"><?= e(footer_short_name()) ?></strong>

                    <small class="text-[10px] font-semibold uppercase tracking-widest text-stone-400">Admin Panel</small>

                </span>

            </a>

            <button type="button" id="admin-sidebar-close"

                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-stone-400 transition hover:bg-white/10 hover:text-white lg:hidden"

                    aria-label="Close menu">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

            </button>

        </div>



        <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto overflow-x-hidden px-3 py-3 scrollbar-thin">

            <p class="mb-2 px-2 text-[10px] font-semibold uppercase tracking-widest text-stone-500 group-data-[collapsed=true]/sidebar:lg:hidden">Menu</p>

            <?php foreach ($navItems as $file => $item):

                $active = admin_is_active_nav($file);

                $badge = '';

                if ($file === 'messages.php' && $unreadMessages > 0) {

                    $badge = '<span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-white/20 px-1.5 text-[10px] font-bold text-white">' . $unreadMessages . '</span>';

                }

                if ($file === 'reservations.php' && $pendingReservations > 0) {

                    $badge = '<span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-400 px-1.5 text-[10px] font-bold text-brand-900">' . $pendingReservations . '</span>';

                }

                $linkClass = $active

                    ? 'bg-accent text-white shadow-md shadow-accent/25'

                    : 'text-stone-400 hover:bg-white/10 hover:text-white';

            ?>

            <a href="<?= base_url('admin/' . $file) ?>"

               class="admin-sidebar-link relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium no-underline transition-colors duration-200 <?= $linkClass ?> group-data-[collapsed=true]/sidebar:lg:justify-center group-data-[collapsed=true]/sidebar:lg:px-2"

               title="<?= e($item['label']) ?>">

                <?= admin_nav_icon($item['icon']) ?>

                <span class="truncate group-data-[collapsed=true]/sidebar:lg:hidden"><?= e($item['label']) ?></span>

                <?php if ($badge !== ''): ?>

                <span class="group-data-[collapsed=true]/sidebar:lg:hidden"><?= $badge ?></span>

                <span class="absolute top-2.5 right-2.5 hidden h-2 w-2 rounded-full bg-amber-400 group-data-[collapsed=true]/sidebar:lg:block"></span>

                <?php endif; ?>

            </a>

            <?php endforeach; ?>

        </nav>



        <div class="shrink-0 border-t border-white/10 p-3">

            <div class="mb-2 flex items-center gap-2.5 rounded-xl px-2 py-2 group-data-[collapsed=true]/sidebar:lg:justify-center">

                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent/30 text-xs font-bold text-accent-light">

                    <?= e(strtoupper(substr($user['username'] ?? 'A', 0, 1))) ?>

                </span>

                <div class="min-w-0 group-data-[collapsed=true]/sidebar:lg:hidden">

                    <p class="truncate text-sm font-semibold text-white"><?= e($user['username'] ?? '') ?></p>

                    <p class="text-xs capitalize text-stone-500"><?= e($user['role'] ?? 'editor') ?></p>

                </div>

            </div>

            <a href="<?= e(home_url()) ?>" target="_blank" rel="noopener"

               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-stone-400 no-underline transition hover:bg-white/10 hover:text-white group-data-[collapsed=true]/sidebar:lg:justify-center group-data-[collapsed=true]/sidebar:lg:px-2"

               title="View Website">

                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>

                <span class="group-data-[collapsed=true]/sidebar:lg:hidden">View Website</span>

            </a>

            <a href="<?= base_url('admin/logout.php') ?>"

               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-stone-400 no-underline transition hover:bg-red-500/15 hover:text-red-300 group-data-[collapsed=true]/sidebar:lg:justify-center group-data-[collapsed=true]/sidebar:lg:px-2"

               title="Logout">

                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>

                <span class="group-data-[collapsed=true]/sidebar:lg:hidden">Logout</span>

            </a>

        </div>

    </aside>



    <div class="flex min-w-0 flex-1 flex-col">

        <header class="sticky top-0 z-30 flex flex-wrap items-center gap-3 border-b border-stone-200 bg-white/95 px-4 py-3 backdrop-blur-md sm:px-5 lg:px-6">

            <button type="button" id="admin-menu-toggle"

                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-stone-100 text-stone-700 transition hover:bg-stone-200 lg:hidden"

                    aria-label="Open menu" aria-expanded="false" aria-controls="admin-sidebar">

                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>

            </button>

            <button type="button" id="admin-sidebar-collapse"

                    class="hidden h-10 w-10 items-center justify-center rounded-xl border border-stone-200 text-stone-600 transition hover:border-accent hover:text-accent lg:inline-flex"

                    aria-label="Collapse sidebar" title="Collapse sidebar">

                <svg id="admin-collapse-icon-expand" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>

                <svg id="admin-collapse-icon-collapse" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>

            </button>

            <div class="min-w-0 flex-1">

                <h1 class="font-display text-xl font-bold text-brand-900 sm:text-2xl"><?= e($adminTitle ?? 'Dashboard') ?></h1>

                <?php if (!empty($adminSubtitle)): ?>

                <p class="mt-0.5 text-sm text-stone-500"><?= e($adminSubtitle) ?></p>

                <?php endif; ?>

            </div>

            <div class="flex w-full flex-wrap items-center gap-2 sm:ml-auto sm:w-auto sm:justify-end">
                <?php require __DIR__ . '/theme-toggle.php'; ?>
                <?php if (!empty($adminActions)): ?>
                <div class="flex flex-wrap items-center gap-2"><?= $adminActions ?></div>
                <?php endif; ?>
            </div>

        </header>



        <main class="flex-1 p-4 sm:p-5 lg:p-8">

            <?php if ($flashSuccess): ?>

            <div class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">

                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>

                <?= e($flashSuccess) ?>

            </div>

            <?php endif; ?>

            <?php if ($flashError): ?>

            <div class="mb-5 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">

                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>

                <?= e($flashError) ?>

            </div>

            <?php endif; ?>

