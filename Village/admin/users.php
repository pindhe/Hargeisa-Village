<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_admin_role();

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    if (($_POST['action'] ?? '') === 'delete') {
        $delId = (int) ($_POST['id'] ?? 0);
        if ($delId !== (int) (current_user()['id'] ?? 0)) {
            $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$delId]);
            flash('success', 'User deleted.');
        } else {
            flash('error', 'You cannot delete your own account.');
        }
        redirect(base_url('admin/users.php'));
    }
    $id = (int) ($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['admin', 'editor'], true) ? $_POST['role'] : 'editor';
    $password = $_POST['password'] ?? '';
    if ($username !== '') {
        if ($id > 0) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $pdo->prepare('UPDATE users SET username=?, email=?, role=?, password_hash=? WHERE id=?')
                    ->execute([$username, $email, $role, $hash, $id]);
            } else {
                $pdo->prepare('UPDATE users SET username=?, email=?, role=? WHERE id=?')
                    ->execute([$username, $email, $role, $id]);
            }
            flash('success', 'User updated.');
        } else {
            if ($password === '') {
                flash('error', 'Password required for new users.');
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $pdo->prepare('INSERT INTO users (username, email, role, password_hash) VALUES (?,?,?,?)')
                    ->execute([$username, $email, $role, $hash]);
                flash('success', 'User created.');
            }
        }
    }
    redirect(base_url('admin/users.php'));
}

$users = $pdo->query('SELECT id, username, email, role, created_at FROM users ORDER BY username')->fetchAll();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$edit = null;
foreach ($users as $u) {
    if ((int) $u['id'] === $editId) {
        $edit = $u;
        break;
    }
}

$adminTitle = 'Users';
$adminSubtitle = 'Manage admin accounts and roles';
require __DIR__ . '/includes/layout.php';
?>
<div class="admin-split-grid">
    <form method="post" class="admin-form-card h-fit">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <h2 class="admin-form-heading"><?= $edit ? 'Edit' : 'Add' ?> User</h2>
        <div><label>Username *</label><input name="username" required value="<?= e($edit['username'] ?? '') ?>"></div>
        <div><label>Email</label><input type="email" name="email" value="<?= e($edit['email'] ?? '') ?>"></div>
        <div><label>Role</label>
            <select name="role">
                <option value="editor" <?= ($edit['role'] ?? '') === 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="admin" <?= ($edit['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div><label>Password <?= $edit ? '(leave blank to keep)' : '*' ?></label><input type="password" name="password" <?= $edit ? '' : 'required' ?>></div>
        <button type="submit" class="admin-btn admin-btn--primary">Save</button>
    </form>
    <div class="admin-panel">
        <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Username</th><th>Role</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><strong><?= e($u['username']) ?></strong><br><span class="text-stone-400 text-xs"><?= e($u['email'] ?? '') ?></span></td>
                <td><span class="admin-badge"><?= e(ucfirst($u['role'])) ?></span></td>
                <td class="whitespace-nowrap">
                    <a href="?edit=<?= (int) $u['id'] ?>" class="admin-table-link">Edit</a>
                    <?php if ((int)$u['id'] !== (int)(current_user()['id'] ?? 0)): ?>
                    <form method="post" class="inline ml-2" onsubmit="return confirm('Delete user?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $u['id'] ?>"><button type="submit" class="admin-text-danger">Delete</button></form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
