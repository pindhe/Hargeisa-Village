<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getConnection();
$slugs = ['about-story', 'about-philosophy', 'about-ambiance'];
$pages = [];
$stmt = $pdo->prepare('SELECT slug, title, content FROM pages WHERE slug = ?');
foreach ($slugs as $slug) {
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    if ($row) {
        $pages[$slug] = $row;
    }
}

$hours = footer_hours_summary();
$restaurantName = Settings::get('restaurant_name', 'Hargeisa Village Restaurant');

$pageTitle = 'About Us';
$currentPage = 'about.php';
$isOverlayHero = true;
$bodyClass = 'xp-page about-page';
require __DIR__ . '/includes/layout/header.php';

$heroPromo = ['href' => base_url('menu.php'), 'text' => 'Weekend Family Platter — 20% off every Friday & Saturday!'];
$heroEyebrow = 'Our Story';
$heroTitle = 'About';
$heroTitleAccent = 'Hargeisa Village';
$heroSubtitle = 'Tradition, warmth, and exceptional Somali dining in the heart of Hargeisa.';
$heroPrimaryBtn = ['href' => base_url('reservations.php'), 'label' => 'Book a Table'];
$heroSecondaryBtn = ['href' => base_url('menu.php'), 'label' => 'View Menu'];
$heroScroll = ['href' => '#main-content', 'label' => 'Our story'];
$heroMediaImage = asset_url('images/about.png');
$heroMediaAlt = 'Our chef at ' . $restaurantName;
$heroMediaLayout = 'showcase';
$heroShowcaseClass = 'hero-cutout-stage--feature';
$heroMediaColumnClass = 'page-hero-media--bleed';
$heroShowcaseCaption = 'Crafted with passion · Every plate';
require __DIR__ . '/includes/partials/page-hero-3d.php';
?>

<section class="site-stat-strip xp-stat-strip">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 site-stat-grid xp-stat-grid">
            <div class="site-stat-card xp-stat-card xp-reveal">
                <p class="site-stat-card-value xp-stat-card-value">7</p>
                <p class="site-stat-card-label xp-stat-card-label">Days open weekly</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay">
                <p class="site-stat-card-value xp-stat-card-value"><?= e($hours['morning']) ?></p>
                <p class="site-stat-card-label xp-stat-card-label">Morning session</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay">
                <p class="site-stat-card-value xp-stat-card-value"><?= e($hours['afternoon']) ?></p>
                <p class="site-stat-card-label xp-stat-card-label">Evening session</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay-2">
                <p class="site-stat-card-value xp-stat-card-value">HV</p>
                <p class="site-stat-card-label xp-stat-card-label">Local favorite since day one</p>
            </div>
    </div>
</section>

<main id="main-content">
    <?php if (isset($pages['about-story'])): ?>
    <section class="site-section xp-section xp-section--white py-16 md:py-24 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
                <div class="rounded-2xl overflow-hidden shadow-xl aspect-[4/3] bg-stone-200 xp-reveal">
                    <img src="<?= e(asset_url('images/BG.png')) ?>" alt="<?= e($restaurantName) ?>" class="w-full h-full object-cover" loading="lazy">
                </div>
                <article class="xp-reveal xp-reveal--delay">
                    <p class="site-section-eyebrow xp-section-eyebrow text-left">Who we are</p>
                    <h2 class="font-display text-3xl text-brand-800 mb-4"><?= e($pages['about-story']['title']) ?></h2>
                    <div class="prose-content text-stone-600 text-lg mt-6"><?= $pages['about-story']['content'] ?></div>
                </article>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="site-section xp-section xp-section--light py-16 md:py-24 bg-stone-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="site-section-head xp-section-head xp-reveal">
                <p class="site-section-eyebrow xp-section-eyebrow">What guides us</p>
                <h2 class="site-section-title xp-section-title">Philosophy &amp; ambiance</h2>
                <p class="site-section-desc xp-section-desc">Every detail — from ingredients to atmosphere — reflects our commitment to memorable dining.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                <?php if (isset($pages['about-philosophy'])): ?>
                <article class="xp-content-card xp-reveal">
                    <h3 class="xp-content-card-title"><?= e($pages['about-philosophy']['title']) ?></h3>
                    <div class="prose-content text-stone-600"><?= $pages['about-philosophy']['content'] ?></div>
                </article>
                <?php endif; ?>
                <?php if (isset($pages['about-ambiance'])): ?>
                <article class="xp-content-card xp-content-card--accent xp-reveal xp-reveal--delay">
                    <h3 class="xp-content-card-title"><?= e($pages['about-ambiance']['title']) ?></h3>
                    <div class="prose-content text-stone-600"><?= $pages['about-ambiance']['content'] ?></div>
                </article>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="site-section xp-section site-section--elevated py-16 bg-white border-y border-stone-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="site-section-head xp-section-head xp-reveal">
                <p class="site-section-eyebrow xp-section-eyebrow">Why guests return</p>
                <h2 class="site-section-title xp-section-title">The Village experience</h2>
            </div>
            <div class="xp-values-grid">
                <div class="xp-value-card xp-reveal">
                    <div class="xp-value-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="font-display text-lg text-brand-900 mb-2">Authentic cuisine</h3>
                    <p class="text-stone-600 text-sm">Traditional recipes and fresh ingredients, prepared with care every day.</p>
                </div>
                <div class="xp-value-card xp-reveal xp-reveal--delay">
                    <div class="xp-value-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <h3 class="font-display text-lg text-brand-900 mb-2">Warm hospitality</h3>
                    <p class="text-stone-600 text-sm">A welcoming space for families, friends, and celebrations of every size.</p>
                </div>
                <div class="xp-value-card xp-reveal xp-reveal--delay-2">
                    <div class="xp-value-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-display text-lg text-brand-900 mb-2">Open 7 days</h3>
                    <p class="text-stone-600 text-sm"><?= e($hours['morning']) ?> · <?= e($hours['afternoon']) ?></p>
                </div>
            </div>
        </div>
    </section>
</main>

<section class="site-cta-band xp-cta-band xp-reveal">
    <div class="site-cta-band-glow xp-cta-band-glow" aria-hidden="true"></div>
    <h2 class="site-cta-band-title xp-cta-band-title">Experience Hargeisa Village</h2>
    <p class="site-cta-band-text xp-cta-band-text">Reserve your table or explore our menu — we look forward to welcoming you.</p>
    <div class="site-cta-band-actions xp-cta-band-actions">
        <a href="<?= base_url('reservations.php') ?>" class="hero-btn-primary">Book a Table</a>
        <a href="<?= base_url('menu.php') ?>" class="site-cta-btn-outline xp-cta-btn-outline">View Menu</a>
        <a href="<?= base_url('gallery.php') ?>" class="site-cta-btn-outline xp-cta-btn-outline">Gallery</a>
    </div>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
