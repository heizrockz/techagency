<?php
/**
 * Admin — Inbox View (Chat History)
 */
?>

<div class="admin-card animate-on-scroll">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h2 style="margin:0;">Chat Session #<?= $sessionData['id'] ?></h2>
            <p style="margin:4px 0 0; color:rgba(255,255,255,0.5); font-size:0.9rem;">
                <?= e($sessionData['user_ip']) ?> | <?= date('M d, Y H:i', strtotime($sessionData['created_at'])) ?>
            </p>
        </div>
        <a href="?action=list" class="btn btn-secondary btn-sm">Back to List</a>
    </div>

    <div class="card-body">
        <!-- User Info Panel -->
        <div class="user-info-strip" style="display:flex; gap:24px; padding:16px; background:rgba(255,255,255,0.03); border-radius:12px; margin-bottom:24px; border:1px solid rgba(255,255,255,0.05);">
            <div>
                <strong style="display:block; font-size:0.75rem; color:var(--neon-cyan); text-transform:uppercase;">Email</strong>
                <span><?= e($sessionData['user_email'] ?: 'Not provided') ?></span>
            </div>
            <div>
                <strong style="display:block; font-size:0.75rem; color:var(--neon-cyan); text-transform:uppercase;">Phone</strong>
                <span><?= e($sessionData['user_phone'] ?: 'Not provided') ?></span>
            </div>
            <div>
                <strong style="display:block; font-size:0.75rem; color:var(--neon-cyan); text-transform:uppercase;">Status</strong>
                <span class="badge badge-<?= $sessionData['status'] === 'Open' ? 'success' : 'secondary' ?>"><?= $sessionData['status'] ?></span>
            </div>
        </div>

        <!-- Chat History Container -->
        <div class="chat-history-container" style="background:#0a0f18; border-radius:16px; padding:24px; height:500px; overflow-y:auto; display:flex; flex-direction:column; gap:16px; border:1px solid rgba(255,255,255,0.1);">
            <?php if (empty($messages)): ?>
                <div style="text-align:center; padding:40px; color:rgba(255,255,255,0.3);">No messages found in this session.</div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <?php $isBot = ($msg['sender'] === 'bot'); ?>
                    <div class="chat-message-bubble <?= $isBot ? 'bot' : 'user' ?>" style="max-width:80%; align-self:<?= $isBot ? 'flex-start' : 'flex-end' ?>;">
                        <div class="bubble-content" style="
                            padding: 12px 16px; 
                            border-radius: 18px; 
                            font-size: 0.95rem; 
                            line-height: 1.5;
                            <?= $isBot 
                                ? 'background:rgba(255,255,255,0.08); color:#fff; border-bottom-left-radius:4px; border:1px solid rgba(255,255,255,0.1);' 
                                : 'background:linear-gradient(135deg, var(--neon-violet), var(--neon-cyan)); color:#fff; border-bottom-right-radius:4px;' 
                            ?>">
                            <?= nl2br(e($msg['message'])) ?>
                        </div>
                        <div class="bubble-time" style="font-size:0.7rem; color:rgba(255,255,255,0.4); margin-top:4px; text-align:<?= $isBot ? 'left' : 'right' ?>;">
                            <?= date('H:i', strtotime($msg['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .chat-history-container::-webkit-scrollbar { width: 6px; }
    .chat-history-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .chat-message-bubble { transition: transform 0.2s ease; }
    .chat-message-bubble:hover { transform: scale(1.01); }
</style>
