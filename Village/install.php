<?php

declare(strict_types=1);

/**
 * One-time installer: creates database tables and admin user.
 * Delete or protect this file after installation.
 */

$config = require __DIR__ . '/config/database.php';
$app = require __DIR__ . '/config/app.php';

$messages = [];
$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $config['host'], $config['port'], $config['charset']);
        $pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $config['dbname']) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo->exec('USE `' . str_replace('`', '``', $config['dbname']) . '`');

        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $sql = preg_replace('/CREATE DATABASE.*?;/s', '', $sql);
        $sql = preg_replace('/USE hargeisa_village;/', '', $sql);
        foreach (array_filter(array_map('trim', explode(';', $sql))) as $statement) {
            if ($statement !== '' && !str_starts_with($statement, '--')) {
                $pdo->exec($statement);
            }
        }

        $adminPass = $_POST['admin_password'] ?? 'Admin@123';
        $hash = password_hash($adminPass, PASSWORD_BCRYPT);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE username = ?')->execute([$hash, 'admin']);

        if (!is_dir($app['upload_path'])) {
            mkdir($app['upload_path'], 0755, true);
            mkdir($app['upload_path'] . '/menu', 0755, true);
            mkdir($app['upload_path'] . '/gallery', 0755, true);
        }

        $messages[] = 'Installation complete! Default admin username: <strong>admin</strong>';
        $messages[] = 'Log in at: <a href="admin/login.php">admin/login.php</a>';
        $messages[] = '<strong>Delete install.php</strong> for security.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install | Hargeisa Village Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-stone-100 flex items-center justify-center p-4">
    <div class="max-w-lg w-full bg-white rounded-2xl shadow-xl border p-8">
        <h1 class="text-2xl font-bold text-brand-800 mb-4">Database Installer</h1>
        <?php if ($error): ?><p class="text-red-600 text-sm mb-4"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php foreach ($messages as $msg): ?><p class="text-green-800 text-sm mb-2"><?= $msg ?></p><?php endforeach; ?>
        <?php if (empty($messages)): ?>
        <p class="text-stone-600 text-sm mb-6">This will create the <code>hargeisa_village</code> database and seed sample data. Ensure MySQL is running in XAMPP.</p>
        <form method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Admin password</label>
                <input type="password" name="admin_password" value="Admin@123" class="w-full border rounded-lg px-4 py-2">
            </div>
            <button type="submit" class="w-full py-3 rounded-full bg-brand-700 text-white font-semibold">Run Installation</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
