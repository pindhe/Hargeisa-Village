<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();
$categories = $pdo->query('SELECT id, name FROM menu_categories ORDER BY display_order, name')->fetchAll();

$formErrors = [];
$isFormView = false;
$isAdd = false;
$edit = null;
$formData = null;

// —— POST: delete, save ——
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $itemId = (int) ($_POST['id'] ?? 0);
        if ($itemId <= 0) {
            flash('error', 'Invalid menu item.');
            redirect(base_url('admin/menu-items.php'));
        }
        $stmt = $pdo->prepare('SELECT id, name, image_url FROM menu_items WHERE id = ?');
        $stmt->execute([$itemId]);
        $row = $stmt->fetch();
        if (!$row) {
            flash('error', 'Menu item not found.');
            redirect(base_url('admin/menu-items.php'));
        }
        $pdo->prepare('DELETE FROM menu_items WHERE id = ?')->execute([$itemId]);
        delete_upload_file($row['image_url'] ?: null);
        flash('success', '“' . $row['name'] . '” was deleted.');
        redirect(base_url('admin/menu-items.php'));
    }

    if ($postAction === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priceRaw = trim($_POST['price'] ?? '');
        $tags = trim($_POST['dietary_tags'] ?? '');
        $available = isset($_POST['is_available']) ? 1 : 0;
        $featured = isset($_POST['is_featured']) ? 1 : 0;
        $removeImage = isset($_POST['remove_image']);

        $existing = null;
        if ($id > 0) {
            $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
            $stmt->execute([$id]);
            $existing = $stmt->fetch() ?: null;
            if (!$existing) {
                flash('error', 'Menu item not found.');
                redirect(base_url('admin/menu-items.php'));
            }
        }

        $imageUrl = $existing['image_url'] ?? null;

        if ($removeImage && $imageUrl) {
            delete_upload_file($imageUrl);
            $imageUrl = null;
        }

        if (!empty($_FILES['image']['name'])) {
            $uploaded = handle_image_upload($_FILES['image'], 'menu');
            if ($uploaded) {
                delete_upload_file($imageUrl);
                $imageUrl = $uploaded;
            } else {
                $formErrors[] = 'Image upload failed. Use JPG, PNG, WebP or GIF under 5 MB.';
            }
        }

        if ($name === '') {
            $formErrors[] = 'Name is required.';
        }
        if ($categoryId <= 0) {
            $formErrors[] = 'Please select a category.';
        }
        if ($priceRaw === '' || !is_numeric($priceRaw)) {
            $formErrors[] = 'Enter a valid price.';
        }
        $price = (float) ($priceRaw !== '' && is_numeric($priceRaw) ? $priceRaw : 0);
        if ($price < 0) {
            $formErrors[] = 'Price cannot be negative.';
        }
        if (empty($categories)) {
            $formErrors[] = 'Create a menu category before adding items.';
        }

        $formData = [
            'id' => $id,
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'price' => $priceRaw,
            'dietary_tags' => $tags,
            'is_available' => $available,
            'is_featured' => $featured,
            'image_url' => $imageUrl ?? '',
        ];

        if ($formErrors === []) {
            if ($id > 0) {
                $pdo->prepare(
                    'UPDATE menu_items SET category_id=?, name=?, description=?, price=?, image_url=?, dietary_tags=?, is_available=?, is_featured=? WHERE id=?'
                )->execute([$categoryId, $name, $description, $price, $imageUrl, $tags, $available, $featured, $id]);
                flash('success', 'Menu item updated.');
            } else {
                $pdo->prepare(
                    'INSERT INTO menu_items (category_id, name, description, price, image_url, dietary_tags, is_available, is_featured) VALUES (?,?,?,?,?,?,?,?)'
                )->execute([$categoryId, $name, $description, $price, $imageUrl, $tags, $available, $featured]);
                flash('success', 'Menu item created.');
            }
            redirect(base_url('admin/menu-items.php'));
        }

        flash('error', 'Please fix the errors below.');
        $isFormView = true;
        $isAdd = $id <= 0;
        if ($existing) {
            $edit = $existing;
        }
    }
}

// —— GET: add / edit form ——
if (!$isFormView) {
    $action = $_GET['action'] ?? '';
    $itemId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $isAdd = $action === 'add';
    $isFormView = $isAdd || $itemId > 0;

    if ($itemId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
        $stmt->execute([$itemId]);
        $edit = $stmt->fetch();
        if (!$edit) {
            flash('error', 'Menu item not found.');
            redirect(base_url('admin/menu-items.php'));
        }
    }
}

if ($isFormView && $formData === null) {
    $formData = $edit ?: [
        'id' => 0,
        'category_id' => $categories[0]['id'] ?? 0,
        'name' => '',
        'description' => '',
        'price' => '',
        'dietary_tags' => '',
        'is_available' => 1,
        'is_featured' => 0,
        'image_url' => '',
    ];
}

$formActionUrl = $isAdd
    ? base_url('admin/menu-items.php?action=add')
    : base_url('admin/menu-items.php?id=' . (int) ($formData['id'] ?? $edit['id'] ?? 0));

// —— List view: filters & query ——
$categoryFilter = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$availFilter = $_GET['available'] ?? '';
$featuredFilter = $_GET['featured'] ?? '';
$search = trim($_GET['q'] ?? '');

$sql = 'SELECT mi.*, mc.name AS category_name FROM menu_items mi
        JOIN menu_categories mc ON mc.id = mi.category_id WHERE 1=1';
$params = [];

if ($categoryFilter > 0) {
    $sql .= ' AND mi.category_id = ?';
    $params[] = $categoryFilter;
}
if ($availFilter === '1') {
    $sql .= ' AND mi.is_available = 1';
} elseif ($availFilter === '0') {
    $sql .= ' AND mi.is_available = 0';
}
if ($featuredFilter === '1') {
    $sql .= ' AND mi.is_featured = 1';
}
if ($search !== '') {
    $sql .= ' AND (mi.name LIKE ? OR mi.description LIKE ? OR mi.dietary_tags LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}
$sql .= ' ORDER BY mc.display_order, mc.name, mi.name';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
$stats = admin_menu_stats();

if ($isFormView) {
    $adminTitle = $isAdd ? 'Add Menu Item' : 'Edit Menu Item';
    $adminSubtitle = $isAdd ? 'Create a new dish for your public menu' : e($formData['name'] ?? '');
    ob_start();
    ?>
<a href="<?= base_url('admin/menu-items.php') ?>" class="admin-btn admin-btn--secondary">&larr; All items</a>
    <?php
    $adminActions = ob_get_clean();
} else {
    $adminTitle = 'Menu Items';
    $adminSubtitle = 'Manage dishes, prices, photos, and visibility on your website';
    ob_start();
    ?>
<a href="<?= base_url('admin/menu-categories.php') ?>" class="admin-btn admin-btn--ghost">Categories</a>
<a href="<?= base_url('admin/menu-items.php?action=add') ?>" class="admin-btn admin-btn--primary">+ Add Item</a>
    <?php
    $adminActions = ob_get_clean();
}

require __DIR__ . '/includes/layout.php';

if ($isFormView):
    $e = $formData;
    $hasImage = !empty($e['image_url']);
    $editingId = (int) ($e['id'] ?? 0);
?>
<div class="admin-form-page-header">
    <span class="admin-badge <?= $isAdd ? 'admin-badge--pending' : 'admin-badge--confirmed' ?>"><?= $isAdd ? 'New item' : 'Editing' ?></span>
    <?php if ($editingId > 0): ?>
    <span class="admin-form-meta">ID #<?= $editingId ?></span>
    <?php endif; ?>
</div>

<?php if ($formErrors !== []): ?>
<div class="admin-alert admin-alert--error admin-form-errors" role="alert">
    <p class="font-semibold">Could not save:</p>
    <ul>
        <?php foreach ($formErrors as $err): ?>
        <li><?= e($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (empty($categories)): ?>
<div class="admin-alert admin-alert--error" role="alert">
    <p>You need at least one <a href="<?= base_url('admin/menu-categories.php') ?>" class="admin-table-link">menu category</a> before saving items.</p>
</div>
<?php endif; ?>

<form method="post" action="<?= e($formActionUrl) ?>" enctype="multipart/form-data" class="admin-form-layout">
    <div class="admin-form-card">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= $editingId ?>">

        <h2 class="admin-form-heading"><?= $isAdd ? 'Item details' : 'Edit item' ?></h2>

        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label for="menu-name">Name *</label>
                <input id="menu-name" name="name" required value="<?= e($e['name'] ?? '') ?>" placeholder="e.g. Somali Rice Plate" <?= empty($categories) ? 'disabled' : '' ?>>
            </div>
            <div>
                <label for="menu-category">Category *</label>
                <select id="menu-category" name="category_id" required <?= empty($categories) ? 'disabled' : '' ?>>
                    <?php if (empty($categories)): ?>
                    <option value="">No categories — create one first</option>
                    <?php else: ?>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int)($e['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label for="menu-price">Price *</label>
                <input id="menu-price" type="number" step="0.01" min="0" name="price" required value="<?= e((string)($e['price'] ?? '')) ?>" placeholder="0.00" <?= empty($categories) ? 'disabled' : '' ?>>
            </div>
            <div class="sm:col-span-2">
                <label for="menu-description">Description</label>
                <textarea id="menu-description" name="description" rows="4" placeholder="Short description shown on the menu page"><?= e($e['description'] ?? '') ?></textarea>
            </div>
            <div class="sm:col-span-2">
                <label for="menu-tags">Dietary tags</label>
                <input id="menu-tags" name="dietary_tags" value="<?= e($e['dietary_tags'] ?? '') ?>" placeholder="vegetarian, spicy, gluten-free">
                <p class="admin-form-hint">Comma-separated labels displayed on menu cards.</p>
            </div>
        </div>

        <h3 class="admin-form-section-title mt-6">Visibility</h3>
        <div class="flex flex-col gap-2">
            <label class="admin-checkbox">
                <input type="checkbox" name="is_available" <?= ($e['is_available'] ?? 1) ? 'checked' : '' ?>>
                Available on menu (guests can see this item)
            </label>
            <label class="admin-checkbox">
                <input type="checkbox" name="is_featured" <?= ($e['is_featured'] ?? 0) ? 'checked' : '' ?>>
                Featured on home page
            </label>
        </div>

        <div class="admin-form-actions-sticky">
            <button type="submit" class="admin-btn admin-btn--primary" <?= empty($categories) ? 'disabled' : '' ?>><?= $isAdd ? 'Create item' : 'Save changes' ?></button>
            <a href="<?= base_url('admin/menu-items.php') ?>" class="admin-btn admin-btn--secondary">Cancel</a>
            <?php if ($editingId > 0): ?>
            <a href="<?= base_url('menu.php') ?>" target="_blank" rel="noopener" class="admin-btn admin-btn--ghost ml-auto">View on site</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-form-card h-fit">
        <h3 class="admin-form-section-title">Photo</h3>
        <div class="admin-image-preview <?= $hasImage ? '' : 'admin-image-preview--empty' ?>" id="menu-image-preview-box">
            <?php if ($hasImage): ?>
            <img src="<?= e(upload_url($e['image_url'])) ?>" alt="Item image" id="menu-image-preview-img">
            <?php else: ?>
            <svg class="w-10 h-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>No image yet</span>
            <?php endif; ?>
        </div>
        <?php if ($hasImage && $editingId > 0): ?>
        <label class="admin-checkbox mt-3">
            <input type="checkbox" name="remove_image" value="1">
            Remove current photo
        </label>
        <?php endif; ?>
        <div class="admin-image-upload">
            <strong><?= $editingId > 0 ? 'Replace image' : 'Upload image' ?></strong>
            <p class="admin-form-hint mt-1">JPG, PNG or WebP. Max 5 MB. Optional.</p>
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" id="menu-image-input">
        </div>
    </div>
</form>

<?php if ($editingId > 0): ?>
<div class="admin-danger-zone">
    <h3 class="admin-danger-zone-title">Delete item</h3>
    <p class="admin-danger-zone-text">This permanently removes “<?= e($e['name']) ?>” from the menu. This cannot be undone.</p>
    <form method="post" action="<?= base_url('admin/menu-items.php') ?>" onsubmit="return confirm(<?= json_encode('Delete “' . ($e['name'] ?? '') . '” permanently?', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $editingId ?>">
        <button type="submit" class="admin-btn admin-btn--danger">Delete menu item</button>
    </form>
</div>
<?php endif; ?>

<script>
(function () {
    const input = document.getElementById('menu-image-input');
    const box = document.getElementById('menu-image-preview-box');
    if (!input || !box) return;
    input.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
            box.classList.remove('admin-image-preview--empty');
            box.innerHTML = '<img src="' + ev.target.result + '" alt="Preview" id="menu-image-preview-img">';
        };
        reader.readAsDataURL(file);
    });
})();
</script>

<?php else: ?>

<div class="admin-stat-grid admin-menu-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-card-icon admin-stat-card-icon--orange">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p class="admin-stat-label">Total items</p>
        <p class="admin-stat-value"><?= $stats['total'] ?></p>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-card-icon admin-stat-card-icon--green">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="admin-stat-label">Available</p>
        <p class="admin-stat-value"><?= $stats['available'] ?></p>
        <p class="admin-stat-hint"><?= $stats['unavailable'] ?> hidden</p>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-card-icon admin-stat-card-icon--amber">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        </div>
        <p class="admin-stat-label">Featured</p>
        <p class="admin-stat-value"><?= $stats['featured'] ?></p>
        <p class="admin-stat-hint">On homepage</p>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-card-icon admin-stat-card-icon--blue">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
        </div>
        <p class="admin-stat-label">Categories</p>
        <p class="admin-stat-value"><?= $stats['categories'] ?></p>
        <p class="admin-stat-hint"><a href="<?= base_url('admin/menu-categories.php') ?>" class="admin-table-link">Manage</a></p>
    </div>
</div>

<form method="get" class="admin-filter-bar">
    <input type="search" name="q" value="<?= e($search) ?>" placeholder="Search name, description, tags…" class="min-w-[200px] flex-1">
    <select name="category">
        <option value="">All categories</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= (int) $c['id'] ?>" <?= $categoryFilter === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="available">
        <option value="">Any availability</option>
        <option value="1" <?= $availFilter === '1' ? 'selected' : '' ?>>Available only</option>
        <option value="0" <?= $availFilter === '0' ? 'selected' : '' ?>>Unavailable only</option>
    </select>
    <select name="featured">
        <option value="">Featured: all</option>
        <option value="1" <?= $featuredFilter === '1' ? 'selected' : '' ?>>Featured only</option>
    </select>
    <button type="submit" class="admin-btn admin-btn--primary">Filter</button>
    <?php if ($search !== '' || $categoryFilter || $availFilter !== '' || $featuredFilter !== ''): ?>
    <a href="<?= base_url('admin/menu-items.php') ?>" class="admin-btn admin-btn--secondary">Clear</a>
    <?php endif; ?>
</form>

<div class="admin-panel">
    <div class="admin-panel-header">
        <h2 class="admin-panel-title"><?= count($items) ?> item<?= count($items) !== 1 ? 's' : '' ?><?= $search || $categoryFilter || $availFilter !== '' || $featuredFilter !== '' ? ' (filtered)' : '' ?></h2>
        <a href="<?= base_url('admin/menu-items.php?action=add') ?>" class="admin-btn admin-btn--ghost text-sm">+ Add item</a>
    </div>
    <?php if (empty($categories)): ?>
    <div class="admin-empty-state">
        <p class="font-semibold text-stone-700">No menu categories yet</p>
        <p class="text-sm mt-2">Create a category before adding menu items.</p>
        <a href="<?= base_url('admin/menu-categories.php') ?>" class="admin-btn admin-btn--primary mt-4">Add category</a>
    </div>
    <?php elseif (empty($items)): ?>
    <div class="admin-empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <p class="font-semibold text-stone-700">No menu items found</p>
        <p class="text-sm mt-2"><?= $search || $categoryFilter || $availFilter !== '' || $featuredFilter !== '' ? 'Try changing filters or ' : '' ?>add your first dish.</p>
        <a href="<?= base_url('admin/menu-items.php?action=add') ?>" class="admin-btn admin-btn--primary mt-4">+ Add menu item</a>
    </div>
    <?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Tags</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <div class="admin-menu-item-cell">
                        <?php if (!empty($item['image_url'])): ?>
                        <img src="<?= e(upload_url($item['image_url'])) ?>" alt="" class="admin-menu-thumb" loading="lazy">
                        <?php else: ?>
                        <span class="admin-menu-thumb admin-menu-thumb--empty">No img</span>
                        <?php endif; ?>
                        <div class="min-w-0">
                            <div class="admin-menu-item-name">
                                <?= e($item['name']) ?>
                                <?php if ($item['is_featured']): ?>
                                <span class="admin-tag admin-tag--featured">Featured</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($item['description'])): ?>
                            <p class="admin-menu-item-desc"><?= e($item['description']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td><span class="admin-badge"><?= e($item['category_name']) ?></span></td>
                <td><strong><?= format_price($item['price']) ?></strong></td>
                <td><?= admin_menu_dietary_tags_html($item['dietary_tags'] ?? null) ?></td>
                <td>
                    <?= $item['is_available']
                        ? '<span class="admin-badge admin-badge--confirmed">Available</span>'
                        : '<span class="admin-badge admin-badge--declined">Hidden</span>' ?>
                </td>
                <td>
                    <div class="admin-table-actions">
                        <a href="<?= base_url('admin/menu-items.php?id=' . (int) $item['id']) ?>" class="admin-table-link">Edit</a>
                        <form method="post" action="<?= base_url('admin/menu-items.php') ?>" onsubmit="return confirm(<?= json_encode('Delete “' . $item['name'] . '”?', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button type="submit" class="admin-text-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php endif;
require __DIR__ . '/includes/layout-end.php';
