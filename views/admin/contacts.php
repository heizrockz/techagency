<?php
$contact = $contact ?? [];
$isNew = empty($contact);
$currentPage = 'contacts';
$action = $action ?? 'list';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts — <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body class="bg-[#0b0e14]" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="flex-1 flex flex-col min-w-0 bg-[#0b0e14]">
        <header class="h-auto lg:h-20 flex flex-col lg:flex-row items-center justify-between px-4 lg:px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100] py-3 lg:py-0 gap-2 lg:gap-0">
            <div class="flex items-center justify-between w-full lg:w-auto">
                <div class="flex flex-col">
                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Entity Registry</div>
                    <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                        <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]"><?= ($action === 'list') ? 'Contacts' : ($isNew ? 'New' : 'Edit') ?></span>
                        <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                        <span class="text-[10px] tracking-widest text-slate-400 uppercase font-black hidden sm:inline-block">Archive</span>
                    </h1>
                </div>
                <div class="lg:hidden">
                    <?php require __DIR__ . '/partials/_topbar.php'; ?>
                </div>
            </div>

            <div class="flex items-center justify-between lg:justify-end gap-6 w-full lg:w-auto">
                <?php if ($action === 'list'): ?>
                    <a href="<?= baseUrl('admin/contacts?action=new') ?>" class="flex-1 lg:flex-none px-6 py-2 bg-cyan-500 hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                        <i class="ph-bold ph-user-plus text-lg"></i> <span>Provision</span>
                    </a>
                <?php else: ?>
                    <a href="<?= baseUrl('admin/contacts') ?>" class="flex-1 lg:flex-none px-6 py-2 bg-white/5 hover:bg-white/10 text-slate-300 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all border border-white/5 flex items-center justify-center gap-2 active:scale-95 shadow-lg">
                        <i class="ph-bold ph-arrow-left text-lg"></i> <span>Return</span>
                    </a>
                <?php endif; ?>
                
                <div class="hidden lg:block">
                    <?php 
                    if ($action === 'list') {
                        $showTopbarSearch = true;
                        $topbarSearchId = 'contactSearch';
                        $topbarSearchPlaceholder = 'FILTER NEXUS IDENTITIES...';
                    }
                    require __DIR__ . '/partials/_topbar.php'; 
                    ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-[#0b0e14] w-full h-full crm-main-scroll">
            <?php if(isset($_GET['saved']) || ($flash = getFlash())): ?>
                <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2">
                    <i class="ph ph-check-circle text-lg"></i>
                    <?= htmlspecialchars($flash ?? 'Contact saved successfully!') ?>
                </div>
            <?php endif; ?>

        <?php if ($action === 'edit' || $action === 'new'): ?>
            <form method="POST" action="<?= baseUrl('admin/contacts') ?>" class="admin-stat-card !bg-glass-bg !p-4 lg:!p-8 border border-white/5 shadow-premium space-y-4 lg:space-y-6 max-w-4xl mx-auto rounded-3xl relative overflow-hidden backdrop-blur-3xl">
                <!-- Atmospheric Decor -->
                <div class="absolute -right-24 -top-24 w-64 h-64 bg-neon-cyan/5 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute -left-24 -bottom-24 w-64 h-64 bg-neon-purple/5 rounded-full blur-[100px] pointer-events-none"></div>

                <input type="hidden" name="id" value="<?= $contact['id'] ?? 0 ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Entity Designation *</label>
                        <div class="relative group">
                            <i class="ph-bold ph-identification-card absolute left-4 top-1/2 -translate-y-1/2 text-slate-700 text-[10px] group-focus-within:text-neon-cyan transition-colors"></i>
                            <input type="text" name="name" value="<?= e($contact['name'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-[11px] font-black text-white focus:outline-none focus:border-neon-cyan placeholder-slate-900 transition-all font-mono shadow-inner" required placeholder="NEXUS_ID_001">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Metabolic Class</label>
                        <div class="relative">
                            <select name="type" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-4 text-[9px] font-black uppercase tracking-widest text-white focus:outline-none focus:border-neon-cyan appearance-none cursor-pointer transition-all shadow-inner">
                                <option value="company" <?= ($contact['type'] ?? 'company') === 'company' ? 'selected' : '' ?>>🏢 Corporate Infrastructure</option>
                                <option value="individual" <?= ($contact['type'] ?? '') === 'individual' ? 'selected' : '' ?>>👤 Biological Unit</option>
                            </select>
                            <i class="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Vocal Frequency (Phone)</label>
                        <div class="relative group">
                            <i class="ph-bold ph-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-700 text-[10px] group-focus-within:text-neon-cyan transition-colors"></i>
                            <input type="text" name="phone" value="<?= e($contact['phone'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-[11px] font-black text-white focus:outline-none focus:border-neon-cyan placeholder-slate-900 transition-all font-mono" placeholder="+00 000 000">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Digital Uplink (Email)</label>
                        <div class="relative group">
                            <i class="ph-bold ph-envelope-simple absolute left-4 top-1/2 -translate-y-1/2 text-slate-700 text-[10px] group-focus-within:text-neon-cyan transition-colors"></i>
                            <input type="email" name="email" value="<?= e($contact['email'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-[11px] font-black text-white focus:outline-none focus:border-neon-cyan placeholder-slate-900 transition-all font-mono lowercase" placeholder="uplink@domain.io">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Tax Matrix Identification</label>
                        <div class="relative group">
                            <i class="ph-bold ph-hash absolute left-4 top-1/2 -translate-y-1/2 text-slate-700 text-[10px] group-focus-within:text-neon-amber transition-colors"></i>
                            <input type="text" name="vat_number" value="<?= e($contact['vat_number'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-[11px] font-black text-white focus:outline-none focus:border-neon-amber placeholder-slate-900 transition-all font-mono uppercase" placeholder="TRN_ARCH_001">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Virtual Construct (URL)</label>
                        <div class="relative group">
                            <i class="ph-bold ph-globe absolute left-4 top-1/2 -translate-y-1/2 text-slate-700 text-[10px] group-focus-within:text-neon-cyan transition-colors"></i>
                            <input type="url" name="website" value="<?= e($contact['website'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-[10px] font-black text-white focus:outline-none focus:border-neon-cyan placeholder-slate-900 transition-all font-mono" placeholder="https://nexus.io">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Geospatial Coordinates</label>
                        <div class="relative group">
                            <i class="ph-bold ph-map-pin absolute left-4 top-1/2 -translate-y-1/2 text-slate-700 text-[10px] group-focus-within:text-neon-purple transition-colors"></i>
                            <input type="text" name="location" value="<?= e($contact['location'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-[11px] font-black text-white focus:outline-none focus:border-neon-purple placeholder-slate-900 transition-all" placeholder="Neo-Dune Sector">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Jurisdictional Domain</label>
                        <div class="relative group">
                            <i class="ph-bold ph-flag absolute left-4 top-1/2 -translate-y-1/2 text-slate-700 text-[10px] group-focus-within:text-neon-purple transition-colors"></i>
                            <input type="text" name="country" value="<?= e($contact['country'] ?? '') ?>" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 pl-10 pr-4 text-[11px] font-black text-white focus:outline-none focus:border-neon-purple placeholder-slate-900 transition-all uppercase tracking-widest" placeholder="Territories">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Personnel Intelligence (POC)</label>
                        <div class="relative group">
                            <i class="ph-bold ph-user-focus absolute left-4 top-4 text-slate-700 text-[10px] group-focus-within:text-neon-emerald transition-colors"></i>
                            <textarea name="poc_details" class="w-full bg-black/40 border border-white/10 rounded-2xl py-3 pl-10 pr-4 text-[10px] font-bold text-white focus:outline-none focus:border-neon-emerald transition-all min-h-[100px] crm-main-scroll placeholder-slate-900 leading-relaxed shadow-inner" placeholder="Personnel Identifiers..."><?= e($contact['poc_details'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.3em] ml-2">Acquisition Pipeline</label>
                        <div class="relative">
                            <select name="source" class="w-full bg-black/40 border border-white/10 rounded-xl py-2 px-4 text-[9px] font-black uppercase tracking-widest text-white focus:outline-none focus:border-neon-emerald appearance-none cursor-pointer transition-all shadow-inner">
                                <?php $src = $contact['source'] ?? ''; ?>
                                <option value="" <?= empty($src) ? 'selected' : '' ?>>— Unspecified Vector —</option>
                                <option value="direct_enquiry" <?= $src === 'direct_enquiry' ? 'selected' : '' ?>>Direct Transmission</option>
                                <option value="website" <?= $src === 'website' ? 'selected' : '' ?>>Digital Portal</option>
                                <option value="referral" <?= $src === 'referral' ? 'selected' : '' ?>>Neural Referral</option>
                                <option value="social_media" <?= $src === 'social_media' ? 'selected' : '' ?>>Social Matrix</option>
                                <option value="cold_call" <?= $src === 'cold_call' ? 'selected' : '' ?>>Outbound Probe</option>
                                <option value="exhibition" <?= $src === 'exhibition' ? 'selected' : '' ?>>Physical Summit</option>
                                <option value="linkedin" <?= $src === 'linkedin' ? 'selected' : '' ?>>Professional Network</option>
                                <option value="other" <?= $src === 'other' ? 'selected' : '' ?>>Classified Source</option>
                            </select>
                            <i class="ph-bold ph-caret-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                        </div>
                        <div class="mt-2 p-3 bg-neon-emerald/5 border border-neon-emerald/10 rounded-xl flex items-start gap-3 transform border-dashed group/note">
                            <i class="ph-bold ph-info text-neon-emerald mt-0.5 text-[10px] group-hover:scale-110 transition-transform"></i>
                            <p class="text-[8px] text-slate-600 font-bold uppercase tracking-[0.1em] leading-relaxed italic">
                                Information integrity is mandatory for CRM predictive modeling.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-4 border-t border-white/5 relative z-10">
                    <a href="<?= baseUrl('admin/contacts') ?>" class="px-6 py-2 bg-white/5 text-slate-600 font-extrabold uppercase tracking-widest text-[8px] rounded-xl hover:bg-white/10 transition-all border border-white/5 flex items-center gap-2">
                        <i class="ph-bold ph-x"></i> Abort Refinement
                    </a>
                    <button type="submit" class="px-10 py-2 bg-neon-emerald hover:bg-emerald-400 text-black text-[9px] font-black uppercase tracking-[0.3em] rounded-xl transition-all shadow-lg active:scale-95 group flex items-center gap-3">
                        <i class="ph-bold ph-check-circle text-lg group-hover:scale-110 transition-transform"></i> Commit Identity
                    </button>
                </div>
            </form>

        <?php else: ?>
            <!-- Contact List -->
            <!-- Contact Inventory Matrix -->
            <div id="contact-list-container" class="admin-table-wrapper backdrop-blur-2xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-premium flex flex-col">
                <div class="p-4 sm:p-10 border-b border-white/5 flex flex-col lg:flex-row items-center justify-end gap-8 bg-white/[0.01]">
                    <?php if (!empty($contacts)): ?>
                    <div class="px-8 py-3 bg-white/5 rounded-[1.25rem] border border-white/5 flex items-center gap-4 group hover:border-neon-cyan/30 transition-all">
                        <div class="w-2 h-2 rounded-full bg-neon-cyan animate-pulse shadow-[0_0_8px_rgba(6,182,212,1)]"></div>
                        <span class="text-[10px] text-slate-500 font-black uppercase tracking-[0.2em]">
                            Nexus Capacity: <span class="text-white ml-2"><?= count($contacts) ?> Units</span>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto crm-main-scroll">
                    <?php if (empty($contacts)): ?>
                        <div class="py-32 text-center">
                            <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 border border-dashed border-white/10">
                                <i class="ph ph-users-three text-slate-700 text-4xl"></i>
                            </div>
                            <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.3em]">Nexus Database Empty</p>
                            <a href="<?= baseUrl('admin/contacts?action=new') ?>" class="inline-block mt-6 text-neon-cyan text-[9px] font-black uppercase tracking-widest hover:underline decoration-2 underline-offset-8">Initialize First Node</a>
                        </div>
                    <?php else: ?>
                        <table class="admin-table w-full text-left border-collapse min-w-[1100px]" id="contactsTable">
                            <thead>
                                <tr class="text-slate-600 text-[8px] font-black uppercase tracking-[0.4em] bg-white/[0.01]">
                                    <th class="py-6 px-10">Client Infrastructure</th>
                                    <th class="py-6 px-6 text-center">Structural Class</th>
                                    <th class="py-6 px-6 text-center">Comms Payload</th>
                                    <th class="py-6 px-6 text-center">Geospatial Sector</th>
                                    <th class="py-6 px-10 text-right">System Navigation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/[0.02]">
                                <?php foreach ($contacts as $c): ?>
                                <tr class="hover:bg-white/[0.03] transition-all group/row border-b border-white/[0.03] last:border-0 relative">
                                    <td class="py-6 px-10" data-label="Identity">
                                        <div class="flex items-center gap-6">
                                            <div class="w-14 h-14 rounded-2xl bg-black/40 border border-white/10 flex items-center justify-center text-3xl shadow-2xl relative group-hover/row:scale-110 group-hover/row:border-neon-cyan/40 group-hover/row:bg-neon-cyan/5 transition-all duration-500 group-hover/row:rotate-3 shadow-inner">
                                                <i class="<?= $c['type']==='company'?'ph-bold ph-buildings':'ph-bold ph-user' ?> text-neon-cyan group-hover/row:scale-110 transition-transform"></i>
                                                <div class="absolute -inset-2 bg-neon-cyan/5 rounded-[2rem] blur-xl opacity-0 group-hover/row:opacity-100 transition-opacity"></div>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-white font-black text-sm tracking-tight group-hover/row:text-neon-cyan transition-colors mb-1 uppercase"><?= e($c['name']) ?></span>
                                                <span class="text-[8px] text-slate-600 font-extrabold uppercase tracking-[0.3em] font-mono">INITIATED: <span class="text-slate-400"><?= date('d.m.y', strtotime($c['created_at'])) ?></span></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6 text-center">
                                        <div class="inline-flex px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-[8px] text-slate-500 font-black uppercase tracking-widest hover:border-neon-cyan/30 transition-colors">
                                            <?= $c['type'] === 'company' ? 'CORP_ARCH' : 'BIO_UNIT' ?>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6 text-center">
                                        <div class="flex flex-col items-center gap-2.5">
                                            <?php if ($c['phone']): ?>
                                                <div class="flex items-center gap-2.5 px-3 py-1 rounded-lg bg-black/40 border border-white/5 hover:border-neon-cyan/40 transition-all group/link shadow-inner">
                                                    <i class="ph-bold ph-phone text-[10px] text-neon-cyan group-hover/link:animate-pulse"></i>
                                                    <span class="text-[9px] font-mono font-black text-slate-500 group-hover/link:text-white uppercase"><?= e($c['phone']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($c['email']): ?>
                                                <div class="flex items-center gap-2.5 px-3 py-1 rounded-lg bg-black/40 border border-white/5 hover:border-neon-cyan/40 transition-all group/link shadow-inner">
                                                    <i class="ph-bold ph-envelope-simple text-[10px] text-neon-cyan group-hover/link:animate-pulse"></i>
                                                    <span class="text-[9px] font-mono font-black text-slate-500 group-hover/link:text-white lowercase"><?= e($c['email']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-6 px-6 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="flex items-center gap-2.5 mb-1.5 p-2 rounded-xl bg-neon-purple/5 border border-neon-purple/10 group-hover/row:border-neon-purple/30 transition-all">
                                                <i class="ph-bold ph-globe text-[11px] text-neon-purple"></i>
                                                <span class="text-[10px] text-white font-black tracking-widest uppercase"><?= e($c['country'] ?? 'GLOBAL') ?></span>
                                            </div>
                                            <span class="text-[8px] text-slate-600 font-black uppercase tracking-[0.2em]"><?= e($c['location'] ?? 'VECTOR_NULL') ?></span>
                                        </div>
                                    </td>
                                    <td class="py-6 px-10 text-right" data-label="Operation">
                                        <div class="flex justify-end gap-3 opacity-0 group-hover/row:opacity-100 transition-all translate-x-4 group-hover/row:translate-x-0">
                                            <a href="<?= baseUrl('admin/contacts?action=edit&id='.$c['id']) ?>" class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan hover:bg-neon-cyan hover:text-black border border-neon-cyan/20 transition-all flex items-center justify-center shadow-lg active:scale-90" title="Refine Identity">
                                                <i class="ph-bold ph-pencil-simple text-lg"></i>
                                            </a>
                                            <button type="button" onclick="showDeleteModal('<?= e($c['name']) ?>', '<?= baseUrl('admin/contacts?action=delete&id='.$c['id']) ?>')" class="w-10 h-10 rounded-xl bg-neon-rose/5 text-neon-rose hover:bg-neon-rose hover:text-white border border-neon-rose/20 transition-all flex items-center justify-center shadow-lg active:scale-90" title="Purge Identity">
                                                <i class="ph-bold ph-trash text-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
                    
            <script>
            document.getElementById('contactSearch').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#contactsTable tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
            </script>
                <?php endif; ?>
            </main>
    </div>
</div>

<style>
    /* Mobile-responsive card transformation */
    @media (max-width: 1024px) {
        .admin-table-wrapper { border-radius: 1.5rem !important; margin: -1rem !important; }
        .admin-table thead { display: none !important; }
        
        .admin-table, 
        .admin-table tbody, 
        .admin-table tr, 
        .admin-table td { 
            display: block !important; 
            width: 100% !important; 
            min-width: 0 !important;
        }
        
        .admin-table tr { 
            margin-bottom: 20px !important; 
            background: rgba(255,255,255,0.02) !important; 
            border-radius: 1.5rem !important; 
            padding: 20px !important;
            border: 1px solid rgba(255,255,255,0.05) !important;
        }
        
        .admin-table td { 
            display: flex !important; 
            justify-content: space-between !important; 
            align-items: center !important; 
            padding: 12px 0 !important; 
            border-bottom: 1px solid rgba(255,255,255,0.03) !important;
            text-align: right !important;
            min-height: 44px !important;
            gap: 16px !important;
        }
        
        .admin-table td:last-child { border-bottom: none !important; }
        
        .admin-table td::before { 
            content: attr(data-label) !important; 
            font-weight: 900 !important; 
            text-transform: uppercase !important; 
            font-size: 0.65rem !important; 
            color: #06b6d4 !important;
            letter-spacing: 2px !important;
            opacity: 0.6 !important;
            text-align: left !important;
            flex-shrink: 0 !important;
        }

        /* Ensure content doesn't overflow or crowd labels */
        .admin-table td > * {
            flex-shrink: 1 !important;
            min-width: 0 !important;
        }
    }
</style>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
