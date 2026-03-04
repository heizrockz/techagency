<?php
$pageTitle = 'Notifications';
$currentPage = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= e($pageTitle) ?> — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body class="bg-[#0b0e14]">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">System Feed</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">All Notifications</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Neural Feed</span>
                </h1>
            </div>
            <div class="flex items-center gap-4">
                <form method="POST" action="<?= baseUrl('admin/notifications') ?>">
                    <input type="hidden" name="action" value="mark_all_read">
                    <button type="submit" class="px-3 sm:px-5 py-2.5 bg-neon-cyan/10 hover:bg-neon-cyan/20 text-neon-cyan text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-neon-cyan/20 flex items-center gap-2">
                        <i class="ph ph-checks"></i> <span class="hidden sm:inline">Mark All as Read</span>
                    </button>
                </form>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 crm-main-scroll bg-[#0b0e14]">
            <div class="max-w-4xl mx-auto space-y-4">
                <?php if ($flash = getFlash()): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl flex items-center gap-3 mb-6">
                        <i class="ph ph-check-circle text-xl"></i>
                        <?= e($flash) ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($allNotifications)): ?>
                    <div class="text-center py-20 bg-[#1a2333]/40 rounded-2xl border border-white/5 shadow-2xl">
                        <div class="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center mx-auto mb-4 text-slate-500">
                            <i class="ph ph-bell-slash text-2xl"></i>
                        </div>
                        <h2 class="text-lg font-medium text-white mb-2">No notifications found</h2>
                        <p class="text-slate-400 text-sm">You're all caught up! When system events occur, they will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="bg-[#1a2333]/40 border border-white/5 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-md">
                        <div class="divide-y divide-white/5">
                            <?php foreach ($allNotifications as $n): ?>
                                <div class="p-5 flex gap-4 transition-colors hover:bg-white/[0.02] <?= $n['is_read'] ? 'opacity-70' : 'bg-white/[0.01]' ?>">
                                    <div class="mt-1 flex-shrink-0">
                                        <?php if($n['type'] === 'booking'): ?>
                                            <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-400 flex items-center justify-center"><i class="ph-fill ph-calendar-check text-xl"></i></div>
                                        <?php elseif($n['type'] === 'chat'): ?>
                                            <div class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center"><i class="ph-fill ph-chat-circle-dots text-xl"></i></div>
                                        <?php elseif($n['type'] === 'visit'): ?>
                                            <div class="w-10 h-10 rounded-full bg-purple-500/10 text-purple-400 flex items-center justify-center"><i class="ph-fill ph-users-three text-xl"></i></div>
                                        <?php elseif($n['type'] === 'login'): ?>
                                            <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-400 flex items-center justify-center"><i class="ph-fill ph-shield-check text-xl"></i></div>
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-full bg-slate-500/10 text-slate-400 flex items-center justify-center"><i class="ph-fill ph-info text-xl"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start mb-1">
                                            <a href="<?= htmlspecialchars(baseUrl($n['link'] ?? '#')) ?>" class="text-base font-semibold text-white hover:text-primary transition-colors flex items-center gap-2">
                                                <?= htmlspecialchars($n['title']) ?>
                                                <?php if(!$n['is_read']): ?>
                                                    <span class="text-[10px] font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-0.5 rounded border border-primary/20">New</span>
                                                <?php endif; ?>
                                            </a>
                                            <span class="text-xs text-slate-500 font-medium whitespace-nowrap ml-4 uppercase tracking-widest"><i class="ph ph-clock mr-1"></i> <?= date('M d, g:i A', strtotime($n['created_at'])) ?></span>
                                        </div>
                                        <p class="text-sm text-slate-400 leading-relaxed"><?= htmlspecialchars($n['content'] ?? $n['message'] ?? '') ?></p>
                                    </div>
                                    <div class="pl-4 flex flex-col justify-center gap-2">
                                        <?php if(!$n['is_read']): ?>
                                            <form method="POST" action="<?= baseUrl('admin/notifications') ?>" class="inline">
                                                <input type="hidden" name="action" value="mark_read">
                                                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                                <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-colors" title="Mark as read">
                                                    <i class="ph ph-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?= baseUrl('admin/notifications') ?>" class="inline" onsubmit="return confirm('Delete this notification?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                            <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center text-red-400 hover:text-red-300 hover:bg-red-400/10 transition-colors" title="Delete">
                                                <i class="ph ph-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
