<?php
require_once dirname(__DIR__) . '/nav.php';
require_once dirname(__DIR__) . '/footer.php';

$brandName = footer_brand_name();
$shortName = footer_short_name();
$description = footer_description();
$socialLinks = footer_social_links();
$hoursSummary = footer_hours_summary();
$hoursRows = footer_hours_rows();
$contactLinks = footer_contact_links();
$showHours = footer_show_hours() && ($hoursSummary['morning'] !== '' || $hoursSummary['afternoon'] !== '');
$navItems = site_nav_items();
?>
</main>
<?php if (empty($hideFooter)): ?>
<footer class="site-footer bg-brand-900 text-stone-300 mt-auto" role="contentinfo">
    <div class="site-footer-glow" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative">
        <div class="py-12 lg:py-14 grid grid-cols-1 sm:grid-cols-2 <?= $showHours ? 'lg:grid-cols-4' : 'lg:grid-cols-3' ?> gap-10 lg:gap-8">
            <!-- Brand -->
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="<?= e(home_url()) ?>" class="inline-flex items-center gap-2 group">
                    <span class="site-footer-logo">HV</span>
                    <span class="font-display text-xl text-white group-hover:text-accent-light transition"><?= e($shortName) ?></span>
                </a>
                <?php if ($description !== ''): ?>
                <p class="text-sm leading-relaxed mt-4 text-stone-400 max-w-xs"><?= e($description) ?></p>
                <?php endif; ?>
                <?php if (count($socialLinks) > 0): ?>
                <div class="flex gap-2 mt-5" aria-label="Social media">
                    <?php foreach ($socialLinks as $social): ?>
                    <a href="<?= e($social['url']) ?>" class="site-footer-social" target="_blank" rel="noopener noreferrer" aria-label="<?= e($social['label']) ?>">
                        <?= footer_social_icon_svg($social['icon']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick links -->
            <div>
                <h4 class="site-footer-heading">Explore</h4>
                <ul class="site-footer-links">
                    <li><a href="<?= e(home_url()) ?>">Home</a></li>
                    <?php foreach ($navItems as $href => $label): ?>
                    <li><a href="<?= nav_url($href) ?>"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?= nav_url('reservations.php') ?>" class="site-footer-cta mt-4">Book a Table</a>
            </div>

            <!-- Hours (dynamic, optional) -->
            <?php if ($showHours): ?>
            <div>
                <h4 class="site-footer-heading">Hours</h4>
                <p class="text-sm font-semibold text-white/90 mb-2"><?= e($hoursSummary['label']) ?></p>
                <dl class="site-footer-hours site-footer-hours--sessions">
                    <div class="site-footer-hours-row">
                        <dt>Morning</dt>
                        <dd><?= e($hoursSummary['morning']) ?></dd>
                    </div>
                    <div class="site-footer-hours-row">
                        <dt>Afternoon</dt>
                        <dd><?= e($hoursSummary['afternoon']) ?></dd>
                    </div>
                </dl>
            </div>
            <?php endif; ?>

            <!-- Contact -->
            <div>
                <h4 class="site-footer-heading">Contact</h4>
                <ul class="site-footer-contact">
                    <?php foreach ($contactLinks as $item): ?>
                    <li>
                        <span class="site-footer-contact-label"><?= e($item['label']) ?></span>
                        <?php if ($item['href']): ?>
                        <a href="<?= e($item['href']) ?>" class="site-footer-contact-value"><?= e($item['value']) ?></a>
                        <?php else: ?>
                        <span class="site-footer-contact-value"><?= e($item['value']) ?></span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="site-footer-bottom border-t border-brand-800/80 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-500">
            <p><?= e(footer_copyright_line()) ?></p>
            <p>
                <a href="<?= base_url('admin/login.php') ?>" class="site-footer-admin-link hover:text-accent-light transition">Admin Login</a>
            </p>
        </div>
    </div>
</footer>
<?php endif; ?>
<script src="<?= asset_url('js/theme.js') ?>"></script>
<script src="<?= asset_url('js/main.js') ?>"></script>
<?php if (!empty($loadExperience)): ?>
<script src="<?= asset_url('js/experience.js') ?>" defer></script>
<?php endif; ?>
</body>
</html>
