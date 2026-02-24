<?php
$currentPage = 'chatbot';
?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Builder — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    
    <div class="admin-main">
        <div class="admin-header">
            <div>
                <h1 style="color: var(--neon-cyan); margin:0;">🤖 Chatbot Builder</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Design your automated conversation flows.</p>
            </div>
            
            <?php if ($action !== 'edit' && $action !== 'new'): ?>
                <a href="?action=new" class="btn-primary" style="display:inline-block; text-decoration:none;">+ New Node</a>
            <?php else: ?>
                <a href="<?= baseUrl('admin/chatbot') ?>" class="admin-btn" style="text-decoration:none;">Back to List</a>
            <?php endif; ?>
        </div>

        <?php if ($saved): ?>
            <div class="glass-card" style="margin-bottom: 20px; border-left: 4px solid var(--neon-emerald); padding: 15px; background: rgba(16, 185, 129, 0.1);">
                <?= t('admin_saved') ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <div class="glass-card fade-up">
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Internal Name</th>
                                <th>Start Node</th>
                                <th>EN Message Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allNodes as $node): ?>
                                <tr>
                                    <td><?= $node['id'] ?></td>
                                    <td><strong><?= e($node['name']) ?></strong></td>
                                    <td>
                                        <?php if ($node['is_root']): ?>
                                            <span style="background: rgba(16,185,129,0.2); color: var(--neon-emerald); padding: 2px 8px; border-radius: 12px; font-size:0.8rem;">Yes</span>
                                        <?php else: ?>
                                            <span style="background: rgba(255,255,255,0.05); color: #888; padding: 2px 8px; border-radius: 12px; font-size:0.8rem;">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= e($node['en_msg']) ?>
                                    </td>
                                    <td>
                                        <a href="?action=edit&id=<?= $node['id'] ?>" class="admin-btn">Edit Flow</a>
                                        <?php if (!$node['is_root']): ?>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this node and all its options?');">
                                                <input type="hidden" name="action" value="delete_node">
                                                <input type="hidden" name="node_id" value="<?= $node['id'] ?>">
                                                <button type="submit" class="admin-btn" style="border-color: rgba(236, 72, 153, 0.5); color: var(--neon-pink);">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($action === 'edit' || $action === 'new'): ?>
            <?php 
                $isNew = ($action === 'new');
                $id = $isNew ? 0 : $editNode['id'];
                $name = $isNew ? '' : $editNode['name'];
                $isRoot = $isNew ? 0 : $editNode['is_root'];
            ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                
                <!-- Node Settings -->
                <div class="glass-card fade-up">
                    <h3 style="margin-bottom: 20px; color: var(--neon-violet);">Node Details</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="save_node">
                        <input type="hidden" name="node_id" value="<?= $id ?>">
                        
                        <div class="form-group">
                            <label>Internal Node Name</label>
                            <input type="text" name="name" class="form-input" value="<?= e($name) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" name="is_root" value="1" <?= $isRoot ? 'checked' : '' ?>>
                                Set as Starting Node (Welcome Message)
                            </label>
                            <small style="color:var(--text-muted); display:block; margin-top:5px;">Only one node can be the starting point.</small>
                        </div>

                        <h4 style="margin: 20px 0 10px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom:10px;">Bot Messages</h4>
                        
                        <?php foreach (SUPPORTED_LOCALES as $loc): ?>
                        <div class="form-group">
                            <label>Message (<?= strtoupper($loc) ?>)</label>
                            <textarea name="message_<?= $loc ?>" class="form-input" rows="3" required><?= $isNew ? '' : e($editNode['translations'][$loc]['message'] ?? '') ?></textarea>
                        </div>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn-primary" style="width: 100%;">Save Node</button>
                    </form>
                </div>

                <!-- Node Options -->
                <?php if (!$isNew): ?>
                <div class="glass-card fade-up" style="animation-delay: 0.1s;">
                    <h3 style="margin-bottom: 20px; color: var(--neon-cyan); display: flex; justify-content: space-between; align-items:center;">
                        User Options 
                        <span style="font-size: 0.8rem; background: rgba(6,182,212,0.1); padding: 4px 8px; border-radius: 4px;">Buttons</span>
                    </h3>
                    
                    <?php if (empty($nodeOptions)): ?>
                        <div style="color:var(--text-muted); text-align:center; padding: 20px;">No options yet. Users will reach a dead end here.</div>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap: 15px; margin-bottom: 25px;">
                            <?php foreach ($nodeOptions as $opt): 
                                $parsedTrans = [];
                                if ($opt['trans']) {
                                    $parts = explode('|', $opt['trans']);
                                    foreach ($parts as $p) {
                                        list($l,$t) = explode(':', $p, 2);
                                        $parsedTrans[$l] = $t;
                                    }
                                }
                            ?>
                            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 15px; border-radius: 8px;">
                                <div style="display:flex; justify-content:space-between; margin-bottom: 10px;">
                                    <strong><?= e($parsedTrans['en'] ?? '') ?></strong>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this option?');">
                                        <input type="hidden" name="action" value="delete_option">
                                        <input type="hidden" name="option_id" value="<?= $opt['id'] ?>">
                                        <button type="submit" style="background:none; border:none; color:var(--neon-pink); cursor:pointer;">&times;</button>
                                    </form>
                                </div>
                                <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:10px;">
                                    Action: <span style="color:var(--neon-emerald);"><?= $opt['action_type'] ?></span> 
                                    (Target: <?= $opt['action_type']==='goto_node' ? 'Node '.$opt['next_node_id'] : e($opt['action_value']) ?>)
                                </div>
                                
                                <form method="POST" style="border-top:1px dashed rgba(255,255,255,0.1); padding-top:10px;">
                                    <input type="hidden" name="action" value="save_option">
                                    <input type="hidden" name="node_id" value="<?= $id ?>">
                                    <input type="hidden" name="option_id" value="<?= $opt['id'] ?>">
                                    
                                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
                                        <input type="text" name="label_en" class="form-input" placeholder="EN Label" value="<?= e($parsedTrans['en'] ?? '') ?>" required style="padding: 5px; height:auto; font-size:0.8rem;">
                                        <input type="text" name="label_ar" class="form-input" placeholder="AR Label" value="<?= e($parsedTrans['ar'] ?? '') ?>" required style="padding: 5px; height:auto; font-size:0.8rem;" dir="rtl">
                                    </div>
                                    <div style="display:grid; grid-template-columns: 1fr 1fr 60px; gap:10px;">
                                        <select name="action_type" class="form-input" style="padding:5px; height:auto; font-size:0.8rem;">
                                            <option value="goto_node" <?= $opt['action_type']==='goto_node' ? 'selected' : '' ?>>Go to Node</option>
                                            <option value="link" <?= $opt['action_type']==='link' ? 'selected' : '' ?>>Open Link</option>
                                            <option value="call" <?= $opt['action_type']==='call' ? 'selected' : '' ?>>Phone Call</option>
                                        </select>
                                        <input type="text" name="action_value" class="form-input" placeholder="Link/Phone URL" value="<?= e($opt['action_value']) ?>" style="padding:5px; height:auto; font-size:0.8rem;">
                                        <input type="number" name="sort_order" class="form-input" placeholder="Sort" value="<?= $opt['sort_order'] ?>" style="padding:5px; height:auto; font-size:0.8rem;">
                                    </div>
                                    <div style="margin-top:10px;">
                                        <select name="next_node_id" class="form-input" style="padding:5px; height:auto; font-size:0.8rem;">
                                            <option value="">-- Target Node --</option>
                                            <?php foreach ($allNodes as $n): ?>
                                                <option value="<?= $n['id'] ?>" <?= $n['id']==$opt['next_node_id']?'selected':'' ?>><?= e($n['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn-primary" style="padding: 8px 15px; font-size:0.85rem; margin-top:10px; width: 100%;">Update Option</button>
                                </form>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div style="background: rgba(59,130,246,0.05); border: 1px solid rgba(59,130,246,0.2); padding: 15px; border-radius: 8px;">
                        <h4 style="margin: 0 0 15px;">Add New Option</h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="save_option">
                            <input type="hidden" name="node_id" value="<?= $id ?>">
                            <input type="hidden" name="option_id" value="0">
                            
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
                                <input type="text" name="label_en" class="form-input" placeholder="EN Label" required>
                                <input type="text" name="label_ar" class="form-input" placeholder="AR Label" required dir="rtl">
                            </div>
                            
                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
                                <select name="action_type" class="form-input">
                                    <option value="goto_node">Go to Node</option>
                                    <option value="link">Open Link</option>
                                    <option value="call">Phone Call (Global Setting)</option>
                                </select>
                                <input type="text" name="action_value" class="form-input" placeholder="URL if Link">
                            </div>

                            <div style="display:grid; grid-template-columns: 1fr 80px; gap:10px; margin-bottom:15px;">
                                <select name="next_node_id" class="form-input">
                                    <option value="">-- Select Target Node --</option>
                                    <?php foreach ($allNodes as $n): ?>
                                        <option value="<?= $n['id'] ?>"><?= e($n['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="sort_order" class="form-input" placeholder="Sort" value="0">
                            </div>
                            
                            <button type="submit" class="btn-primary" style="width: 100%;">+ Add Option</button>
                        </form>
                    </div>

                </div>
                <?php else: ?>
                    <div class="glass-card" style="display:flex; align-items:center; justify-content:center; color:var(--text-muted);">
                        Save the node first to add options.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
