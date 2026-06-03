<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getConnection();

$homeIntro = $pdo->prepare('SELECT content FROM pages WHERE slug = ?');
$homeIntro->execute(['home-intro']);
$introContent = $homeIntro->fetchColumn() ?: '';

$featured = $pdo->query(
    'SELECT mi.*, mc.name AS category_name FROM menu_items mi
     JOIN menu_categories mc ON mc.id = mi.category_id
     WHERE mi.is_featured = 1 AND mi.is_available = 1 AND mc.is_active = 1
     ORDER BY mi.updated_at DESC LIMIT 6'
)->fetchAll();

$hoursSummary = footer_hours_summary();

$pageTitle = 'Home';
$currentPage = 'home';
$isHomeHero = true;
$heroBg = asset_url('images/BG.png');
$heroVideo = asset_url('video/3D.mp4');
require __DIR__ . '/includes/layout/header.php';
?>

<section class="home-hero relative min-h-screen flex flex-col overflow-hidden" aria-label="Welcome">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat scale-105" style="background-image: url('<?= e($heroBg) ?>')" role="presentation"></div>
    <div class="absolute inset-0 home-hero-overlay" aria-hidden="true"></div>

    <?php require __DIR__ . '/includes/layout/hero-nav.php'; ?>

    <div class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 pb-12 lg:pb-16 pt-4 lg:pt-8">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center min-h-[calc(100vh-12rem)]">
            <div class="text-white order-2 lg:order-1">
                <a href="<?= base_url('menu.php') ?>" class="hero-promo-badge inline-flex items-center gap-2 mb-6 max-w-full">
                    <span class="w-2 h-2 rounded-full bg-accent-light shrink-0 animate-pulse"></span>
                    <span class="truncate">Weekend Family Platter — 20% off every Friday &amp; Saturday!</span>
                    <span class="shrink-0" aria-hidden="true">&rarr;</span>
                </a>
                <p class="text-accent-light text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase mb-3">
                    Authentic Somaliland Cuisine &amp; Warm Hospitality
                </p>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold leading-[1.1] mb-4">
    Welcome to<br>
    <span class="text-brand-500">The Village</span>
    <span class="text-white/95">Hargeisa</span>
</h1> 
                <p class="text-lg sm:text-xl text-white/75 max-w-md mb-8">
                    <?= e(Settings::get('tagline', 'Where tradition meets exceptional flavor')) ?>
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="<?= base_url('reservations.php') ?>" class="hero-btn-primary">
                        Reserve a Table
                    </a>
                    <a href="<?= base_url('menu.php') ?>" class="hero-btn-secondary">
                        Explore Menu
                    </a>
                </div>
            </div>
            <div class="order-1 lg:order-2 flex justify-center lg:justify-end items-center">
                <div class="hero-video-stage">
                    <div class="hero-video-glow" aria-hidden="true"></div>
                    <video
                        class="hero-video"
                        src="<?= e($heroVideo) ?>"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="auto"
                        aria-label="Restaurant showcase animation"
                    ></video>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="site-section py-16 md:py-24 bg-stone-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 grid md:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="font-display text-3xl text-brand-800 mb-4">About Us</h2>
            <div class="prose-content text-stone-600"><?= $introContent ?></div>
            <a href="<?= base_url('about.php') ?>" class="inline-block mt-6 text-brand-700 font-semibold hover:underline">Read our story &rarr;</a>
        </div>
        <div class="rounded-2xl overflow-hidden shadow-xl aspect-[4/3] bg-stone-200">
            <img src="https://i.pinimg.com/736x/e1/70/ab/e170ab13cce5c3f1bb8d5c6908f7062b.jpg" alt="Restaurant dining area" class="w-full h-full object-cover" loading="lazy">
        </div>
    </div>
</section>

<?php if (count($featured) > 0): ?>
<section class="site-section site-section--elevated py-16 bg-white border-y border-stone-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl text-brand-800">Featured Dishes</h2>
            <p class="text-stone-500 mt-2">Customer favorites and chef specials</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featured as $item): ?>
            <article class="site-card rounded-2xl overflow-hidden border border-stone-100 shadow-sm hover:shadow-md bg-stone-50">
                <div class="aspect-video bg-stone-200">
                    <img src="<?= e(upload_url($item['image_url'])) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover" loading="lazy">
                </div>
                <div class="p-5">
                    <p class="text-xs text-brand-600 uppercase tracking-wide"><?= e($item['category_name']) ?></p>
                    <h3 class="font-display text-xl text-brand-900 mt-1"><?= e($item['name']) ?></h3>
                    <p class="text-sm text-stone-500 mt-2 line-clamp-2"><?= e($item['description']) ?></p>
                    <p class="mt-3 font-semibold text-brand-700"><?= format_price($item['price']) ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-10">
            <a href="<?= base_url('menu.php') ?>" class="inline-flex px-6 py-3 rounded-full bg-brand-700 text-white font-semibold hover:bg-brand-800 transition">Explore Full Menu</a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="site-section site-section--default py-16 md:py-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 grid md:grid-cols-2 gap-12">
        <div class="site-panel-hours site-card--soft bg-brand-50 rounded-2xl p-8 border border-brand-100">
            <h2 class="font-display text-2xl text-brand-800 mb-2">Operating Hours</h2>
            <p class="text-sm font-semibold text-brand-700 mb-4"><?= e($hoursSummary['label']) ?></p>
            <dl class="space-y-4">
                <div class="text-sm border-b border-brand-100 pb-3">
                    <dt class="font-medium text-stone-700">Morning</dt>
                    <dd class="text-stone-600 mt-1"><?= e($hoursSummary['morning']) ?></dd>
                </div>
                <div class="text-sm">
                    <dt class="font-medium text-stone-700">Afternoon</dt>
                    <dd class="text-stone-600 mt-1"><?= e($hoursSummary['afternoon']) ?></dd>
                </div>
            </dl>
        </div>
        <div>
            <h2 class="font-display text-2xl text-brand-800 mb-4">Visit Us</h2>
            <p class="text-stone-600"><?= e(Settings::get('address')) ?></p>
            <p class="mt-3"><a href="tel:<?= e(preg_replace('/\s+/', '', Settings::get('phone'))) ?>" class="text-brand-700 font-medium"><?= e(Settings::get('phone')) ?></a></p>
            <p><a href="mailto:<?= e(Settings::get('email')) ?>" class="text-brand-700"><?= e(Settings::get('email')) ?></a></p>
            <a href="<?= base_url('contact.php') ?>" class="inline-block mt-6 px-5 py-2 rounded-full border-2 border-brand-700 text-brand-700 font-semibold hover:bg-brand-700 hover:text-white transition">Contact & Directions</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
