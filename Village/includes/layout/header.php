<?php

/** @var string $pageTitle */

/** @var string|null $pageDescription */

$siteName = Settings::get('restaurant_name', app_config()['name']);

$title = isset($pageTitle) ? $pageTitle . ' | ' . $siteName : $siteName;

$desc = $pageDescription ?? Settings::get('tagline');

$currentPage = $currentPage ?? '';

?>

<!DOCTYPE html>

<html lang="en" class="scroll-smooth">
<head>
    <script>
    (function(){try{var t=localStorage.getItem('hv-theme');if(t==='dark'){document.documentElement.classList.add('dark');document.documentElement.style.colorScheme='dark';}}catch(e){}})();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($desc) ?>">
    <?= port_fix_script_tag() ?>
    <link rel="canonical" href="<?= e(home_url()) ?>">
    <base href="<?= e(rtrim(site_base_url(), '/') . '/') ?>">

    <title><?= e($title) ?></title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <script>

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {

                    colors: {

                        brand: { 50: '#fdf8f3', 100: '#f5e6d3', 200: '#e8c9a8', 500: '#a16207', 600: '#854d0e', 700: '#78350f', 800: '#5c2a0c', 900: '#451a03' },

                        accent: { DEFAULT: '#e85d04', dark: '#dc2f02', light: '#f48c06' },

                    },

                    fontFamily: {

                        display: ['"Playfair Display"', 'Georgia', 'serif'],

                        sans: ['"DM Sans"', 'system-ui', 'sans-serif'],

                    },

                },

            },

        };

    </script>

    <link rel="stylesheet" href="<?= asset_url('css/custom.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/dark-mode.css') ?>">
    <?php
    $loadExperience = !empty($isOverlayHero)
        || str_contains((string) ($bodyClass ?? ''), 'xp-page')
        || str_contains((string) ($bodyClass ?? ''), 'contact-page');
    if ($loadExperience) {
        echo '<link rel="stylesheet" href="' . e(asset_url('css/experience.css')) . '">' . "\n";
    }
    if (str_contains((string) ($bodyClass ?? ''), 'contact-page')) {
        echo '<link rel="stylesheet" href="' . e(asset_url('css/contact.css')) . '">' . "\n";
    }
    ?>
    <meta name="theme-color" content="#451a03">

</head>

<body class="font-sans text-stone-800 bg-stone-50 antialiased flex flex-col min-h-screen<?= !empty($isHomeHero) ? ' home-page' : '' ?><?= !empty($isOverlayHero) ? ' overlay-hero-page' : '' ?><?= !empty($bodyClass) ? ' ' . e($bodyClass) : '' ?>">

<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 bg-accent text-white px-4 py-2 rounded z-[100]">Skip to content</a>

<?php if (!empty($isHomeHero) || !empty($isOverlayHero)): ?>

<main id="main" class="flex-grow">

<?php else: ?>

<?php $navVariant = 'sticky'; require __DIR__ . '/site-navbar.php'; ?>

<main id="main" class="flex-grow">

<?php endif; ?>

