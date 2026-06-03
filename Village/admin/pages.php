<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $id = (int) ($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    if ($id > 0 && $title !== '') {
        $pdo->prepare('UPDATE pages SET title = ?, content = ? WHERE id = ?')->execute([$title, $content, $id]);
        flash('success', 'Page content saved.');
    }
    redirect(base_url('admin/pages.php?edit=' . $id));
}

$pages = $pdo->query('SELECT * FROM pages ORDER BY slug')->fetchAll();
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$edit = null;
foreach ($pages as $p) {
    if ((int) $p['id'] === $editId) {
        $edit = $p;
        break;
    }
}

$adminTitle = 'Pages';
$adminSubtitle = 'Edit website content (CMS)';
require __DIR__ . '/includes/layout.php';
?>
<div class="admin-cms-grid">
    <nav class="admin-page-list" aria-label="Pages">
        <?php foreach ($pages as $p): ?>
        <a href="?edit=<?= (int) $p['id'] ?>" class="admin-page-list-item <?= $editId === (int)$p['id'] ? 'admin-page-list-item--active' : '' ?>">
            <strong><?= e($p['title']) ?></strong>
            <span><?= e($p['slug']) ?></span>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-cms-editor">
        <?php if ($edit): ?>
        <form method="post" class="admin-form-card">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $edit['id'] ?>">
            <p class="admin-form-meta">Slug: <code><?= e($edit['slug']) ?></code></p>
            <div>
                <label>Title</label>
                <input name="title" required value="<?= e($edit['title']) ?>">
            </div>
            <div>
                <label>Content</label>
                <div id="editor" class="admin-quill-editor"><?= $edit['content'] ?></div>
                <textarea name="content" id="content-input" class="hidden"><?= e($edit['content']) ?></textarea>
            </div>
            <button type="submit" class="admin-btn admin-btn--primary">Save Content</button>
        </form>
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
        const quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [['bold','italic'],['link'],[{list:'ordered'},{list:'bullet'}],['clean']] } });
        document.querySelector('form').addEventListener('submit', () => {
            document.getElementById('content-input').value = quill.root.innerHTML;
        });
        </script>
        <?php else: ?>
        <div class="admin-panel admin-empty-panel">Select a page from the list to edit its content.</div>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
