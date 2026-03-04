<?php
$currentPage = 'profile';
$emojiList = ['👤','😎','🧑‍💻','👩‍💼','🦁','🐺','🦅','🔥','⚡','💎','🎯','🚀','🧠','🎨','🌟','👑','🤖','🦊','🐱','🎵'];
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body class="bg-[#0b0e14]">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Subject Authorization</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">My Profile</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Neural Signature</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 bg-[#0b0e14]">
            <div class="max-w-4xl mx-auto pb-20 space-y-10">
                <?php if ($saved): ?>
                    <div class="p-4 bg-neon-emerald/10 border border-neon-emerald/20 rounded-2xl text-neon-emerald text-[10px] font-black uppercase tracking-widest flex items-center gap-3 shadow-lg">
                        <i class="ph-bold ph-check-circle text-lg"></i> Signature Committed Successfully
                    </div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="p-4 bg-neon-rose/10 border border-neon-rose/20 text-neon-rose text-[10px] font-black uppercase tracking-widest flex items-center gap-3 shadow-lg">
                        <i class="ph-bold ph-warning-circle text-lg"></i> Security Alert: <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= baseUrl('admin/profile') ?>" class="space-y-6">

                    <!-- Profile Identity Card -->
                    <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01] relative group">
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-neon-cyan/5 rounded-full blur-[100px] transition-all duration-700 group-hover:bg-neon-cyan/10"></div>
                        
                        <div class="relative z-10 flex flex-col lg:flex-row gap-10 items-center lg:items-start">
                            <!-- Avatar Section -->
                            <div class="shrink-0 flex flex-col items-center gap-6">
                                <div class="w-28 h-28 lg:w-32 lg:h-32 rounded-3xl bg-black/40 border border-white/10 flex items-center justify-center text-6xl shadow-2xl relative group/avatar overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-br from-neon-cyan/10 to-transparent opacity-50"></div>
                                    <span id="current-emoji-display" class="relative z-10 filter drop-shadow-[0_0_15px_rgba(6,182,212,0.4)]"><?= e($admin['avatar_emoji'] ?? '👤') ?></span>
                                </div>
                                <div class="w-full">
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] mb-4 text-center">Visual Signifier</p>
                                    <div class="grid grid-cols-5 gap-2 p-3 bg-black/40 rounded-2xl border border-white/5">
                                        <?php foreach ($emojiList as $em): ?>
                                            <label class="cursor-pointer group/item">
                                                <input type="radio" name="avatar_emoji" value="<?= $em ?>" class="hidden peer" <?= ($admin['avatar_emoji'] ?? '👤') === $em ? 'checked' : '' ?> onchange="document.getElementById('current-emoji-display').innerText = this.value">
                                                <span class="w-9 h-9 flex items-center justify-center text-lg rounded-xl transition-all duration-300 hover:bg-white/10 border-2 border-transparent peer-checked:border-neon-cyan peer-checked:bg-neon-cyan/10 peer-checked:shadow-[0_0_15px_rgba(6,182,212,0.2)]"><?= $em ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="flex-1 space-y-8 w-full">
                                <div class="text-center lg:text-left">
                                    <h2 class="text-2xl lg:text-3xl font-black text-white tracking-tight mb-2 uppercase"><?= e($admin['full_name'] ?: $admin['username']) ?></h2>
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-neon-cyan/10 border border-neon-cyan/20 text-neon-cyan text-[9px] font-black uppercase tracking-widest shadow-[0_0_10px_rgba(6,182,212,0.1)]">
                                        <i class="ph-bold ph-shield-check text-xs"></i> 
                                        <span><?= e(ucwords(str_replace('_', ' ', $admin['role']))) ?>_AUTHORIZATION</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Entity Name</label>
                                        <div class="relative group/input">
                                            <i class="ph-bold ph-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-700 group-focus-within/input:text-neon-cyan transition-colors"></i>
                                            <input type="text" name="full_name" value="<?= e($admin['full_name'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-4 text-white text-[11px] font-black uppercase tracking-widest focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-900 shadow-inner" placeholder="SUBJECT_IDENTITY">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest ml-1 opacity-50">Username (Non-Modifiable)</label>
                                        <div class="relative opacity-60">
                                            <i class="ph-bold ph-lock-key absolute left-5 top-1/2 -translate-y-1/2 text-slate-800"></i>
                                            <input type="text" value="<?= e($admin['username'] ?? '') ?>" class="w-full bg-black/20 border border-white/5 rounded-2xl pl-12 pr-6 py-4 text-slate-600 text-[11px] font-black uppercase tracking-widest cursor-not-allowed shadow-inner" disabled>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Recovery Up-link (Email)</label>
                                        <div class="relative group/input">
                                            <i class="ph-bold ph-envelope-simple absolute left-5 top-1/2 -translate-y-1/2 text-slate-700 group-focus-within/input:text-neon-cyan transition-colors"></i>
                                            <input type="email" name="recovery_email" value="<?= e($admin['recovery_email'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-4 text-white text-[10px] font-bold focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-900 shadow-inner" placeholder="RECOVERY@NEURAL.LINK">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Secure Frequency (Phone)</label>
                                        <div class="relative group/input">
                                            <i class="ph-bold ph-device-mobile-speaker absolute left-5 top-1/2 -translate-y-1/2 text-slate-700 group-focus-within/input:text-neon-cyan transition-colors"></i>
                                            <input type="text" name="recovery_phone" value="<?= e($admin['recovery_phone'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-2xl pl-12 pr-6 py-4 text-white text-[10px] font-bold focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-900 shadow-inner" placeholder="+971 50 XXX XXXX">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01]">
                        <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                            <div class="w-10 h-10 rounded-xl bg-neon-amber/10 text-neon-amber flex items-center justify-center text-xl shadow-lg border border-neon-amber/20">
                                <i class="ph-bold ph-lock-key"></i>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Security Encryption Protocol</h3>
                                <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Leave empty to maintain existing cryptographic signature</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">New Access Key</label>
                                <input type="password" name="new_password" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-[10px] focus:outline-none focus:border-neon-amber transition-all placeholder-slate-900 shadow-inner" placeholder="••••••••" autocomplete="new-password">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Confirm Access Key</label>
                                <input type="password" name="confirm_password" class="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-white text-[10px] focus:outline-none focus:border-neon-amber transition-all placeholder-slate-900 shadow-inner" placeholder="••••••••" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex flex-col sm:flex-row justify-end gap-6 pt-4">
                        <button type="reset" class="px-8 py-4 text-[11px] font-black uppercase tracking-widest text-slate-600 hover:text-white transition-colors rounded-2xl border border-white/5 hover:border-white/10">Abort Sequence</button>
                        <button type="submit" class="px-10 py-4 bg-neon-cyan hover:bg-cyan-400 text-black text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-[0_0_20px_rgba(6,182,212,0.2)] active:scale-95 flex items-center gap-3">
                            <i class="ph-bold ph-floppy-disk text-lg"></i> Commit Signature
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<style>
    input[type="radio"]:checked + .emoji-pick {
        border-color: #6366f1 !important;
        background: rgba(99, 102, 241, 0.15);
        box-shadow: 0 0 12px rgba(99, 102, 241, 0.25);
    }
</style>
</body>
</html>
