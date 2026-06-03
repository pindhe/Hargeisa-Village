<?php

declare(strict_types=1);

/** @return array<string, array{label: string, icon: string}> */
function admin_nav_items(): array
{
    $user = current_user();
    $items = [
        'index.php' => ['label' => 'Dashboard', 'icon' => 'dashboard'],
        'reservations.php' => ['label' => 'Reservations', 'icon' => 'calendar'],
        'menu-items.php' => ['label' => 'Menu Items', 'icon' => 'menu'],
        'menu-categories.php' => ['label' => 'Categories', 'icon' => 'folder'],
        'gallery.php' => ['label' => 'Gallery', 'icon' => 'image'],
        'pages.php' => ['label' => 'Pages', 'icon' => 'document'],
        'messages.php' => ['label' => 'Messages', 'icon' => 'mail'],
        'settings.php' => ['label' => 'Settings', 'icon' => 'settings'],
    ];
    if (($user['role'] ?? '') === 'admin') {
        $items['users.php'] = ['label' => 'Users', 'icon' => 'users'];
    }
    return $items;
}

function admin_nav_icon(string $icon): string
{
    $icons = [
        'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'menu' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
        'folder' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>',
        'image' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'mail' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    ];
    $path = $icons[$icon] ?? $icons['dashboard'];
    return '<svg class="h-5 w-5 shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">' . $path . '</svg>';
}

function admin_is_active_nav(string $file): bool
{
    $current = basename($_SERVER['PHP_SELF'] ?? '');
    if ($file === 'menu-items.php' && $current === 'menu-categories.php') {
        return false;
    }
    return $current === $file;
}

function admin_status_badge(string $status): string
{
    $class = match ($status) {
        'pending' => 'admin-badge admin-badge--pending',
        'confirmed' => 'admin-badge admin-badge--confirmed',
        'declined' => 'admin-badge admin-badge--declined',
        'seated' => 'admin-badge admin-badge--seated',
        'cancelled' => 'admin-badge admin-badge--cancelled',
        default => 'admin-badge',
    };
    return '<span class="' . $class . '">' . e(ucfirst($status)) . '</span>';
}

function admin_unread_messages_count(): int
{
    static $count = null;
    if ($count === null) {
        $pdo = Database::getConnection();
        $count = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
    }
    return $count;
}

function admin_pending_reservations_count(): int
{
    static $count = null;
    if ($count === null) {
        $pdo = Database::getConnection();
        $count = (int) $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'pending'")->fetchColumn();
    }
    return $count;
}

/** @return array<string, int> */
function admin_dashboard_stats(): array
{
    $pdo = Database::getConnection();
    $today = date('Y-m-d');
    $weekStart = date('Y-m-d', strtotime('monday this week'));

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE reservation_date >= ?');
    $stmt->execute([$weekStart]);
    $weekReservations = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE reservation_date = ?');
    $stmt->execute([$today]);
    $todayReservations = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM reservations WHERE reservation_date >= ? AND status IN ('confirmed', 'seated')"
    );
    $stmt->execute([$weekStart]);
    $weekConfirmed = (int) $stmt->fetchColumn();

    return [
        'today_reservations' => $todayReservations,
        'week_reservations' => $weekReservations,
        'week_confirmed' => $weekConfirmed,
        'pending' => admin_pending_reservations_count(),
        'menu_items' => (int) $pdo->query('SELECT COUNT(*) FROM menu_items')->fetchColumn(),
        'menu_unavailable' => (int) $pdo->query('SELECT COUNT(*) FROM menu_items WHERE is_available = 0')->fetchColumn(),
        'gallery_images' => (int) $pdo->query('SELECT COUNT(*) FROM gallery_images')->fetchColumn(),
        'unread_messages' => admin_unread_messages_count(),
        'total_reservations' => (int) $pdo->query('SELECT COUNT(*) FROM reservations')->fetchColumn(),
    ];
}

/** @return list<array<string, mixed>> */
function admin_dashboard_upcoming(int $limit = 5): array
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
        "SELECT * FROM reservations
         WHERE reservation_date >= CURDATE()
           AND status IN ('pending', 'confirmed')
         ORDER BY reservation_date ASC, reservation_time ASC
         LIMIT ?"
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/** @return list<array<string, mixed>> */
/** @return array{total: int, available: int, featured: int, unavailable: int, categories: int} */
function admin_menu_stats(): array
{
    $pdo = Database::getConnection();
    return [
        'total' => (int) $pdo->query('SELECT COUNT(*) FROM menu_items')->fetchColumn(),
        'available' => (int) $pdo->query('SELECT COUNT(*) FROM menu_items WHERE is_available = 1')->fetchColumn(),
        'unavailable' => (int) $pdo->query('SELECT COUNT(*) FROM menu_items WHERE is_available = 0')->fetchColumn(),
        'featured' => (int) $pdo->query('SELECT COUNT(*) FROM menu_items WHERE is_featured = 1')->fetchColumn(),
        'categories' => (int) $pdo->query('SELECT COUNT(*) FROM menu_categories')->fetchColumn(),
    ];
}

function admin_menu_dietary_tags_html(?string $tags): string
{
    if ($tags === null || trim($tags) === '') {
        return '<span class="text-stone-400 text-xs">—</span>';
    }
    $html = '';
    foreach (array_filter(array_map('trim', explode(',', $tags))) as $tag) {
        $html .= '<span class="admin-tag">' . e($tag) . '</span>';
    }
    return $html;
}

function admin_dashboard_recent_messages(int $limit = 4): array
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
        'SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC LIMIT ?'
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}
