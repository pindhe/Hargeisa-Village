<?php

declare(strict_types=1);

// Wrong port (Cursor preview / Java API) — send users to XAMPP on port 80
if (PHP_SAPI !== 'cli') {
    $serverPort = (string) ($_SERVER['SERVER_PORT'] ?? '');
    if (in_array($serverPort, ['8081', '8080'], true)) {
        $uri = $_SERVER['REQUEST_URI'] ?? '/Village/';
        header('Location: http://localhost:80' . $uri, true, 302);
        exit;
    }
}

session_name('hv_restaurant_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require dirname(__DIR__) . '/config/app.php';
date_default_timezone_set($config['timezone']);

require_once dirname(__DIR__) . '/includes/Database.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/Settings.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/Mailer.php';
require_once dirname(__DIR__) . '/includes/footer.php';

$adminHelpers = dirname(__DIR__) . '/admin/includes/helpers.php';
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
if (is_file($adminHelpers) && str_contains($scriptPath, '/admin/')) {
    require_once $adminHelpers;
}
