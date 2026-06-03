<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_login();

$pdo = Database::getConnection();

if (isset($_GET['read'])) {
    $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([(int) $_GET['read']]);
    redirect(base_url('admin/messages.php'));
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
$adminTitle = 'Messages';
$adminSubtitle = 'Inquiries from the contact form';
require __DIR__ . '/includes/layout.php';
?>
<div class="admin-message-list">
    <?php foreach ($messages as $m): ?>
    <article class="admin-message-card <?= !$m['is_read'] ? 'admin-message-card--unread' : '' ?>">
        <div class="admin-message-card-header">
            <div>
                <h3 class="admin-message-card-name"><?= e($m['full_name']) ?></h3>
                <p class="admin-message-card-email"><?= e($m['email']) ?></p>
                <?php if ($m['subject']): ?><p class="admin-message-card-subject"><?= e($m['subject']) ?></p><?php endif; ?>
                <p class="admin-message-card-date"><?= e($m['created_at']) ?></p>
            </div>
            <?php if (!$m['is_read']): ?>
            <a href="?read=<?= (int) $m['id'] ?>" class="admin-btn admin-btn--secondary text-xs">Mark read</a>
            <?php else: ?>
            <span class="admin-badge">Read</span>
            <?php endif; ?>
        </div>
        <p class="admin-message-card-body"><?= e($m['message']) ?></p>
    </article>
    <?php endforeach; ?>
    <?php if (empty($messages)): ?><p class="admin-empty-state">No messages yet.</p><?php endif; ?>
</div>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
