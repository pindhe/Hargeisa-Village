<?php
/** @var string $bannerTitle */
/** @var string|null $bannerSubtitle */
/** @var string|null $bannerEyebrow */
$bg = asset_url('images/BG.png');
?>
<section class="page-banner relative py-20 md:py-28 overflow-hidden">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= e($bg) ?>')" role="presentation"></div>
    <div class="absolute inset-0 page-banner-overlay" aria-hidden="true"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 text-center text-white">
        <?php if (!empty($bannerEyebrow)): ?>
        <p class="text-accent-light text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase mb-3"><?= e($bannerEyebrow) ?></p>
        <?php endif; ?>
        <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-bold"><?= e($bannerTitle) ?></h1>
        <?php if (!empty($bannerSubtitle)): ?>
        <p class="mt-4 text-lg text-white/75 max-w-2xl mx-auto"><?= e($bannerSubtitle) ?></p>
        <?php endif; ?>
        <?php if (!empty($bannerActions)) echo $bannerActions; ?>
    </div>
</section>
