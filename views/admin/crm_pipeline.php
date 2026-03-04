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
    <header class="h-auto lg:h-20 flex flex-col lg:flex-row items-center justify-between px-4 lg:px-8 bg-white/[0.02] backdrop-blur-xl border-b border-white/5 shrink-0 relative overflow-hidden py-4 lg:py-0 gap-4 lg:gap-0">
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/5 via-transparent to-transparent"></div>
        
        <!-- Row 1 (Mobile) / Left Content (Desktop) -->
        <div class="relative flex items-center justify-between w-full lg:w-auto">
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

        <!-- Row 2 (Mobile) / Right Content (Desktop) -->
        <div class="relative flex items-center justify-between lg:justify-end gap-3 sm:gap-6 w-full lg:w-auto">
            <!-- View Switcher -->
            <div class="flex bg-black/40 rounded-xl p-1 border border-white/10 shadow-inner">
                <a href="?view=kanban&search=<?= urlencode($search) ?>" class="p-2 rounded-lg transition-all <?= $view === 'kanban' ? 'bg-cyan-500 text-black shadow-lg shadow-cyan-500/20' : 'text-slate-500 hover:text-white' ?>" title="Kanban View">
                    <i class="ph ph-layout text-lg"></i>
                </a>
                <a href="?view=list&search=<?= urlencode($search) ?>" class="p-2 rounded-lg transition-all <?= $view === 'list' ? 'bg-cyan-500 text-black shadow-lg shadow-cyan-500/20' : 'text-slate-500 hover:text-white' ?>" title="List View">
                    <i class="ph ph-list text-lg"></i>
                </a>
            </div>

            <!-- Action Button -->
            <button onclick="document.getElementById('addLeadModal').classList.remove('hidden')" class="flex-1 lg:flex-none group flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 rounded-xl transition-all duration-300">
                <i class="ph ph-plus-circle text-lg text-emerald-500 group-hover:rotate-90 transition-transform duration-500"></i>
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500 whitespace-nowrap">
                    <span class="hidden sm:inline">Create Opportunity</span>
                    <span class="sm:hidden">Create</span>
                </span>
            </button>

            <!-- Topbar on Desktop -->
            <div class="hidden lg:block">
                <?php 
                $showTopbarSearch = true;
                $topbarSearchPlaceholder = 'SEARCH PIPELINE...';
                $topbarSearchHiddenFields = ['view' => $view ?? 'kanban'];
                require __DIR__ . '/partials/_topbar.php'; 
                ?>
            </div>
        </div>
    </header>

    <div class="px-4 lg:px-8 py-4 bg-[#0b0e14]">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white/[0.02] backdrop-blur-xl p-3 rounded-2xl border border-white/5 shadow-2xl">
            <div class="w-full sm:w-auto flex items-center justify-between sm:justify-start gap-4 flex-1">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest hidden lg:block">Pipeline Controls</div>
                <!-- Search on Mobile -->
                <div class="lg:hidden flex-1 relative group">
                    <form action="" method="GET" class="w-full">
                        <input type="hidden" name="view" value="<?= e($view ?? 'kanban') ?>">
                        <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-600 group-focus-within:text-cyan-500"></i>
                        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="SEARCH..." class="w-full bg-black/40 border border-white/10 rounded-xl pl-9 pr-4 py-2 text-[10px] font-black uppercase tracking-widest text-white outline-none focus:border-cyan-500/40 transition-all">
                    </form>
                </div>
            </div>

            <div class="flex items-center justify-between w-full sm:w-auto gap-3">
                <div class="relative flex-1 sm:flex-none">
                    <button onclick="toggleDropdown('filter-menu', event)" class="w-full px-4 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-cyan-500 hover:bg-cyan-500/5 rounded-xl flex items-center justify-center sm:justify-start gap-2 transition-all border border-transparent hover:border-cyan-500/20 active:scale-95">
                        <i class="ph ph-funnel-simple-bold text-base"></i> <span class="hidden sm:inline">Filters</span>
                    </button>
                    <div id="filter-menu" class="hidden absolute right-0 top-12 w-56 bg-[#161d27] border border-white/10 rounded-2xl shadow-premium py-3 z-[200] backdrop-blur-2xl">
                        <div class="px-4 py-2 text-[9px] font-black text-slate-600 uppercase tracking-widest border-b border-white/5 mb-2">Outcome Status</div>
                        <a href="?search=Won" class="flex items-center justify-between px-4 py-2.5 text-[10px] text-slate-400 hover:bg-emerald-500/10 hover:text-emerald-500 transition-all uppercase tracking-widest font-black">Won Deals <i class="ph ph-check-circle text-sm"></i></a>
                        <a href="?search=Lost" class="flex items-center justify-between px-4 py-2.5 text-[10px] text-slate-400 hover:bg-pink-500/10 hover:text-pink-500 transition-all uppercase tracking-widest font-black">Lost Deals <i class="ph ph-x-circle text-sm"></i></a>
                        <a href="?search=New" class="flex items-center justify-between px-4 py-2.5 text-[10px] text-slate-400 hover:bg-cyan-500/10 hover:text-cyan-500 transition-all uppercase tracking-widest font-black">New Leads <i class="ph ph-sparkle text-sm"></i></a>
                    </div>
                </div>
                <div class="hidden sm:block w-px h-6 bg-white/10 mx-1"></div>
                <div class="relative flex-1 sm:flex-none">
                    <button onclick="toggleDropdown('group-menu', event)" class="w-full px-4 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 hover:text-purple-500 hover:bg-purple-500/5 rounded-xl flex items-center justify-center sm:justify-start gap-2 transition-all border border-transparent hover:border-purple-500/20 active:scale-95">
                        <i class="ph ph-stack-bold text-base"></i> <span class="hidden sm:inline">Grouping</span>
                    </button>
                    <div id="group-menu" class="hidden absolute right-0 top-12 w-56 bg-[#161d27] border border-white/10 rounded-2xl shadow-premium py-2 z-[200] backdrop-blur-2xl">
                        <a href="?group=stage" class="block px-4 py-2 text-[10px] text-slate-400 hover:bg-white/5 hover:text-white transition-all uppercase tracking-widest font-black">By Sector (Stage)</a>
                        <a href="?group=salesperson" class="block px-4 py-2 text-[10px] text-slate-400 hover:bg-white/5 hover:text-white transition-all uppercase tracking-widest font-black">By Handler (Sales)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($flash = getFlash()): ?>
        <div class="m-6 mb-0 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2 shrink-0">
            <i class="ph ph-check-circle text-lg"></i>
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <!-- Board/List Container -->
    <main class="flex-1 overflow-x-auto overflow-y-hidden p-6 pt-2 crm-main-scroll">
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
            <div class="flex h-full lg:flex-row flex-col lg:flex-nowrap gap-6 items-start pb-8" id="stages-wrapper" style="min-width: 0;">
                
                <?php foreach ($pipeline as $stageName => $stageData): 
                    $opps = $stageData['opportunities'];
                    $info = $stageData['info'];
                    $isCollapsed = (bool)$info['is_collapsed'];
                ?>
                
                <?php if ($isCollapsed): ?>
                    <!-- Collapsed Stage -->
                    <div class="lg:h-[calc(100vh-280px)] h-16 shrink-0 flex items-center border border-white/5 rounded-2xl lg:pt-6 lg:pb-4 lg:w-16 w-full cursor-pointer hover:bg-white/[0.03] transition-all bg-white/[0.01] group/collapsed shadow-premium px-4 lg:flex-col" onclick="toggleCollapse(<?= $info['id'] ?>)">
                        <div class="text-slate-600 mb-6 group-hover/collapsed:text-neon-cyan transition-colors" title="Expand Stage">
                            <i class="ph-bold ph-arrows-out-line-horizontal text-xl"></i>
                        </div>
                        <div class="flex-1 w-full flex lg:justify-center lg:mt-2 relative">
                            <div class="lg:transform lg:rotate-180" style="writing-mode: horizontal-tb; text-orientation: mixed;">
                                <span class="text-slate-500 font-black tracking-[0.3em] uppercase text-[10px] flex items-center gap-4 group-hover/collapsed:text-white transition-colors lg:[writing-mode:vertical-rl]">
                                    <span class="text-neon-cyan"><?= count($opps) ?> Deals</span>
                                    <span class="opacity-20">/ / /</span>
                                    <?= htmlspecialchars($stageName) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Expanded Stage -->
                    <div class="w-full lg:w-[320px] flex flex-col lg:h-[calc(100vh-280px)] shrink-0 stage-column group/stage" data-stage-id="<?= $info['id'] ?>" data-stage-name="<?= htmlspecialchars($stageName) ?>">
                        <!-- Stage Header -->
                        <div class="flex items-center justify-between mb-4 px-2 relative">
                            <div class="opacity-0 group-hover/stage:opacity-100 cursor-grab text-slate-600 hover:text-neon-cyan transition-all mr-2 stage-drag-handle">
                                <i class="ph-bold ph-dots-six-vertical text-lg"></i>
                            </div>
                            
                            <h3 class="font-black text-white text-[10px] uppercase tracking-[0.2em] flex-1 truncate flex items-center gap-3">
                                <?= htmlspecialchars($stageName) ?>
                                <span class="px-2 py-0.5 bg-white/5 text-slate-500 text-[8px] rounded-md font-black italic opp-count border border-white/5"><?= count($opps) ?></span>
                            </h3>
                            
                            <div class="relative">
                                <button type="button" class="text-slate-500 hover:text-white p-1.5 rounded-lg hover:bg-white/5 transition-all active:scale-90" onclick="toggleDropdown('stage-menu-<?= $info['id'] ?>', event)">
                                    <i class="ph-bold ph-dots-three text-lg"></i>
                                </button>
                                <div id="stage-menu-<?= $info['id'] ?>" class="hidden absolute right-0 top-10 w-44 bg-glass-bg border border-white/10 rounded-xl shadow-premium py-2 z-[200] backdrop-blur-2xl">
                                    <button onclick="toggleCollapse(<?= $info['id'] ?>)" class="w-full text-left px-4 py-2 text-[10px] font-black text-slate-400 hover:bg-white/5 hover:text-white flex items-center gap-2 uppercase tracking-widest border-b border-white/5 mb-1 transition-all">
                                        <i class="ph ph-arrows-in-line-horizontal text-base"></i> Collapse Stage
                                    </button>
                                    <button type="button" onclick="deleteStage(<?= $info['id'] ?>)" class="w-full text-left px-4 py-2 text-[10px] font-black text-neon-rose/60 hover:bg-neon-rose/5 hover:text-neon-rose flex items-center gap-2 uppercase tracking-widest transition-all">
                                        <i class="ph ph-trash text-base"></i> Delete Stage
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sector Grid (Kanban Body) -->
                        <div class="flex-1 overflow-y-auto crm-main-scroll rounded-2xl bg-white/[0.01] border border-white/[0.03] p-4 pb-[200px] flex flex-col gap-4 kanban-column min-h-[300px] lg:min-h-[150px] shadow-inner transition-colors hover:border-white/5" data-stage="<?= htmlspecialchars($stageName) ?>">
                            
                            <?php foreach ($opps as $opp): ?>
                                <div class="kanban-card admin-stat-card !p-4 !bg-glass-bg border border-white/5 shadow-premium cursor-grab active:cursor-grabbing hover:border-neon-cyan/40 hover:bg-white/[0.05] transition-all relative group/card shrink-0 min-h-[140px] <?= !empty($opp['color_code']) ? 'crm-color-'.$opp['color_code'] : '' ?>" 
                                     draggable="true" data-id="<?= $opp['id'] ?>" onclick="if(!document.body.classList.contains('is-dragging')) window.location.href='<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>'">
                                    
                                    <!-- Options Link -->
                                    <button type="button" title="Quick Access" onclick="toggleDropdown('card-menu-<?= $opp['id'] ?>', event)" class="absolute top-2 right-2 opacity-0 group-hover/card:opacity-100 transition-all p-1 text-slate-600 hover:text-neon-cyan hover:bg-white/5 rounded-md active:scale-75">
                                        <i class="ph-bold ph-dots-three-vertical text-base"></i>
                                    </button>

                                    <!-- Quick Context Menu -->
                                    <div id="card-menu-<?= $opp['id'] ?>" onclick="event.stopPropagation()" class="hidden absolute right-2 top-10 w-64 bg-[#161d27] border border-white/10 rounded-2xl shadow-2xl p-5 z-[300] backdrop-blur-3xl">
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

                                    <!-- Opportunity Title -->
                                    <div class="mb-2 pr-4">
                                        <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="font-black text-white group-hover/card:text-neon-cyan transition-colors text-[11px] uppercase tracking-wide line-clamp-2 leading-relaxed">
                                            <?= htmlspecialchars($opp['title']) ?>
                                        </a>
                                    </div>

                                    <!-- Organization/Client -->
                                    <?php if (!empty($opp['contact_name'])): ?>
                                        <div class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-3 flex items-center gap-1.5 px-2 py-1 bg-white/[0.03] rounded-md border border-white/5 w-fit">
                                            <i class="ph ph-buildings opacity-50"></i>
                                            <?= htmlspecialchars($opp['contact_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Priority & Forecast -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-0.5">
                                            <?php for($s=1; $s<=3; $s++): ?>
                                                <i class="ph-fill ph-circle text-[6px] <?= $s <= $opp['priority'] ? 'text-neon-amber' : 'text-slate-800' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <?php if ($opp['expected_revenue'] > 0): ?>
                                            <span class="text-[10px] font-black text-neon-emerald tracking-tight">
                                                $<?= number_format($opp['expected_revenue'] / 1000, 1) ?>K
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Personnel Assignment (FIXED) -->
                                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-white/[0.03] text-[9px] text-slate-500 font-black">
                                        <div class="flex items-center gap-3">
                                            <span class="flex items-center gap-1.5 opacity-60"><i class="ph ph-clock text-slate-700"></i> <?= date('d M', strtotime($opp['created_at'])) ?></span>
                                            <?php if ($opp['probability'] > 0): ?>
                                                <span class="text-neon-cyan/60"><?= $opp['probability'] ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                            $spName = '--';
                                            $spEmoji = '';
                                            $spTitle = 'Unassigned Pulse';
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
                                            <span class="text-[7px] text-slate-700 uppercase tracking-tighter hidden group-hover/card:block transition-all"><?= explode(' ', $spTitle)[0] ?></span>
                                            <div class="w-7 h-7 rounded-xl flex items-center justify-center text-[10px] font-black border transition-all <?= $isActiveHandler ? 'bg-neon-cyan/10 border-neon-cyan/30 text-neon-cyan shadow-[0_0_10px_rgba(6,182,212,0.1)]' : 'bg-white/5 border-white/10 text-slate-700' ?>">
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

                            <div class="drop-indicator hidden h-1 bg-neon-cyan/20 rounded-full my-2 w-full border border-dashed border-neon-cyan/30"></div>
                            
                            <div class="mt-2 opacity-0 group-hover/stage:opacity-100 transition-all transform translate-y-2 group-hover/stage:translate-y-0">
                                <button onclick="openLeadModal('<?= htmlspecialchars($stageName) ?>')" class="w-full py-3 bg-white/[0.02] border border-dashed border-white/10 rounded-xl text-slate-600 hover:text-neon-cyan hover:border-neon-cyan/50 hover:bg-neon-cyan/5 transition-all text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2">
                                    <i class="ph ph-plus-circle text-base"></i> Sync New Lead
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
    // System Initialization & Drag Dynamics
    let draggedItem = null;
    let targetStage = null;
    const body = document.body;

    // Pulse Effects & Hover Interactions
    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedItem = this;
            body.classList.add('is-dragging');
            this.classList.add('dragging');
            e.dataTransfer.setData('text/plain', this.dataset.id);
            // Multi-dimensional shift
            setTimeout(() => this.style.display = 'none', 0);
        });

        card.addEventListener('dragend', function() {
            draggedItem = null;
            body.classList.remove('is-dragging');
            this.classList.remove('dragging');
            this.style.display = 'block';
            document.querySelectorAll('.drop-indicator').forEach(di => di.classList.add('hidden'));
        });

        // 3D Tilt Reality Factor
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

    // Sector Management (Columns)
    document.querySelectorAll('.kanban-column').forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            const indicator = this.querySelector('.drop-indicator');
            if (indicator) indicator.classList.remove('hidden');
            this.classList.add('drag-over');
        });

        column.addEventListener('dragleave', function() {
            const indicator = this.querySelector('.drop-indicator');
            if (indicator) indicator.classList.add('hidden');
            this.classList.remove('drag-over');
        });

        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const oppId = e.dataTransfer.getData('text/plain');
            const stageName = this.dataset.stage;
            const indicator = this.querySelector('.drop-indicator');
            if (indicator) indicator.classList.add('hidden');

            if (draggedItem && oppId && stageName) {
                this.insertBefore(draggedItem, indicator);
                
                // Real-time Sector Uplink
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
            }
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
        document.querySelectorAll('[id^="filter-"], [id^="group-"], [id^="fav-"], [id^="stage-menu-"], [id^="card-menu-"]').forEach(d => {
            d.classList.add('hidden');
        });

        if (!isOpen) el.classList.remove('hidden');
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
    </div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
