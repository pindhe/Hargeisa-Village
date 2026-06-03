<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getConnection();
$filter = $_GET['category'] ?? '';
$sql = 'SELECT * FROM gallery_images ORDER BY display_order ASC, uploaded_at DESC';
$params = [];
if ($filter !== '' && in_array($filter, ['food', 'interior', 'event'], true)) {
    $sql = 'SELECT * FROM gallery_images WHERE category = ? ORDER BY display_order ASC, uploaded_at DESC';
    $params = [$filter];
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$images = $stmt->fetchAll();

$totalImages = (int) $pdo->query('SELECT COUNT(*) FROM gallery_images')->fetchColumn();
$categoryLabels = ['food' => 'Food', 'interior' => 'Interior', 'event' => 'Events'];
$restaurantName = Settings::get('restaurant_name', 'Hargeisa Village Restaurant');

$pageTitle = 'Gallery';
$currentPage = 'gallery.php';
$isOverlayHero = true;
$bodyClass = 'xp-page gallery-page';
require __DIR__ . '/includes/layout/header.php';

$heroPromo = ['href' => base_url('menu.php'), 'text' => 'Weekend Family Platter — 20% off every Friday & Saturday!'];
$heroEyebrow = 'Moments & Memories';
$heroTitle = 'Our';
$heroTitleAccent = 'Gallery';
$heroSubtitle = 'Food, ambiance, and celebrations at Village Hargeisa — browse our photo collection below.';
$heroPrimaryBtn = ['href' => '#gallery-grid', 'label' => 'View photos'];
$heroSecondaryBtn = ['href' => base_url('reservations.php'), 'label' => 'Book a Table'];
$heroScroll = ['href' => '#gallery-grid', 'label' => 'Explore'];
$heroMediaVideo = gallery_hero_video_url();
$heroMediaAlt = $restaurantName . ' — gallery showcase video';
$heroMediaLayout = 'video-showcase';
$heroShowcaseClass = 'hero-cutout-stage--luxury hero-cutout-stage--cinematic hero-cutout-stage--gallery';
$heroMediaColumnClass = 'page-hero-media--bleed page-hero-media--gallery';
$heroShowcaseCaption = 'Village Hargeisa · Flavors & atmosphere';
require __DIR__ . '/includes/partials/page-hero-3d.php';
?>

<section class="site-stat-strip xp-stat-strip">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 site-stat-grid xp-stat-grid">
            <div class="site-stat-card xp-stat-card xp-reveal">
                <p class="site-stat-card-value xp-stat-card-value"><?= $totalImages ?></p>
                <p class="site-stat-card-label xp-stat-card-label">Photos</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay">
                <p class="site-stat-card-value xp-stat-card-value">3</p>
                <p class="site-stat-card-label xp-stat-card-label">Categories</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay">
                <p class="site-stat-card-value xp-stat-card-value">HV</p>
                <p class="site-stat-card-label xp-stat-card-label"><?= e(footer_short_name()) ?></p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay-2">
                <p class="site-stat-card-value xp-stat-card-value">↗</p>
                <p class="site-stat-card-label xp-stat-card-label">Tap to enlarge</p>
            </div>
    </div>
</section>

<section class="site-section xp-section xp-section--light py-16 md:py-24 bg-stone-50 xp-gallery-stage" id="gallery-grid">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="site-section-head xp-section-head xp-reveal">
            <p class="site-section-eyebrow xp-section-eyebrow">Photo collection</p>
            <h2 class="site-section-title xp-section-title">Food, space &amp; events</h2>
            <p class="site-section-desc xp-section-desc">Filter by category or browse everything. Select any photo for a full-screen view.</p>
        </div>

        <nav class="xp-gallery-filters xp-reveal" aria-label="Gallery categories">
            <a href="<?= base_url('gallery.php') ?>" class="xp-filter-pill <?= $filter === '' ? 'xp-filter-pill--active' : '' ?>">All</a>
            <?php foreach ($categoryLabels as $key => $label): ?>
            <a href="<?= base_url('gallery.php?category=' . $key) ?>" class="xp-filter-pill <?= $filter === $key ? 'xp-filter-pill--active' : '' ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>

        <?php if (empty($images)): ?>
        <div class="xp-empty xp-reveal">
            <p class="text-stone-600 font-medium">Gallery images coming soon.</p>
            <p class="text-stone-500 text-sm mt-2">Our team is adding photos — check back shortly.</p>
            <a href="<?= base_url('contact.php') ?>" class="text-accent font-semibold hover:underline mt-4 inline-block">Contact us</a>
        </div>
        <?php else: ?>
        <div class="xp-gallery-grid">
            <?php foreach ($images as $i => $img):
                $catLabel = $categoryLabels[$img['category'] ?? ''] ?? ucfirst($img['category'] ?? '');
            ?>
            <a href="#"
               class="xp-gallery-card gallery-item gallery-grid-card xp-reveal <?= $i % 3 === 0 ? '' : ($i % 3 === 1 ? 'xp-reveal--delay' : 'xp-reveal--delay-2') ?>"
               data-gallery-src="<?= e(upload_url($img['image_url'])) ?>"
               data-gallery-alt="<?= e($img['title'] ?? 'Gallery image') ?>">
                <img src="<?= e(upload_url($img['image_url'])) ?>" alt="<?= e($img['title'] ?? '') ?>" loading="lazy">
                <div class="xp-gallery-card-overlay">
                    <?php if ($img['title']): ?>
                    <p class="xp-gallery-card-title"><?= e($img['title']) ?></p>
                    <?php endif; ?>
                    <?php if ($catLabel): ?>
                    <p class="xp-gallery-card-cat"><?= e($catLabel) ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="site-cta-band xp-cta-band xp-reveal">
    <div class="site-cta-band-glow xp-cta-band-glow" aria-hidden="true"></div>
    <h2 class="site-cta-band-title xp-cta-band-title">See it in person</h2>
    <p class="site-cta-band-text xp-cta-band-text">Visit us for the full experience — reserve your table today.</p>
    <div class="site-cta-band-actions xp-cta-band-actions">
        <a href="<?= base_url('reservations.php') ?>" class="hero-btn-primary">Book a Table</a>
        <a href="<?= base_url('about.php') ?>" class="site-cta-btn-outline xp-cta-btn-outline">About us</a>
    </div>
</section>

<div id="lightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 p-4" role="dialog" aria-modal="true" aria-label="Image viewer">
    <button type="button" data-lightbox-close class="absolute top-4 right-4 text-white text-3xl hover:text-accent-light" aria-label="Close">&times;</button>
    <button type="button" data-lightbox-prev class="absolute left-4 text-white text-4xl hover:text-accent-light" aria-label="Previous">&lsaquo;</button>
    <img data-lightbox-img src="" alt="" class="max-h-[90vh] max-w-full rounded-lg shadow-2xl">
    <button type="button" data-lightbox-next class="absolute right-4 text-white text-4xl hover:text-accent-light" aria-label="Next">&rsaquo;</button>
</div>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
