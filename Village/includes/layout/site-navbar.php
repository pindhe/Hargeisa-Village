<?php

require_once dirname(__DIR__) . '/nav.php';



$navVariant = $navVariant ?? 'sticky';

$isHero = $navVariant === 'hero';

$shortName = 'Hargeisa Village';

$navStructure = site_nav_structure();

$mobileMenuId = $isHero ? 'hero-mobile-menu' : 'site-mobile-menu';

$mobileBtnId = $isHero ? 'hero-mobile-menu-btn' : 'site-mobile-menu-btn';

?>

<header class="site-header <?= $isHero ? 'site-header--hero' : 'site-header--sticky' ?>" data-site-header>

    <div class="site-topbar">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-wrap items-center justify-between gap-2 py-2 text-xs sm:text-sm">

            <div class="flex flex-wrap items-center gap-4 site-topbar-muted">

                <a href="tel:<?= e(preg_replace('/\s+/', '', Settings::get('phone'))) ?>" class="inline-flex items-center gap-1.5 hover:text-white transition">

                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>

                    <?= e(Settings::get('phone')) ?>

                </a>

                <span class="hidden sm:inline-flex items-center gap-1.5">

                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>

                    <?= e(hero_hours_summary()) ?>

                </span>

            </div>

            <div class="flex items-center gap-2 sm:gap-3">

                <?php require __DIR__ . '/theme-toggle.php'; ?>

                <?php

                foreach (['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook'] as $key => $label):

                    $url = Settings::get($key);

                    if ($url && $url !== '#'):

                ?>

                <a href="<?= e($url) ?>" target="_blank" rel="noopener" class="site-topbar-muted hover:text-accent transition" aria-label="<?= e($label) ?>">

                    <?php if ($key === 'instagram_url'): ?>

                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>

                    <?php else: ?>

                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>

                    <?php endif; ?>

                </a>

                <?php endif; endforeach; ?>

            </div>

        </div>

    </div>

    <nav class="site-nav max-w-7xl mx-auto px-4 sm:px-6 py-3 lg:py-4 flex items-center justify-between gap-4" aria-label="Main navigation">

        <a href="<?= e(home_url()) ?>" class="site-logo font-display text-xl sm:text-2xl font-semibold tracking-tight shrink-0">

            <?= e($shortName) ?>

        </a>

        <button type="button" id="<?= e($mobileBtnId) ?>" class="site-nav-toggle lg:hidden p-2 rounded-lg" aria-expanded="false" aria-controls="<?= e($mobileMenuId) ?>">

            <span class="sr-only">Open menu</span>

            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>

        </button>

        <div class="hidden lg:flex items-center gap-1 flex-1 justify-center">

            <?php foreach ($navStructure as $item): ?>

                <?php if ($item['type'] === 'link'): ?>

            <a href="<?= nav_url($item['href']) ?>" class="site-nav-link <?= nav_is_active($item['href']) ? 'site-nav-link--active' : '' ?>"><?= e($item['label']) ?></a>

                <?php else:

                    $dropdownActive = nav_is_dropdown_active($item['children']);

                ?>

            <div class="site-nav-dropdown" data-nav-dropdown>

                <button type="button" class="site-nav-link site-nav-dropdown-trigger <?= $dropdownActive ? 'site-nav-link--active' : '' ?>" aria-expanded="false" aria-haspopup="true">

                    <?= e($item['label']) ?>

                    <svg class="site-nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>

                </button>

                <div class="site-nav-dropdown-menu" role="menu">

                    <?php foreach ($item['children'] as $child): ?>

                    <a href="<?= nav_url($child['href']) ?>" class="site-nav-dropdown-item <?= nav_is_active($child['href']) ? 'site-nav-dropdown-item--active' : '' ?>" role="menuitem"><?= e($child['label']) ?></a>

                    <?php endforeach; ?>

                </div>

            </div>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>

        <a href="<?= nav_url('reservations.php') ?>" class="site-cta hidden sm:inline-flex items-center gap-2 shrink-0">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>

            Book a Table

        </a>

    </nav>

    <div id="<?= e($mobileMenuId) ?>" class="hidden lg:hidden site-mobile-panel px-4 pb-4">

        <div class="site-mobile-panel-inner flex flex-col gap-1 p-3">

            <?php foreach ($navStructure as $item): ?>

                <?php if ($item['type'] === 'link'): ?>

            <a href="<?= nav_url($item['href']) ?>" class="site-mobile-link <?= nav_is_active($item['href']) ? 'site-mobile-link--active' : '' ?>"><?= e($item['label']) ?></a>

                <?php else:

                    $dropdownActive = nav_is_dropdown_active($item['children']);

                ?>

            <div class="site-mobile-dropdown" data-mobile-dropdown>

                <button type="button" class="site-mobile-dropdown-trigger w-full flex items-center justify-between site-mobile-link <?= $dropdownActive ? 'site-mobile-link--active' : '' ?>" aria-expanded="false">

                    <span><?= e($item['label']) ?></span>

                    <svg class="site-mobile-chevron w-5 h-5 shrink-0 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>

                </button>

                <div class="site-mobile-dropdown-panel hidden pl-3 pb-1 flex flex-col gap-0.5">

                    <?php foreach ($item['children'] as $child): ?>

                    <a href="<?= nav_url($child['href']) ?>" class="site-mobile-link site-mobile-sublink <?= nav_is_active($child['href']) ? 'site-mobile-link--active' : '' ?>"><?= e($child['label']) ?></a>

                    <?php endforeach; ?>

                </div>

            </div>

                <?php endif; ?>

            <?php endforeach; ?>

            <a href="<?= nav_url('reservations.php') ?>" class="site-mobile-cta mt-2 text-center">Book a Table</a>

        </div>

    </div>

</header>

