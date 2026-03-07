<?php
// Ensure this file is only accessed through the controller
if (!defined('APP_NAME')) die('Direct access prevented');
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>App Licenses — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <script>
    function addFeatureRow(key = '', val = '') {
        const container = document.getElementById('features-container');
        const row = document.createElement('div');
        row.className = 'flex items-center gap-3 mb-3 group/row';
        row.innerHTML = `
            <input type="text" name="feature_keys[]" value="${key}" placeholder="Feature Name (e.g. max_users)" class="form-input flex-1 bg-white/5 border-white/10 focus:border-orange-500/50 text-sm">
            <input type="text" name="feature_values[]" value="${val}" placeholder="Value" class="form-input flex-1 bg-white/5 border-white/10 focus:border-orange-500/50 text-sm">
            <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center text-pink-500 hover:bg-pink-500 hover:text-white transition-all opacity-50 group-hover/row:opacity-100 shrink-0">
                <i class="ph ph-trash text-lg"></i>
            </button>
        `;
        container.appendChild(row);
    }
    </script>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php $currentPage = 'app-licenses'; require __DIR__ . '/partials/sidebar.php'; ?>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="absolute inset-0 bg-gradient-to-r from-orange-500/5 via-transparent to-transparent"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20">
                    <i class="ph ph-key text-2xl text-orange-500 animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-tight">App Licenses</h1>
                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-black hidden sm:block">Access Provisioning</p>
                </div>
            </div>
            <div class="relative flex items-center gap-4">
                <a href="<?= baseUrl('admin/app-licenses?action=new') ?>" class="group flex items-center gap-2 px-3 sm:px-5 py-2.5 bg-orange-500/10 hover:bg-orange-500/20 border border-orange-500/20 hover:border-orange-500/40 rounded-xl transition-all duration-300">
                    <i class="ph ph-plus-circle text-lg text-orange-500 group-hover:rotate-90 transition-transform duration-500"></i>
                    <span class="text-sm font-semibold text-orange-500 hidden sm:inline">Issue License</span>
                </a>
                <div class="h-8 w-px bg-white/10 mx-2"></div>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 crm-main-scroll bg-[#0b0e14]">
            <?php if ($flash = getFlash()): ?>
                <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center border border-emerald-500/20">
                        <i class="ph ph-check-circle text-emerald-500"></i>
                    </div>
                    <p class="text-emerald-500 font-medium"><?= e($flash) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($action === 'edit' || $action === 'new'): ?>
                <div class="max-w-4xl mx-auto">
                    <div class="admin-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i class="ph ph-key text-8xl text-orange-500"></i>
                        </div>
                        
                        <div class="relative flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center border border-orange-500/20 text-orange-500">
                                <i class="ph ph-pencil-line text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-white"><?= $action === 'edit' ? 'Edit License Details' : 'Issue New License' ?></h2>
                        </div>

                        <form method="POST" action="<?= baseUrl('admin/app-licenses') ?>" class="relative space-y-8">
                            <input type="hidden" name="id" value="<?= $editLicense['id'] ?? 0 ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Target Product <span class="text-pink-500">*</span></label>
                                    <select name="product_id" class="form-input bg-white/5 border-white/10 focus:border-orange-500/50" required>
                                        <option value="">Select App Product...</option>
                                        <?php foreach ($allProducts as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= ($editLicense['product_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
                                                <?= e($p['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">License Key</label>
                                    <input type="text" name="license_key" class="form-input font-mono bg-white/5 border-white/10 focus:border-orange-500/50" value="<?= e($editLicense['license_key'] ?? '') ?>" placeholder="Auto-generated if empty">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Customer / Label</label>
                                    <input type="text" name="label" class="form-input bg-white/5 border-white/10 focus:border-orange-500/50" value="<?= e($editLicense['label'] ?? '') ?>" placeholder="e.g. Acme Corp">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">License Type</label>
                                    <select name="type" class="form-input bg-white/5 border-white/10 focus:border-orange-500/50">
                                        <?php foreach (['trial', 'standard', 'pro', 'enterprise'] as $t): ?>
                                            <option value="<?= $t ?>" <?= ($editLicense['type'] ?? 'standard') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Operational Status</label>
                                    <select name="status" class="form-input bg-white/5 border-white/10 focus:border-orange-500/50">
                                        <?php foreach (['active', 'suspended', 'expired', 'revoked'] as $s): ?>
                                            <option value="<?= $s ?>" <?= ($editLicense['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Max Devices</label>
                                    <input type="number" name="max_devices" class="form-input bg-white/5 border-white/10 focus:border-orange-500/50" value="<?= $editLicense['max_devices'] ?? 1 ?>" min="1">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Expiration Date</label>
                                    <input type="datetime-local" name="expires_at" class="form-input bg-white/5 border-white/10 focus:border-orange-500/50" 
                                           value="<?= !empty($editLicense['expires_at']) ? date('Y-m-d\TH:i', strtotime($editLicense['expires_at'])) : '' ?>">
                                    <p class="text-xs text-white/40 ml-1 mt-1">Leave blank for lifetime validity.</p>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Admin Notes</label>
                                    <textarea name="notes" rows="2" class="form-input bg-white/5 border-white/10 focus:border-orange-500/50"><?= e($editLicense['notes'] ?? '') ?></textarea>
                                </div>

                                <div class="space-y-2 col-span-1 md:col-span-2">
                                    <label class="text-sm font-semibold text-white/60 ml-1">Bound Hardware ID (Product Code)</label>
                                    <input type="text" name="bound_hardware_id" class="form-input font-mono bg-white/5 border-white/10 focus:border-orange-500/50" value="<?= e($editFeatures['bound_hardware_id']['feature_value'] ?? '') ?>" placeholder="Optional: Lock license to specific hardware (e.g. M3-REC-...)">
                                    <p class="text-xs text-white/40 ml-1 mt-1">If provided, the software must match this exact identifier during activation.</p>
                                </div>
                            </div>

                            <!-- Features List -->
                            <div class="mt-8 pt-8 border-t border-white/5">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Custom Features</h3>
                                        <p class="text-xs text-white/40">Inject proprietary flags attached to the license.</p>
                                    </div>
                                    <button type="button" onclick="addFeatureRow()" class="px-4 py-2 bg-orange-500/10 text-orange-500 rounded-lg text-sm font-bold hover:bg-orange-500/20 transition-colors border border-orange-500/20">
                                        + Add Feature
                                    </button>
                                </div>
                                
                                <div id="features-container" class="space-y-3">
                                    <?php if(!empty($editFeatures)): ?>
                                        <?php foreach($editFeatures as $f): ?>
                                            <script>addFeatureRow('<?= addslashes($f['feature_key']) ?>', '<?= addslashes($f['feature_value']) ?>');</script>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- One blank row initially if empty -->
                                        <script>if(!document.getElementById('features-container').children.length) addFeatureRow();</script>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-6 border-t border-white/5">
                                <button type="submit" class="group relative px-8 py-3 bg-orange-500 text-black font-bold rounded-xl hover:bg-orange-400 transition-all shadow-lg shadow-orange-500/20 overflow-hidden">
                                    <span class="relative z-10">Save License</span>
                                    <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                                </button>
                                <a href="<?= baseUrl('admin/app-licenses') ?>" class="px-8 py-3 bg-white/5 text-white font-semibold rounded-xl hover:bg-white/10 transition-all border border-white/10">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                
                <!-- Filters -->
                <div class="mb-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <form method="GET" action="<?= baseUrl('admin/app-licenses') ?>" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-white/40"></i>
                            <input type="text" name="search" value="<?= e($_GET['search'] ?? '') ?>" placeholder="Search keys or labels..." class="form-input !pl-10 !py-2 w-full text-sm">
                        </div>
                        <select name="product" class="form-input !py-2 text-sm w-full sm:w-auto">
                            <option value="">All Products</option>
                            <?php foreach ($allProducts as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= (isset($_GET['product']) && $_GET['product'] == $p['id']) ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="status" class="form-input !py-2 text-sm w-full sm:w-auto">
                            <option value="">Status: All</option>
                            <?php foreach(['active','suspended','expired','revoked'] as $st): ?>
                                <option value="<?= $st ?>" <?= (isset($_GET['status']) && $_GET['status'] === $st) ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-white/5 text-white/60 hover:text-white hover:bg-white/10 rounded-lg border border-white/10 transition-colors text-sm font-semibold">
                            Filter
                        </button>
                    </form>
                </div>

                <form method="POST" action="<?= baseUrl('admin/app-licenses?action=bulk') ?>" id="bulk-form">
                    <div class="admin-card p-0 overflow-hidden border-white/5">
                        <div class="p-6 border-b border-white/5 flex flex-wrap items-center justify-between bg-white/[0.01] gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-500/10 flex items-center justify-center border border-orange-500/20">
                                    <i class="ph ph-key text-orange-500 text-xl"></i>
                                </div>
                                <h3 class="font-bold text-white tracking-tight text-lg">License Registry</h3>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <select name="bulk_action" id="bulk_action" class="form-input !py-1.5 text-xs bg-white/5 disabled:opacity-50" disabled>
                                    <option value="">Bulk Actions...</option>
                                    <option value="activate">Set Active</option>
                                    <option value="suspend">Suspend</option>
                                    <option value="revoke">Revoke</option>
                                </select>
                                <button type="submit" id="bulk_submit" class="px-4 py-1.5 bg-white/5 text-white/40 disabled:opacity-50 rounded-lg text-xs font-bold uppercase tracking-widest border border-white/10" disabled>
                                    Apply
                                </button>
                                
                                <div class="w-px h-6 bg-white/10 mx-2"></div>
                                <div class="bg-white/5 px-4 py-1.5 rounded-full border border-white/10">
                                    <span class="text-[10px] text-white/40 uppercase tracking-widest font-bold">Total Issued:</span>
                                    <span class="text-sm font-mono text-orange-500 font-bold ml-2"><?= count($licenses) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="admin-table min-w-[1000px]">
                                <thead>
                                    <tr>
                                        <th class="!pl-6 w-12">
                                            <input type="checkbox" id="check-all" class="rounded border-white/10 bg-white/5 text-orange-500 focus:ring-orange-500/50">
                                        </th>
                                        <th>Key / Customer</th>
                                        <th>Product</th>
                                        <th class="text-center">Devices</th>
                                        <th>Expiration</th>
                                        <th class="text-center">Status</th>
                                        <th class="!pr-8 text-right">Operations</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <?php if (empty($licenses)): ?>
                                        <tr><td colspan="7" class="text-center py-8 text-white/40">No licenses found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($licenses as $l): ?>
                                            <tr class="group hover:bg-white/[0.02] transition-colors duration-300">
                                                <td class="!pl-6">
                                                    <input type="checkbox" name="selected_ids[]" value="<?= $l['id'] ?>" class="row-checkbox rounded border-white/10 bg-white/5 text-orange-500 focus:ring-orange-500/50">
                                                </td>
                                                <td>
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-mono font-medium text-white group-hover:text-orange-400 transition-colors"><?= e($l['license_key']) ?></span>
                                                        <?php if(!empty($l['label'])): ?>
                                                            <span class="text-xs text-white/50 mt-0.5"><i class="ph ph-user text-[10px] text-white/30 mr-1"></i><?= e($l['label']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-bold text-white uppercase tracking-wider"><?= e($l['product_name']) ?></span>
                                                        <span class="text-[10px] text-orange-500/70 font-mono uppercase"><?= e($l['type']) ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= baseUrl('admin/app-devices?license='.$l['id']) ?>" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                                                        <span class="text-sm font-mono <?= $l['online_count'] > 0 ? 'text-emerald-400' : 'text-white/60' ?>"><?= $l['device_count'] ?></span>
                                                        <span class="text-xs text-white/40">/ <?= $l['max_devices'] ?></span>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if(empty($l['expires_at'])): ?>
                                                        <span class="text-xs font-mono text-white/30">Never</span>
                                                    <?php else: ?>
                                                        <div class="flex flex-col">
                                                            <span class="text-xs font-mono <?=strtotime($l['expires_at']) < time() ? 'text-pink-500' : 'text-white/60' ?>"><?= date('Y-m-d', strtotime($l['expires_at'])) ?></span>
                                                            <span class="text-[10px] text-white/30"><?= date('H:i', strtotime($l['expires_at'])) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                        $sc = 'white'; 
                                                        if($l['status'] === 'active') $sc = 'emerald';
                                                        if($l['status'] === 'suspended') $sc = 'orange';
                                                        if($l['status'] === 'revoked' || $l['status'] === 'expired') $sc = 'pink';
                                                    ?>
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-<?= $sc ?>-500/10 text-<?= $sc ?>-500 text-[10px] font-bold uppercase tracking-widest border border-<?= $sc ?>-500/20">
                                                        <?= e($l['status']) ?>
                                                    </span>
                                                </td>
                                                <td class="!pr-8 text-right">
                                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <a href="<?= baseUrl('admin/app-licenses?action=edit&id='.$l['id']) ?>" class="w-8 h-8 rounded-lg bg-orange-500/10 flex items-center justify-center text-orange-500 hover:bg-orange-500 hover:text-black transition-all">
                                                            <i class="ph ph-pencil-simple"></i>
                                                        </a>
                                                        <button type="button" onclick="showDeleteModal('<?= e($l['license_key']) ?>', '<?= baseUrl('admin/app-licenses?action=delete&id='.$l['id']) ?>')" class="w-8 h-8 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-500 hover:bg-pink-500 hover:text-white transition-all">
                                                            <i class="ph ph-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require __DIR__ . '/partials/_delete_modal.php'; ?>

<script>
    // Bulk Delete Selection
    const checkAll = document.getElementById('check-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkAction = document.getElementById('bulk_action');
    const bulkSubmit = document.getElementById('bulk_submit');

    function updateBulkControls() {
        const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
        bulkAction.disabled = !anyChecked;
        
        if (!anyChecked) {
            bulkSubmit.disabled = true;
            bulkSubmit.classList.remove('text-orange-500', 'border-orange-500/50', 'bg-orange-500/10');
            bulkSubmit.classList.add('text-white/40', 'border-white/10', 'bg-white/5');
            bulkAction.value = '';
        } else if (bulkAction.value !== '') {
            bulkSubmit.disabled = false;
            bulkSubmit.classList.add('text-orange-500', 'border-orange-500/50', 'bg-orange-500/10', 'hover:bg-orange-500', 'hover:text-black');
            bulkSubmit.classList.remove('text-white/40', 'border-white/10', 'bg-white/5');
        }
    }

    if(checkAll) {
        checkAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = checkAll.checked);
            updateBulkControls();
        });

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                checkAll.checked = allChecked;
                updateBulkControls();
            });
        });

        bulkAction.addEventListener('change', updateBulkControls);
    }
</script>
</body>
</html>
