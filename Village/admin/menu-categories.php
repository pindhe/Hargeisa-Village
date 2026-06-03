<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete' && isset($_POST['id'])) {
        $pdo->prepare('DELETE FROM menu_categories WHERE id = ?')->execute([(int) $_POST['id']]);
        flash('success', 'Category deleted.');
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $order = (int) ($_POST['display_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($name !== '') {
            if ($id > 0) {
                $pdo->prepare('UPDATE menu_categories SET name=?, description=?, display_order=?, is_active=? WHERE id=?')
                    ->execute([$name, $description, $order, $active, $id]);
                flash('success', 'Category updated.');
            } else {
                $pdo->prepare('INSERT INTO menu_categories (name, description, display_order, is_active) VALUES (?,?,?,?)')
                    ->execute([$name, $description, $order, $active]);
                flash('success', 'Category created.');
            }
        }
    }
    redirect(base_url('admin/menu-categories.php'));
}

$categories = $pdo->query('SELECT * FROM menu_categories ORDER BY display_order, name')->fetchAll();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$edit = null;
foreach ($categories as $c) {
    if ((int) $c['id'] === $editId) {
        $edit = $c;
        break;
    }
}

$adminTitle = 'Categories';
$adminSubtitle = 'Organize menu sections';
require __DIR__ . '/includes/layout.php';
?>
<div class="admin-split-grid">
    <form method="post" class="admin-form-card h-fit">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <h2 class="admin-form-heading"><?= $edit ? 'Edit' : 'Add' ?> Category</h2>
        <div><label>Name *</label><input name="name" required value="<?= e($edit['name'] ?? '') ?>"></div>
        <div><label>Description</label><textarea name="description" rows="2"><?= e($edit['description'] ?? '') ?></textarea></div>
        <div><label>Display Order</label><input type="number" name="display_order" value="<?= (int) ($edit['display_order'] ?? 0) ?>"></div>
        <label class="admin-checkbox"><input type="checkbox" name="is_active" <?= ($edit['is_active'] ?? 1) ? 'checked' : '' ?>> Active on menu</label>
        <div class="flex gap-3">
            <button type="submit" class="admin-btn admin-btn--primary">Save</button>
            <?php if ($edit): ?><a href="<?= base_url('admin/menu-categories.php') ?>" class="admin-btn admin-btn--secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
    <div class="admin-panel">
        <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Order</th><th>Active</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td><strong><?= e($c['name']) ?></strong></td>
                <td><?= (int) $c['display_order'] ?></td>
                <td><?= $c['is_active'] ? '<span class="admin-badge admin-badge--confirmed">Yes</span>' : '<span class="admin-badge">No</span>' ?></td>
                <td class="whitespace-nowrap">
                    <a href="?edit=<?= (int) $c['id'] ?>" class="admin-table-link">Edit</a>
                    <form method="post" class="inline ml-2" onsubmit="return confirm('Delete this category and all its items?');">
                        <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <button type="submit" class="admin-text-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
