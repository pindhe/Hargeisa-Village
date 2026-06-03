<?php

declare(strict_types=1);

function app_config(): array
{
    static $config = null;
    if ($config === null) {
        $config = require dirname(__DIR__) . '/config/app.php';
    }
    return $config;
}

function base_url(string $path = ''): string
{
    $base = rtrim(site_base_url(), '/');
    $path = ltrim($path, '/');
    return $path === '' ? $base : $base . '/' . $path;
}

/** Canonical site root — always port 80 for local XAMPP, never the preview port. */
function site_base_url(): string
{
    $url = rtrim(app_config()['url'], '/');
    $parts = parse_url($url);
    if (!$parts || empty($parts['host'])) {
        return 'http://localhost/Village';
    }
    $host = $parts['host'];
    $scheme = $parts['scheme'] ?? 'http';
    $path = isset($parts['path']) ? rtrim($parts['path'], '/') : '/Village';

    if (in_array($host, ['localhost', '127.0.0.1'], true)) {
        // Port 80 is default for http://localhost — avoids broken :80 in some browsers
        return $scheme . '://' . $host . $path;
    }

    return $url;
}

/** Absolute home URL — always XAMPP, never preview port 8081. */
function home_url(): string
{
    return base_url();
}

function port_fix_script_tag(): string
{
    return '<script src="' . e(asset_url('js/port-fix.js')) . '"></script>';
}

function asset_url(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function hero_hours_summary(): string
{
    $morning = site_hours_morning();
    $afternoon = site_hours_afternoon();
    return '7 days · ' . $morning . ' & ' . $afternoon;
}

function upload_url(?string $path): string
{
    if ($path === null || $path === '') {
        return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80';
    }
    if (str_starts_with($path, 'http')) {
        return $path;
    }
    return rtrim(app_config()['upload_url'], '/') . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function dietary_tags_array(?string $tags): array
{
    if ($tags === null || trim($tags) === '') {
        return [];
    }
    return array_filter(array_map('trim', explode(',', strtolower($tags))));
}

function dietary_badge_class(string $tag): string
{
    return match ($tag) {
        'vegetarian' => 'bg-green-100 text-green-800',
        'vegan' => 'bg-emerald-100 text-emerald-800',
        'gluten-free' => 'bg-amber-100 text-amber-800',
        'spicy' => 'bg-red-100 text-red-800',
        default => 'bg-stone-100 text-stone-700',
    };
}

function format_price(float|string $price): string
{
    return '$' . number_format((float) $price, 2);
}

function format_date(string $date): string
{
    return date('M j, Y', strtotime($date));
}

function format_time(string $time): string
{
    return date('g:i A', strtotime($time));
}

function handle_image_upload(array $file, string $subdir = 'menu'): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $config = app_config();
    if ($file['size'] > $config['max_upload_bytes']) {
        return null;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $config['allowed_image_types'], true)) {
        return null;
    }
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        default => null,
    };
    if ($ext === null) {
        return null;
    }
    $dir = $config['upload_path'] . '/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = uniqid($subdir . '_', true) . '.' . $ext;
    $dest = $dir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return null;
    }
    return $subdir . '/' . $filename;
}

function delete_upload_file(?string $relativePath): void
{
    if ($relativePath === null || $relativePath === '') {
        return;
    }
    $full = app_config()['upload_path'] . '/' . ltrim($relativePath, '/');
    if (is_file($full)) {
        unlink($full);
    }
}

function reservation_time_slots(): array
{
    $slots = [];
    for ($h = 11; $h <= 21; $h++) {
        foreach (['00', '30'] as $m) {
            if ($h === 21 && $m === '30') {
                continue;
            }
            $slots[] = sprintf('%02d:%s', $h, $m);
        }
    }
    return $slots;
}

/** WhatsApp business line (digits only, no +). */
function whatsapp_number(): string
{
    return '252636249555';
}

function whatsapp_display(): string
{
    return '+252 63 624 9555';
}

function whatsapp_url(?string $message = null): string
{
    $url = 'https://wa.me/' . whatsapp_number();
    if ($message !== null && $message !== '') {
        $url .= '?text=' . rawurlencode($message);
    }
    return $url;
}

function maps_search_url(string $address): string
{
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
}

/** @return array{bg: string, video: string} */
function page_experience_assets(): array
{
    return [
        'bg' => asset_url('images/BG.png'),
        'video' => asset_url('video/3D.mp4'),
    ];
}

function menu_hero_image_url(): string
{
    $path = dirname(__DIR__) . '/assets/images/menu.png';
    if (is_file($path)) {
        return asset_url('images/menu.png');
    }
    return asset_url('images/BG.png');
}

function gallery_hero_image_url(): string
{
    $path = dirname(__DIR__) . '/assets/images/gallery-hero.png';
    if (is_file($path)) {
        return asset_url('images/gallery-hero.png');
    }
    return asset_url('images/BG.png');
}

function gallery_hero_video_url(): string
{
    $path = dirname(__DIR__) . '/assets/video/another.mp4';
    if (is_file($path)) {
        return asset_url('video/another.mp4');
    }
    return page_experience_assets()['video'];
}

/** @return list<string> */
function contact_subject_options(): array
{
    return [
        'General Inquiry',
        'Reservation Question',
        'Private Event / Catering',
        'Menu & Dining',
        'Feedback',
        'Other',
    ];
}

function old_input(string $key, string $default = ''): string
{
    return e($_SESSION['old_input'][$key] ?? $default);
}

function clear_old_input(): void
{
    unset($_SESSION['old_input']);
}

function store_old_input(array $data): void
{
    $_SESSION['old_input'] = $data;
}
