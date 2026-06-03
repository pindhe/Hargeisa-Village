<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $keys = [
        'restaurant_name', 'tagline', 'phone', 'email', 'address',
        'facebook_url', 'instagram_url', 'twitter_url', 'google_maps_embed',
        'notification_email', 'menu_pdf_url',
        'hours_morning', 'hours_afternoon',
        'footer_about', 'footer_show_hours', 'footer_copyright',
    ];
    foreach ($keys as $key) {
        if ($key === 'footer_show_hours') {
            continue;
        }
        if (isset($_POST[$key])) {
            Settings::set($key, trim((string) $_POST[$key]));
        }
    }
    site_hours_sync_week();
    Settings::set('footer_show_hours', isset($_POST['footer_show_hours']) ? '1' : '0');
    flash('success', 'Settings saved.');
    redirect(base_url('admin/settings.php'));
}

$settings = Settings::all();
$adminTitle = 'Settings';
$adminSubtitle = 'Restaurant info, hours, footer, and integrations';
require __DIR__ . '/includes/layout.php';
?>
<form method="post" class="admin-form-card max-w-2xl space-y-6">
    <?= csrf_field() ?>
    <fieldset class="space-y-4">
        <legend class="font-semibold text-stone-800">General</legend>
        <?php foreach (['restaurant_name' => 'Restaurant Name', 'tagline' => 'Tagline', 'phone' => 'Phone', 'email' => 'Email', 'address' => 'Address', 'notification_email' => 'Notification Email'] as $k => $label): ?>
        <div><label class="block text-sm mb-1"><?= e($label) ?></label><input name="<?= e($k) ?>" value="<?= e($settings[$k] ?? '') ?>" class="w-full border rounded-lg px-3 py-2"></div>
        <?php endforeach; ?>
    </fieldset>
    <fieldset class="space-y-4">
        <legend class="font-semibold text-stone-800">Social Media</legend>
        <?php foreach (['facebook_url' => 'Facebook', 'instagram_url' => 'Instagram', 'twitter_url' => 'Twitter/X'] as $k => $label): ?>
        <div><label class="block text-sm mb-1"><?= e($label) ?> URL</label><input name="<?= e($k) ?>" value="<?= e($settings[$k] ?? '') ?>" class="w-full border rounded-lg px-3 py-2"></div>
        <?php endforeach; ?>
    </fieldset>
    <fieldset class="space-y-4">
        <legend class="font-semibold text-stone-800">Operating Hours</legend>
        <p class="text-sm text-stone-500">Same schedule every day (Monday–Sunday).</p>
        <div>
            <label class="block text-sm mb-1">Morning session</label>
            <input name="hours_morning" value="<?= e($settings['hours_morning'] ?? site_hours_morning()) ?>" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="8:30 AM - 3:00 PM">
        </div>
        <div>
            <label class="block text-sm mb-1">Afternoon session</label>
            <input name="hours_afternoon" value="<?= e($settings['hours_afternoon'] ?? site_hours_afternoon()) ?>" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="4:30 PM - 11:00 PM">
        </div>
    </fieldset>
    <fieldset class="space-y-4">
        <legend class="font-semibold text-stone-800">Footer</legend>
        <div>
            <label class="block text-sm mb-1">Footer description</label>
            <textarea name="footer_about" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Leave empty to use site tagline"><?= e($settings['footer_about'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="footer_show_hours" value="1" <?= ($settings['footer_show_hours'] ?? '1') !== '0' ? 'checked' : '' ?>>
                Show opening hours in footer
            </label>
        </div>
        <div>
            <label class="block text-sm mb-1">Extra copyright text (optional)</label>
            <input name="footer_copyright" value="<?= e($settings['footer_copyright'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="e.g. Made with love in Hargeisa">
        </div>
    </fieldset>
    <fieldset class="space-y-4">
        <legend class="font-semibold text-stone-800">Other</legend>
        <div><label class="block text-sm mb-1">Menu PDF URL</label><input name="menu_pdf_url" value="<?= e($settings['menu_pdf_url'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2" placeholder="https://... or /Village/uploads/menu.pdf"></div>
        <div><label class="block text-sm mb-1">Google Maps Embed URL</label><textarea name="google_maps_embed" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"><?= e($settings['google_maps_embed'] ?? '') ?></textarea></div>
    </fieldset>
    <button type="submit" class="admin-btn admin-btn--primary">Save Settings</button>
</form>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
