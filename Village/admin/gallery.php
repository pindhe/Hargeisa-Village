<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT image_url FROM gallery_images WHERE id = ?');
        $stmt->execute([$id]);
        delete_upload_file($stmt->fetchColumn() ?: null);
        $pdo->prepare('DELETE FROM gallery_images WHERE id = ?')->execute([$id]);
        flash('success', 'Image deleted.');
        redirect(base_url('admin/gallery.php'));
    }
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? '';
    $order = (int) ($_POST['display_order'] ?? 0);
    $imageUrl = null;
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT image_url FROM gallery_images WHERE id = ?');
        $stmt->execute([$id]);
        $imageUrl = $stmt->fetchColumn();
    }
    if (!empty($_FILES['image']['name'])) {
        $uploaded = handle_image_upload($_FILES['image'], 'gallery');
        if ($uploaded) {
            delete_upload_file($imageUrl);
            $imageUrl = $uploaded;
        }
    }
    if ($imageUrl) {
        if ($id > 0) {
            $pdo->prepare('UPDATE gallery_images SET title=?, description=?, category=?, display_order=?, image_url=? WHERE id=?')
                ->execute([$title, $description, $category, $order, $imageUrl, $id]);
            flash('success', 'Image updated.');
        } else {
            $pdo->prepare('INSERT INTO gallery_images (title, description, category, display_order, image_url) VALUES (?,?,?,?,?)')
                ->execute([$title, $description, $category, $order, $imageUrl]);
            flash('success', 'Image uploaded.');
        }
        redirect(base_url('admin/gallery.php'));
    }
    flash('error', 'Please upload an image.');
}

$images = $pdo->query('SELECT * FROM gallery_images ORDER BY display_order, uploaded_at DESC')->fetchAll();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$showForm = isset($_GET['action']) && $_GET['action'] === 'add' || $editId > 0;
$edit = null;
foreach ($images as $img) {
    if ((int) $img['id'] === $editId) {
        $edit = $img;
        break;
    }
}

$adminTitle = 'Gallery';
$adminSubtitle = 'Restaurant photos and media';
if (!$showForm) {
    ob_start();
?>
<a href="?action=add" class="admin-btn admin-btn--primary">+ Upload Image</a>
<?php
    $adminActions = ob_get_clean();
}
require __DIR__ . '/includes/layout.php';
?>
<?php if ($showForm):
    $g = $edit ?: ['title' => '', 'description' => '', 'category' => 'food', 'display_order' => 0, 'image_url' => ''];
?>
<form method="post" enctype="multipart/form-data" class="admin-form-card max-w-lg space-y-4 mb-8">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
    <div><label>Title</label><input name="title" value="<?= e($g['title'] ?? '') ?>"></div>
    <div><label>Description</label><textarea name="description" rows="2"><?= e($g['description'] ?? '') ?></textarea></div>
    <div><label>Category</label>
        <select name="category">
            <?php foreach (['food', 'interior', 'event'] as $cat): ?>
            <option value="<?= $cat ?>" <?= ($g['category'] ?? '') === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div><label>Display order</label><input type="number" name="display_order" value="<?= (int) ($g['display_order'] ?? 0) ?>"></div>
    <?php if (!empty($g['image_url'])): ?><img src="<?= e(upload_url($g['image_url'])) ?>" class="h-32 rounded mb-2" alt=""><?php endif; ?>
    <div><label>Image <?= $edit ? '(leave empty to keep)' : '*' ?></label><input type="file" name="image" accept="image/*" <?= $edit ? '' : 'required' ?>></div>
    <div class="flex gap-3">
        <button type="submit" class="admin-btn admin-btn--primary">Save</button>
        <a href="<?= base_url('admin/gallery.php') ?>" class="admin-btn admin-btn--secondary">Cancel</a>
    </div>
</form>
<?php elseif (!$showForm): ?>
<div class="admin-gallery-grid">
    <?php foreach ($images as $img): ?>
    <article class="admin-gallery-card">
        <img src="<?= e(upload_url($img['image_url'])) ?>" alt="<?= e($img['title'] ?: 'Gallery image') ?>">
        <div class="admin-gallery-card-body">
            <p class="admin-gallery-card-title"><?= e($img['title'] ?: 'Untitled') ?></p>
            <p class="admin-gallery-card-meta"><?= e(ucfirst($img['category'] ?? '')) ?></p>
            <div class="admin-gallery-card-actions">
                <a href="?edit=<?= (int) $img['id'] ?>" class="admin-table-link">Edit</a>
                <form method="post" onsubmit="return confirm('Delete this image?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $img['id'] ?>"><button type="submit" class="admin-text-danger">Delete</button></form>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
    <?php if (empty($images)): ?>
    <p class="admin-empty-state">No images yet. Upload your first photo.</p>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
