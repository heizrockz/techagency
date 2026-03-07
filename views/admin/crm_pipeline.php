<?php
$pageTitle = 'CRM Pipeline';
$currentPage = 'crm_pipeline';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle . ' - ' . APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SortableJS for smooth Kanban dragging -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL) ?>/assets/css/style.css">
    <style> 
        .kanban-column { min-height: 200px; }
        .crm-main-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
        .crm-main-scroll::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.02); }
        .crm-main-scroll::-webkit-scrollbar-thumb { background: rgba(6, 182, 212, 0.1); border-radius: 10px; }
        .crm-main-scroll::-webkit-scrollbar-thumb:hover { background: rgba(6, 182, 212, 0.3); }
    </style>
</head>
<body class="bg-[#0b0e14]">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
    <!-- Topbar -->
    <header class="h-auto lg:h-20 flex flex-col lg:flex-row items-center px-4 lg:px-8 bg-white/[0.02] backdrop-blur-xl border-b border-white/5 shrink-0 relative z-[100] py-4 lg:py-0 gap-4 lg:gap-8">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 via-transparent to-transparent pointer-events-none"></div>
        
        <!-- 1. Left Content (Logo/Title & Mobile Profile) -->
        <div class="relative flex items-center justify-between w-full lg:w-auto shrink-0 z-[120]">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-cyan-500/10 flex items-center justify-center border border-cyan-500/20">
                    <i class="ph ph-kanban text-2xl text-cyan-500"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">Opportunity Pipeline</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Deal Progress Management</p>
                </div>
            </div>
            <!-- Topbar on Mobile -->
            <div class="lg:hidden">
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </div>

        <!-- 2. Middle Content (Search & Actions) -->
        <div class="relative flex-1 w-full lg:w-auto flex flex-wrap lg:flex-nowrap items-center justify-start lg:justify-center gap-3 pb-1 lg:pb-0 z-[110]">
            
            <!-- Action Button -->
            <button onclick="document.getElementById('addLeadModal').classList.remove('hidden')" class="shrink-0 h-10 group flex items-center justify-center gap-2 px-4 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 rounded-xl transition-all duration-300">
                <i class="ph ph-plus-circle text-lg text-emerald-500 group-hover:rotate-90 transition-transform duration-500"></i>
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500 whitespace-nowrap">
                    <span class="hidden lg:inline">New Lead</span>
                    <span class="lg:hidden">New</span>
                </span>
            </button>

            <!-- MEGA SEARCH BAR -->
            <div class="relative flex-1 min-w-[150px] lg:w-80 lg:max-w-md h-10 bg-black/40 rounded-xl border border-cyan-500/20 shadow-inner group focus-within:border-cyan-500/50 transition-all duration-300 z-[120]">
                <div class="flex items-center px-4 h-full">
                    <i class="ph ph-magnifying-glass text-cyan-500"></i>
                    <form action="" method="GET" class="flex-1 px-3 flex items-center h-full">
                        <input type="hidden" name="view" value="<?= e($view ?? 'kanban') ?>">
                        <input type="text" name="search" value="<?= e($search ?? '') ?>" 
                               placeholder="SEARCH PIPELINE..." 
                               class="bg-transparent border-none text-[11px] font-bold text-white placeholder-slate-600 outline-none w-full uppercase tracking-widest h-full">
                    </form>
                    <div class="h-4 w-px bg-white/10 mx-2"></div>
                    <button type="button" onclick="toggleDropdown('mega-filter-menu', event)" class="p-1 text-slate-400 hover:text-white transition-colors flex items-center justify-center">
                        <i class="ph-bold ph-caret-down text-xs"></i>
                    </button>
                </div>

                <!-- MEGA DROPDOWN (Odoo Inspired) -->
                <div id="mega-filter-menu" class="hidden fixed lg:absolute left-4 right-4 lg:left-0 lg:right-auto top-[140px] lg:top-[calc(100%+8px)] w-auto lg:w-[600px] max-w-full lg:max-w-[90vw] bg-[#161d27] border border-white/10 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] z-[9999] backdrop-blur-3xl overflow-hidden animate-in fade-in zoom-in duration-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-white/5">
                        <!-- Filters Column -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 text-[10px] font-black text-cyan-500 uppercase tracking-widest mb-4">
                                <i class="ph-bold ph-funnel-simple"></i> Filters
                            </div>
                            <div class="space-y-1">
                                <a href="?search=Won" class="flex items-center justify-between px-3 py-2 text-[10px] font-bold text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Won Deals <i class="ph ph-check-circle opacity-0 group-hover:opacity-100"></i></a>
                                <a href="?search=Lost" class="flex items-center justify-between px-3 py-2 text-[10px] font-bold text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">Lost Deals</a>
                                <a href="?search=New" class="flex items-center justify-between px-3 py-2 text-[10px] font-bold text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">New Leads</a>
                                <hr class="border-white/5 my-2">
                                <a href="#" class="flex items-center gap-2 px-3 py-2 text-[9px] font-black text-slate-500 italic hover:text-cyan-500 transition-colors uppercase tracking-widest">
                                    <i class="ph ph-plus-circle"></i> Add Custom Filter
                                </a>
                            </div>
                        </div>

                        <!-- Group By Column -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 text-[10px] font-black text-purple-500 uppercase tracking-widest mb-4">
                                <i class="ph-bold ph-stack"></i> Group By
                            </div>
                            <div class="space-y-1">
                                <a href="?group=stage" class="px-3 py-2 block text-[10px] font-bold text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">By Sector (Stage)</a>
                                <a href="?group=salesperson" class="px-3 py-2 block text-[10px] font-bold text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">By Handler (Sales)</a>
                                <a href="?group=priority" class="px-3 py-2 block text-[10px] font-bold text-slate-400 hover:text-white hover:bg-white/5 rounded-lg transition-all">By Priority</a>
                                <hr class="border-white/5 my-2">
                                <a href="#" class="flex items-center gap-2 px-3 py-2 text-[9px] font-black text-slate-500 italic hover:text-purple-500 transition-colors uppercase tracking-widest">
                                    <i class="ph ph-plus-circle"></i> Add Custom Group
                                </a>
                            </div>
                        </div>

                        <!-- Favorites Column -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 text-[10px] font-black text-amber-500 uppercase tracking-widest mb-4">
                                <i class="ph-bold ph-star text-xs"></i> Favorites
                            </div>
                            <div class="space-y-1">
                                <div class="px-3 py-2 text-[10px] font-bold text-slate-600 bg-white/[0.02] rounded-lg border border-white/5 flex flex-col gap-1">
                                    <span class="text-[8px] font-black uppercase text-slate-700">Recent Search</span>
                                    <span>High Value Active</span>
                                </div>
                                <hr class="border-white/5 my-2">
                                <a href="#" class="flex items-center gap-2 px-3 py-2 text-[9px] font-black text-slate-500 hover:text-amber-500 transition-colors uppercase tracking-widest">
                                    <i class="ph ph-floppy-disk"></i> Save Search
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Switcher -->
            <div class="flex h-10 bg-black/40 rounded-xl p-1 border border-white/10 shadow-inner shrink-0 items-center">
                <a href="?view=kanban&search=<?= urlencode($search) ?>" class="p-1 px-2 rounded-lg transition-all <?= $view === 'kanban' ? 'bg-cyan-500 text-black shadow-lg shadow-cyan-500/20' : 'text-slate-500 hover:text-white' ?>" title="Kanban View">
                    <i class="ph ph-layout text-lg"></i>
                </a>
                <a href="?view=list&search=<?= urlencode($search) ?>" class="p-1 px-2 rounded-lg transition-all <?= $view === 'list' ? 'bg-cyan-500 text-black shadow-lg shadow-cyan-500/20' : 'text-slate-500 hover:text-white' ?>" title="List View">
                    <i class="ph ph-list text-lg"></i>
                </a>
            </div>
        </div>

        <!-- 3. Desktop Profile Content -->
        <div class="relative hidden lg:flex items-center shrink-0 z-[120] pl-2">
            <?php 
            $showTopbarSearch = false; // Disabled to favor the unified mega search
            require __DIR__ . '/partials/_topbar.php'; 
            ?>
        </div>
    </header>

    <?php if ($flash = getFlash()): ?>
        <div class="m-6 mb-0 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2 shrink-0">
            <i class="ph ph-check-circle text-lg"></i>
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <!-- Board/List Container -->
    <main class="flex-1 overflow-x-auto overflow-y-auto p-6 pt-2 crm-main-scroll">
        <?php if ($view === 'list'): ?>
            <!-- Sequence View (List) -->
            <div id="pipeline-list-container" class="admin-table-wrapper backdrop-blur-xl border border-white/5 rounded-3xl overflow-hidden shadow-premium flex flex-col">
                <table class="admin-table w-full text-left border-collapse min-w-[1000px]">
                    <thead>
                        <tr class="text-slate-500 text-[9px] font-black uppercase tracking-[0.3em] bg-white/[0.01]">
                            <th class="py-6 px-8">Opportunity Details</th>
                            <th class="py-6 px-6">Contact Information</th>
                            <th class="py-6 px-6 text-center">Pipeline Stage</th>
                            <th class="py-6 px-6 text-center">Expected Value</th>
                            <th class="py-6 px-6 text-center">Date Added</th>
                            <th class="py-6 px-8 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.02]">
                        <?php foreach ($opportunities as $opp): ?>
                        <tr class="hover:bg-white/[0.04] transition-all group/row border-b border-white/[0.03] last:border-0">
                            <td class="py-6 px-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-1.5 h-8 rounded-full <?= !empty($opp['color_code']) ? 'crm-color-'.$opp['color_code'] : 'bg-slate-700' ?> shadow-[0_0_12px_currentColor] opacity-70 group-hover/row:opacity-100 transition-opacity"></div>
                                    <div class="flex flex-col">
                                        <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="text-white font-black hover:text-neon-cyan transition-colors text-[11px] uppercase tracking-wider block mb-1">
                                            <?= htmlspecialchars($opp['title']) ?>
                                        </a>
                                        <span class="text-[8px] text-slate-600 font-black uppercase tracking-widest font-mono">ID: <?= str_pad($opp['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-6 px-6">
                                <?php if ($opp['email']): ?>
                                    <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-black/20 border border-white/5 w-fit">
                                        <i class="ph ph-envelope-simple text-[10px] text-neon-cyan"></i> 
                                        <span class="text-[10px] font-mono text-slate-400 lowercase"><?= htmlspecialchars($opp['email']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-[9px] text-slate-700 font-black uppercase tracking-widest italic">No Intel Uplink</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-6 px-6 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-neon-cyan/5 border border-neon-cyan/20 text-[9px] text-neon-cyan font-black uppercase tracking-widest">
                                    <?= htmlspecialchars($opp['stage']) ?>
                                </span>
                            </td>
                            <td class="py-6 px-6 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-neon-emerald font-black text-xs">$<?= number_format($opp['expected_revenue'], 0) ?></span>
                                    <span class="text-[8px] text-slate-600 font-bold uppercase tracking-widest mt-0.5">Estimated</span>
                                </div>
                            </td>
                            <td class="py-6 px-6 text-center text-[10px] text-slate-500 font-black font-mono">
                                <?= date('d M Y', strtotime($opp['created_at'])) ?>
                            </td>
                            <td class="py-6 px-8 text-right">
                                <div class="flex justify-end translate-x-2 opacity-0 group-hover/row:opacity-100 group-hover/row:translate-x-0 transition-all duration-300">
                                    <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="w-10 h-10 rounded-xl bg-neon-cyan/10 text-neon-cyan hover:bg-neon-cyan hover:text-black transition-all flex items-center justify-center shadow-lg border border-neon-cyan/20 active:scale-90" title="Expand Opportunity">
                                        <i class="ph-bold ph-arrow-square-out text-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Matrix View (Kanban) -->
            <div class="flex h-full flex-row flex-nowrap overflow-x-auto lg:overflow-visible gap-6 items-start pb-8" id="stages-wrapper" style="min-width: 0;">
                
                <?php foreach ($pipeline as $stageName => $stageData): 
                    $opps = $stageData['opportunities'];
                    $info = $stageData['info'];
                    $isCollapsed = (bool)$info['is_collapsed'];
                ?>
                
                <?php if ($isCollapsed): ?>
                    <!-- Collapsed Stage -->
                    <div class="lg:h-[calc(100vh-280px)] h-[calc(100vh-280px)] shrink-0 flex items-center border border-white/5 rounded-2xl pt-6 pb-4 w-16 cursor-pointer hover:bg-white/[0.03] transition-all bg-white/[0.01] group/collapsed shadow-premium px-4 flex-col" onclick="toggleCollapse(<?= $info['id'] ?>)">
                        <div class="text-slate-600 mb-6 group-hover/collapsed:text-neon-cyan transition-colors" title="Expand Stage">
                            <i class="ph-bold ph-arrows-out-line-horizontal text-xl"></i>
                        </div>
                        <div class="flex-1 w-full flex justify-center mt-2 relative">
                            <div class="transform rotate-180" style="writing-mode: horizontal-tb; text-orientation: mixed;">
                                <span class="text-slate-500 font-black tracking-[0.3em] uppercase text-[10px] flex items-center gap-4 group-hover/collapsed:text-white transition-colors [writing-mode:vertical-rl]">
                                    <span class="text-neon-cyan"><?= count($opps) ?> Deals</span>
                                    <span class="opacity-20">/ / /</span>
                                    <?= htmlspecialchars($stageName) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Expanded Stage -->
                    <div class="w-[300px] lg:w-[320px] flex flex-col shrink-0 stage-column group/stage" data-stage-id="<?= $info['id'] ?>" data-stage-name="<?= htmlspecialchars($stageName) ?>">
                        <!-- Stage Header -->
                        <div class="flex items-center justify-between mb-4 px-2 relative">
                            <div class="opacity-0 group-hover/stage:opacity-100 cursor-grab text-slate-600 hover:text-neon-cyan transition-all mr-2 stage-drag-handle">
                                <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                            </div>
                            
                            <h3 class="font-bold text-white text-xs flex-1 truncate flex items-center gap-3">
                                <?= htmlspecialchars($stageName) ?>
                                <span class="px-2 py-0.5 bg-white/5 text-slate-400 text-[10px] rounded-md opp-count border border-white/5"><?= count($opps) ?></span>
                            </h3>
                            
                            <div class="relative">
                                <button type="button" class="text-slate-500 hover:text-white p-1.5 rounded-lg hover:bg-white/5 transition-all active:scale-90" onclick="toggleDropdown('stage-menu-<?= $info['id'] ?>', event)">
                                    <i class="ph-bold ph-dots-three text-lg"></i>
                                </button>
                                <div id="stage-menu-<?= $info['id'] ?>" class="hidden absolute right-0 top-10 w-44 bg-glass-bg border border-white/10 rounded-xl shadow-premium py-2 z-[200] backdrop-blur-2xl">
                                    <button onclick="toggleCollapse(<?= $info['id'] ?>)" class="w-full text-left px-4 py-2 text-[11px] font-semibold text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-2 border-b border-white/5 mb-1 transition-all">
                                        <i class="ph ph-arrows-in-line-horizontal text-base"></i> Collapse Stage
                                    </button>
                                    <button type="button" onclick="deleteStage(<?= $info['id'] ?>)" class="w-full text-left px-4 py-2 text-[11px] font-semibold text-neon-rose/80 hover:bg-neon-rose/5 hover:text-neon-rose flex items-center gap-2 transition-all">
                                        <i class="ph ph-trash text-base"></i> Delete Stage
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sector Grid (Kanban Body) -->
                        <div class="flex-1 rounded-2xl bg-white/[0.01] border border-white/[0.03] p-4 pb-12 flex flex-col gap-4 kanban-column min-h-[500px] lg:min-h-[150px] shadow-inner transition-colors hover:border-white/5" data-stage="<?= htmlspecialchars($stageName) ?>">
                            
                            <?php foreach ($opps as $opp): ?>
                                <div class="kanban-card admin-stat-card !p-4 !bg-[#161d27] border border-white/5 shadow-premium cursor-grab active:cursor-grabbing hover:border-neon-cyan/40 transition-all relative group/card shrink-0 min-h-[130px] !overflow-visible <?= !empty($opp['color_code']) ? 'crm-color-'.$opp['color_code'] : '' ?>" 
                                     data-id="<?= $opp['id'] ?>" onclick="if(!document.body.classList.contains('is-dragging')) window.location.href='<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>'">
                                    
                                    <!-- Quick Access Trigger -->
                                    <button type="button" title="Quick Access" onclick="toggleDropdown('card-menu-<?= $opp['id'] ?>', event)" class="absolute top-2 right-2 opacity-0 group-hover/card:opacity-100 transition-all p-1 text-slate-500 hover:text-neon-cyan hover:bg-white/5 rounded-md active:scale-75">
                                        <i class="ph-bold ph-dots-three-vertical text-base"></i>
                                    </button>

                                    <!-- Opportunity Title -->
                                    <div class="mb-2 pr-4">
                                        <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="font-semibold text-slate-100 group-hover/card:text-neon-cyan transition-colors text-sm line-clamp-2 leading-snug">
                                            <?= htmlspecialchars($opp['title']) ?>
                                        </a>
                                    </div>

                                    <!-- Organization/Client -->
                                    <?php if (!empty($opp['contact_name'])): ?>
                                        <div class="text-[11px] text-slate-400 font-medium mb-3 flex items-center gap-1.5 px-2 py-1 bg-white/[0.02] rounded-md border border-white/5 w-fit">
                                            <i class="ph ph-buildings opacity-50"></i>
                                            <?= htmlspecialchars($opp['contact_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Priority & Forecast -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-1">
                                            <?php for($s=1; $s<=3; $s++): ?>
                                                <i class="ph-fill ph-circle text-[8px] <?= $s <= $opp['priority'] ? 'text-neon-amber' : 'text-slate-700' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <?php if ($opp['expected_revenue'] > 0): ?>
                                            <span class="text-xs font-bold text-neon-emerald tracking-tight">
                                                $<?= number_format($opp['expected_revenue'] / 1000, 1) ?>K
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Personnel Assignment -->
                                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-white/[0.03] text-[10px] text-slate-400 font-medium tracking-wide">
                                        <div class="flex items-center gap-3">
                                            <span class="flex items-center gap-1 opacity-70"><i class="ph ph-clock text-slate-500"></i> <?= date('d M', strtotime($opp['created_at'])) ?></span>
                                            <?php if ($opp['probability'] > 0): ?>
                                                <span class="text-neon-cyan/60"><?= $opp['probability'] ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                            $spName = 'U';
                                            $spEmoji = '';
                                            $spTitle = 'Unassigned';
                                            $isActiveHandler = false;
                                            if (!empty($opp['salesperson_id'])) {
                                                foreach ($salespersons as $sp) {
                                                    if ($sp['id'] == $opp['salesperson_id']) {
                                                        $spName = strtoupper(substr($sp['full_name'] ?: ($sp['username'] ?? 'U'), 0, 2));
                                                        $spEmoji = $sp['avatar_emoji'] ?? '';
                                                        $spTitle = $sp['full_name'] ?: $sp['username'];
                                                        $isActiveHandler = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="flex items-center gap-2 group/avatar relative" title="<?= e($spTitle) ?>">
                                            <span class="text-[9px] text-slate-500 hidden group-hover/card:block transition-all"><?= explode(' ', $spTitle)[0] ?></span>
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold border transition-all <?= $isActiveHandler ? 'bg-neon-cyan/10 border-neon-cyan/30 text-neon-cyan shadow-[0_0_10px_rgba(6,182,212,0.1)]' : 'bg-white/5 border-white/10 text-slate-500' ?>">
                                                <?= $spEmoji ?: $spName ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Indicator Glow -->
                                    <?php if($isActiveHandler): ?>
                                        <div class="absolute -bottom-px left-8 right-8 h-px bg-gradient-to-r from-transparent via-neon-cyan/30 to-transparent group-hover/card:via-neon-cyan transition-all"></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="mt-2 opacity-0 group-hover/stage:opacity-100 transition-all transform translate-y-2 group-hover/stage:translate-y-0">
                                <button onclick="openLeadModal('<?= htmlspecialchars($stageName) ?>')" class="w-full py-2.5 bg-white/[0.02] border border-dashed border-white/10 rounded-xl text-slate-400 hover:text-neon-cyan hover:border-neon-cyan/50 hover:bg-neon-cyan/5 transition-all text-[11px] font-semibold flex items-center justify-center gap-2">
                                    <i class="ph ph-plus-circle text-base"></i> Add Target
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
                
                <div class="w-16 shrink-0 flex flex-col items-center pt-2">
                    <button onclick="document.getElementById('addStageModal').classList.remove('hidden')" class="w-12 h-12 rounded-2xl bg-white/[0.02] hover:bg-neon-cyan/10 border border-white/5 hover:border-neon-cyan/30 flex items-center justify-center text-slate-600 hover:text-neon-cyan transition-all shadow-premium group/add" title="Add Neural Sector">
                        <i class="ph-bold ph-plus text-xl group-hover/add:scale-125 transition-transform"></i>
                    </button>
                    <span class="text-[8px] text-slate-700 font-black uppercase tracking-widest mt-4 [writing-mode:vertical-rl] opacity-0 group-hover:opacity-100 transition-opacity">Provision Sector</span>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>


    <!-- Initialization Modals -->
    
    <!-- Add Lead Modem -->
    <div id="addLeadModal" class="hidden fixed inset-0 z-[500] flex items-center justify-center p-4 backdrop-blur-md bg-black/60">
        <div class="bg-glass-bg border border-white/10 w-full max-w-lg rounded-3xl shadow-premium overflow-hidden transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                <h3 class="text-white font-black uppercase tracking-[0.2em] text-[11px] flex items-center gap-3">
                    <i class="ph ph-sparkle text-neon-cyan anime-pulse"></i> 
                    Initialize Intelligence Lead
                </h3>
                <button onclick="document.getElementById('addLeadModal').classList.add('hidden')" class="text-slate-500 hover:text-white transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="p-8 space-y-6">
                <input type="hidden" name="action" value="add_opportunity">
                <input type="hidden" name="stage" id="modalStageInput" value="New">
                
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest pl-1">Primary Objective (Title)</label>
                    <input type="text" name="title" required placeholder="Designation of the opportunity..." 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[11px] font-bold text-white focus:border-neon-cyan outline-none transition-all placeholder:text-slate-700 uppercase tracking-wider">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest pl-1">Value Forecast ($)</label>
                        <div class="relative">
                            <i class="ph ph-currency-circle-dollar absolute left-4 top-1/2 -translate-y-1/2 text-neon-emerald"></i>
                            <input type="number" name="expected_revenue" value="0" step="0.01"
                                   class="w-full bg-black/40 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-[11px] font-bold text-white focus:border-neon-emerald outline-none transition-all uppercase tracking-wider">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest pl-1">Strategic Priority</label>
                        <select name="priority" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[11px] font-bold text-white focus:border-neon-amber outline-none transition-all appearance-none uppercase tracking-wider cursor-pointer">
                            <option value="1">Level 1 - Alpha</option>
                            <option value="2">Level 2 - Beta</option>
                            <option value="3">Level 3 - Gamma (Critical)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest pl-1">Operational Handler (Sales)</label>
                    <select name="salesperson_id" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[11px] font-bold text-white focus:border-neon-purple outline-none transition-all appearance-none uppercase tracking-wider cursor-pointer">
                        <option value="">Unassigned Pulse</option>
                        <?php foreach ($salespersons as $sp): ?>
                            <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['full_name'] ?: $sp['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('addLeadModal').classList.add('hidden')" class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white transition-all bg-white/5 rounded-xl border border-white/5 active:scale-95">
                        Cancel Sync
                    </button>
                    <button type="submit" class="flex-[2] py-3 bg-neon-cyan hover:bg-cyan-400 text-black text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95">
                        Confirm Initialization
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Sector Modem -->
    <div id="addStageModal" class="hidden fixed inset-0 z-[500] flex items-center justify-center p-4 backdrop-blur-md bg-black/60">
        <div class="bg-glass-bg border border-white/10 w-full max-w-md rounded-3xl shadow-premium overflow-hidden transform transition-all animate-in fade-in zoom-in duration-300">
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                <h3 class="text-white font-black uppercase tracking-[0.2em] text-[11px] flex items-center gap-3">
                    <i class="ph ph-stack-plus text-neon-purple"></i> 
                    Provision Pipeline Sector
                </h3>
                <button onclick="document.getElementById('addStageModal').classList.add('hidden')" class="text-slate-500 hover:text-white transition-colors">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>
            <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="p-8 space-y-6">
                <input type="hidden" name="action" value="add_stage">
                
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest pl-1">Sector Designation (Name)</label>
                    <input type="text" name="name" required placeholder="Target sector name..." 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-[11px] font-bold text-white focus:border-neon-purple outline-none transition-all placeholder:text-slate-700 uppercase tracking-wider">
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('addStageModal').classList.add('hidden')" class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-white transition-all bg-white/5 rounded-xl border border-white/5 active:scale-95">
                        Abort
                    </button>
                    <button type="submit" class="flex-[2] py-3 bg-neon-purple hover:bg-purple-400 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg active:scale-95 shadow-neon-purple/20">
                        Verify Sector
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- Helper Form for Toggling Collapse -->
<form id="collapseForm" action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="hidden">
    <input type="hidden" name="action" value="toggle_collapse">
    <input type="hidden" name="id" id="collapseId">
</form>

<script>
    // System Initialization & Kanban Dynamics
    const body = document.body;

    // Initialize SortableJS for smooth Kanban dragging
    document.querySelectorAll('.kanban-column').forEach(column => {
        new Sortable(column, {
            group: 'pipeline',
            animation: 250,
            ghostClass: 'opacity-30',
            dragClass: 'rotate-3',
            easing: "cubic-bezier(0.25, 1, 0.5, 1)",
            onEnd: function (evt) {
                const itemEl = evt.item;  // dragged element
                const to = evt.to;        // target list
                const from = evt.from;    // original list
                
                const oppId = itemEl.dataset.id;
                const stageName = to.dataset.stage;
                
                if (oppId && stageName && from !== to) {
                    // Update Sector in Database
                    fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=update_stage&id=${oppId}&stage=${encodeURIComponent(stageName)}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showPulseNotification('Vector Synchronized', 'neon-cyan');
                            updateCounts();
                        }
                    });
                } else if (from === to) {
                    // Just reordered within the same column (if position matters later)
                }
            },
        });
    });

    // 3D Tilt Reality Factor & Pulse Effects
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 8;
            const rotateY = (centerX - x) / 8;
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'none';
        });
    });

    // Helper functions
    function showPulseNotification(msg, color) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-8 right-8 px-6 py-3 rounded-xl bg-glass-bg border border-${color}/30 text-${color} text-[10px] font-black uppercase tracking-widest shadow-premium z-[1000] animate-in slide-in-from-bottom duration-300`;
        toast.innerHTML = `<i class="ph ph-check-circle mr-2"></i> ${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('animate-out', 'fade-out', 'translate-y-4');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    function updateCounts() {
        document.querySelectorAll('.stage-column').forEach(col => {
            const count = col.querySelectorAll('.kanban-card').length;
            const countTag = col.querySelector('.opp-count');
            if (countTag) countTag.textContent = count;
        });
    }

    // Modal Control & Dropdown Logic (Existing with Refinements)
    function openLeadModal(stage) {
        document.getElementById('modalStageInput').value = stage;
        document.getElementById('addLeadModal').classList.remove('hidden');
    }

    function toggleDropdown(id, e) {
        e.stopPropagation();
        const el = document.getElementById(id);
        const isOpen = !el.classList.contains('hidden');
        
        // Auto-close others
        document.querySelectorAll('[id^="filter-"], [id^="group-"], [id^="fav-"], [id^="stage-menu-"], [id^="card-menu-"], #mega-filter-menu').forEach(d => {
            if (d.id !== id) d.classList.add('hidden');
        });

        if (!isOpen) {
            el.classList.remove('hidden');
            
            // Re-position fixed dropdowns (like card menus) dynamically
            if (el.id.startsWith('card-menu-')) {
                const btnRect = e.currentTarget.getBoundingClientRect();
                
                // Set initial hidden states to get accurate dimensions
                el.style.visibility = 'hidden';
                el.classList.remove('hidden');
                const menuRect = el.getBoundingClientRect();
                el.classList.add('hidden');
                el.style.visibility = 'visible';
                
                let topPos = btnRect.bottom + 8;
                let leftPos = btnRect.right - menuRect.width;

                // Adjust if it goes off bottom viewport
                if (topPos + menuRect.height > window.innerHeight) {
                    topPos = btnRect.top - menuRect.height - 8;
                }
                
                // Adjust if it goes off left viewport
                if (leftPos < 0) {
                    leftPos = btnRect.left;
                }
                
                el.style.top = topPos + 'px';
                el.style.left = leftPos + 'px';
                el.classList.remove('hidden');
            }
        }
    }

    // Close on outer click
    window.addEventListener('click', () => {
        document.querySelectorAll('[id$="-menu"], [id^="filter-"], [id^="group-"], [id^="fav-"], [id^="card-menu-"]').forEach(d => d.classList.add('hidden'));
    });

    // Stage Collapsing
    function toggleCollapse(id) {
        fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=toggle_collapse&id=${id}`
        }).then(() => location.reload());
    }

    function deleteStage(id) {
        if (confirm('CAUTION: System sector purge imminent. Proceed?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="action" value="delete_stage"><input type="hidden" name="id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function updateCardColor(id, color) {
        fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=update_color&id=${id}&color=${color}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const card = document.querySelector(`.kanban-card[data-id="${id}"]`);
                if (card) {
                    // Remove existing colors
                    card.className = card.className.replace(/crm-color-\w+/g, '');
                    if (color !== 'none') card.classList.add(`crm-color-${color}`);
                }
                showPulseNotification('Frequency Adjusted', 'neon-purple');
            }
        });
    }

    function showDeleteModal(title, formId) {
        if (confirm(`PURGE RECORD: ${title}? This action cannot be reverted.`)) {
            document.getElementById(formId).submit();
        }
    }
</script>
    <!-- Master Dropdown Registry (Root Level to Escape Transforms) -->
    <div id="dropdown-registry">
        <?php foreach ($pipeline as $stageName => $stageData): ?>
            <?php foreach ($stageData['opportunities'] as $opp): ?>
                <div id="card-menu-<?= $opp['id'] ?>" onclick="event.stopPropagation()" class="hidden fixed w-56 bg-[#161d27] border border-white/10 rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.8)] p-4 z-[9999] backdrop-blur-3xl transition-opacity">
                    <div class="text-[9px] text-slate-600 mb-3 font-black uppercase tracking-[0.2em] border-b border-white/5 pb-1">Actions</div>
                    <div class="space-y-1 mb-5">
                        <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="w-full text-left px-3 py-2 text-[10px] font-black text-slate-400 hover:bg-white/5 hover:text-white flex items-center gap-2 rounded-lg transition-all uppercase tracking-widest">
                            <i class="ph ph-eye text-base"></i> View Details
                        </a>
                        <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" id="deleteOpp_<?= $opp['id'] ?>">
                            <input type="hidden" name="action" value="delete_opportunity">
                            <input type="hidden" name="id" value="<?= $opp['id'] ?>">
                            <button type="button" onclick="showDeleteModal('<?= htmlspecialchars(addslashes($opp['title'])) ?>', 'deleteOpp_<?= $opp['id'] ?>')" class="w-full text-left px-3 py-2 text-[10px] font-black text-neon-rose/60 hover:bg-neon-rose/5 hover:text-neon-rose flex items-center gap-2 rounded-lg transition-all uppercase tracking-widest">
                                <i class="ph ph-trash text-base"></i> Delete Record
                            </button>
                        </form>
                    </div>
                    
                    <div class="text-[9px] text-slate-600 mb-3 font-black uppercase tracking-[0.2em] border-b border-white/5 pb-1">Visual Signature</div>
                    <div class="grid grid-cols-4 gap-2">
                        <?php $colors = ['none', 'red', 'orange', 'yellow', 'green', 'blue', 'purple', 'teal', 'pink']; ?>
                        <?php foreach ($colors as $c): ?>
                            <div onclick="updateCardColor(<?= $opp['id'] ?>, '<?= $c ?>')" 
                                 class="w-full aspect-square rounded-lg cursor-pointer border border-white/10 hover:scale-110 active:scale-90 transition-all flex items-center justify-center
                                        <?= $c === 'none' ? 'bg-slate-700' : 'bg-'.$c.'-500' ?>" 
                                 title="<?= ucfirst($c) ?>">
                                 <?php if($c === 'none'): ?><i class="ph ph-prohibit-inset text-slate-400 text-sm"></i><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
