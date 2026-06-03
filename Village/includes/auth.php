<?php

declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect(base_url('admin/login.php'));
    }
}

function require_admin_role(): void
{
    require_login();
    $user = current_user();
    if (($user['role'] ?? '') !== 'admin') {
        flash('error', 'You do not have permission for this action.');
        redirect(base_url('admin/index.php'));
    }
}

function attempt_login(string $username, string $password): bool
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }
    unset($user['password_hash']);
    $_SESSION['user'] = $user;
    session_regenerate_id(true);
    return true;
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
