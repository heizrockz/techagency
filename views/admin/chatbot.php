<?php $currentPage = 'chatbot'; ?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Flow Builder — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= baseUrl('assets/css/style.css') ?>">
    <style>
    /* ═══ Flow Builder Canvas ═══ */
    .flow-builder-wrap { display:flex; height:calc(100vh - 70px); overflow:hidden; background:#0d0f13; }
    .flow-canvas-area { flex:1; position:relative; overflow:hidden; cursor:grab; }
    .flow-canvas-area.grabbing { cursor:grabbing; }
    .flow-canvas { position:absolute; top:0; left:0; width:10000px; height:10000px; }
    .flow-svg { position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:0; }
    .flow-svg path { fill:none; stroke:rgba(16,185,129,0.4); stroke-width:2; }
    .flow-svg path.active { stroke:var(--neon-cyan); stroke-width:3; }

    /* ═══ Nodes ═══ */
    .flow-node { position:absolute; width:260px; background:rgba(26,29,36,0.95); border:1px solid rgba(255,255,255,0.1); border-radius:12px; z-index:10; cursor:move; user-select:none; box-shadow:0 4px 20px rgba(0,0,0,0.4); transition: box-shadow 0.2s; }
    .flow-node:hover { box-shadow: 0 6px 30px rgba(16,185,129,0.15); }
    .flow-node.selected { border-color: var(--neon-cyan); box-shadow: 0 0 20px rgba(6,182,212,0.3); }
    .flow-node.root { border-color: rgba(16,185,129,0.4); }
    .flow-node-header { padding:12px 14px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .flow-node-title { font-size:0.85rem; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .flow-node-badge { font-size:0.65rem; padding:2px 8px; border-radius:20px; font-weight:600; flex-shrink:0; }
    .badge-root { background:rgba(16,185,129,0.2); color:var(--neon-emerald); }
    .badge-preset { background:rgba(6,182,212,0.15); color:var(--neon-cyan); }
    .badge-input { background:rgba(251,191,36,0.15); color:var(--theme-gold); }
    .flow-node-body { padding:10px 14px; font-size:0.78rem; color:var(--text-muted); max-height:60px; overflow:hidden; line-height:1.5; }
    .flow-node-footer { padding:8px 14px; border-top:1px solid rgba(255,255,255,0.04); display:flex; flex-wrap:wrap; gap:4px; }
    .flow-opt-pill { font-size:0.7rem; padding:3px 10px; border-radius:12px; background:rgba(255,255,255,0.05); color:var(--text-secondary); border:1px solid rgba(255,255,255,0.08); }

    /* Connection ports */
    .flow-port { position:absolute; width:14px; height:14px; border-radius:50%; background:rgba(16,185,129,0.5); border:2px solid var(--neon-emerald); cursor:crosshair; z-index:20; }
    .flow-port-in { top:-7px; left:50%; transform:translateX(-50%); }
    .flow-port-out { bottom:-7px; left:50%; transform:translateX(-50%); }
    .flow-port:hover { background:var(--neon-emerald); transform:translateX(-50%) scale(1.3); }

    /* ═══ Toolbar ═══ */
    .flow-toolbar { position:absolute; top:16px; left:16px; display:flex; gap:8px; z-index:50; }
    .flow-toolbar button { padding:8px 16px; border:1px solid rgba(255,255,255,0.1); border-radius:8px; background:rgba(26,29,36,0.95); color:#fff; font-size:0.85rem; font-weight:600; cursor:pointer; backdrop-filter:blur(8px); font-family:inherit; transition:all 0.2s; }
    .flow-toolbar button:hover { border-color:var(--neon-cyan); color:var(--neon-cyan); }
    .flow-toolbar button.danger:hover { border-color:var(--neon-pink); color:var(--neon-pink); }

    /* ═══ Slide-out Editor Panel ═══ */
    .flow-editor-panel { width:0; overflow:hidden; background:#13151b; border-left:1px solid rgba(255,255,255,0.06); transition:width 0.35s cubic-bezier(0.4,0,0.2,1); flex-shrink:0; }
    .flow-editor-panel.active { width:420px; overflow-y:auto; }
    .flow-editor-inner { width:420px; padding:24px 20px; }
    .flow-editor-inner h3 { font-size:1.1rem; font-weight:700; color:var(--neon-cyan); margin:0 0 20px; display:flex; align-items:center; justify-content:space-between; }
    .editor-close { background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer; padding:4px; }
    .editor-close:hover { color:#fff; }
    .flow-editor-inner .field-group { margin-bottom:16px; }
    .flow-editor-inner .field-group label { display:block; font-size:0.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:6px; }
    .flow-editor-inner .field-group input,
    .flow-editor-inner .field-group textarea,
    .flow-editor-inner .field-group select { width:100%; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:10px 12px; border-radius:8px; font-size:0.88rem; font-family:inherit; }
    .flow-editor-inner .field-group textarea { min-height:70px; resize:vertical; }
    .flow-editor-inner .field-group input:focus,
    .flow-editor-inner .field-group textarea:focus,
    .flow-editor-inner .field-group select:focus { outline:none; border-color:var(--neon-cyan); }
    .editor-save-btn { width:100%; padding:12px; background:linear-gradient(135deg,var(--theme-primary),var(--neon-cyan)); border:none; color:#fff; font-size:0.95rem; font-weight:700; border-radius:8px; cursor:pointer; font-family:inherit; transition:opacity 0.2s; margin-top:10px; }
    .editor-save-btn:hover { opacity:0.9; }
    .editor-divider { border:none; border-top:1px solid rgba(255,255,255,0.06); margin:20px 0; }
    .opt-row { background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.06); border-radius:8px; padding:12px; margin-bottom:10px; position:relative; }
    .opt-row .opt-delete { position:absolute; top:8px; right:8px; background:none; border:none; color:var(--neon-pink); cursor:pointer; font-size:1rem; }
    .opt-row .opt-delete:hover { color:#f43f5e; }
    .add-opt-btn { width:100%; padding:10px; background:rgba(59,130,246,0.1); border:1px dashed rgba(59,130,246,0.3); color:var(--neon-cobalt); border-radius:8px; cursor:pointer; font-family:inherit; font-weight:600; transition:all 0.2s; }
    .add-opt-btn:hover { background:rgba(59,130,246,0.2); border-color:var(--neon-cobalt); }

    /* Zoom label */
    .flow-zoom-label { position:absolute; bottom:16px; right:16px; background:rgba(26,29,36,0.9); color:var(--text-muted); padding:6px 14px; border-radius:8px; font-size:0.8rem; z-index:50; border:1px solid rgba(255,255,255,0.06); }
    </style>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main" style="padding:0; overflow:hidden;">
        <!-- Toolbar -->
        <div style="padding:14px 20px; background:rgba(0,0,0,0.3); border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:space-between;">
            <div>
                <h1 style="color:var(--neon-cyan); margin:0; font-size:1.2rem;">🤖 Chatbot Flow Builder</h1>
            </div>
            <div style="display:flex; gap:8px;">
                <button onclick="FB.addNode()" class="btn-primary" style="padding:8px 18px; font-size:0.85rem;">+ Add Node</button>
                <button onclick="FB.saveAllPositions()" class="admin-btn" style="font-size:0.85rem;">💾 Save Layout</button>
            </div>
        </div>

        <div class="flow-builder-wrap">
            <!-- Canvas Area -->
            <div class="flow-canvas-area" id="canvasArea">
                <div class="flow-canvas" id="flowCanvas">
                    <svg class="flow-svg" id="flowSvg"></svg>
                </div>
                <div class="flow-zoom-label" id="zoomLabel">100%</div>
            </div>

            <!-- Slide-out Editor -->
            <div class="flow-editor-panel" id="editorPanel">
                <div class="flow-editor-inner" id="editorInner">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const API_URL = '<?= baseUrl("admin/chatbot") ?>';
const LOCALES = <?= json_encode(SUPPORTED_LOCALES) ?>;

const FB = {
    nodes: [],
    selectedNodeId: null,
    dragging: null,
    dragOffset: { x: 0, y: 0 },
    panStart: null,
    canvasOffset: { x: 0, y: 0 },
    zoom: 1,
    linking: null, // { fromNodeId, fromOptId }

    async init() {
        await this.loadAll();
        this.setupPanZoom();
    },

    async api(action, data = {}) {
        const fd = new FormData();
        fd.append('api_action', action);
        for (const [k, v] of Object.entries(data)) fd.append(k, v);
        const res = await fetch(API_URL, { method: 'POST', body: fd });
        return res.json();
    },

    async loadAll() {
        const res = await this.api('get_all');
        if (res.success) {
            this.nodes = res.nodes;
            this.render();
        }
    },

    render() {
        const canvas = document.getElementById('flowCanvas');
        // Remove old nodes
        canvas.querySelectorAll('.flow-node').forEach(n => n.remove());

        this.nodes.forEach(node => {
            const el = document.createElement('div');
            el.className = 'flow-node' + (node.is_root == 1 ? ' root' : '') + (node.id == this.selectedNodeId ? ' selected' : '');
            el.dataset.nodeId = node.id;
            el.style.left = (node.pos_x || 100) + 'px';
            el.style.top = (node.pos_y || 100) + 'px';

            const replyBadge = node.reply_type === 'user_input'
                ? '<span class="flow-node-badge badge-input">Input</span>'
                : '<span class="flow-node-badge badge-preset">Preset</span>';
            const rootBadge = node.is_root == 1 ? '<span class="flow-node-badge badge-root">Start</span>' : '';

            const msgPreview = (node.translations?.en || '').substring(0, 80);
            const optionPills = (node.options || []).map(o => 
                `<span class="flow-opt-pill">${this.esc(o.translations?.en || '?')}</span>`
            ).join('');

            el.innerHTML = `
                <div class="flow-port flow-port-in" data-port="in"></div>
                <div class="flow-node-header">
                    <span class="flow-node-title">${this.esc(node.name)}</span>
                    <span style="display:flex;gap:4px;">${rootBadge}${replyBadge}</span>
                </div>
                <div class="flow-node-body">${this.esc(msgPreview)}</div>
                ${optionPills ? `<div class="flow-node-footer">${optionPills}</div>` : ''}
                <div class="flow-port flow-port-out" data-port="out"></div>
            `;

            // Drag handlers
            el.addEventListener('mousedown', (e) => {
                if (e.target.classList.contains('flow-port')) return;
                e.stopPropagation();
                this.dragging = node.id;
                const rect = el.getBoundingClientRect();
                this.dragOffset = { x: e.clientX - rect.left, y: e.clientY - rect.top };
                el.style.zIndex = 100;
            });

            // Click to select
            el.addEventListener('click', (e) => {
                if (e.target.classList.contains('flow-port')) return;
                this.selectNode(node.id);
            });

            // Prevent double-click text selection
            el.addEventListener('dblclick', (e) => {
                e.preventDefault();
                this.selectNode(node.id);
            });

            canvas.appendChild(el);
        });

        this.drawConnections();
    },

    drawConnections() {
        const svg = document.getElementById('flowSvg');
        svg.innerHTML = '';
        this.nodes.forEach(node => {
            (node.options || []).forEach(opt => {
                if (opt.action_type === 'goto_node' && opt.next_node_id) {
                    const fromEl = document.querySelector(`.flow-node[data-node-id="${node.id}"]`);
                    const toEl = document.querySelector(`.flow-node[data-node-id="${opt.next_node_id}"]`);
                    if (fromEl && toEl) {
                        const x1 = parseInt(fromEl.style.left) + fromEl.offsetWidth / 2;
                        const y1 = parseInt(fromEl.style.top) + fromEl.offsetHeight;
                        const x2 = parseInt(toEl.style.left) + toEl.offsetWidth / 2;
                        const y2 = parseInt(toEl.style.top);
                        const midY = (y1 + y2) / 2;
                        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        path.setAttribute('d', `M${x1},${y1} C${x1},${midY} ${x2},${midY} ${x2},${y2}`);
                        if (node.id == this.selectedNodeId) path.classList.add('active');
                        svg.appendChild(path);
                    }
                }
            });
        });
    },

    setupPanZoom() {
        const area = document.getElementById('canvasArea');
        const canvas = document.getElementById('flowCanvas');

        // Pan
        area.addEventListener('mousedown', (e) => {
            if (e.target === area || e.target === canvas || e.target.tagName === 'svg') {
                this.panStart = { x: e.clientX - this.canvasOffset.x, y: e.clientY - this.canvasOffset.y };
                area.classList.add('grabbing');
            }
        });

        document.addEventListener('mousemove', (e) => {
            // Dragging a node
            if (this.dragging) {
                const nodeEl = document.querySelector(`.flow-node[data-node-id="${this.dragging}"]`);
                if (nodeEl) {
                    const newX = (e.clientX - area.getBoundingClientRect().left - this.canvasOffset.x) / this.zoom - this.dragOffset.x;
                    const newY = (e.clientY - area.getBoundingClientRect().top - this.canvasOffset.y) / this.zoom - this.dragOffset.y;
                    nodeEl.style.left = Math.max(0, newX) + 'px';
                    nodeEl.style.top = Math.max(0, newY) + 'px';
                    // Update local data
                    const node = this.nodes.find(n => n.id == this.dragging);
                    if (node) { node.pos_x = Math.max(0, Math.round(newX)); node.pos_y = Math.max(0, Math.round(newY)); }
                    this.drawConnections();
                }
            }
            // Panning canvas
            if (this.panStart) {
                this.canvasOffset.x = e.clientX - this.panStart.x;
                this.canvasOffset.y = e.clientY - this.panStart.y;
                canvas.style.transform = `translate(${this.canvasOffset.x}px, ${this.canvasOffset.y}px) scale(${this.zoom})`;
            }
        });

        document.addEventListener('mouseup', () => {
            if (this.dragging) {
                const nodeEl = document.querySelector(`.flow-node[data-node-id="${this.dragging}"]`);
                if (nodeEl) nodeEl.style.zIndex = 10;
                this.dragging = null;
            }
            if (this.panStart) {
                this.panStart = null;
                area.classList.remove('grabbing');
            }
        });

        // Zoom
        area.addEventListener('wheel', (e) => {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.05 : 0.05;
            this.zoom = Math.max(0.3, Math.min(2, this.zoom + delta));
            canvas.style.transform = `translate(${this.canvasOffset.x}px, ${this.canvasOffset.y}px) scale(${this.zoom})`;
            document.getElementById('zoomLabel').textContent = Math.round(this.zoom * 100) + '%';
        }, { passive: false });
    },

    selectNode(id) {
        this.selectedNodeId = id;
        document.querySelectorAll('.flow-node').forEach(n => n.classList.remove('selected'));
        const el = document.querySelector(`.flow-node[data-node-id="${id}"]`);
        if (el) el.classList.add('selected');
        this.drawConnections();
        this.openEditor(id);
    },

    openEditor(nodeId) {
        const node = this.nodes.find(n => n.id == nodeId);
        if (!node) return;
        const panel = document.getElementById('editorPanel');
        const inner = document.getElementById('editorInner');

        const localeFields = LOCALES.map(loc => `
            <div class="field-group">
                <label>Message (${loc.toUpperCase()})</label>
                <textarea id="ed_msg_${loc}" ${loc === 'ar' ? 'dir="rtl"' : ''}>${this.esc(node.translations?.[loc] || '')}</textarea>
            </div>
        `).join('');

        const optionsHtml = (node.options || []).map((opt, i) => {
            const labelFields = LOCALES.map(loc => `
                <input type="text" id="ed_opt_${opt.id}_label_${loc}" value="${this.esc(opt.translations?.[loc] || '')}" placeholder="${loc.toUpperCase()} Label" ${loc === 'ar' ? 'dir="rtl"' : ''} style="margin-bottom:6px;">
            `).join('');

            const nodeSelectOpts = this.nodes.map(n => 
                `<option value="${n.id}" ${n.id == opt.next_node_id ? 'selected' : ''}>${this.esc(n.name)}</option>`
            ).join('');

            return `<div class="opt-row">
                <button class="opt-delete" onclick="FB.deleteOption(${opt.id})">&times;</button>
                ${labelFields}
                <select id="ed_opt_${opt.id}_action" style="margin-bottom:6px;">
                    <option value="goto_node" ${opt.action_type==='goto_node'?'selected':''}>Go to Node</option>
                    <option value="link" ${opt.action_type==='link'?'selected':''}>Open Link</option>
                    <option value="call" ${opt.action_type==='call'?'selected':''}>Phone Call</option>
                </select>
                <select id="ed_opt_${opt.id}_next">
                    <option value="">-- Target Node --</option>
                    ${nodeSelectOpts}
                </select>
                <input type="text" id="ed_opt_${opt.id}_val" value="${this.esc(opt.action_value || '')}" placeholder="Link/Phone URL" style="margin-top:6px;">
                <input type="number" id="ed_opt_${opt.id}_sort" value="${opt.sort_order || 0}" placeholder="Sort" style="margin-top:6px; width:80px;">
                <button class="editor-save-btn" style="margin-top:8px; font-size:0.8rem; padding:8px;" onclick="FB.saveOption(${opt.id}, ${node.id})">Update Option</button>
            </div>`;
        }).join('');

        inner.innerHTML = `
            <h3>
                Edit Node #${node.id}
                <button class="editor-close" onclick="FB.closeEditor()">&times;</button>
            </h3>
            <div class="field-group">
                <label>Node Name</label>
                <input type="text" id="ed_name" value="${this.esc(node.name)}">
            </div>
            <div class="field-group">
                <label>Reply Type</label>
                <select id="ed_reply_type">
                    <option value="preset" ${node.reply_type==='preset'?'selected':''}>Preset Buttons</option>
                    <option value="user_input" ${node.reply_type==='user_input'?'selected':''}>User Text Input</option>
                </select>
            </div>
            <div class="field-group" id="ed_var_group" style="${node.reply_type==='user_input'?'':'display:none'}">
                <label>Input Variable Name</label>
                <input type="text" id="ed_input_var" value="${this.esc(node.input_var_name || '')}" placeholder="e.g. user_name">
            </div>
            <div class="field-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" id="ed_is_root" ${node.is_root == 1 ? 'checked' : ''}> Starting Node
                </label>
            </div>
            ${localeFields}
            <button class="editor-save-btn" onclick="FB.saveCurrentNode()">💾 Save Node</button>
            <button class="editor-save-btn danger" style="background:rgba(244,63,94,0.2); color:var(--neon-pink); margin-top:8px;" onclick="FB.deleteNode(${node.id})">🗑 Delete Node</button>

            <hr class="editor-divider">
            <h3 style="font-size:0.95rem; color:var(--text-primary);">Options / Buttons</h3>
            ${optionsHtml}
            <button class="add-opt-btn" onclick="FB.addOption(${node.id})">+ Add Option</button>
        `;

        // Toggle input var visibility
        document.getElementById('ed_reply_type').addEventListener('change', (e) => {
            document.getElementById('ed_var_group').style.display = e.target.value === 'user_input' ? '' : 'none';
        });

        panel.classList.add('active');
    },

    closeEditor() {
        document.getElementById('editorPanel').classList.remove('active');
        this.selectedNodeId = null;
        document.querySelectorAll('.flow-node').forEach(n => n.classList.remove('selected'));
        this.drawConnections();
    },

    async saveCurrentNode() {
        const node = this.nodes.find(n => n.id == this.selectedNodeId);
        if (!node) return;
        const data = {
            node_id: node.id,
            name: document.getElementById('ed_name').value,
            is_root: document.getElementById('ed_is_root').checked ? 1 : 0,
            pos_x: node.pos_x || 100,
            pos_y: node.pos_y || 100,
            reply_type: document.getElementById('ed_reply_type').value,
            input_var_name: document.getElementById('ed_input_var')?.value || ''
        };
        LOCALES.forEach(loc => {
            data['message_' + loc] = document.getElementById('ed_msg_' + loc)?.value || '';
        });
        const res = await this.api('save_node', data);
        if (res.success) { await this.loadAll(); this.selectNode(node.id); }
    },

    async addNode() {
        const posX = Math.round((-this.canvasOffset.x / this.zoom) + 200 + Math.random() * 200);
        const posY = Math.round((-this.canvasOffset.y / this.zoom) + 200 + Math.random() * 200);
        const data = { node_id: 0, name: 'New Node', is_root: 0, pos_x: posX, pos_y: posY, reply_type: 'preset', input_var_name: '' };
        LOCALES.forEach(loc => data['message_' + loc] = '');
        const res = await this.api('save_node', data);
        if (res.success) { await this.loadAll(); this.selectNode(res.id); }
    },

    async deleteNode(id) {
        if (!confirm('Delete this node and all its options?')) return;
        await this.api('delete_node', { node_id: id });
        this.closeEditor();
        await this.loadAll();
    },

    async saveOption(optId, nodeId) {
        const data = {
            option_id: optId,
            node_id: nodeId,
            action_type: document.getElementById(`ed_opt_${optId}_action`).value,
            next_node_id: document.getElementById(`ed_opt_${optId}_next`).value,
            action_value: document.getElementById(`ed_opt_${optId}_val`).value,
            sort_order: document.getElementById(`ed_opt_${optId}_sort`).value
        };
        LOCALES.forEach(loc => {
            data['label_' + loc] = document.getElementById(`ed_opt_${optId}_label_${loc}`)?.value || '';
        });
        const res = await this.api('save_option', data);
        if (res.success) { await this.loadAll(); this.selectNode(nodeId); }
    },

    async addOption(nodeId) {
        const data = { option_id: 0, node_id: nodeId, action_type: 'goto_node', next_node_id: '', action_value: '', sort_order: 0 };
        LOCALES.forEach(loc => data['label_' + loc] = 'New Option');
        const res = await this.api('save_option', data);
        if (res.success) { await this.loadAll(); this.selectNode(nodeId); }
    },

    async deleteOption(optId) {
        if (!confirm('Delete this option?')) return;
        await this.api('delete_option', { option_id: optId });
        if (this.selectedNodeId) { await this.loadAll(); this.selectNode(this.selectedNodeId); }
    },

    async saveAllPositions() {
        const positions = this.nodes.map(n => ({ id: n.id, x: n.pos_x || 100, y: n.pos_y || 100 }));
        await this.api('save_positions', { positions: JSON.stringify(positions) });
    },

    esc(str) {
        const d = document.createElement('div');
        d.textContent = str || '';
        return d.innerHTML;
    }
};

// Boot
document.addEventListener('DOMContentLoaded', () => FB.init());
</script>

</body>
</html>
