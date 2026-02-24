<?php
$title = 'View Chat Session';
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
                <h1 style="color: var(--neon-cobalt); margin:0;">💬 Chat Session</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">
                    Session ID: <span style="font-family: monospace; color: var(--text-primary);"><?= e($sessionData['session_uuid']) ?></span><br>
                    Started: <?= date('M j, Y - g:i A', strtotime($sessionData['created_at'])) ?>
                </p>
            </div>
            <a href="<?= baseUrl('admin/inbox') ?>" class="admin-btn">Back to Inbox</a>
        </div>

        <div class="admin-card" style="max-width: 800px; margin: 0 auto; background: var(--bg-secondary); padding: 0; overflow: hidden; display: flex; flex-direction: column; max-height: 70vh;">
            <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); background: rgba(0,0,0,0.2);">
                <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; justify-content: space-between;">
                    <span><strong>IP:</strong> <?= e($sessionData['user_ip'] ?: 'Unknown') ?></span>
                    <span><strong>Status:</strong> <?= ucfirst($sessionData['status']) ?></span>
                </div>
                <?php if($sessionData['user_agent']): ?>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px; opacity: 0.7;">
                        <?= e($sessionData['user_agent']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="padding: 30px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 15px;">
                <?php if (empty($messages)): ?>
                    <p style="color: var(--text-muted); text-align: center; margin: auto;">No messages recorded for this session.</p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <?php if ($msg['sender'] === 'bot'): ?>
                            <div style="align-self: flex-start; max-width: 80%; background: var(--glass-bg); border: 1px solid var(--glass-border); padding: 12px 16px; border-radius: 12px 12px 12px 0;">
                                <div style="font-size: 0.7rem; color: var(--neon-cyan); margin-bottom: 4px; font-weight: 700; text-transform: uppercase;">Bot</div>
                                <div style="color: var(--text-primary); font-size: 0.95rem; line-height: 1.5;">
                                    <?= nl2br(e($msg['message'])) ?>
                                </div>
                                <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 8px; text-align: right;">
                                    <?= date('g:i A', strtotime($msg['created_at'])) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="align-self: flex-end; max-width: 80%; background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(251,191,36,0.1)); border: 1px solid rgba(16,185,129,0.3); padding: 12px 16px; border-radius: 12px 12px 0 12px;">
                                <div style="font-size: 0.7rem; color: var(--theme-gold); margin-bottom: 4px; font-weight: 700; text-transform: uppercase; text-align: right;">User</div>
                                <div style="color: var(--text-primary); font-size: 0.95rem; line-height: 1.5;">
                                    <?= nl2br(e($msg['message'])) ?>
                                </div>
                                <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 8px; text-align: right;">
                                    <?= date('g:i A', strtotime($msg['created_at'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
