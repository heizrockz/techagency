<?php
$pageTitle = $opportunity ? $opportunity['title'] : 'New Opportunity';
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
</head>
<body>
<div class="admin-layout flex w-full">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="crm-main leading-relaxed text-slate-300">
    <!-- Top Navigation / Breadcrumbs -->
    <header class="bg-[#1a2333] border-b border-white/5 px-6 py-3 shrink-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 text-sm">
                <a href="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" class="text-slate-400 hover:text-white transition-colors flex items-center gap-1">
                    <i class="ph ph-kanban"></i> Pipeline
                </a>
                <span class="text-slate-600">/</span>
                <span class="text-white font-medium truncate max-w-[200px]"><?= $opportunity ? htmlspecialchars($opportunity['title']) : 'New' ?></span>
            </div>
            <div class="flex items-center gap-3">
                <button class="text-slate-400 hover:text-white p-1"><i class="ph ph-caret-left-bold"></i></button>
                <button class="text-slate-400 hover:text-white p-1"><i class="ph ph-caret-right-bold"></i></button>
            </div>
        </div>
    </header>

    <!-- Action Bar / Stages -->
    <div class="bg-slate-800/30 border-b border-white/5 px-6 py-2 flex flex-wrap items-center justify-between gap-4 shrink-0">
        <div class="flex items-center gap-2">
            <?php if ($opportunity): ?>
                <button type="button" id="editBtn" onclick="toggleEditMode()" class="px-4 py-1.5 rounded bg-white/5 text-slate-300 hover:text-white text-xs font-bold uppercase transition-colors border border-white/10 flex items-center gap-2">
                    <i class="ph ph-note-pencil"></i> Edit
                </button>
                <button type="submit" form="oppForm" id="saveBtn" class="hidden px-4 py-1.5 rounded bg-primary text-white text-xs font-bold uppercase transition-colors border border-primary/20 shadow-lg shadow-primary/10">Save</button>
                <div class="h-6 w-px bg-white/5 mx-1"></div>
                <button onclick="document.getElementById('createInvoiceModal').classList.remove('hidden')" class="btn-ghost py-1.5 px-3 text-xs uppercase tracking-wider font-bold">New Quotation</button>
                <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="inline">
                    <input type="hidden" name="action" value="update_stage">
                    <input type="hidden" name="id" value="<?= $opportunity['id'] ?>">
                    <input type="hidden" name="stage" value="Won">
                    <button type="submit" class="px-3 py-1.5 rounded bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 text-xs font-bold uppercase transition-colors border border-emerald-500/20">Won</button>
                </form>
                <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="inline">
                    <input type="hidden" name="action" value="update_stage">
                    <input type="hidden" name="id" value="<?= $opportunity['id'] ?>">
                    <input type="hidden" name="stage" value="Lost">
                    <button type="submit" class="px-3 py-1.5 rounded bg-red-400/10 text-red-400 hover:bg-red-400/20 text-xs font-bold uppercase transition-colors border border-red-400/20">Lost</button>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Stage Progress Indicator (Odoo Style) -->
        <div class="flex items-center">
            <?php 
            $allStages = ['New Lead', 'Know Your Client', 'Post Casting', 'Quote & Proposal', 'LPO', 'Casting & Production', 'Won'];
            $currentStage = $opportunity ? $opportunity['stage'] : 'New Lead';
            if ($currentStage === 'Lost') $allStages[] = 'Lost';
            
            foreach ($allStages as $s): 
                $isActive = ($currentStage === $s);
            ?>
                <div class="flex items-center">
                    <span class="px-3 py-1 text-[10px] uppercase font-bold tracking-tighter whitespace-nowrap <?= $isActive ? 'text-primary bg-primary/10 rounded-sm' : 'text-slate-500' ?>">
                        <?= htmlspecialchars($s) ?>
                    </span>
                    <?php if ($s !== end($allStages)): ?>
                        <i class="ph ph-caret-right text-slate-700 text-[10px] mx-1"></i>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <main class="flex-1 overflow-hidden flex flex-col lg:flex-row">
        <!-- Scrollable Form Container -->
        <div class="flex-1 overflow-y-auto p-8 border-r border-white/5 bg-slate-900/20">
            <div class="max-w-4xl mx-auto">
                <?php if ($flash = getFlash()): ?>
                    <div class="mb-6 p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-sm flex items-center gap-2">
                        <i class="ph ph-check-circle text-lg"></i>
                        <?= htmlspecialchars($flash) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity<?= $opportunity ? '?id='.$opportunity['id'] : '' ?>" method="POST" id="oppForm" class="opp-read-only">
                    <input type="hidden" name="action" value="save_opportunity">
                    
                    <!-- Opportunity Title Header -->
                    <div class="mb-8">
                        <input type="text" name="title" required 
                               value="<?= $opportunity ? htmlspecialchars($opportunity['title']) : '' ?>" 
                               placeholder="Opportunity Title..."
                               class="w-full bg-transparent border-none text-3xl font-bold text-white placeholder:text-slate-700 focus:ring-0 p-0 mb-2">
                        <div class="flex items-center gap-4">
                             <div class="flex items-center gap-1">
                                <?php for($i=1; $i<=3; $i++): ?>
                                    <i class="ph-fill ph-star <?= ($opportunity && $opportunity['priority'] >= $i) ? 'text-amber-400' : 'text-slate-800' ?> cursor-pointer hover:text-amber-300"></i>
                                <?php endfor; ?>
                             </div>
                             <div class="h-4 w-px bg-white/10"></div>
                             <div class="flex items-center gap-2">
                                <?php $colors = ['red', 'orange', 'yellow', 'green', 'blue', 'purple', 'teal', 'pink']; ?>
                                <?php foreach($colors as $c): ?>
                                    <label class="w-3 h-3 rounded-full bg-<?= $c ?>-500 cursor-pointer border border-white/10 ring-offset-2 ring-offset-slate-900 peer-checked:ring-2 block">
                                        <input type="radio" name="color_code" value="<?= $c ?>" class="hidden peer" <?= ($opportunity && $opportunity['color_code'] === $c) ? 'checked' : '' ?>>
                                    </label>
                                <?php endforeach; ?>
                             </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 mb-10">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1 flex items-center gap-1">
                                    Expected Revenue <i class="ph ph-question text-slate-600 cursor-help" title="Potential income from this deal"></i>
                                </label>
                                <div class="flex items-center gap-2 group">
                                    <span class="text-slate-400 font-medium">AED</span>
                                    <input type="number" step="0.01" name="expected_revenue" value="<?= $opportunity ? htmlspecialchars($opportunity['expected_revenue']) : '0.00' ?>" 
                                           class="bg-transparent border-b border-white/5 focus:border-primary border-t-0 border-l-0 border-r-0 rounded-none w-full p-0 py-1 text-white font-semibold focus:ring-0 transition-colors">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Probability</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" step="0.1" name="probability" value="<?= $opportunity ? htmlspecialchars($opportunity['probability']) : '0' ?>" 
                                           class="bg-transparent border-b border-white/5 focus:border-primary border-t-0 border-l-0 border-r-0 rounded-none w-32 p-0 py-1 text-white font-semibold focus:ring-0 transition-colors">
                                    <span class="text-slate-400">%</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Customer</label>
                                <select name="contact_id" class="bg-transparent border-b border-white/5 focus:border-primary border-t-0 border-l-0 border-r-0 rounded-none w-full p-0 py-1 text-white font-medium focus:ring-0 transition-colors cursor-pointer appearance-none">
                                    <option value="" class="bg-slate-900">-- Select Contact --</option>
                                    <?php foreach ($contacts as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($opportunity && $opportunity['contact_id'] == $c['id']) ? 'selected' : '' ?> class="bg-slate-900">
                                            <?= htmlspecialchars($c['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Salesperson</label>
                                <div class="flex items-center gap-2 p-1 bg-white/5 rounded-lg border border-white/5">
                                    <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-[10px] font-bold">AD</div>
                                    <span class="text-sm text-slate-300">Administrator</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Email</label>
                                <input type="email" name="email" value="<?= $opportunity ? htmlspecialchars($opportunity['email']) : '' ?>" 
                                       class="bg-transparent border-b border-white/5 focus:border-primary border-t-0 border-l-0 border-r-0 rounded-none w-full p-0 py-1 text-white font-medium focus:ring-0 transition-colors" placeholder="email@example.com">
                            </div>
                            <div class="h-4 w-px bg-white/10"></div>
                            <div class="flex items-center gap-2">
                                <label class="block text-xs font-semibold text-slate-500 uppercase">Phone</label>
                                <input type="text" name="phone" value="<?= $opportunity ? htmlspecialchars($opportunity['phone']) : '' ?>" 
                                       class="bg-transparent border-b border-white/5 focus:border-primary border-t-0 border-l-0 border-r-0 rounded-none w-full p-0 py-1 text-white font-medium focus:ring-0 transition-colors" placeholder="+971 ...">
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Section -->
                    <div class="border-b border-white/5 flex gap-6 mb-6">
                        <button type="button" class="pb-2 border-b-2 border-primary text-sm font-bold text-white">Internal Notes</button>
                        <button type="button" class="pb-2 border-b-2 border-transparent text-sm font-bold text-slate-500 hover:text-slate-300 transition-colors">Extra Information</button>
                    </div>

                    <!-- Linked Invoices Section -->
                    <?php if (!empty($linkedInvoices)): ?>
                    <div class="mb-10">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase mb-4 flex items-center gap-2">
                            <i class="ph ph-receipt text-lg"></i> Linked Documents
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <?php foreach ($linkedInvoices as $inv): ?>
                                <div class="p-3 bg-slate-800/40 border border-white/5 rounded-xl flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-slate-700 flex items-center justify-center text-primary">
                                            <i class="ph ph-file-text"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white"><?= htmlspecialchars($inv['invoice_number']) ?></div>
                                            <div class="text-[10px] text-slate-500 uppercase tracking-wider"><?= htmlspecialchars($inv['payment_terms']) ?></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="<?= baseUrl('admin/invoices?action=edit&id=' . $inv['id']) ?>" class="p-1.5 text-slate-500 hover:text-white transition-colors" title="Edit">
                                            <i class="ph ph-note-pencil"></i>
                                        </a>
                                        <a href="<?= baseUrl('admin/invoices?action=receipt&id=' . $inv['id']) ?>" target="_blank" class="p-1.5 text-slate-500 hover:text-emerald-400 transition-colors" title="Receipt">
                                            <i class="ph ph-shield-check"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="mb-10 text-center py-6 border-2 border-dashed border-white/5 rounded-2xl group hover:border-primary/20 transition-colors">
                        <button type="button" onclick="document.getElementById('createInvoiceModal').classList.remove('hidden')" class="text-sm text-slate-500 hover:text-primary transition-colors flex flex-col items-center gap-2 mx-auto">
                            <i class="ph ph-plus-circle text-3xl"></i>
                            <span>No invoices yet. Click to create one.</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Description / Internal Notes -->
                    <div class="mb-10">
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-3">Description</label>
                        <textarea name="notes" rows="8" class="w-full bg-[#1a2333] border border-white/10 rounded-2xl p-6 text-white focus:ring-1 focus:ring-primary placeholder:text-slate-700 leading-relaxed shadow-inner" placeholder="Detailed notes about this opportunity..."><?= htmlspecialchars($opportunity['notes'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-between items-center py-6 border-t border-white/5 foot-actions hidden">
                        <button type="submit" class="btn-primary px-8">Save Changes</button>
                        <?php if($opportunity): ?>
                            <span class="text-xs text-slate-600 italic">ID: #<?= $opportunity['id'] ?> • Created <?= date('M d, Y', strtotime($opportunity['created_at'])) ?></span>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Chatter / Timeline -->
        <div class="lg:w-[450px] bg-slate-800/20 border-l border-white/5 flex flex-col shrink-0 overflow-hidden">
            <!-- Chatter Header -->
            <div class="p-4 border-b border-white/5 flex items-center justify-between gap-3 bg-slate-800/50">
                <button type="button" onclick="showLogForm()" class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-colors shadow-sm">Log Note</button>
                <div class="flex items-center gap-4">
                    <div class="relative group">
                        <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-500 group-hover:text-primary transition-colors z-10"></i>
                        <input type="text" id="logNoteSearch" onkeyup="filterLogNotes()" placeholder="Search notes..." class="form-input text-xs py-2 pl-8 !w-48 bg-[#1a2333] border-white/10 hover:border-white/20 focus:border-primary transition-all rounded-lg shadow-inner">
                    </div>
                    <button title="Followers (+1)" class="w-8 h-8 rounded-full bg-slate-800 border border-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition-all"><i class="ph ph-user-plus text-lg"></i></button>
                </div>
            </div>

            <!-- Composer (Odoo Style) -->
            <div id="logForm" class="p-4 bg-slate-800/40 border-b border-white/5 hidden">
                <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=<?= $opportunity['id'] ?>" method="POST" enctype="multipart/form-data" id="chatterForm">
                    <input type="hidden" name="action" value="add_log_note">
                    <input type="hidden" name="note_type" value="note">
                    
                    <textarea name="content" id="logTextarea" rows="3" required 
                              class="w-full bg-slate-900 border border-white/10 rounded-lg p-3 text-sm text-white focus:ring-1 focus:ring-primary placeholder:text-slate-600 mb-3" 
                              placeholder="Log an internal note..."></textarea>
                    
                    <div id="chatterAttachmentPreview" class="mb-3 flex flex-wrap gap-2"></div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 text-slate-400">
                            <label class="cursor-pointer hover:text-white transition-colors" title="Attach file">
                                <i class="ph ph-paperclip text-lg"></i>
                                <input type="file" name="attachment" class="hidden" onchange="handleChatterAttach(this)">
                            </label>
                            <button type="button" class="hover:text-white transition-colors" title="Add emoji"><i class="ph ph-smiley text-lg"></i></button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="showLogForm(false)" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white">Discard</button>
                            <button type="submit" class="px-5 py-1.5 bg-primary text-white text-xs font-bold uppercase rounded-md shadow-lg shadow-primary/20">Log</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Direct Opportunity Attachments (Pinned/Files) -->
            <?php 
            $directAttachments = array_filter($attachments, function($a) {
                return $a['linked_type'] === 'opportunity';
            });
            if (!empty($directAttachments)):
            ?>
                <div class="p-4 bg-slate-800/20 border-b border-white/5">
                    <div class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mb-3">Opportunity Files</div>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($directAttachments as $att): ?>
                            <a href="<?= htmlspecialchars(BASE_URL . $att['file_path']) ?>" target="_blank" class="p-2 bg-slate-900/50 rounded-lg border border-white/5 hover:bg-slate-800 transition-all flex items-center gap-2 max-w-[150px] truncate" title="<?= htmlspecialchars($att['file_name']) ?>">
                                <i class="ph ph-file text-primary"></i>
                                <span class="text-[10px] text-slate-300 truncate"><?= htmlspecialchars($att['file_name']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Timeline Thread -->

            <div class="flex-1 overflow-y-auto overflow-x-hidden p-4 space-y-6 chatter-thread" id="logNotesContainer">
                <?php if (empty($log_notes)): ?>
                    <div class="text-center py-10 opacity-20" id="emptyNotesState">
                        <i class="ph ph-chat-circle-dots text-5xl mb-2"></i>
                        <p class="text-xs font-medium uppercase tracking-widest">Chatter started</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $lastDate = '';
                    foreach ($log_notes as $note): 
                        $noteDate = date('F d, Y', strtotime($note['created_at']));
                        if ($noteDate !== $lastDate):
                            $lastDate = $noteDate;
                    ?>
                        <div class="flex items-center gap-4 py-2 note-date-header">
                            <div class="flex-1 h-px bg-white/5"></div>
                            <span class="text-[10px] uppercase font-bold text-slate-600 tracking-widest"><?= $noteDate ?></span>
                            <div class="flex-1 h-px bg-white/5"></div>
                        </div>
                    <?php endif; ?>

                    <div id="log-note-<?= $note['id'] ?>" class="group relative flex gap-3 log-item <?= !empty($note['is_deleted']) ? 'opacity-50' : '' ?>" data-content="<?= htmlspecialchars(strtolower($note['content'])) ?>">
                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-sm font-bold uppercase shrink-0">
                            <?= substr($note['username'], 0, 1) ?>
                        </div>
                        <div class="flex-1 leading-normal">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-200 text-sm"><?= htmlspecialchars($note['full_name'] ?: $note['username']) ?></span>
                                    <span class="text-[10px] text-slate-600 font-medium">- <?= date('H:i', strtotime($note['created_at'])) ?></span>
                                </div>
                                <?php if (empty($note['is_deleted'])): ?>
                                <button type="button" onclick="deleteLogNote(<?= $note['id'] ?>, <?= $opportunity['id'] ?>)" class="transition-opacity text-slate-500 hover:text-red-400 p-1" title="Delete this log note">
                                    <i class="ph ph-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($note['is_deleted'])): ?>
                                <div class="text-sm italic text-slate-600 flex items-center gap-1.5">
                                    <i class="ph ph-prohibit"></i> This note was deleted
                                </div>
                            <?php else: ?>
                            <div class="text-sm text-slate-400 chatter-content" data-content="<?= htmlspecialchars($note['content']) ?>">
                                <?= parseLogContent($note['content']) ?>
                            </div>

                            <!-- Attachments -->
                            <?php foreach ($attachments as $att): ?>
                                <?php if ($att['linked_id'] == $note['id'] && $att['linked_type'] === 'log_note'): ?>
                                    <div class="mt-3 inline-block">
                                        <?php if (strpos($att['file_type'], 'image') !== false): ?>
                                            <div class="relative group/att">
                                                <img src="<?= htmlspecialchars(BASE_URL . $att['file_path']) ?>" class="max-w-[200px] max-h-[150px] rounded-lg border border-white/10 shadow-lg cursor-zoom-in hover:brightness-110 transition-all">
                                                <a href="<?= htmlspecialchars(BASE_URL . $att['file_path']) ?>" download class="absolute top-2 right-2 bg-black/60 p-1.5 rounded-full text-white opacity-0 group-hover/att:opacity-100 transition-opacity">
                                                    <i class="ph ph-download"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?= htmlspecialchars(BASE_URL . $att['file_path']) ?>" download class="flex items-center gap-3 p-2 bg-slate-900 rounded-lg border border-white/5 hover:bg-slate-800 transition-colors">
                                                <div class="w-10 h-10 bg-slate-800 rounded flex items-center justify-center text-slate-500">
                                                    <i class="ph ph-file-doc text-2xl"></i>
                                                </div>
                                                <div class="flex flex-col flex-1">
                                                    <span class="text-xs font-bold text-slate-300"><?= htmlspecialchars($att['file_name']) ?></span>
                                                    <span class="text-[10px] text-slate-600 uppercase"><?= number_format($att['file_size'] / 1024, 1) ?> KB</span>
                                                </div>
                                                <i class="ph ph-download-simple text-slate-500"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </main>
</div>

<?php 
function parseLogContent($content) {
    // Escape HTML first
    $content = htmlspecialchars($content);
    // Simple URL to Link detection
    $urlPattern = '~(?:https?://([-\w\.]+)+(:\d+)?(/([\w/_\.#-]*(\?\S+)?)?)?)~';
    
    return preg_replace_callback($urlPattern, function($matches) {
        $url = $matches[0];
        $preview = fetchLinkPreview($url); // Call helper in controller
        
        $html = "
        <div class='my-3 border border-white/10 rounded-xl overflow-hidden bg-[#0f1115] group/preview max-w-sm'>
            <a href='$url' target='_blank' rel='noopener' class='block p-3 hover:bg-white/5 transition-colors'>
                <div class='flex items-center gap-3 mb-2'>
                    <div class='w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center text-primary shrink-0'>
                        <i class='ph ph-link-simple text-lg'></i>
                    </div>
                    <div class='truncate'>
                        <div class='text-xs font-bold text-slate-200 truncate'>" . ($preview['title'] ?: $url) . "</div>
                        <div class='text-[10px] text-slate-500 truncate'>$url</div>
                    </div>
                </div>";
        
        if ($preview['image']) {
            $html .= "<img src='{$preview['image']}' class='w-full h-32 object-cover border-t border-white/5 bg-slate-800' onerror='this.style.display=\"none\"'>";
        }
        
        if ($preview['description']) {
            $html .= "<div class='p-3 text-[11px] text-slate-400 border-t border-white/5 leading-relaxed truncate-3'>" . htmlspecialchars($preview['description']) . "</div>";
        }
        
        $html .= "</a></div>";
        return $html;
    }, $content);
}
?>

<!-- Create Invoice Modal -->
<div id="createInvoiceModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Generate Invoices</h3>
            <button onclick="document.getElementById('createInvoiceModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="p-6">
            <input type="hidden" name="action" value="create_invoice">
            <input type="hidden" name="id" value="<?= $opportunity['id'] ?>">
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">Payment Terms (Split Strategy)</label>
                <div class="grid grid-cols-1 gap-3">
                    <label class="relative flex items-center p-3 rounded-xl border border-white/5 bg-white/5 cursor-pointer hover:bg-white/10 transition-colors">
                        <input type="radio" name="split_type" value="100" checked class="text-primary focus:ring-primary bg-slate-800 border-white/10">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-white">Full Payment (100%)</span>
                            <span class="block text-xs text-slate-500">Create a single invoice for the full amount.</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 rounded-xl border border-white/5 bg-white/5 cursor-pointer hover:bg-white/10 transition-colors">
                        <input type="radio" name="split_type" value="50-50" class="text-primary focus:ring-primary bg-slate-800 border-white/10">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-white">Milestone (50% / 50%)</span>
                            <span class="block text-xs text-slate-500">Split into two equal invoices (Upfront & Completion).</span>
                        </div>
                    </label>
                    <label class="relative flex items-center p-3 rounded-xl border border-white/5 bg-white/5 cursor-pointer hover:bg-white/10 transition-colors">
                        <input type="radio" name="split_type" value="30-70" class="text-primary focus:ring-primary bg-slate-800 border-white/10">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-white">Deposit (30% / 70%)</span>
                            <span class="block text-xs text-slate-500">Small upfront deposit and large remaining balance.</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full btn-primary py-3 justify-center text-sm uppercase tracking-widest font-bold">Generate Documents</button>
        </form>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Add Product to Opportunity</h3>
            <button onclick="document.getElementById('addProductModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <form action="<?= htmlspecialchars(BASE_URL) ?>/admin/crm_pipeline" method="POST" class="p-6">
            <input type="hidden" name="action" value="add_opportunity_item">
            <input type="hidden" name="id" value="<?= $opportunity['id'] ?>">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-300 mb-2">Select Product</label>
                <select name="item_id" required class="w-full bg-[#1a2333] border border-white/10 rounded-xl p-3 text-white focus:ring-1 focus:ring-primary">
                    <option value="">-- Choose a product --</option>
                    <?php foreach ($allProducts as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= number_format($p['price'], 2) ?> AED)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-300 mb-2">Quantity</label>
                <input type="number" name="qty" value="1" min="1" class="w-full bg-[#1a2333] border border-white/10 rounded-xl p-3 text-white focus:ring-1 focus:ring-primary">
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addProductModal').classList.add('hidden')" class="flex-1 btn-secondary py-3 justify-center uppercase tracking-widest font-bold">Cancel</button>
                <button type="submit" class="flex-1 btn-primary py-3 justify-center uppercase tracking-widest font-bold">Add to Lists</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showLogForm(show = true) {
        document.getElementById('logForm').classList.toggle('hidden', !show);
        if (show) document.getElementById('logTextarea').focus();
    }

    function handleChatterAttach(input) {
        const preview = document.getElementById('chatterAttachmentPreview');
        preview.innerHTML = '';
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const div = document.createElement('div');
            div.className = 'px-3 py-1 bg-primary/20 text-primary text-[10px] font-bold uppercase rounded-full border border-primary/30 flex items-center gap-2';
            div.innerHTML = `<i class="ph ph-file"></i> ${file.name} <i class="ph ph-x cursor-pointer" onclick="clearAttach()"></i>`;
            preview.appendChild(div);
            // Auto open form if not open
            showLogForm(true);
        }
    }

    function clearAttach() {
        const input = document.querySelector('input[name="attachment"]');
        input.value = '';
        document.getElementById('chatterAttachmentPreview').innerHTML = '';
    }

    function toggleEditMode() {
        const form = document.getElementById('oppForm');
        const editBtn = document.getElementById('editBtn');
        const saveBtn = document.getElementById('saveBtn');
        const foot = document.querySelector('.foot-actions');
        
        const isEditing = !form.classList.contains('opp-read-only');
        
        if (isEditing) {
            form.classList.add('opp-read-only');
            editBtn.classList.remove('hidden');
            saveBtn.classList.add('hidden');
            if(foot) foot.classList.add('hidden');
        } else {
            form.classList.remove('opp-read-only');
            editBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
            if(foot) foot.classList.remove('hidden');
        }
    }

    function deleteLogNote(noteId, oppId) {
        if (!confirm('Delete this log note?')) return;
        
        const formData = new FormData();
        formData.append('action', 'delete_log_note');
        formData.append('note_id', noteId);

        fetch('<?= htmlspecialchars(BASE_URL) ?>/admin/crm_opportunity?id=' + oppId, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // WhatsApp-style "deleted" placeholder
                const noteBlock = document.getElementById('log-note-' + noteId);
                if (noteBlock) {
                    noteBlock.style.transition = 'all 0.3s ease';
                    noteBlock.style.opacity = '0.5';
                    // Replace content area with deleted message
                    const contentArea = noteBlock.querySelector('.chatter-content');
                    if (contentArea) {
                        contentArea.innerHTML = '<span class="italic text-slate-600 text-sm flex items-center gap-1.5"><i class="ph ph-prohibit"></i> This note was deleted</span>';
                    }
                    // Hide attachments
                    noteBlock.querySelectorAll('.mt-3.inline-block').forEach(el => el.remove());
                    // Hide delete button
                    const delBtn = noteBlock.querySelector('button[title="Delete this log note"]');
                    if (delBtn) delBtn.remove();
                }
            } else {
                alert('Failed to delete log note.');
            }
        }).catch((err) => {
            console.error('Delete failed', err);
            // Fallback: Reload the page if JSON parsing fails but request went through
            window.location.reload();
        });
    }

    function filterLogNotes() {
        let input = document.getElementById('logNoteSearch').value.toLowerCase();
        let notes = document.querySelectorAll('.log-item');
        let hasVisible = false;

        notes.forEach(note => {
            if (note.dataset.content.includes(input)) {
                note.style.display = 'flex';
                hasVisible = true;
            } else {
                note.style.display = 'none';
            }
        });

        const headers = document.querySelectorAll('.note-date-header');
        if (input.length > 0) {
            headers.forEach(h => h.style.display = 'none');
        } else {
            headers.forEach(h => h.style.display = 'flex');
        }
    }
</script>
    </div>
</div>
</body>
</html>
