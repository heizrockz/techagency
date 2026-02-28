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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL) ?>/assets/css/style.css">
    <style> .kanban-column { min-height: 200px; } </style>
</head>
<body>
<div class="admin-layout flex w-full">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="crm-main leading-relaxed text-slate-300">
    <!-- Topbar -->
    <header class="p-6 pb-2 shrink-0">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <h1 class="text-2xl font-bold text-white flex items-center gap-3">
                <i class="ph ph-kanban text-primary"></i>
                Pipeline
            </h1>
            <div class="flex items-center gap-3">
                <!-- View Switcher -->
                <div class="flex bg-slate-800 rounded-lg p-1 border border-white/10">
                    <a href="?view=kanban&search=<?= urlencode($search) ?>" class="p-2 rounded-md transition-colors <?= $view === 'kanban' ? 'bg-primary text-white' : 'text-slate-400 hover:text-white' ?>" title="Kanban View">
                        <i class="ph ph-layout text-lg"></i>
                    </a>
                    <a href="?view=list&search=<?= urlencode($search) ?>" class="p-2 rounded-md transition-colors <?= $view === 'list' ? 'bg-primary text-white' : 'text-slate-400 hover:text-white' ?>" title="List View">
                        <i class="ph ph-list text-lg"></i>
                    </a>
                </div>
                <button onclick="document.getElementById('addLeadModal').classList.remove('hidden')" class="btn-primary">
                    <i class="ph ph-plus mr-2"></i> New Lead
                </button>
            </div>
        </div>

        <div class="flex items-center gap-4 bg-slate-800/30 p-2 rounded-xl border border-white/5">
            <div class="flex-1 relative">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <form action="" method="GET" class="w-full">
                    <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Search leads, email, or phone..." 
                           class="w-full bg-slate-900/40 border-none focus:ring-1 focus:ring-primary rounded-lg pl-10 pr-4 py-2 text-sm text-white transition-all">
                </form>
            </div>
            <div class="hidden md:flex items-center gap-2">
                <div class="relative">
                    <button onclick="toggleDropdown('filter-menu', event)" class="px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-white/5 rounded-lg flex items-center gap-2 transition-colors">
                        <i class="ph ph-funnel"></i> Filters
                    </button>
                    <div id="filter-menu" class="hidden absolute right-0 top-10 w-48 bg-slate-800 border border-white/10 rounded-lg shadow-xl py-1 z-[100]">
                        <a href="?search=Won" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Won Deals</a>
                        <a href="?search=Lost" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Lost Deals</a>
                        <a href="?search=New" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">New Leads</a>
                    </div>
                </div>
                <div class="relative">
                    <button onclick="toggleDropdown('group-menu', event)" class="px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-white/5 rounded-lg flex items-center gap-2 transition-colors">
                        <i class="ph ph-stack"></i> Group By
                    </button>
                    <div id="group-menu" class="hidden absolute right-0 top-10 w-48 bg-slate-800 border border-white/10 rounded-lg shadow-xl py-1 z-[100]">
                        <a href="?group=stage" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">By Stage</a>
                        <a href="?group=salesperson" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">By Salesperson</a>
                    </div>
                </div>
                <div class="relative">
                    <button onclick="toggleDropdown('fav-menu', event)" class="px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-white/5 rounded-lg flex items-center gap-2 transition-colors">
                        <i class="ph ph-star"></i> Favorites
                    </button>
                    <div id="fav-menu" class="hidden absolute right-0 top-10 w-48 bg-slate-800 border border-white/10 rounded-lg shadow-xl py-1 z-[100]">
                        <a href="#" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Save Current Search</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <?php if ($flash = getFlash()): ?>
        <div class="m-6 mb-0 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2 shrink-0">
            <i class="ph ph-check-circle text-lg"></i>
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <!-- Board/List Container -->
    <main class="flex-1 overflow-x-auto overflow-y-hidden p-6 pt-2 crm-main-scroll">
        <?php if ($view === 'list'): ?>
            <!-- List View -->
            <div class="h-full overflow-y-auto pr-2">
                <table class="crm-list-table">
                    <thead>
                        <tr class="!bg-transparent text-slate-400 text-xs uppercase tracking-wider text-left">
                            <th class="font-medium">Opportunity</th>
                            <th class="font-medium">Contact</th>
                            <th class="font-medium">Stage</th>
                            <th class="font-medium">Revenue</th>
                            <th class="font-medium">Created</th>
                            <th class="font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($opportunities as $opp): ?>
                        <tr class="group">
                            <td class="flex items-center gap-3">
                                <div class="w-1 h-8 rounded-full <?= !empty($opp['color_code']) ? 'crm-color-'.$opp['color_code'] : 'bg-slate-700' ?>"></div>
                                <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="text-white font-medium hover:text-primary transition-colors">
                                    <?= htmlspecialchars($opp['title']) ?>
                                </a>
                            </td>
                            <td class="text-sm text-slate-400">
                                <?php if ($opp['email']): ?>
                                    <div class="flex items-center gap-1.5"><i class="ph ph-envelope"></i> <?= htmlspecialchars($opp['email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-xs bg-slate-700 text-slate-300 px-2.5 py-1 rounded-full border border-white/5">
                                    <?= htmlspecialchars($opp['stage']) ?>
                                </span>
                            </td>
                            <td class="text-emerald-400 font-medium">
                                $<?= number_format($opp['expected_revenue'], 2) ?>
                            </td>
                            <td class="text-sm text-slate-500">
                                <?= date('M d, Y', strtotime($opp['created_at'])) ?>
                            </td>
                            <td class="text-right">
                                <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="text-slate-400 hover:text-white p-2">
                                    <i class="ph ph-arrow-square-out text-lg"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Kanban View -->
            <div class="flex h-full gap-3 items-start pb-4" id="stages-wrapper" style="min-width: max-content;">
                
                <?php foreach ($pipeline as $stageName => $stageData): 
                    $opps = $stageData['opportunities'];
                    $info = $stageData['info'];
                    $isCollapsed = (bool)$info['is_collapsed'];
                ?>
                
                <?php if ($isCollapsed): ?>
                    <!-- Collapsed Column -->
                    <div class="h-full shrink-0 flex flex-col items-center border border-white/5 rounded-xl pt-4 pb-2 w-14 cursor-pointer hover:bg-white/5 transition-colors" onclick="toggleCollapse(<?= $info['id'] ?>)">
                        <div class="text-white/40 mb-4 hover:text-white transition-colors" title="Expand Stage">
                            <i class="ph ph-arrows-out-line-horizontal text-lg"></i>
                        </div>
                        <div class="flex-1 w-full flex justify-center mt-2 relative">
                            <div class="transform rotate-180" style="writing-mode: vertical-rl; text-orientation: mixed;">
                                <span class="text-white font-semibold tracking-widest uppercase text-sm flex items-center gap-3">
                                    <?= count($opps) ?>
                                    <span class="text-slate-500">•</span>
                                    <?= htmlspecialchars($stageName) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Expanded Column -->
                    <div class="w-[260px] flex flex-col h-full shrink-0 stage-column" data-stage-id="<?= $info['id'] ?>" data-stage-name="<?= htmlspecialchars($stageName) ?>">
                        <!-- Column Header -->
                        <div class="flex items-center justify-between mb-2 px-1 group relative">
                            <div class="opacity-0 group-hover:opacity-100 cursor-grab text-slate-500 hover:text-white transition-all mr-2 stage-drag-handle">
                                <i class="ph ph-dots-six-vertical"></i>
                            </div>
                            
                            <h3 class="font-bold text-white text-xs uppercase tracking-wider flex-1 truncate pr-2 flex items-center gap-2">
                                <?= htmlspecialchars($stageName) ?>
                                <span class="bg-white/5 text-white/40 text-[9px] py-0.5 px-1.5 rounded-full font-bold opp-count"><?= count($opps) ?></span>
                            </h3>
                            
                            <div class="relative">
                                <button type="button" class="text-slate-400 hover:text-white p-1 rounded hover:bg-white/10 transition-colors" onclick="toggleDropdown('stage-menu-<?= $info['id'] ?>', event)">
                                    <i class="ph ph-gear"></i>
                                </button>
                                <div id="stage-menu-<?= $info['id'] ?>" class="hidden absolute right-0 top-8 w-40 bg-slate-800 border border-white/10 rounded-lg shadow-xl py-1 z-50">
                                    <button onclick="toggleCollapse(<?= $info['id'] ?>)" class="w-full text-left px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-2">
                                        <i class="ph ph-arrows-in-line-horizontal"></i> Fold
                                    </button>
                                    <hr class="border-white/5 my-1">
                                    <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" onsubmit="return confirm('Delete this stage?');">
                                        <input type="hidden" name="action" value="delete_stage">
                                        <input type="hidden" name="id" value="<?= $info['id'] ?>">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-400/10 flex items-center gap-2">
                                            <i class="ph ph-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Column Body -->
                        <div class="flex-1 overflow-y-auto rounded-xl kanban-column-body p-2 flex flex-col gap-2 kanban-column min-h-[150px]" data-stage="<?= htmlspecialchars($stageName) ?>">
                            
                            <?php foreach ($opps as $opp): ?>
                                <div class="kanban-card bg-transparent rounded-lg p-2.5 border border-white/5 shadow-sm cursor-grab active:cursor-grabbing hover:border-primary/50 hover:bg-white/[0.02] transition-all relative group <?= !empty($opp['color_code']) ? 'crm-color-'.$opp['color_code'] : '' ?>" 
                                     draggable="true" data-id="<?= $opp['id'] ?>" onclick="if(!document.body.classList.contains('is-dragging')) window.location.href='<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>'">
                                    
                                    <!-- Context Menu Toggle -->
                                    <button title="Options" onclick="toggleDropdown('card-menu-<?= $opp['id'] ?>', event)" class="absolute top-1.5 right-1.5 opacity-0 group-hover:opacity-100 transition-opacity p-1 text-slate-500 hover:text-white">
                                        <i class="ph ph-dots-three-vertical-bold"></i>
                                    </button>

                                    <!-- Color Picker & Actions Dropdown -->
                                    <div id="card-menu-<?= $opp['id'] ?>" class="hidden absolute right-2 top-8 w-48 bg-slate-800 border border-white/10 rounded-lg shadow-2xl p-3 z-50">
                                        <div class="text-[10px] text-slate-500 mb-2 font-bold uppercase tracking-widest">Quick Actions</div>
                                        <div class="space-y-1 mb-4">
                                            <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="w-full text-left px-2 py-1.5 text-xs text-slate-300 hover:bg-white/5 hover:text-white flex items-center gap-2 rounded transition-colors">
                                                <i class="ph ph-eye"></i> View Details
                                            </a>
                                            <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" onsubmit="return confirm('Delete this opportunity?');">
                                                <input type="hidden" name="action" value="delete_opportunity">
                                                <input type="hidden" name="id" value="<?= $opp['id'] ?>">
                                                <button type="submit" class="w-full text-left px-2 py-1.5 text-xs text-red-400 hover:bg-red-400/10 flex items-center gap-2 rounded transition-colors">
                                                    <i class="ph ph-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <div class="text-[10px] text-slate-500 mb-2 font-bold uppercase tracking-widest">Card Color</div>
                                        <div class="grid grid-cols-4 gap-2">
                                            <?php $colors = ['none', 'red', 'orange', 'yellow', 'green', 'blue', 'purple', 'teal', 'pink']; ?>
                                            <?php foreach ($colors as $c): ?>
                                                <div onclick="updateCardColor(<?= $opp['id'] ?>, '<?= $c ?>')" 
                                                     class="w-7 h-7 rounded-md cursor-pointer border border-white/10 hover:scale-110 transition-transform flex items-center justify-center
                                                            <?= $c === 'none' ? 'bg-slate-600' : 'bg-'.$c.'-500' ?>" 
                                                     title="<?= ucfirst($c) ?>">
                                                     <?php if($c === 'none'): ?><i class="ph ph-prohibit text-slate-400 text-[10px]"></i><?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <!-- Card Title -->
                                    <div class="mb-1.5 pr-5">
                                        <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opp['id'] ?>" class="font-medium text-white hover:text-primary transition-colors text-xs line-clamp-2 leading-snug">
                                            <?= htmlspecialchars($opp['title']) ?>
                                        </a>
                                    </div>

                                    <!-- Customer Name -->
                                    <?php if (!empty($opp['contact_name'])): ?>
                                        <div class="text-[11px] text-slate-400 mb-2 truncate"><?= htmlspecialchars($opp['contact_name']) ?></div>
                                    <?php endif; ?>
                                    
                                    <!-- Tags Row -->
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        <?php if ($opp['priority'] > 0): ?>
                                            <div class="flex items-center gap-0.5">
                                                <?php for($s=1; $s<=$opp['priority']; $s++): ?>
                                                    <i class="ph-fill ph-star text-amber-400 text-[10px]"></i>
                                                <?php endfor; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($opp['expected_revenue'] > 0): ?>
                                            <span class="text-xs bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded ml-auto">
                                                $<?= number_format($opp['expected_revenue'] / 1000, 1) ?>k
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Footer: Date + Activity icon + Avatar -->
                                    <div class="flex items-center justify-between mt-auto pt-2 border-t border-white/5 text-[10px] text-slate-500">
                                        <div class="flex items-center gap-1.5">
                                            <span class="flex items-center gap-1"><i class="ph ph-clock text-slate-600"></i> <?= date('M d', strtotime($opp['created_at'])) ?></span>
                                            <?php if ($opp['probability'] > 0): ?>
                                                <span class="opacity-60"><?= $opp['probability'] ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="w-5 h-5 rounded-full bg-primary/80 flex items-center justify-center text-[9px] font-bold text-white" title="Administrator">A</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="drop-indicator hidden h-1 bg-primary rounded my-1 w-full"></div>
                            
                            <div class="mt-2 text-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="openLeadModal('<?= htmlspecialchars($stageName) ?>')" class="text-slate-400 hover:text-white text-sm flex items-center justify-center gap-1 w-full py-2 hover:bg-white/5 rounded-lg border border-dashed border-white/10">
                                    <i class="ph ph-plus"></i> Add Lead
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
                
                <div class="w-14 shrink-0 flex flex-col items-center pt-2">
                    <button onclick="document.getElementById('addStageModal').classList.remove('hidden')" class="w-10 h-10 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition-all shadow-sm" title="Add Stage">
                        <i class="ph ph-plus text-lg"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- Add Lead Modal -->
<div id="addLeadModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300" style="opacity: 1;">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md shadow-2xl scale-95 transition-transform duration-300 transform translate-y-0 relative">
        <div class="p-6 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Quick Add Lead</h3>
            <button type="button" onclick="document.getElementById('addLeadModal').classList.add('hidden')" class="text-slate-400 hover:text-white transition-colors">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="p-6">
            <input type="hidden" name="action" value="quick_add">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-2">Opportunity Title</label>
                <input type="text" name="title" required class="form-input" placeholder="e.g. Website Redesign for XYZ Corp">
            </div>

            <div class="mb-4 flex items-center gap-8">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Priority</label>
                    <div class="flex items-center flex-row-reverse justify-end gap-1 crm-star-rating">
                        <?php for ($i = 3; $i >= 1; $i--): ?>
                            <input type="radio" name="priority" value="<?= $i ?>" id="prio_new_<?= $i ?>" class="hidden peer" <?= $i === 1 ? 'checked' : '' ?>>
                            <label for="prio_new_<?= $i ?>" class="cursor-pointer text-slate-600 peer-checked:text-amber-400 hover:text-amber-400 peer-checked:hover:text-amber-500 transition-colors">
                                <i class="ph-fill ph-star text-2xl drop-shadow-sm"></i>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Color</label>
                    <div class="flex items-center gap-1.5">
                        <?php $colors = ['red', 'orange', 'yellow', 'green', 'blue', 'purple', 'teal', 'pink']; ?>
                        <?php foreach($colors as $c): ?>
                            <label class="w-4 h-4 rounded-full bg-<?= $c ?>-500 cursor-pointer border border-white/10 ring-offset-2 ring-offset-slate-900 peer-checked:ring-2 block">
                                <input type="radio" name="color_code" value="<?= $c ?>" class="hidden peer">
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">Initial Stage</label>
                <select name="stage" id="leadStageSelect" class="form-input">
                    <?php foreach ($stages as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addLeadModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Add Lead</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Stage Modal -->
<div id="addStageModal" class="hidden fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 opacity-0 transition-opacity duration-300" style="opacity: 1;">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-sm shadow-2xl scale-95 transition-transform duration-300 transform translate-y-0 relative">
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Create Stage</h3>
            <button type="button" onclick="document.getElementById('addStageModal').classList.add('hidden')" class="text-slate-400 hover:text-white transition-colors">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="p-5">
            <input type="hidden" name="action" value="add_stage">
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">Stage Name</label>
                <input type="text" name="name" required class="form-input" placeholder="e.g. In Negotiation">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addStageModal').classList.add('hidden')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Add Stage</button>
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
// Global UI Helpers
function updateCardColor(id, color) {
    const formData = new FormData();
    formData.append('action', 'update_color');
    formData.append('id', id);
    formData.append('color', color);

    fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => {
          if(data.success) {
              const card = document.querySelector(`.kanban-card[data-id="${id}"]`);
              // Remove old color classes
              const colors = ['none', 'red', 'orange', 'yellow', 'green', 'blue', 'purple', 'teal', 'pink'];
              colors.forEach(c => card.classList.remove(`crm-color-${c}`));
              
              if (color !== 'none') {
                  card.classList.add(`crm-color-${color}`);
              }
              // Close menu
              document.getElementById(`card-menu-${id}`).classList.add('hidden');
          } else {
              alert('Failed to update color.');
          }
      });
}

function openLeadModal(preselectStage = null) {
    document.getElementById('addLeadModal').classList.remove('hidden');
    if (preselectStage) {
        let select = document.getElementById('leadStageSelect');
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === preselectStage) {
                select.selectedIndex = i;
                break;
            }
        }
    }
}

function toggleCollapse(id) {
    document.getElementById('collapseId').value = id;
    document.getElementById('collapseForm').submit();
}

function toggleDropdown(id, event) {
    event.stopPropagation();
    // Close other dropdowns
    document.querySelectorAll('[id^=stage-menu-]').forEach(el => {
        if (el.id !== id) el.classList.add('hidden');
    });
    const menu = document.getElementById(id);
    menu.classList.toggle('hidden');
}

// Close Dropdowns on outside click
document.addEventListener('click', (e) => {
    document.querySelectorAll('[id^=stage-menu-]').forEach(el => el.classList.add('hidden'));
});
// Kanban Drag and Drop Logic (Cards)
document.addEventListener('DOMContentLoaded', () => {
    // ---- OPPORTUNITY CARD DRAGGING ----
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-column');
    let draggedCard = null;

    cards.forEach(card => {
        card.addEventListener('dragstart', (e) => {
            draggedCard = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            // Set a transparent ghost image if needed, but default is fine
        });

        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            draggedCard = null;
        });
    });

    columns.forEach(column => {
        // column IS the body now because of the class mismatch in PHP
        const body = column; 
        
        body.addEventListener('dragover', e => {
            e.preventDefault();
            if (draggedCard) {
                body.classList.add('drag-over');
                document.body.classList.add('is-dragging');
            }
        });

        body.addEventListener('dragleave', () => {
            body.classList.remove('drag-over');
        });

        body.addEventListener('drop', e => {
            e.preventDefault();
            body.classList.remove('drag-over');
            document.body.classList.remove('is-dragging');
            
            if (draggedCard) {
                const dropIndicator = body.querySelector('.drop-indicator');
                body.insertBefore(draggedCard, dropIndicator);

                const newStage = body.getAttribute('data-stage');
                const cardId = draggedCard.getAttribute('data-id');

                // AJAX call to update stage
                const formData = new FormData();
                formData.append('action', 'update_stage');
                formData.append('id', cardId);
                formData.append('stage', newStage);

                fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
                  .then(data => {
                      if(!data.success) {
                          alert('Failed to update stage.');
                      } else {
                          updateColumnCounts();
                      }
                  });
            }
        });
    });

    function updateColumnCounts() {
        document.querySelectorAll('.stage-column').forEach(col => {
            const count = col.querySelectorAll('.kanban-card').length;
            const countEl = col.querySelector('.opp-count');
            if (countEl) countEl.innerText = count;
        });
    }

    // ---- STAGE COLUMN DRAGGING ----
    const stageWrapper = document.getElementById('stages-wrapper');
    const stageColumns = document.querySelectorAll('.stage-column');
    let draggedStage = null;

    stageColumns.forEach(stage => {
        const handle = stage.querySelector('.stage-drag-handle');
        if (!handle) return;
        
        handle.setAttribute('draggable', 'true');
        
        handle.addEventListener('dragstart', (e) => {
            draggedStage = stage;
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(() => stage.classList.add('opacity-50'), 0);
        });

        stage.addEventListener('dragover', function (e) {
            e.preventDefault();
            if (draggedStage && draggedStage !== stage) {
                const rect = stage.getBoundingClientRect();
                const offset = e.clientX - rect.left;
                if (offset > rect.width / 2) {
                    stage.after(draggedStage);
                } else {
                    stage.before(draggedStage);
                }
            }
        });

        stage.addEventListener('dragend', function () {
            draggedStage.classList.remove('opacity-50');
            saveStageOrder();
            draggedStage = null;
        });
    });

    function saveStageOrder() {
        const reorderedStages = document.querySelectorAll('.stage-column');
        const orderData = [];
        reorderedStages.forEach(stg => {
            orderData.push(stg.getAttribute('data-stage-id'));
        });

        const formData = new FormData();
        formData.append('action', 'reorder_stages');
        orderData.forEach(id => formData.append('order[]', id));

        fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline', {
            method: 'POST',
            body: formData
        }).then(response => response.json())
          .then(data => {
              if(!data.success) {
                  console.error('Failed to save stage order');
              }
          });
    }
});
</script>
    </div>
</div>
</body>
</html>
