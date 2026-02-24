<?php
$title = 'Chatbot Inbox Dashboard';
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
        <div class="admin-header" style="margin-bottom:0;">
            <div>
                <h1 style="color: var(--neon-cobalt); margin:0; font-size:1.8rem;">📥 Inbox</h1>
                <p style="color: var(--text-muted); font-size: 0.85rem;">Manage and monitor automated conversations.</p>
            </div>
            <div class="header-actions" style="display:flex; gap:12px;">
                <button class="btn btn-ghost btn-sm" style="padding:6px 12px; font-size:0.75rem;">See how to use ▶</button>
                <button class="btn btn-secondary btn-sm" style="padding:6px 12px; font-size:0.75rem;">Help ?</button>
            </div>
        </div>

        <!-- Dashboard Layout -->
        <div class="inbox-dashboard">
            
            <!-- Left Sidebar: Sessions -->
            <div class="inbox-sidebar">
                <div class="inbox-sidebar-header">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-weight:600; font-size:0.9rem;">Chats</span>
                        <span style="font-size:0.8rem; color:var(--text-muted);">Recent</span>
                    </div>
                </div>
                <div class="session-list">
                    <?php if (empty($sessions)): ?>
                        <div style="padding:40px; text-align:center; color:var(--text-muted); font-size:0.85rem;">No chats found.</div>
                    <?php else: ?>
                        <?php foreach ($sessions as $s): ?>
                            <?php 
                                $isActive = ($selectedId == $s['id']); 
                                $uuid = (string)($s['session_uuid'] ?? 'VISI');
                                $snippet = $s['user_email'] ?: ($s['user_phone'] ?: 'Visitor ' . substr($uuid, 0, 4));
                            ?>
                            <a href="<?= baseUrl('admin/inbox?id=' . $s['id']) ?>" class="session-item <?= $isActive ? 'active' : '' ?>">
                                <div class="session-avatar">
                                    <?php 
                                        $initial = (string)($s['user_email'] ?: 'V');
                                        echo strtoupper(substr($initial, 0, 1));
                                    ?>
                                </div>
                                <div class="session-info">
                                    <div class="session-name"><?= e($snippet) ?></div>
                                    <div class="session-snippet"><?= $s['msg_count'] ?> messages • <?= date('H:i', strtotime($s['updated_at'])) ?></div>
                                </div>
                                <?php if($s['status'] === 'Open'): ?>
                                    <span style="width:8px; height:8px; background:var(--theme-primary); border-radius:50%; margin-top:5px;"></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Middle: Chat Area -->
            <div class="inbox-chat">
                <?php if (!$sessionData): ?>
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:var(--text-muted);">
                        <span style="font-size:3rem; margin-bottom:16px;">💬</span>
                        <p>Select a conversation to start viewing.</p>
                    </div>
                <?php else: ?>
                    <div class="chat-header">
                        <div>
                            <div style="font-weight:600; font-size:1rem; color:white;"><?= e($sessionData['user_email'] ?: 'Anonymous Visitor') ?></div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                <?= e($sessionData['user_ip']) ?> • Active since <?= date('M d, H:i', strtotime($sessionData['created_at'])) ?>
                            </div>
                        </div>
                        <div style="display:flex; gap:12px;">
                            <span class="badge" style="background:rgba(16, 185, 129, 0.1); color:var(--theme-primary); border:1px solid rgba(16, 185, 129, 0.2);">
                                <?= $sessionData['status'] ?>
                            </span>
                            <button class="btn btn-ghost btn-sm" style="padding:4px 8px;">⋮</button>
                        </div>
                    </div>
                    
                    <div class="chat-messages-area">
                        <?php if (empty($messages)): ?>
                            <div style="text-align:center; padding:40px; color:var(--text-muted);">No messages in this session.</div>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                                <?php $isBot = ($msg['sender'] === 'bot'); ?>
                                <div style="display:flex; flex-direction:column; align-items: <?= $isBot ? 'flex-start' : 'flex-end' ?>; width:100%;">
                                    <div style="
                                        max-width: 80%;
                                        padding: 12px 16px;
                                        border-radius: 16px;
                                        font-size: 0.9rem;
                                        line-height: 1.5;
                                        <?= $isBot 
                                            ? 'background:rgba(255,255,255,0.08); color:white; border-bottom-left-radius:4px;' 
                                            : 'background:linear-gradient(135deg, var(--neon-cobalt), var(--neon-cyan)); color:white; border-bottom-right-radius:4px;' 
                                        ?>
                                    ">
                                        <?= nl2br(e($msg['message'])) ?>
                                    </div>
                                    <div style="font-size:0.65rem; color:var(--text-muted); margin-top:4px;">
                                        <?= date('H:i', strtotime($msg['created_at'])) ?> <?= $isBot ? 'Bot ✓' : '' ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="chat-footer">
                        <div class="chat-input-wrapper">
                            <textarea placeholder="Type your message here... Use Shift + Enter for next line" rows="1"></textarea>
                            <button class="btn btn-primary btn-sm" style="padding:8px 16px; font-size:0.8rem;">Send As Agent</button>
                        </div>
                        <div style="font-size:0.7rem; color:var(--text-muted); margin-top:8px; display:flex; gap:12px;">
                            <span>🌐 Transcribe</span>
                            <span>⌨️ Shortcuts</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Visitor Profile -->
            <div class="inbox-profile">
                <?php if (!$sessionData): ?>
                    <div style="text-align:center; padding:40px; color:var(--text-muted); font-size:0.85rem;">No user selected.</div>
                <?php else: ?>
                    <div class="profile-section">
                        <div class="profile-section-title">Visitor Profile</div>
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                            <div class="session-avatar" style="width:48px; height:48px; font-size:1.5rem;">
                                <?= strtoupper(substr($sessionData['user_email'] ?: 'V', 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:0.95rem;"><?= e($sessionData['user_email'] ?: 'Guest') ?></div>
                                <div style="font-size:0.7rem; color:var(--text-muted);"><?= e($sessionData['user_phone'] ?: 'No phone provided') ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <div class="profile-section-title">AI Summary <span style="font-size:0.6rem; opacity:0.5;">▼</span></div>
                        <div style="background:rgba(255,255,255,0.03); border-radius:8px; padding:12px; font-size:0.8rem; color:var(--text-secondary);">
                            User inquired about services and shared contact details. Potential lead for <?= e($sessionData['user_email'] ? 'Direct Marketing' : 'Discovery') ?>.
                        </div>
                    </div>

                    <div class="profile-section">
                        <div class="profile-section-title">User Details <span style="font-size:0.6rem; opacity:0.5;">▼</span></div>
                        <div class="profile-data-row">
                            <div class="profile-label">Email</div>
                            <div class="profile-value"><?= e($sessionData['user_email'] ?: 'N/A') ?></div>
                        </div>
                        <div class="profile-data-row">
                            <div class="profile-label">Phone</div>
                            <div class="profile-value"><?= e($sessionData['user_phone'] ?: 'N/A') ?></div>
                        </div>
                        <div class="profile-data-row">
                            <div class="profile-label">IP Address</div>
                            <div class="profile-value"><?= e($sessionData['user_ip']) ?></div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <div class="profile-section-title">Device Properties <span style="font-size:0.6rem; opacity:0.5;">▼</span></div>
                        <div style="font-size:0.75rem; color:var(--text-muted); line-height:1.4;">
                            <?= e($sessionData['user_agent']) ?>
                        </div>
                    </div>

                    <div class="profile-section">
                        <div class="profile-section-title">Private Notes <span style="font-size:0.6rem; opacity:0.5;">+ Add</span></div>
                        <textarea style="width:100%; height:60px; background:rgba(255,255,255,0.05); border:1px solid var(--glass-border); border-radius:4px; color:white; font-size:0.8rem; padding:8px;" placeholder="Add internal note..."></textarea>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script>
    // Simple auto-scroll for chat area
    window.onload = function() {
        const chatArea = document.querySelector('.chat-messages-area');
        if (chatArea) {
            chatArea.scrollTop = chatArea.scrollHeight;
        }
    }
</script>

</body>
</html>
