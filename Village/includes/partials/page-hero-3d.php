<?php
/**
 * Inner-page hero — matches home layout (BG.png, overlay nav, promo, cutout media).
 *
 * $heroEyebrow, $heroTitle, $heroTitleAccent (optional), $heroSubtitle,
 * $heroPrimaryBtn, $heroSecondaryBtn (href, label, class?, attrs?, icon?),
 * $heroScroll (href, label), $heroId, $heroExtra, $heroPromo (href, text),
 * $heroBg, $heroMediaImage, $heroMediaVideo, $heroMediaAlt,
 * $heroMediaLayout — 'showcase' | 'video-showcase' | '3d' (default video stage),
 * $heroShowcaseCaption, $heroShowcaseClass (e.g. hero-cutout-stage--feature),
 * $heroMediaColumnClass — optional column wrapper (e.g. page-hero-media--bleed),
 */
$assets = page_experience_assets();
$heroBg = $heroBg ?? $assets['bg'];
$heroMediaImage = $heroMediaImage ?? null;
$heroMediaVideo = $heroMediaVideo ?? null;
$heroMediaAlt = $heroMediaAlt ?? 'Hargeisa Village';
$heroMediaLayout = $heroMediaLayout ?? ($heroMediaImage ? 'showcase' : ($heroMediaVideo ? 'video-showcase' : '3d'));
$heroShowcaseCaption = $heroShowcaseCaption ?? '';
$heroShowcaseClass = trim($heroShowcaseClass ?? 'hero-cutout-stage--medium');
$heroShowcasePlain = str_contains($heroShowcaseClass, 'plain') || str_contains($heroShowcaseClass, 'hero-cutout');
$heroId = $heroId ?? 'page-hero-title';
$heroPrimaryBtn = $heroPrimaryBtn ?? null;
$heroSecondaryBtn = $heroSecondaryBtn ?? null;
$heroScroll = $heroScroll ?? null;
$heroPromo = $heroPromo ?? null;
$heroMediaColumnClass = trim($heroMediaColumnClass ?? '');
$heroShowcaseLuxury = str_contains($heroShowcaseClass, '--luxury');
$heroShowcaseCinematic = str_contains($heroShowcaseClass, '--cinematic');
$defaultVideo = $assets['video'];
?>
<section class="home-hero page-hero relative min-h-screen flex flex-col overflow-hidden" aria-labelledby="<?= e($heroId) ?>">
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat scale-105" style="background-image: url('<?= e($heroBg) ?>')" role="presentation"></div>
    <div class="absolute inset-0 home-hero-overlay" aria-hidden="true"></div>

    <?php require dirname(__DIR__) . '/layout/hero-nav.php'; ?>

    <div class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 pb-12 lg:pb-20 pt-2 lg:pt-6">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center min-h-[calc(100vh-11rem)]">
            <div class="text-white order-2 lg:order-1 xp-reveal">
                <?php if (!empty($heroPromo['text'])): ?>
                <a href="<?= e($heroPromo['href'] ?? base_url('menu.php')) ?>" class="hero-promo-badge inline-flex items-center gap-2 mb-6 max-w-full">
                    <span class="w-2 h-2 rounded-full bg-accent-light shrink-0 animate-pulse"></span>
                    <span class="truncate"><?= e($heroPromo['text']) ?></span>
                    <span class="shrink-0" aria-hidden="true">&rarr;</span>
                </a>
                <?php endif; ?>
                <p class="text-accent-light text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase mb-3">
                    <?= e($heroEyebrow ?? '') ?>
                </p>
                <h1 id="<?= e($heroId) ?>" class="font-display text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold leading-[1.1] mb-4">
                    <?= e($heroTitle ?? '') ?><?php if (!empty($heroTitleAccent)): ?><br><span class="text-brand-500"><?= e($heroTitleAccent) ?></span><?php endif; ?>
                </h1>
                <?php if (!empty($heroSubtitle)): ?>
                <p class="text-lg sm:text-xl text-white/75 max-w-md mb-8 leading-relaxed"><?= e($heroSubtitle) ?></p>
                <?php endif; ?>
                <?php if ($heroPrimaryBtn || $heroSecondaryBtn): ?>
                <div class="flex flex-wrap gap-4">
                    <?php if ($heroPrimaryBtn): ?>
                    <a href="<?= e($heroPrimaryBtn['href']) ?>" class="hero-btn-primary <?= e($heroPrimaryBtn['class'] ?? '') ?>"<?= !empty($heroPrimaryBtn['attrs']) ? ' ' . $heroPrimaryBtn['attrs'] : '' ?>>
                        <?= $heroPrimaryBtn['icon'] ?? '' ?>
                        <?= e($heroPrimaryBtn['label']) ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($heroSecondaryBtn): ?>
                    <a href="<?= e($heroSecondaryBtn['href']) ?>" class="hero-btn-secondary <?= e($heroSecondaryBtn['class'] ?? '') ?>"<?= !empty($heroSecondaryBtn['attrs']) ? ' ' . $heroSecondaryBtn['attrs'] : '' ?>>
                        <?= e($heroSecondaryBtn['label']) ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?= $heroExtra ?? '' ?>
            </div>

            <div class="order-1 lg:order-2 flex flex-col justify-center items-center lg:items-end xp-reveal xp-reveal--delay<?= $heroMediaColumnClass !== '' ? ' ' . e($heroMediaColumnClass) : '' ?>">
                <?php if ($heroMediaLayout === 'video-showcase' && $heroMediaVideo): ?>
                <div class="hero-cutout-stage hero-cinema-stage <?= e($heroShowcaseClass) ?><?= $heroShowcaseCinematic ? ' hero-cutout-stage--animated' : '' ?>"<?= $heroShowcaseCinematic ? ' data-cinema-stage' : '' ?>>
                    <?php if ($heroShowcaseCinematic): ?>
                    <div class="hero-cinema-particles" aria-hidden="true">
                        <?php for ($pi = 0; $pi < 12; $pi++): ?>
                        <span class="hero-cinema-particle" style="--particle-i: <?= $pi ?>"></span>
                        <?php endfor; ?>
                    </div>
                    <div class="hero-cutout-shadow-floor" aria-hidden="true"></div>
                    <div class="hero-cutout-shadow-deep" aria-hidden="true"></div>
                    <div class="hero-cinema-dof" aria-hidden="true"></div>
                    <div class="hero-cinema-shine" aria-hidden="true"></div>
                    <?php endif; ?>
                    <div class="hero-video-glow hero-cutout-glow hero-cinema-glow" aria-hidden="true"></div>
                    <video
                        class="hero-cutout-video<?= $heroShowcaseCinematic ? ' hero-cinema-video--float' : '' ?>"
                        src="<?= e($heroMediaVideo) ?>"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="auto"
                        aria-label="<?= e($heroMediaAlt) ?>"
                    ></video>
                </div>
                <?php elseif ($heroMediaImage && $heroMediaLayout === 'showcase'): ?>
                <div class="hero-cutout-stage <?= e($heroShowcaseClass) ?><?= $heroShowcaseLuxury ? ' hero-cutout-stage--animated' : '' ?>">
                    <?php if ($heroShowcaseLuxury): ?>
                    <div class="hero-cutout-shadow-floor" aria-hidden="true"></div>
                    <div class="hero-cutout-shadow-deep" aria-hidden="true"></div>
                    <?php endif; ?>
                    <div class="hero-video-glow hero-cutout-glow" aria-hidden="true"></div>
                    <img
                        class="hero-cutout-img<?= $heroShowcaseLuxury ? ' hero-cutout-img--float' : '' ?>"
                        src="<?= e($heroMediaImage) ?>"
                        alt="<?= e($heroMediaAlt) ?>"
                        width="640"
                        height="800"
                        fetchpriority="high"
                    >
                </div>
                <?php else: ?>
                <div class="hero-video-stage">
                    <div class="hero-video-glow" aria-hidden="true"></div>
                    <video
                        class="hero-video"
                        src="<?= e($heroMediaVideo ?: $defaultVideo) ?>"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="auto"
                        aria-label="<?= e($heroMediaAlt) ?>"
                    ></video>
                </div>
                <?php endif; ?>
                <?php if (!empty($heroShowcaseCaption)): ?>
                <p class="hero-cutout-caption mt-4">
                    <span class="hero-cutout-caption-dot" aria-hidden="true"></span>
                    <?= e($heroShowcaseCaption) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if (!empty($heroScroll['href'])): ?>
    <a href="<?= e($heroScroll['href']) ?>" class="page-hero-scroll" data-xp-scroll<?= ($heroScroll['href'] ?? '') === '#contact-form' ? ' data-scroll-to-form' : '' ?> aria-label="<?= e($heroScroll['label']) ?>">
        <span class="page-hero-scroll-line"></span>
        <span class="page-hero-scroll-label"><?= e($heroScroll['label']) ?></span>
    </a>
    <?php endif; ?>
</section>
