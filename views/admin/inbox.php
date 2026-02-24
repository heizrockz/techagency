<?php
$title = 'Chatbot Inbox';
$currentPage = 'inbox';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <div>
                <h1 style="color: var(--neon-cobalt); margin:0;">📥 Chatbot Inbox</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Review automated conversations between users and the chatbot.</p>
            </div>
        </div>

        <div class="admin-card">
            <?php if (empty($sessions)): ?>
                <p style="color: var(--text-muted); text-align: center; padding: 40px 0;">No chatbot conversations recorded yet.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Date / Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--text-primary);">
                                        <?= e(substr($session['session_uuid'], 0, 8)) ?>...
                                    </strong>
                                </td>
                                <td><?= (int)$session['msg_count'] ?> messages</td>
                                <td>
                                    <?php if($session['status'] === 'active'): ?>
                                        <span class="status-badge" style="background: rgba(16, 185, 129, 0.1); color: var(--theme-primary);">Active</span>
                                    <?php else: ?>
                                        <span class="status-badge" style="background: rgba(255, 255, 255, 0.1); color: var(--text-muted);">Closed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M j, Y - g:i A', strtotime($session['updated_at'])) ?></td>
                                <td>
                                    <a href="<?= baseUrl('admin/inbox?action=view&id=' . $session['id']) ?>" class="admin-btn" style="padding: 6px 12px; font-size: 0.8rem;">View Chat</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
