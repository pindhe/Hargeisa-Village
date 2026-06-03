<?php

declare(strict_types=1);

return [
    'name' => 'Hargeisa Village Restaurant',
    // Explicit :80 so links never follow Cursor/preview port 8081
    'url' => 'http://localhost/Village',
    'timezone' => 'Africa/Mogadishu',
    'upload_path' => dirname(__DIR__) . '/uploads',
    'upload_url' => '/Village/uploads',
    'max_upload_bytes' => 5 * 1024 * 1024,
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    'session_name' => 'hv_restaurant_session',
    'admin_path' => '/Village/admin',
];
