<?php
$title = 'Chatbot Inbox Dashboard';
$currentPage = 'inbox';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title><?= e($title) ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Communication Matrix</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Neural Inbox</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-[10px] tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block">Transmission Stream</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <div class="hidden lg:flex items-center gap-2">
                    <button class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white hover:bg-white/5 transition-all border border-transparent hover:border-white/10">Documentation</button>
                    <button class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white hover:bg-white/5 transition-all border border-transparent hover:border-white/10">Support</button>
                </div>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <!-- Dashboard Layout -->
        <div class="inbox-dashboard <?= $sessionData ? 'session-selected' : '' ?>">
            
            <!-- Left Sidebar: Sessions -->
            <div class="inbox-sidebar">
                <div class="inbox-sidebar-header p-6 border-b border-white/5 bg-white/[0.01]">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Active Channels</span>
                        <span class="text-[8px] text-slate-600 font-black uppercase tracking-widest bg-white/5 px-2 py-0.5 rounded border border-white/5">Real-time Feed</span>
                    </div>
                </div>
                <div class="session-list">
                    <?php if (empty($sessions)): ?>
                        <div class="py-24 text-center">
                            <i class="ph-duotone ph-chats-circle text-5xl text-slate-800 mb-4 block"></i>
                            <p class="text-slate-700 text-[10px] font-black uppercase tracking-[0.2em]">No active streams.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($sessions as $s): ?>
                            <?php 
                                $isActive = ($selectedId == $s['id']); 
                                $uuid = (string)($s['session_uuid'] ?? 'VISI');
                                $snippet = $s['user_email'] ?: ($s['user_phone'] ?: 'Visitor ' . substr($uuid, 0, 4));
                            ?>
                            <a href="<?= baseUrl('admin/inbox?id=' . $s['id']) ?>" class="session-item group/session border-b border-white/[0.02] last:border-0 hover:bg-white/[0.03] transition-all relative <?= $isActive ? 'active bg-white/[0.05]' : '' ?> px-4 lg:px-6 py-4 lg:py-5">
                                <?php if($isActive): ?>
                                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-neon-cyan rounded-r-full shadow-[0_0_15px_rgba(6,182,212,0.8)]"></div>
                                <?php endif; ?>
                                <div class="session-avatar w-12 h-12 rounded-2xl bg-black/40 border border-white/10 flex items-center justify-center text-xl shadow-lg group-hover/session:border-neon-cyan/30 transition-all font-black text-neon-cyan">
                                    <?php 
                                        $initial = (string)($s['user_email'] ?: 'V');
                                        echo strtoupper(substr($initial, 0, 1));
                                    ?>
                                </div>
                                <div class="session-info flex-1 min-w-0 flex flex-col justify-center gap-1">
                                    <div class="session-name flex items-center gap-2">
                                        <span class="text-[11px] font-black text-white uppercase tracking-tight group-hover/session:text-neon-cyan transition-colors truncate min-w-0"><?= e($snippet) ?></span>
                                        <?php if(($s['is_read'] ?? 1) == 0): ?>
                                            <span class="w-2 h-2 rounded-full bg-neon-emerald animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="session-snippet text-[8px] text-slate-600 font-bold uppercase tracking-widest flex items-center gap-2">
                                        <span><?= $s['msg_count'] ?> TR_LOGS</span>
                                        <span class="opacity-30">•</span>
                                        <span class="font-mono"><?= date('H:i', strtotime($s['updated_at'])) ?></span>
                                    </div>
                                </div>
                                <?php if($s['status'] === 'Open'): ?>
                                    <div class="flex items-center" title="Status: Open">
                                        <div class="w-1.5 h-1.5 rounded-full bg-neon-cyan/40"></div>
                                    </div>
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
                    <div class="chat-header p-6 border-b border-white/5 bg-white/[0.01] flex justify-between items-center backdrop-blur-md">
                        <div class="flex items-center gap-6">
                            <a href="<?= baseUrl('admin/inbox') ?>" class="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-500 hover:text-white transition-all hover:bg-white/10 lg:hidden">
                                <i class="ph-bold ph-arrow-left"></i>
                            </a>
                            <div class="flex flex-col">
                                <div class="text-[12px] font-black text-white uppercase tracking-tight"><?= e($sessionData['user_email'] ?: 'Anonymous Visitor') ?></div>
                                <div class="text-[8px] text-slate-600 font-black uppercase tracking-[0.2em] mt-1">
                                    Vector ID: <span class="font-mono text-slate-400"><?= e($sessionData['user_ip']) ?></span> • Established: <?= date('M d, H:i', strtotime($sessionData['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="px-3 py-1 rounded-lg border border-neon-emerald/20 bg-neon-emerald/10 text-neon-emerald text-[8px] font-black uppercase tracking-widest shadow-[0_0_10px_rgba(16,185,129,0.1)]">
                                <?= $sessionData['status'] ?> SIGNAL
                            </div>
                            <button class="w-8 h-8 rounded-lg hover:bg-white/5 text-slate-500 hover:text-white transition-all">
                                <i class="ph-bold ph-dots-three-vertical"></i>
                            </button>
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

                    <div class="chat-footer p-8 border-t border-white/5 bg-white/[0.01]">
                        <div class="chat-input-wrapper bg-black/40 border border-white/10 rounded-2xl p-4 flex gap-4 items-end shadow-inner focus-within:border-neon-cyan/30 transition-all">
                            <textarea placeholder="Synthesize protocol response... (Shift + Enter for logic skip)" rows="1" class="flex-1 bg-transparent border-none text-white text-[11px] font-bold focus:outline-none placeholder-slate-800 resize-none min-h-[44px] leading-relaxed"></textarea>
                            <button class="px-6 py-3 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 flex items-center gap-2">
                                <i class="ph-bold ph-paper-plane-right"></i> Commit
                            </button>
                        </div>
                        <div class="flex gap-6 mt-4">
                            <div class="flex items-center gap-2 text-slate-800 group cursor-pointer hover:text-slate-600 transition-colors">
                                <i class="ph ph-globe text-[11px] text-neon-cyan/40"></i>
                                <span class="text-[8px] font-black uppercase tracking-widest">Global Transcribe</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-800 group cursor-pointer hover:text-slate-600 transition-colors">
                                <i class="ph ph-command text-[11px] text-neon-purple/40"></i>
                                <span class="text-[8px] font-black uppercase tracking-widest">Macro Bindings</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Visitor Profile -->
            <div class="inbox-profile">
                <?php if (!$sessionData): ?>
                    <div style="text-align:center; padding:40px; color:var(--text-muted); font-size:0.85rem;">No user selected.</div>
                <?php else: ?>
                    <div class="profile-section p-6 border-b border-white/5">
                        <div class="text-[9px] font-black text-neon-cyan uppercase tracking-[0.3em] mb-6">Subject Identity</div>
                        <div class="flex items-center gap-4 bg-white/5 p-4 rounded-2xl border border-white/5">
                            <div class="w-14 h-14 rounded-2xl bg-black/40 border border-white/10 flex items-center justify-center text-2xl shadow-lg font-black text-neon-cyan">
                                <?= strtoupper(substr($sessionData['user_email'] ?: 'V', 0, 1)) ?>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <div class="text-[11px] font-black text-white uppercase tracking-tight truncate"><?= e($sessionData['user_email'] ?: 'Unidentified Guest') ?></div>
                                <div class="text-[8px] text-slate-600 font-bold uppercase tracking-widest mt-1 truncate"><?= e($sessionData['user_phone'] ?: 'No signal sync') ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section p-6 border-b border-white/5">
                        <div class="text-[9px] font-black text-neon-purple uppercase tracking-[0.3em] mb-4">Intelligence Synthesis</div>
                        <div class="bg-black/30 border border-white/5 rounded-2xl p-4">
                            <p class="text-[10px] text-slate-400 leading-relaxed italic">
                                Entity initiated contact via automated channel. Primary objective: <span class="text-neon-cyan"><?= e($sessionData['user_email'] ? 'Direct Inquiry' : 'Passive Observation') ?></span>. High probability of lead conversion based on interaction frequency.
                            </p>
                        </div>
                    </div>

                    <div class="profile-section p-6 border-b border-white/5">
                        <div class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mb-6">Identity Parameters</div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center bg-white/[0.02] p-3 rounded-xl border border-white/5">
                                <span class="text-[8px] text-slate-600 font-black uppercase tracking-widest">Protocol Email</span>
                                <span class="text-[9px] text-white font-mono"><?= e($sessionData['user_email'] ?: 'VOID') ?></span>
                            </div>
                            <div class="flex justify-between items-center bg-white/[0.02] p-3 rounded-xl border border-white/5">
                                <span class="text-[8px] text-slate-600 font-black uppercase tracking-widest">Signal Frequency</span>
                                <span class="text-[9px] text-white font-mono"><?= e($sessionData['user_phone'] ?: 'ASYNC') ?></span>
                            </div>
                            <div class="flex justify-between items-center bg-white/[0.02] p-3 rounded-xl border border-white/5">
                                <span class="text-[8px] text-slate-600 font-black uppercase tracking-widest">Vector Origin</span>
                                <span class="text-[9px] text-neon-cyan font-mono"><?= e($sessionData['user_ip']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section p-6 border-b border-white/5">
                        <div class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mb-4">Transmission Specs</div>
                        <div class="bg-black/20 rounded-xl p-4 border border-white/5">
                            <p class="text-[7px] text-slate-700 font-bold uppercase tracking-widest leading-loose">
                                <?= e($sessionData['user_agent']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="profile-section p-6">
                        <div class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4">Internal Comm Logs</div>
                        <textarea class="w-full h-24 bg-black/40 border border-white/10 rounded-2xl p-4 text-[10px] text-slate-400 focus:outline-none focus:border-neon-purple transition-all placeholder-slate-900 shadow-inner resize-none" placeholder="Append internal tactical observation..."></textarea>
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
