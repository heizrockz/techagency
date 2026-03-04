<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Site Settings — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'settings'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">System Configuration</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Nexus Control</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Operational Parameters</span>
                </h1>
            </div>
            <div class="flex items-center gap-6">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>
        
        <main class="flex-1 overflow-y-auto p-8 bg-[#0b0e14]">
            <?php if ($saved): ?>
                <div class="mb-8 p-4 bg-neon-emerald/10 border border-neon-emerald/20 rounded-2xl text-neon-emerald text-[10px] font-black uppercase tracking-widest flex items-center gap-3">
                    <i class="ph-bold ph-check-circle text-lg"></i> Parameters Committed Successfully
                </div>
            <?php endif; ?>

        <form method="POST" action="<?= baseUrl('admin/settings') ?>" enctype="multipart/form-data">
            <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01] mb-10">
                <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                    <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                        <i class="ph-duotone ph-toggle-left"></i>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Interface Protocol Toggles</h2>
                        <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Configure visibility for frontend neural nodes</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php 
                    $toggles = [
                        ['id' => 'show_clients_section', 'label' => 'Client Showcase Registry'],
                        ['id' => 'show_products_section', 'label' => 'Intellectual Property Ledger'],
                        ['id' => 'show_stats_section', 'label' => 'Metric Visualization Nodes'],
                        ['id' => 'show_marketing_section', 'label' => 'Neural Marketing Stream'],
                        ['id' => 'show_tagline_section', 'label' => 'Core Value Proposition'],
                        ['id' => 'show_process_section', 'label' => 'Operational Workflow'],
                        ['id' => 'show_team', 'label' => 'Subject Collective'],
                        ['id' => 'show_testimonials', 'label' => 'Feedback Synthesizer'],
                        ['id' => 'show_blog_section', 'label' => 'Thought Leadership Logs'],
                        ['id' => 'show_booking_section', 'label' => 'Engagement Gateway'],
                        ['id' => 'show_contact_section', 'label' => 'Transmission Terminal'],
                    ];
                    foreach($toggles as $t): ?>
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-black/20 border border-white/5 group hover:border-neon-cyan/30 transition-all cursor-pointer relative overflow-hidden">
                            <div class="relative flex items-center justify-center z-10">
                                <input type="checkbox" name="settings[<?= $t['id'] ?>]" id="<?= $t['id'] ?>" <?= ($settings[$t['id']]['setting_value'] ?? '1') === '1' ? 'checked' : '' ?>
                                    class="peer appearance-none w-6 h-6 rounded-lg border border-white/20 checked:border-neon-cyan checked:bg-neon-cyan/20 transition-all cursor-pointer">
                                <i class="ph-bold ph-check absolute text-xs text-neon-cyan opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <label for="<?= $t['id'] ?>" class="text-[10px] font-black text-slate-500 group-hover:text-slate-300 transition-colors uppercase tracking-[0.15em] cursor-pointer z-10"><?= $t['label'] ?></label>
                            <div class="absolute inset-0 bg-gradient-to-r from-neon-cyan/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-neon-emerald/5 border border-neon-emerald/20 group hover:border-neon-emerald/40 transition-all cursor-pointer relative overflow-hidden lg:col-span-1">
                        <div class="relative flex items-center justify-center z-10">
                            <input type="checkbox" name="settings[announcement_active]" id="announcement_active" <?= ($settings['announcement_active']['setting_value'] ?? '0') === '1' ? 'checked' : '' ?>
                                class="peer appearance-none w-6 h-6 rounded-lg border border-neon-emerald/20 checked:border-neon-emerald checked:bg-neon-emerald/20 transition-all cursor-pointer">
                            <i class="ph-bold ph-check absolute text-xs text-neon-emerald opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <label for="announcement_active" class="text-[10px] font-black text-neon-emerald/60 group-hover:text-neon-emerald transition-colors uppercase tracking-[0.15em] cursor-pointer z-10">Neural Broadcast (Active)</label>
                        <div class="absolute inset-0 bg-gradient-to-r from-neon-emerald/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                </div>
            </div>

            <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01] mb-10">
                <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                    <div class="w-10 h-10 rounded-xl bg-neon-purple/10 text-neon-purple flex items-center justify-center text-xl shadow-lg border border-neon-purple/20">
                        <i class="ph-duotone ph-palette"></i>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Visual Identity Protocol</h2>
                        <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Refine core brand signifiers and system labels</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3 md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">System Internal Identity</label>
                        <input type="text" name="settings[site_name]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-purple transition-all placeholder-slate-800 shadow-inner" value="<?= e($settings['site_name']['setting_value'] ?? 'Mico Sage') ?>">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Brand Designation (EN)</label>
                        <input type="text" name="settings[company_name_en]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-purple transition-all placeholder-slate-800 shadow-inner" value="<?= e($settings['company_name_en']['setting_value'] ?? '') ?>" placeholder="CORE_BRAND_NAME">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Brand Designation (AR)</label>
                        <input type="text" name="settings[company_name_ar]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[14px] font-black text-white focus:outline-none focus:border-neon-purple transition-all placeholder-slate-800 shadow-inner text-right" value="<?= e($settings['company_name_ar']['setting_value'] ?? '') ?>" placeholder="الهوية_الأساسية" dir="rtl">
                    </div>
                    <div class="md:col-span-2 flex items-center gap-8 bg-black/20 p-6 rounded-3xl border border-white/5">
                        <div class="flex-1 space-y-3">
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Neural Vector (Logo Upload)</label>
                            <div class="relative group">
                                <input type="file" name="site_logo" id="site_logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept="image/*">
                                <div class="w-full bg-black/40 border border-dashed border-white/10 rounded-2xl py-8 px-6 text-center group-hover:border-neon-purple/50 transition-all">
                                    <i class="ph-duotone ph-upload-simple text-3xl text-slate-700 mb-2 block group-hover:text-neon-purple transition-colors"></i>
                                    <span class="text-[9px] text-slate-600 font-bold uppercase tracking-widest">Deploy New Visual Signifier</span>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($settings['site_logo']['setting_value'])): ?>
                            <div class="w-32 h-32 bg-white rounded-2xl flex items-center justify-center p-4 shadow-xl border border-white/10 shrink-0">
                                <img src="<?= baseUrl($settings['site_logo']['setting_value']) ?>" class="max-w-full max-h-full object-contain">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="admin-card" style="margin-bottom: 20px;">
                <h3>Company Stats</h3>
                <div class="admin-grid-3" style="margin-top: 15px;">
                    <div class="form-group">
                        <label>Projects Number</label>
                        <input type="text" name="settings[stat_projects_num]" class="form-input" value="<?= e($settings['stat_projects_num']['setting_value'] ?? '150+') ?>">
                        <input type="text" name="settings[stat_projects_label_en]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_projects_label_en']['setting_value'] ?? '') ?>" placeholder="Label (EN)">
                        <input type="text" name="settings[stat_projects_label_ar]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_projects_label_ar']['setting_value'] ?? '') ?>" placeholder="Label (AR)" dir="rtl">
                    </div>
                    <div class="form-group">
                        <label>Clients Number</label>
                        <input type="text" name="settings[stat_clients_num]" class="form-input" value="<?= e($settings['stat_clients_num']['setting_value'] ?? '50+') ?>">
                        <input type="text" name="settings[stat_clients_label_en]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_clients_label_en']['setting_value'] ?? '') ?>" placeholder="Label (EN)">
                        <input type="text" name="settings[stat_clients_label_ar]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_clients_label_ar']['setting_value'] ?? '') ?>" placeholder="Label (AR)" dir="rtl">
                    </div>
                    <div class="form-group">
                        <label>Years Number</label>
                        <input type="text" name="settings[stat_years_num]" class="form-input" value="<?= e($settings['stat_years_num']['setting_value'] ?? '8+') ?>">
                        <input type="text" name="settings[stat_years_label_en]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_years_label_en']['setting_value'] ?? '') ?>" placeholder="Label (EN)">
                        <input type="text" name="settings[stat_years_label_ar]" class="form-input" style="margin-top:5px;" value="<?= e($settings['stat_years_label_ar']['setting_value'] ?? '') ?>" placeholder="Label (AR)" dir="rtl">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-10">
                <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01]">
                    <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                        <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                            <i class="ph-duotone ph-phone"></i>
                        </div>
                        <div class="flex flex-col">
                            <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Transmission Terminal</h2>
                            <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Signal endpoints for subject engagement</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Voice Up-link</label>
                            <input type="text" name="settings[contact_phone]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-800 shadow-inner" value="<?= e($settings['contact_phone']['setting_value'] ?? '') ?>" placeholder="+1 234 567 8900">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Digital Mailbox</label>
                            <input type="email" name="settings[contact_email]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-800 shadow-inner" value="<?= e($settings['contact_email']['setting_value'] ?? '') ?>" placeholder="HELLO@NEURAL.LINK">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Secure Messaging (WhatsApp)</label>
                            <input type="text" name="settings[whatsapp_number]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-800 shadow-inner" value="<?= e($settings['whatsapp_number']['setting_value'] ?? '') ?>" placeholder="+971500000000">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Geographic Vector</label>
                            <input type="text" name="settings[contact_location]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-800 shadow-inner" value="<?= e($settings['contact_location']['setting_value'] ?? '') ?>" placeholder="DUBAI_OPERATIONS_CENTER">
                        </div>
                    </div>
                </div>

                <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01]">
                    <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                        <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                            <i class="ph-duotone ph-share-network"></i>
                        </div>
                        <div class="flex flex-col">
                            <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Social Nexus Grid</h2>
                            <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">External matrix distribution channels</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <?php 
                        $socials = [
                            ['id' => 'social_facebook', 'label' => 'Meta Matrix (Facebook)', 'placeholder' => 'HTTPS://FACEBOOK.COM/IDENTITY'],
                            ['id' => 'social_twitter', 'label' => 'Signal Flow (X/Twitter)', 'placeholder' => 'HTTPS://TWITTER.COM/SIGNAL'],
                            ['id' => 'social_instagram', 'label' => 'Visual Grid (Instagram)', 'placeholder' => 'HTTPS://INSTAGRAM.COM/GRID'],
                            ['id' => 'social_linkedin', 'label' => 'Professional Node (LinkedIn)', 'placeholder' => 'HTTPS://LINKEDIN.COM/IN/PROX'],
                        ];
                        foreach($socials as $s): ?>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1"><?= $s['label'] ?></label>
                            <input type="url" name="settings[<?= $s['id'] ?>]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[10px] font-bold text-slate-400 focus:outline-none focus:border-neon-cyan transition-all placeholder-slate-800 shadow-inner" value="<?= e($settings[$s['id']]['setting_value'] ?? '') ?>" placeholder="<?= $s['placeholder'] ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Announcement Bar Group -->
            <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium p-10 bg-white/[0.01] mb-10">
                <div class="flex items-center gap-4 mb-8 border-b border-white/5 pb-6">
                    <div class="w-10 h-10 rounded-xl bg-neon-emerald/10 text-neon-emerald flex items-center justify-center text-xl shadow-lg border border-neon-emerald/20">
                        <i class="ph-duotone ph-megaphone"></i>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-[11px] font-black uppercase tracking-[0.3em] text-white">Neural Broadcast Protocol</h2>
                        <p class="text-[8px] text-slate-600 font-bold uppercase tracking-tight mt-1">Dynamic island transmission parameters</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Transmission Payload (EN)</label>
                        <textarea name="settings[announcement_message]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-emerald transition-all placeholder-slate-800 shadow-inner h-24 resize-none" placeholder="DEPLOY_MESSAGE_EN"><?= e($settings['announcement_message']['setting_value'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Transmission Payload (AR)</label>
                        <textarea name="settings[announcement_message_ar]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[14px] font-black text-white focus:outline-none focus:border-neon-emerald transition-all placeholder-slate-800 shadow-inner h-24 resize-none text-right" placeholder="رسالة_البث" dir="rtl"><?= e($settings['announcement_message_ar']['setting_value'] ?? '') ?></textarea>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Establishment Duration (SEC)</label>
                        <input type="number" name="settings[announcement_duration]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-emerald transition-all shadow-inner" value="<?= e($settings['announcement_duration']['setting_value'] ?? '5') ?>">
                        <p class="text-[7px] text-slate-700 font-bold uppercase tracking-widest ml-1 mt-1">ZERO_VAL = PERMANENT_SYNC</p>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Decommission Vector (EXPIRY)</label>
                        <input type="datetime-local" name="settings[announcement_end_date]" class="w-full bg-black/40 border border-white/10 rounded-2xl py-4 px-6 text-[11px] font-black text-white focus:outline-none focus:border-neon-emerald transition-all shadow-inner" value="<?= e($settings['announcement_end_date']['setting_value'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="flex justify-center mb-20">
                <button type="submit" class="px-12 py-5 bg-neon-cyan hover:bg-cyan-400 text-black text-[12px] font-black uppercase tracking-[0.3em] rounded-2xl transition-all shadow-[0_0_30px_rgba(6,182,212,0.3)] active:scale-95 flex items-center gap-3 group">
                    <i class="ph-bold ph-shield-check text-xl group-hover:scale-110 transition-transform"></i> Commit System Parameters
                </button>
            </div>
        </form>

        <!-- Announcement History Table -->
            <div class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium mb-20">
                <div class="p-8 border-b border-white/5 bg-white/[0.01] flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan flex items-center justify-center text-xl shadow-lg border border-neon-cyan/20">
                            <i class="ph-duotone ph-list-numbers"></i>
                        </div>
                        <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-white m-0">Broadcast Temporal Logs</h3>
                    </div>
                    <span class="px-4 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[8px] font-black text-slate-500 uppercase tracking-widest"><?= $historyCount ?? 0 ?> TOTAL_SEQUENCES</span>
                </div>
                
                <div class="overflow-x-auto crm-main-scroll">
                    <table class="admin-table w-full text-left border-collapse min-w-[1000px]">
                        <thead>
                            <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                <th class="py-6 px-10">Temporal Marker</th>
                                <th class="py-6 px-6">Payload Alpha (EN)</th>
                                <th class="py-6 px-10 text-right">Payload Beta (AR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.02]">
                            <?php if(!empty($announcementHistory)): ?>
                                <?php foreach($announcementHistory as $hist): ?>
                                <tr class="hover:bg-white/[0.03] transition-all group/row">
                                    <td class="py-6 px-10">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-neon-cyan/40"></div>
                                            <span class="text-[10px] font-black font-mono text-slate-500"><?= date('M j, Y H:i', strtotime($hist['created_at'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6">
                                        <span class="text-[11px] font-bold text-white tracking-tight"><?= e($hist['message_en'] ?? '-') ?></span>
                                    </td>
                                    <td class="py-6 px-10 text-right" dir="rtl">
                                        <span class="text-[14px] font-bold text-slate-400"><?= e($hist['message_ar'] ?? '-') ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="py-20 text-center">
                                        <i class="ph-duotone ph-ghost text-5xl text-slate-800 mb-4 block"></i>
                                        <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest">NO_LOG_DATA_FOUND</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
