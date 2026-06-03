<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getConnection();
$categories = $pdo->query(
    'SELECT * FROM menu_categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC'
)->fetchAll();

$itemsByCategory = [];
$totalItems = 0;
$featuredCount = 0;
if ($categories) {
    $ids = array_column($categories, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare(
        "SELECT * FROM menu_items WHERE category_id IN ($placeholders) ORDER BY is_featured DESC, name ASC"
    );
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $item) {
        $itemsByCategory[$item['category_id']][] = $item;
        if ($item['is_available']) {
            $totalItems++;
        }
        if ($item['is_featured'] && $item['is_available']) {
            $featuredCount++;
        }
    }
}

$pdfUrl = Settings::get('menu_pdf_url');
$restaurantName = Settings::get('restaurant_name', 'Hargeisa Village Restaurant');
$pageTitle = 'Menu';
$currentPage = 'menu.php';
$isOverlayHero = true;
$bodyClass = 'xp-page menu-page';
require __DIR__ . '/includes/layout/header.php';

$heroPromo = ['href' => base_url('reservations.php'), 'text' => 'Weekend Family Platter — 20% off every Friday & Saturday!'];
$heroEyebrow = 'Authentic Somaliland Cuisine';
$heroTitle = 'Our';
$heroTitleAccent = 'Menu';
$heroSubtitle = 'Explore carefully crafted dishes — from traditional favorites to modern creations.';
$heroPrimaryBtn = ['href' => '#menu-list', 'label' => 'Browse dishes'];
$heroSecondaryBtn = $pdfUrl
    ? ['href' => $pdfUrl, 'label' => 'Download PDF', 'class' => '', 'attrs' => 'target="_blank" rel="noopener"']
    : ['href' => base_url('reservations.php'), 'label' => 'Book a Table'];
$heroScroll = ['href' => '#menu-list', 'label' => 'See menu'];
$heroMediaImage = menu_hero_image_url();
$heroMediaAlt = 'Signature dishes at ' . $restaurantName;
$heroMediaLayout = 'showcase';
$heroShowcaseClass = 'hero-cutout-stage--luxury hero-cutout-stage--menu';
$heroMediaColumnClass = 'page-hero-media--bleed page-hero-media--menu';
$heroShowcaseCaption = 'Fresh flavors · Crafted daily';
require __DIR__ . '/includes/partials/page-hero-3d.php';
?>

<section class="site-stat-strip xp-stat-strip">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 site-stat-grid xp-stat-grid">
            <div class="site-stat-card xp-stat-card xp-reveal">
                <p class="site-stat-card-value xp-stat-card-value"><?= count($categories) ?></p>
                <p class="site-stat-card-label xp-stat-card-label">Categories</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay">
                <p class="site-stat-card-value xp-stat-card-value"><?= $totalItems ?></p>
                <p class="site-stat-card-label xp-stat-card-label">Available dishes</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay">
                <p class="site-stat-card-value xp-stat-card-value"><?= $featuredCount ?></p>
                <p class="site-stat-card-label xp-stat-card-label">Chef picks</p>
            </div>
            <div class="site-stat-card xp-stat-card xp-reveal xp-reveal--delay-2">
                <p class="site-stat-card-value xp-stat-card-value">7</p>
                <p class="site-stat-card-label xp-stat-card-label">Days a week</p>
            </div>
    </div>
</section>

<section class="site-section xp-section xp-section--light py-16 md:py-24 bg-stone-50" id="menu-list">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="site-section-head xp-section-head xp-reveal">
            <p class="site-section-eyebrow xp-section-eyebrow">Full menu</p>
            <h2 class="site-section-title xp-section-title">Fresh flavors, every category</h2>
            <p class="site-section-desc xp-section-desc">Tap a category to jump — prices and dietary tags shown on each item.</p>
        </div>

        <?php if (empty($categories)): ?>
        <div class="xp-empty xp-reveal">
            <p class="text-stone-600 font-medium">Our menu is being updated.</p>
            <p class="text-stone-500 text-sm mt-2">Please check back soon or contact us.</p>
            <a href="<?= base_url('contact.php') ?>" class="btn-accent mt-6">Contact us</a>
        </div>
        <?php else: ?>
        <nav class="xp-menu-nav xp-reveal" data-menu-nav aria-label="Menu categories">
            <?php foreach ($categories as $i => $cat): ?>
            <a href="#cat-<?= (int) $cat['id'] ?>" class="menu-category-pill <?= $i === 0 ? 'menu-category-pill--active' : '' ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </nav>

        <?php foreach ($categories as $cat):
            $items = $itemsByCategory[$cat['id']] ?? [];
        ?>
        <div id="cat-<?= (int) $cat['id'] ?>" class="xp-menu-category scroll-anchor xp-reveal">
            <div class="xp-menu-category-head">
                <div>
                    <h2 class="xp-menu-category-title"><?= e($cat['name']) ?></h2>
                    <?php if ($cat['description']): ?>
                    <p class="text-stone-500 mt-2 max-w-xl"><?= e($cat['description']) ?></p>
                    <?php endif; ?>
                </div>
                <span class="text-sm font-semibold text-accent"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?></span>
            </div>
            <?php if (empty($items)): ?>
            <p class="text-stone-400 italic">No items in this category yet.</p>
            <?php else: ?>
            <div class="grid md:grid-cols-2 gap-6">
                <?php foreach ($items as $item):
                    $tags = dietary_tags_array($item['dietary_tags']);
                    $unavailable = !$item['is_available'];
                    $hasImage = !empty($item['image_url']);
                    $featured = $item['is_featured'] && $item['is_available'];
                ?>
                <article class="xp-menu-item menu-card <?= $hasImage ? 'xp-menu-item--row menu-card--row' : '' ?> <?= $featured ? 'xp-menu-item--featured' : '' ?> <?= $unavailable ? 'opacity-60' : '' ?>">
                    <?php if ($hasImage): ?>
                    <div class="xp-menu-item-img sm:w-36 md:w-40 aspect-[4/3] sm:aspect-auto">
                        <img src="<?= e(upload_url($item['image_url'])) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                    </div>
                    <?php endif; ?>
                    <div class="xp-menu-item-body">
                        <div class="xp-menu-item-top">
                            <h3 class="xp-menu-item-name">
                                <?= e($item['name']) ?>
                                <?php if ($featured): ?><span class="xp-menu-item-badge">Featured</span><?php endif; ?>
                            </h3>
                            <span class="xp-menu-item-price"><?= format_price($item['price']) ?></span>
                        </div>
                        <?php if ($item['description']): ?>
                        <p class="text-stone-600 text-sm mt-2 flex-grow"><?= e($item['description']) ?></p>
                        <?php endif; ?>
                        <div class="flex flex-wrap gap-2 mt-4">
                            <?php foreach ($tags as $tag): ?>
                            <span class="text-xs px-2.5 py-0.5 rounded-full <?= dietary_badge_class($tag) ?>"><?= e(ucfirst($tag)) ?></span>
                            <?php endforeach; ?>
                            <?php if ($unavailable): ?>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-stone-200 text-stone-600">Unavailable</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="site-cta-band xp-cta-band xp-reveal">
    <div class="site-cta-band-glow xp-cta-band-glow" aria-hidden="true"></div>
    <h2 class="site-cta-band-title xp-cta-band-title">Ready to dine with us?</h2>
    <p class="site-cta-band-text xp-cta-band-text">Book a table for lunch, dinner, or a special celebration.</p>
    <div class="site-cta-band-actions xp-cta-band-actions">
        <a href="<?= base_url('reservations.php') ?>" class="hero-btn-primary">Book a Table</a>
        <a href="<?= base_url('contact.php') ?>" class="site-cta-btn-outline xp-cta-btn-outline">Contact us</a>
    </div>
</section>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
