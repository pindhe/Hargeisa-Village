<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$errors = [];
$contactSuccess = false;
$scrollToForm = false;

$whatsappMessage = 'Hello Hargeisa Village, I would like to get in touch.';
$whatsappUrl = whatsapp_url($whatsappMessage);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? 'General Inquiry');
        $message = trim($_POST['message'] ?? '');
        if ($name === '') {
            $errors[] = 'Name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if ($message === '') {
            $errors[] = 'Message is required.';
        }
        if (empty($errors)) {
            $pdo = Database::getConnection();
            $pdo->prepare('INSERT INTO contact_messages (full_name, email, subject, message) VALUES (?, ?, ?, ?)')
                ->execute([$name, $email, $subject, $message]);
            clear_old_input();
            $contactSuccess = true;
            $scrollToForm = true;
        } else {
            store_old_input($_POST);
            $scrollToForm = true;
        }
    }
}

$phone = Settings::get('phone');
$email = Settings::get('email');
$address = Settings::get('address');
$mapEmbed = trim(Settings::get('google_maps_embed', ''));
$mapsOpenUrl = maps_search_url($address);
$hours = footer_hours_summary();
$subjectOptions = contact_subject_options();
$selectedSubject = 'General Inquiry';
if (!empty($_SESSION['old_input']['subject'])) {
    $selectedSubject = (string) $_SESSION['old_input']['subject'];
}
if (!in_array($selectedSubject, $subjectOptions, true)) {
    $selectedSubject = $subjectOptions[0];
}
$pageTitle = 'Contact';
$currentPage = 'contact.php';
$isOverlayHero = true;
$bodyClass = 'contact-page overlay-hero-page';
require __DIR__ . '/includes/layout/header.php';

$waIcon = '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>';

$heroPromo = ['href' => base_url('menu.php'), 'text' => 'Weekend Family Platter — 20% off every Friday & Saturday!'];
$heroEyebrow = 'Get in Touch';
$heroTitle = "Let's Start a";
$heroTitleAccent = 'Conversation';
$heroSubtitle = 'Questions, events, or feedback — reach us on WhatsApp or send a message below. We reply as soon as we can.';
$heroId = 'contact-hero-title';
$heroMediaVideo = asset_url('video/3D.mp4');
$heroMediaLayout = '3d';
$heroMediaAlt = 'Restaurant showcase';
$heroPrimaryBtn = [
    'href' => $whatsappUrl,
    'label' => 'Chat on WhatsApp',
    'class' => 'gap-2',
    'attrs' => 'target="_blank" rel="noopener noreferrer"',
    'icon' => $waIcon,
];
$heroSecondaryBtn = [
    'href' => '#contact-form',
    'label' => 'Send Message',
    'attrs' => 'data-scroll-to-form',
];
$heroScroll = ['href' => '#contact-form', 'label' => 'Message us'];
$heroExtra = '<p class="mt-4 text-sm text-white/50">WhatsApp: <a href="' . e($whatsappUrl) . '" target="_blank" rel="noopener noreferrer" class="text-white/80 hover:text-accent-light underline-offset-2 hover:underline">' . e(whatsapp_display()) . '</a></p>';
require __DIR__ . '/includes/partials/page-hero-3d.php';
?>

<!-- Quick contact cards -->
<section class="contact-cards-section site-stat-strip relative z-20 pb-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-card contact-card--whatsapp contact-reveal">
                <div class="contact-card-icon contact-card-icon--whatsapp">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <h3 class="contact-card-title">WhatsApp</h3>
                <p class="contact-card-value"><?= e(whatsapp_display()) ?></p>
                <span class="contact-card-link">Open chat &rarr;</span>
            </a>
            <a href="tel:<?= e(preg_replace('/\D+/', '', $phone)) ?>" class="contact-card contact-reveal contact-reveal--delay">
                <div class="contact-card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="contact-card-title">Phone</h3>
                <p class="contact-card-value"><?= e($phone) ?></p>
                <span class="contact-card-link">Tap to call &rarr;</span>
            </a>
            <a href="mailto:<?= e($email) ?>" class="contact-card contact-reveal contact-reveal--delay-2">
                <div class="contact-card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="contact-card-title">Email</h3>
                <p class="contact-card-value break-all"><?= e($email) ?></p>
                <span class="contact-card-link">Send email &rarr;</span>
            </a>
            <div class="contact-card contact-card--static contact-reveal contact-reveal--delay-2">
                <div class="contact-card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="contact-card-title">Visit</h3>
                <p class="contact-card-value"><?= e($address) ?></p>
                <?php if ($mapEmbed !== ''): ?>
                <a href="#contact-map" class="contact-card-link">View map &darr;</a>
                <?php else: ?>
                <span class="contact-card-link text-stone-400">Hargeisa, Somaliland</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="contact-find-section" id="contact-map" aria-labelledby="contact-find-title">
    <div class="contact-find-bg" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center py-14 md:py-20">
            <div class="contact-reveal">
                <p class="contact-section-eyebrow">Find Us</p>
                <h2 id="contact-find-title" class="font-display text-3xl md:text-4xl lg:text-[2.75rem] text-white leading-tight">Visit Hargeisa Village</h2>
                <p class="text-white/65 mt-4 text-lg leading-relaxed">Dine with us in the heart of Hargeisa — authentic Somali flavors, warm hospitality, and a welcoming space for every occasion.</p>

                <div class="contact-find-address mt-8">
                    <span class="contact-find-address-icon" aria-hidden="true">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <div>
                        <p class="contact-find-address-label">Address</p>
                        <p class="contact-find-address-text"><?= e($address) ?></p>
                    </div>
                </div>

                <div class="contact-find-hours mt-8">
                    <p class="contact-find-hours-title"><?= e($hours['label']) ?></p>
                    <table class="contact-hours-table">
                        <tbody>
                            <tr>
                                <th scope="row">Morning</th>
                                <td><?= e($hours['morning']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Afternoon</th>
                                <td><?= e($hours['afternoon']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="contact-find-actions mt-8 flex flex-wrap gap-3">
                    <a href="<?= e($mapsOpenUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-find-btn contact-find-btn--maps">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        Open in Maps
                    </a>
                    <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-find-btn contact-find-btn--whatsapp">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                    </a>
                    <a href="#contact-form" class="contact-find-btn contact-find-btn--ghost" data-scroll-to-form>Send a message</a>
                </div>
            </div>

            <div class="contact-reveal contact-reveal--delay">
                <div class="contact-map-frame" data-tilt-subtle>
                    <span class="contact-map-corner contact-map-corner--tl" aria-hidden="true"></span>
                    <span class="contact-map-corner contact-map-corner--br" aria-hidden="true"></span>
                    <?php if ($mapEmbed !== ''): ?>
                    <div class="contact-map-frame-inner">
                        <iframe src="<?= e($mapEmbed) ?>" title="Hargeisa Village on Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                    <a href="<?= e($mapsOpenUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-map-open-btn">Open in Maps</a>
                    <?php else: ?>
                    <div class="contact-map-placeholder">
                        <p class="font-display text-xl text-white mb-2">Map preview</p>
                        <p class="text-white/60 text-sm mb-4">Add a Google Maps embed URL in Admin → Settings.</p>
                        <a href="<?= e($mapsOpenUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-find-btn contact-find-btn--maps">View on Google Maps</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact form -->
<section class="contact-main py-16 md:py-24 bg-stone-50" id="contact-form"<?= $scrollToForm ? ' data-scroll-on-load tabindex="-1"' : '' ?>>
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 md:mb-16 contact-reveal">
            <p class="contact-section-eyebrow contact-section-eyebrow--light">Contact Form</p>
            <h2 class="font-display text-3xl md:text-4xl text-brand-900">Send Us a Message</h2>
            <p class="text-stone-500 mt-3 max-w-lg mx-auto">We typically respond within 24 hours. For faster help, use <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-accent font-semibold hover:underline">WhatsApp</a>.</p>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            <aside class="lg:col-span-4 space-y-5">
                <div class="contact-aside-card contact-aside-card--whatsapp contact-reveal">
                    <span class="contact-aside-tag">Fastest reply</span>
                    <h3 class="contact-aside-title">Chat on WhatsApp</h3>
                    <p class="contact-aside-text">Reservations, menu questions, events — our team replies quickly on WhatsApp.</p>
                    <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-whatsapp-cta">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <?= e(whatsapp_display()) ?>
                    </a>
                </div>
                <div class="contact-aside-card contact-aside-card--dark contact-reveal contact-reveal--delay">
                    <h3 class="contact-aside-title contact-aside-title--light">Opening hours</h3>
                    <table class="contact-hours-table contact-hours-table--aside">
                        <tbody>
                            <tr>
                                <th scope="row"><?= e($hours['label']) ?></th>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row">Morning</th>
                                <td><?= e($hours['morning']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Afternoon</th>
                                <td><?= e($hours['afternoon']) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="contact-aside-card contact-aside-card--accent contact-reveal contact-reveal--delay">
                    <h3 class="contact-aside-title contact-aside-title--light">Prefer to book a table?</h3>
                    <p class="contact-aside-text contact-aside-text--light">Reserve online for groups, celebrations, or weekend dining.</p>
                    <a href="<?= base_url('reservations.php') ?>" class="contact-aside-cta">
                        Book a Table
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
                <div class="contact-social contact-reveal contact-reveal--delay-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-stone-500 mb-3">Follow Us</p>
                    <div class="flex gap-3">
                        <?php foreach (['instagram_url' => 'Instagram', 'facebook_url' => 'Facebook', 'twitter_url' => 'X'] as $key => $label):
                            $url = Settings::get($key);
                            if ($url && $url !== '#'):
                        ?>
                        <a href="<?= e($url) ?>" target="_blank" rel="noopener" class="contact-social-btn" aria-label="<?= e($label) ?>"><?= e(substr($label, 0, 2)) ?></a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </aside>

            <div class="lg:col-span-8 contact-reveal contact-reveal--delay">
                <?php if ($contactSuccess): ?>
                <div class="contact-form-wrap contact-success-state" role="alert">
                    <div class="contact-success-icon">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="font-display text-2xl text-brand-900 mt-6">Message Sent!</h2>
                    <p class="text-stone-600 mt-2 max-w-sm mx-auto">Thank you for reaching out. Our team will get back to you as soon as possible.</p>
                    <div class="flex flex-wrap gap-3 justify-center mt-8">
                        <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-hero-btn contact-hero-btn--whatsapp">WhatsApp us</a>
                        <a href="<?= base_url('contact.php') ?>" class="btn-accent">Send Another Message</a>
                    </div>
                </div>
                <?php else: ?>
                <?php if ($errors): ?>
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-red-800 text-sm contact-shake" role="alert">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <form method="post" class="contact-form-wrap contact-form-wrap--enhanced" data-contact-form action="#contact-form" novalidate>
                    <?= csrf_field() ?>
                    <div class="contact-form-head">
                        <h3 class="contact-form-head-title">Write to our team</h3>
                        <p class="contact-form-head-desc">Fields marked with * are required.</p>
                    </div>
                    <div class="contact-form-grid">
                        <div class="contact-field contact-field--float">
                            <input type="text" id="full_name" name="full_name" required placeholder=" " autocomplete="name" class="contact-field-input" value="<?= old_input('full_name') ?>">
                            <label for="full_name">Your Name *</label>
                            <span class="contact-field-line"></span>
                        </div>
                        <div class="contact-field contact-field--float">
                            <input type="email" id="email" name="email" required placeholder=" " autocomplete="email" class="contact-field-input" value="<?= old_input('email') ?>">
                            <label for="email">Email Address *</label>
                            <span class="contact-field-line"></span>
                        </div>
                        <div class="contact-field contact-field--select contact-field--full">
                            <label for="subject" class="contact-field-label-static">Subject</label>
                            <select id="subject" name="subject" class="contact-field-select">
                                <?php foreach ($subjectOptions as $opt): ?>
                                <option value="<?= e($opt) ?>" <?= $selectedSubject === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="contact-field contact-field--float contact-field--full">
                            <textarea id="message" name="message" rows="5" required placeholder=" " class="contact-field-input contact-field-input--area" maxlength="2000"><?= old_input('message') ?></textarea>
                            <label for="message">Your Message *</label>
                            <span class="contact-field-line"></span>
                        </div>
                    </div>
                    <p class="contact-form-note">By submitting, you agree we may contact you about your inquiry. We never share your details with third parties.</p>
                    <button type="submit" class="contact-submit" data-submit-btn>
                        <span class="contact-submit-text">Send Message</span>
                        <span class="contact-submit-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset_url('js/contact.js') ?>" defer></script>

<?php require __DIR__ . '/includes/layout/footer.php'; ?>
