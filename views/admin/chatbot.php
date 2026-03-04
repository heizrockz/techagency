<?php $currentPage = 'chatbot'; ?>
<!DOCTYPE html>
<html lang="<?= e(getCurrentLocale()) ?>" dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">
<head>
    <title>Chatbot Flow Builder — <?= APP_NAME ?></title>
    <?php require __DIR__ . '/partials/_head_assets.php'; ?>
    <style>
    * { box-sizing: border-box; }

    /* ═══ Layout ═══ */
    .fb-wrap { display:flex; height:100%; overflow:hidden; background:#0a0c10; }

    /* ═══ Canvas ═══ */
    .fb-canvas-area { flex:1; position:relative; overflow:auto; cursor:grab; background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 20px 20px; }
    .fb-canvas-area.grabbing { cursor:grabbing; }
    .fb-canvas { position:absolute; top:0; left:0; width:5000px; height:5000px; transform-origin:0 0; }
    .fb-svg { position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:1; }
    .fb-svg path { fill:none; stroke:rgba(16,185,129,0.35); stroke-width:2.5; transition: stroke 0.15s; }
    .fb-svg path.hl { stroke:var(--neon-cyan); stroke-width:3; }
    .fb-svg path.temp { stroke:rgba(251,191,36,0.6); stroke-width:2; stroke-dasharray:8 4; }

    /* ═══ Nodes ═══ */
    .fb-node { position:absolute; width:240px; background:#181b22; border:1.5px solid rgba(255,255,255,0.08); border-radius:14px; z-index:10; cursor:move; user-select:none; box-shadow:0 6px 24px rgba(0,0,0,0.5); font-family:Inter,sans-serif; }
    .fb-node:hover { border-color:rgba(255,255,255,0.15); }
    .fb-node.sel { border-color:var(--neon-cyan); box-shadow:0 0 0 2px rgba(6,182,212,0.25), 0 8px 30px rgba(0,0,0,0.5); }
    .fb-node.root { border-color:rgba(16,185,129,0.35); }

    .fb-n-head { padding:10px 12px; display:flex; align-items:center; justify-content:space-between; gap:6px; border-bottom:1px solid rgba(255,255,255,0.04); }
    .fb-n-name { font-size:0.82rem; font-weight:700; color:#e2e8f0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; flex:1; }
    .fb-badge { font-size:0.6rem; padding:2px 7px; border-radius:50px; font-weight:700; letter-spacing:0.3px; text-transform:uppercase; flex-shrink:0; }
    .fb-badge-root { background:rgba(16,185,129,0.15); color:#34d399; }
    .fb-badge-preset { background:rgba(6,182,212,0.12); color:#22d3ee; }
    .fb-badge-input { background:rgba(251,191,36,0.12); color:#fbbf24; }

    .fb-n-body { padding:8px 12px; font-size:0.72rem; color:#64748b; line-height:1.5; max-height:50px; overflow:hidden; }
    .fb-n-foot { display:flex; flex-wrap:wrap; gap:6px; padding:8px 12px; border-top:1px solid rgba(255,255,255,0.04); }
    .fb-pill { position:relative; display:inline-flex; align-items:center; font-size:0.65rem; padding:2px 22px 2px 8px; border-radius:50px; background:rgba(255,255,255,0.04); color:#94a3b8; border:1px solid rgba(255,255,255,0.06); }
    .fb-pill .fb-port-out { right: 6px; left: auto; top: 50%; bottom: auto; transform: translateY(-50%); width: 10px; height: 10px; border-width: 1.5px; }
    .fb-pill .fb-port-out:hover { transform: translateY(-50%) scale(1.3); }

    /* ═══ Ports ═══ */
    .fb-port { position:absolute; width:16px; height:16px; border-radius:50%; border:2.5px solid #10b981; background:#0a0c10; cursor:crosshair; z-index:20; transition:all 0.15s; }
    .fb-port:hover { background:#10b981; transform:translateX(-50%) scale(1.3); box-shadow:0 0 10px rgba(16,185,129,0.5); }
    .fb-port-in { top:-8px; left:50%; transform:translateX(-50%); }
    .fb-port-out { bottom:-8px; left:50%; transform:translateX(-50%); }

    /* ═══ Editor Panel ═══ */
    .fb-editor { width:0; overflow:hidden; background:#0f1117; border-left:1px solid rgba(255,255,255,0.06); transition:width 0.3s cubic-bezier(0.4,0,0.2,1); flex-shrink:0; }
    .fb-editor.open { width:380px; overflow-y:auto; }
    .fb-editor::-webkit-scrollbar { width:5px; }
    .fb-editor::-webkit-scrollbar-track { background:transparent; }
    .fb-editor::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.08); border-radius:10px; }

    .fb-ed { width:380px; padding:0; }
    .fb-ed-header { position:sticky; top:0; z-index:10; padding:16px 18px; background:#0f1117; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:space-between; }
    .fb-ed-header h3 { font-size:0.95rem; font-weight:700; color:#e2e8f0; margin:0; }
    .fb-ed-close { background:none; border:none; color:#64748b; font-size:1.3rem; cursor:pointer; width:30px; height:30px; display:flex; align-items:center; justify-content:center; border-radius:6px; transition:all 0.15s; }
    .fb-ed-close:hover { background:rgba(255,255,255,0.06); color:#fff; }

    .fb-ed-body { padding:16px 18px; }

    .fb-fg { margin-bottom:14px; }
    .fb-fg label { display:block; font-size:0.73rem; font-weight:600; color:#94a3b8; margin-bottom:5px; text-transform:uppercase; letter-spacing:0.5px; }
    .fb-fg input, .fb-fg textarea, .fb-fg select { width:100%; background:#1a1d26; border:1px solid rgba(255,255,255,0.08); color:#e2e8f0; padding:9px 12px; border-radius:8px; font-size:0.85rem; font-family:Inter,sans-serif; outline:none; transition:border 0.15s; }
    .fb-fg input:focus, .fb-fg textarea:focus, .fb-fg select:focus { border-color:var(--neon-cyan); }
    .fb-fg textarea { min-height:60px; resize:vertical; line-height:1.5; }

    .fb-chk { display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.82rem; color:#cbd5e1; }
    .fb-chk input { width:16px; height:16px; accent-color:var(--neon-emerald); }

    .fb-save { width:100%; padding:10px; background:linear-gradient(135deg,#10b981,#06b6d4); border:none; color:#fff; font-size:0.85rem; font-weight:700; border-radius:8px; cursor:pointer; font-family:Inter,sans-serif; transition:opacity 0.15s; }
    .fb-save:hover { opacity:0.9; }
    .fb-del-btn { width:100%; padding:9px; background:rgba(244,63,94,0.08); border:1px solid rgba(244,63,94,0.2); color:#f43f5e; font-size:0.82rem; font-weight:600; border-radius:8px; cursor:pointer; font-family:Inter,sans-serif; margin-top:8px; transition:all 0.15s; }
    .fb-del-btn:hover { background:rgba(244,63,94,0.15); }

    .fb-divider { border:none; border-top:1px solid rgba(255,255,255,0.05); margin:18px 0; }

    .fb-section-title { font-size:0.78rem; font-weight:700; color:#cbd5e1; margin:0 0 12px; display:flex; align-items:center; justify-content:space-between; }

    /* Option rows */
    .fb-opt { background:#14161d; border:1px solid rgba(255,255,255,0.05); border-radius:10px; padding:12px; margin-bottom:10px; position:relative; }
    .fb-opt-del { position:absolute; top:8px; right:8px; background:none; border:none; color:#f43f5e; cursor:pointer; font-size:1rem; opacity:0.5; transition:opacity 0.15s; }
    .fb-opt-del:hover { opacity:1; }
    .fb-opt .fb-fg { margin-bottom:8px; }
    .fb-opt .fb-fg:last-child { margin-bottom:0; }
    .fb-opt-save { width:100%; padding:7px; background:rgba(6,182,212,0.1); border:1px solid rgba(6,182,212,0.2); color:#22d3ee; font-size:0.78rem; font-weight:600; border-radius:6px; cursor:pointer; font-family:Inter,sans-serif; margin-top:6px; transition:all 0.15s; }
    .fb-opt-save:hover { background:rgba(6,182,212,0.2); }

    .fb-add-opt { width:100%; padding:10px; background:rgba(99,102,241,0.06); border:1.5px dashed rgba(99,102,241,0.25); color:#818cf8; font-size:0.8rem; font-weight:600; border-radius:8px; cursor:pointer; font-family:Inter,sans-serif; transition:all 0.15s; }
    .fb-add-opt:hover { background:rgba(99,102,241,0.12); border-color:#818cf8; }

    /* ═══ Zoom label ═══ */
    .fb-zoom { position:absolute; bottom:14px; right:14px; background:rgba(15,17,23,0.9); color:#64748b; padding:5px 12px; border-radius:6px; font-size:0.75rem; z-index:50; border:1px solid rgba(255,255,255,0.05); font-family:Inter,sans-serif; }

    /* ═══ Connection drawing hint ═══ */
    .fb-hint { position:absolute; bottom:14px; left:14px; background:rgba(251,191,36,0.1); color:#fbbf24; padding:6px 14px; border-radius:6px; font-size:0.75rem; z-index:50; border:1px solid rgba(251,191,36,0.2); font-family:Inter,sans-serif; display:none; }
    .fb-hint.show { display:block; }

    @media (max-width: 768px) {
        .fb-wrap { height: auto; min-height: 500px; flex-direction: column; }
        .fb-canvas-area { height: 400px; }
        .fb-editor.open { width: 100%; position: relative; border-left: none; border-top: 1px solid rgba(255,255,255,0.06); height: auto; max-height: 500px; overflow-y: auto; }
        .fb-ed { width: 100%; }
        .fb-fg label { font-size: 0.8rem; }
        .fb-fg input, .fb-fg textarea, .fb-fg select { font-size: 1rem; }
    }
    </style>
</head>
<body dir="<?= isRTL() ? 'rtl' : 'ltr' ?>">

<div class="admin-layout flex w-full h-screen overflow-hidden">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0 bg-[#0b0e14]">
        <header class="h-20 flex items-center justify-between px-8 bg-glass-bg border-b border-white/5 shrink-0 backdrop-blur-xl sticky top-0 z-[100]">
            <div class="flex flex-col">
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] mb-1 hidden sm:block">Intelligence Gathering</div>
                <h1 class="text-xl font-black text-white tracking-tight flex items-center gap-3 group">
                    <span class="text-neon-cyan drop-shadow-[0_0_8px_rgba(6,182,212,0.4)]">Synthetic Logic</span>
                    <span class="opacity-20 translate-y-px hidden sm:inline">/</span>
                    <span class="text-sm tracking-widest text-slate-400 uppercase font-black hidden sm:inline">Flow Builder</span>
                </h1>
            </div>
            <div class="flex items-center gap-4 flex-wrap justify-end">
                <div class="flex items-center gap-2">
                    <button onclick="FB.centerView()" class="px-4 py-2 bg-white/5 hover:bg-white/10 text-white text-[9px] font-black uppercase tracking-widest rounded-lg transition-all border border-white/10 flex items-center gap-1.5">
                        <i class="ph ph-arrows-out"></i> <span class="hidden sm:inline">Center</span>
                    </button>
                    <button onclick="FB.addNode()" class="px-4 py-2 bg-neon-cyan hover:bg-cyan-400 text-black text-[9px] font-black uppercase tracking-widest rounded-lg transition-all shadow-lg active:scale-95 flex items-center gap-1.5">
                        <i class="ph ph-plus-circle text-base"></i> <span class="hidden sm:inline">Provision</span>
                    </button>
                </div>
                <?php require __DIR__ . '/partials/_topbar.php'; ?>
            </div>
        </header>

        <div class="fb-wrap">
            <div class="fb-canvas-area" id="canvasArea">
                <div class="fb-canvas" id="fbCanvas">
                    <svg class="fb-svg" id="fbSvg"></svg>
                </div>
                <div class="fb-zoom" id="zoomLabel">100%</div>
                <div class="fb-hint" id="linkHint">🔗 Drag to a node's input port to connect...</div>
            </div>

            <div class="fb-editor" id="edPanel">
                <div class="fb-ed" id="edInner"></div>
            </div>
        </div>
    </div>
</div>

<script>
const API = '<?= baseUrl("admin/chatbot") ?>';
const LOC = <?= json_encode(SUPPORTED_LOCALES) ?>;
const LS_KEY = 'fb_canvas_state';

const FB = {
    nodes: [],
    selId: null,
    drag: null,
    dragOff: {x:0,y:0},
    pan: null,
    offset: {x:0,y:0},
    zoom: 1,

    centerView() {
        const area = document.getElementById('canvasArea');
        const canvas = document.getElementById('fbCanvas');
        if (!area || !canvas) return;
        
        // Find center of current nodes or default to 0,0
        if (this.nodes.length > 0) {
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            this.nodes.forEach(n => {
                minX = Math.min(minX, n.pos_x); minY = Math.min(minY, n.pos_y);
                maxX = Math.max(maxX, n.pos_x + 240); maxY = Math.max(maxY, n.pos_y + 150);
            });
            const midX = (minX + maxX) / 2;
            const midY = (minY + maxY) / 2;
            this.offset.x = (area.offsetWidth / 2) - (midX * this.zoom);
            this.offset.y = (area.offsetHeight / 2) - (midY * this.zoom);
        } else {
            this.offset = {x: 50, y: 50};
        }
        this.applyTransform();
        this.saveCanvasState();
    },
    // Linking state
    linking: false,
    linkFrom: null, // { nodeId, optionId }
    tempLine: null,

    async init() {
        // Restore canvas state from localStorage
        const saved = localStorage.getItem(LS_KEY);
        if (saved) {
            try {
                const s = JSON.parse(saved);
                this.offset = s.offset || {x:0,y:0};
                this.zoom = s.zoom || 1;
            } catch(e) {}
        }
        this.applyTransform();
        document.getElementById('zoomLabel').textContent = Math.round(this.zoom*100)+'%';

        await this.loadAll();
        this.setupEvents();
    },

    saveCanvasState() {
        localStorage.setItem(LS_KEY, JSON.stringify({ offset: this.offset, zoom: this.zoom }));
    },

    applyTransform() {
        document.getElementById('fbCanvas').style.transform =
            `translate(${this.offset.x}px,${this.offset.y}px) scale(${this.zoom})`;
    },

    async api(action, data={}) {
        const fd = new FormData();
        fd.append('api_action', action);
        for (const [k,v] of Object.entries(data)) fd.append(k, v);
        const r = await fetch(API, {method:'POST', body:fd});
        return r.json();
    },

    async loadAll() {
        const r = await this.api('get_all');
        if (r.success) { this.nodes = r.nodes; this.render(); }
    },

    render() {
        const c = document.getElementById('fbCanvas');
        c.querySelectorAll('.fb-node').forEach(n=>n.remove());

        this.nodes.forEach(n => {
            const el = document.createElement('div');
            el.className = 'fb-node' + (n.is_root==1?' root':'') + (n.id==this.selId?' sel':'');
            el.dataset.id = n.id;
            el.style.left = (n.pos_x||100)+'px';
            el.style.top = (n.pos_y||100)+'px';

            const rb = n.reply_type==='user_input'
                ? '<span class="fb-badge fb-badge-input">Input</span>'
                : '<span class="fb-badge fb-badge-preset">Preset</span>';
            const rootB = n.is_root==1 ? '<span class="fb-badge fb-badge-root">Start</span>' : '';
            const msg = (n.translations?.en||'').substring(0,70);
            const pills = (n.options||[]).map(o=>
                `<span class="fb-pill" data-option="${o.id}">
                    ${this.esc(o.translations?.en||'?')}
                    <div class="fb-port fb-port-out" data-port="out" data-node="${n.id}" data-option="${o.id}"></div>
                </span>`
            ).join('');

            el.innerHTML = `
                <div class="fb-port fb-port-in" data-port="in" data-node="${n.id}"></div>
                <div class="fb-n-head">
                    <span class="fb-n-name">${this.esc(n.name)}</span>
                    ${rootB}${rb}
                </div>
                <div class="fb-n-body">${this.esc(msg)}</div>
                ${pills?`<div class="fb-n-foot">${pills}</div>`:''}
            `;

            // Node drag
            el.addEventListener('mousedown', (e) => {
                if (e.target.dataset.port) return;
                e.stopPropagation();
                this.drag = n.id;
                const rect = el.getBoundingClientRect();
                this.dragOff = {x:e.clientX-rect.left, y:e.clientY-rect.top};
                el.style.zIndex = 100;
            });

            // Select on click
            el.addEventListener('click', (e) => {
                if (e.target.dataset.port) return;
                this.selectNode(n.id);
            });

            el.addEventListener('dblclick', (e) => { e.preventDefault(); this.selectNode(n.id); });

            c.appendChild(el);
        });

        this.drawLines();
    },

    drawLines() {
        const svg = document.getElementById('fbSvg');
        // Remove old paths (keep temp line if linking)
        svg.querySelectorAll('path:not(.temp)').forEach(p=>p.remove());

        this.nodes.forEach(n => {
            (n.options||[]).forEach(o => {
                if (o.action_type==='goto_node' && o.next_node_id) {
                    const fromNode = document.querySelector(`.fb-node[data-id="${n.id}"]`);
                    const fromPill = fromNode?.querySelector(`.fb-pill[data-option="${o.id}"]`);
                    const toNode = document.querySelector(`.fb-node[data-id="${o.next_node_id}"]`);
                    
                    if (fromNode && toNode) {
                        let x1, y1;
                        if (fromPill) {
                            const rect = fromPill.getBoundingClientRect();
                            const cRect = document.getElementById('fbCanvas').getBoundingClientRect();
                            x1 = (rect.right - cRect.left) / this.zoom;
                            y1 = (rect.top + rect.height/2 - cRect.top) / this.zoom;
                        } else {
                            x1 = parseInt(fromNode.style.left) + fromNode.offsetWidth/2;
                            y1 = parseInt(fromNode.style.top) + fromNode.offsetHeight;
                        }

                        const x2 = parseInt(toNode.style.left) + toNode.offsetWidth/2;
                        const y2 = parseInt(toNode.style.top);
                        const dy = Math.abs(y2-y1);
                        const cp = Math.max(50, dy*0.5);
                        const p = document.createElementNS('http://www.w3.org/2000/svg','path');
                        p.setAttribute('d',`M${x1},${y1} C${x1},${y1+cp} ${x2},${y2-cp} ${x2},${y2}`);
                        if (n.id==this.selId) p.classList.add('hl');
                        svg.appendChild(p);
                    }
                }
            });
        });
    },

    setupEvents() {
        const area = document.getElementById('canvasArea');
        const canvas = document.getElementById('fbCanvas');
        const svg = document.getElementById('fbSvg');
        const hint = document.getElementById('linkHint');

        // ── Out port mousedown → start linking ──
        canvas.addEventListener('mousedown', (e) => {
            const port = e.target.closest('[data-port="out"]');
            if (port) {
                e.stopPropagation();
                this.linking = true;
                this.linkFrom = { 
                    nodeId: parseInt(port.dataset.node),
                    optionId: parseInt(port.dataset.option)
                };
                hint.classList.add('show');

                // Create temp SVG line
                const pillEl = e.target.closest('.fb-pill');
                const portRect = port.getBoundingClientRect();
                const cRect = canvas.getBoundingClientRect();
                const x1 = (portRect.left + portRect.width/2 - cRect.left) / this.zoom;
                const y1 = (portRect.top + portRect.height/2 - cRect.top) / this.zoom;

                this.tempLine = document.createElementNS('http://www.w3.org/2000/svg','path');
                this.tempLine.classList.add('temp');
                this.tempLine.setAttribute('d',`M${x1},${y1} L${x1},${y1}`);
                svg.appendChild(this.tempLine);
                return;
            }
        });

        // ── Pan start ──
        area.addEventListener('mousedown', (e) => {
            if (this.linking) return;
            if (e.target===area || e.target===canvas || e.target.tagName==='svg') {
                this.pan = {x:e.clientX-this.offset.x, y:e.clientY-this.offset.y};
                area.classList.add('grabbing');
            }
        });

        // ── Mousemove: drag node / pan canvas / link line ──
        document.addEventListener('mousemove', (e) => {
            const areaRect = area.getBoundingClientRect();

            // Linking: update temp line
            if (this.linking && this.tempLine && this.linkFrom) {
                let x1, y1;
                const nodeEl = document.querySelector(`.fb-node[data-id="${this.linkFrom.nodeId}"]`);
                const portEl = this.linkFrom.optionId 
                    ? nodeEl?.querySelector(`.fb-pill[data-option="${this.linkFrom.optionId}"] .fb-port-out`)
                    : nodeEl?.querySelector('.fb-port-out');
                
                if (portEl) {
                    const portRect = portEl.getBoundingClientRect();
                    const cRect = canvas.getBoundingClientRect();
                    x1 = (portRect.left + portRect.width/2 - cRect.left) / this.zoom;
                    y1 = (portRect.top + portRect.height/2 - cRect.top) / this.zoom;
                } else {
                    x1 = parseInt(nodeEl.style.left) + nodeEl.offsetWidth/2;
                    y1 = parseInt(nodeEl.style.top) + nodeEl.offsetHeight;
                }

                const mx = (e.clientX - areaRect.left - this.offset.x) / this.zoom;
                const my = (e.clientY - areaRect.top - this.offset.y) / this.zoom;
                const dy = Math.abs(my-y1);
                const cp = Math.max(40, dy*0.4);
                this.tempLine.setAttribute('d',`M${x1},${y1} C${x1},${y1+cp} ${mx},${my-cp} ${mx},${my}`);
                return;
            }

            // Dragging node
            if (this.drag) {
                const el = document.querySelector(`.fb-node[data-id="${this.drag}"]`);
                if (el) {
                    const nx = (e.clientX - areaRect.left - this.offset.x)/this.zoom - this.dragOff.x;
                    const ny = (e.clientY - areaRect.top - this.offset.y)/this.zoom - this.dragOff.y;
                    el.style.left = Math.max(0,nx)+'px';
                    el.style.top = Math.max(0,ny)+'px';
                    const nd = this.nodes.find(n=>n.id==this.drag);
                    if (nd) { nd.pos_x = Math.max(0,Math.round(nx)); nd.pos_y = Math.max(0,Math.round(ny)); }
                    this.drawLines();
                }
                return;
            }

            // Panning
            if (this.pan) {
                this.offset.x = e.clientX - this.pan.x;
                this.offset.y = e.clientY - this.pan.y;
                this.applyTransform();
            }
        });

        // ── Mouseup ──
        document.addEventListener('mouseup', (e) => {
            // Finish linking
            if (this.linking) {
                hint.classList.remove('show');
                if (this.tempLine) { this.tempLine.remove(); this.tempLine=null; }

                // Check if dropped on an input port
                const target = document.elementFromPoint(e.clientX, e.clientY);
                const inPort = target?.closest('[data-port="in"]');
                if (inPort && this.linkFrom) {
                    const targetNodeId = parseInt(inPort.dataset.node);
                    if (targetNodeId !== this.linkFrom.nodeId) {
                        this.createConnection(this.linkFrom.nodeId, targetNodeId, this.linkFrom.optionId);
                    }
                }
                this.linking = false;
                this.linkFrom = null;
                return;
            }

            // Finish dragging node — auto save position
            if (this.drag) {
                const el = document.querySelector(`.fb-node[data-id="${this.drag}"]`);
                if (el) el.style.zIndex = 10;
                this.autoSavePos(this.drag);
                this.drag = null;
            }

            // Finish panning
            if (this.pan) {
                this.pan = null;
                area.classList.remove('grabbing');
                this.saveCanvasState();
            }
        });

        // ── Zoom ──
        area.addEventListener('wheel', (e) => {
            e.preventDefault();
            this.zoom = Math.max(0.25, Math.min(2.5, this.zoom + (e.deltaY>0?-0.06:0.06)));
            this.applyTransform();
            document.getElementById('zoomLabel').textContent = Math.round(this.zoom*100)+'%';
            this.saveCanvasState();
        }, {passive:false});
    },

    async createConnection(fromNodeId, toNodeId, optionId = null) {
        const node = this.nodes.find(n=>n.id==fromNodeId);
        if (!node) return;

        if (optionId) {
            const opt = (node.options||[]).find(o=>o.id==optionId);
            if (opt) {
                await this.api('save_option', {
                    option_id: opt.id,
                    node_id: fromNodeId,
                    action_type: 'goto_node',
                    next_node_id: toNodeId,
                    action_value: opt.action_value||'',
                    sort_order: opt.sort_order||0,
                    ...this.optionLabelPayload(opt)
                });
            }
        } else {
            // Fallback: Find or create an option on fromNode that goes to toNodeId
            const unlinked = (node.options||[]).find(o=>o.action_type==='goto_node' && !o.next_node_id);
            if (unlinked) {
                await this.api('save_option', {
                    option_id: unlinked.id,
                    node_id: fromNodeId,
                    action_type: 'goto_node',
                    next_node_id: toNodeId,
                    action_value: '',
                    sort_order: unlinked.sort_order||0,
                    ...this.optionLabelPayload(unlinked)
                });
            } else {
                const data = {
                    option_id: 0, node_id: fromNodeId,
                    action_type: 'goto_node', next_node_id: toNodeId,
                    action_value: '', sort_order: (node.options||[]).length
                };
                LOC.forEach(l => data['label_'+l] = 'Go');
                await this.api('save_option', data);
            }
        }
        await this.loadAll();
        if (this.selId) this.openEditor(this.selId);
    },

    optionLabelPayload(opt) {
        const d = {};
        LOC.forEach(l => d['label_'+l] = opt.translations?.[l] || 'Go');
        return d;
    },

    async autoSavePos(nodeId) {
        const nd = this.nodes.find(n=>n.id==nodeId);
        if (!nd) return;
        const positions = [{ id:nd.id, x:nd.pos_x||100, y:nd.pos_y||100 }];
        await this.api('save_positions', { positions: JSON.stringify(positions) });
    },

    selectNode(id) {
        this.selId = id;
        document.querySelectorAll('.fb-node').forEach(n=>n.classList.remove('sel'));
        const el = document.querySelector(`.fb-node[data-id="${id}"]`);
        if (el) el.classList.add('sel');
        this.drawLines();
        this.openEditor(id);
    },

    openEditor(nid) {
        const n = this.nodes.find(x=>x.id==nid);
        if (!n) return;
        const panel = document.getElementById('edPanel');
        const inner = document.getElementById('edInner');

        const msgs = LOC.map(l=>`
            <div class="fb-fg">
                <label>Message (${l.toUpperCase()})</label>
                <textarea id="em_${l}" ${l==='ar'?'dir="rtl"':''}>${this.esc(n.translations?.[l]||'')}</textarea>
            </div>
        `).join('');

        const opts = (n.options||[]).map(o => {
            const labels = LOC.map(l=>`
                <div class="fb-fg">
                    <label>${l.toUpperCase()} Label</label>
                    <input id="eo_${o.id}_l_${l}" value="${this.esc(o.translations?.[l]||'')}" ${l==='ar'?'dir="rtl"':''}>
                </div>
            `).join('');

            const nsel = this.nodes.map(x=>
                `<option value="${x.id}" ${x.id==o.next_node_id?'selected':''}>${this.esc(x.name)}</option>`
            ).join('');

            return `<div class="fb-opt">
                <button class="fb-opt-del" onclick="FB.delOpt(${o.id})" title="Delete option">&times;</button>
                ${labels}
                <div class="fb-fg">
                    <label>Action</label>
                    <select id="eo_${o.id}_act">
                        <option value="goto_node" ${o.action_type==='goto_node'?'selected':''}>Go to Node</option>
                        <option value="link" ${o.action_type==='link'?'selected':''}>Open Link</option>
                        <option value="call" ${o.action_type==='call'?'selected':''}>Phone Call</option>
                    </select>
                </div>
                <div class="fb-fg">
                    <label>Target Node</label>
                    <select id="eo_${o.id}_next"><option value="">—</option>${nsel}</select>
                </div>
                <div class="fb-fg">
                    <label>URL / Phone</label>
                    <input id="eo_${o.id}_val" value="${this.esc(o.action_value||'')}">
                </div>
                <div style="display:flex; gap:6px;">
                    <button class="fb-opt-save" style="flex:1;" onclick="FB.saveOpt(${o.id},${n.id})">Save Option</button>
                    ${o.next_node_id ? `<button class="fb-opt-save" style="flex:1; background:rgba(244,63,94,0.1); border-color:rgba(244,63,94,0.2); color:#f43f5e;" onclick="FB.breakLink(${o.id},${n.id})">Break Link</button>`:''}
                </div>
            </div>`;
        }).join('');

        inner.innerHTML = `
            <div class="fb-ed-header">
                <h3>Node #${n.id}</h3>
                <button class="fb-ed-close" onclick="FB.closeEditor()">&times;</button>
            </div>
            <div class="fb-ed-body">
                <div class="fb-fg">
                    <label>Name</label>
                    <input type="text" id="en" value="${this.esc(n.name)}">
                </div>
                <div class="fb-fg">
                    <label>Reply Type</label>
                    <select id="ert" onchange="document.getElementById('evar').style.display=this.value==='user_input'?'':'none'">
                        <option value="preset" ${n.reply_type==='preset'?'selected':''}>Preset Buttons</option>
                        <option value="user_input" ${n.reply_type==='user_input'?'selected':''}>User Text Input</option>
                    </select>
                </div>
                <div class="fb-fg" id="evar" style="${n.reply_type==='user_input'?'':'display:none'}">
                    <label>Input Variable</label>
                    <input id="eiv" value="${this.esc(n.input_var_name||'')}" placeholder="e.g. user_name">
                </div>
                <label class="fb-chk" style="margin-bottom:14px;">
                    <input type="checkbox" id="eroot" ${n.is_root==1?'checked':''}>
                    Starting Node (Welcome)
                </label>
                ${msgs}
                <button class="fb-save" onclick="FB.saveNode()">💾 Save Node</button>
                <button class="fb-del-btn" onclick="FB.delNode(${n.id})">🗑 Delete Node</button>

                <hr class="fb-divider">
                <div class="fb-section-title">Options / Buttons</div>
                ${opts}
                <button class="fb-add-opt" onclick="FB.addOpt(${n.id})">+ Add Option</button>
            </div>
        `;

        panel.classList.add('open');
    },

    closeEditor() {
        document.getElementById('edPanel').classList.remove('open');
        this.selId = null;
        document.querySelectorAll('.fb-node').forEach(n=>n.classList.remove('sel'));
        this.drawLines();
    },

    async saveNode() {
        const n = this.nodes.find(x=>x.id==this.selId);
        if (!n) return;
        const d = {
            node_id:n.id, name:document.getElementById('en').value,
            is_root:document.getElementById('eroot').checked?1:0,
            pos_x:n.pos_x||100, pos_y:n.pos_y||100,
            reply_type:document.getElementById('ert').value,
            input_var_name:document.getElementById('eiv')?.value||''
        };
        LOC.forEach(l=> d['message_'+l] = document.getElementById('em_'+l)?.value||'');
        const r = await this.api('save_node', d);
        if (r.success) { await this.loadAll(); this.openEditor(n.id); }
    },

    async addNode() {
        const px = Math.round((-this.offset.x/this.zoom)+250+Math.random()*150);
        const py = Math.round((-this.offset.y/this.zoom)+200+Math.random()*150);
        const d = {node_id:0, name:'New Node', is_root:0, pos_x:px, pos_y:py, reply_type:'preset', input_var_name:''};
        LOC.forEach(l=> d['message_'+l] = '');
        const r = await this.api('save_node', d);
        if (r.success) { await this.loadAll(); this.selectNode(r.id); }
    },

    delNode(id) {
        showDeleteModal('Node', async () => {
            const el = document.querySelector(`.fb-node[data-id="${id}"]`);
            if (el) el.style.opacity = '0.5';
            const r = await this.api('delete_node', {node_id:id});
            if (r.success) {
                this.closeEditor();
                await this.loadAll();
            }
        });
    },

    async saveOpt(oid, nid) {
        const btn = event.target;
        const oldText = btn.textContent;
        btn.textContent = 'Saving...';
        btn.disabled = true;
        
        const d = {
            option_id:oid, node_id:nid,
            action_type: document.getElementById(`eo_${oid}_act`).value,
            next_node_id: document.getElementById(`eo_${oid}_next`).value,
            action_value: document.getElementById(`eo_${oid}_val`).value,
            sort_order: 0
        };
        LOC.forEach(l=> d['label_'+l] = document.getElementById(`eo_${oid}_l_${l}`)?.value||'');
        const r = await this.api('save_option', d);
        if (r.success) { await this.loadAll(); this.openEditor(nid); }
    },

    async addOpt(nid) {
        const btn = event.target;
        btn.textContent = 'Adding...';
        btn.disabled = true;
        const d = {option_id:0, node_id:nid, action_type:'goto_node', next_node_id:'', action_value:'', sort_order:0};
        LOC.forEach(l=> d['label_'+l] = 'New Option');
        const r = await this.api('save_option', d);
        if (r.success) { await this.loadAll(); this.openEditor(nid); }
    },

    delOpt(oid) {
        const btn = document.querySelector(`.fb-opt-del[onclick="FB.delOpt(${oid})"]`);
        showDeleteModal('Option', async () => {
            if (btn) btn.style.opacity = '0.3';
            const r = await this.api('delete_option', {option_id:oid});
            if (r.success && this.selId) { 
                await this.loadAll(); 
                this.openEditor(this.selId); 
            }
        });
    },
    
    async breakLink(oid, nid) {
        const d = {
            option_id:oid, node_id:nid,
            action_type: 'goto_node',
            next_node_id: '',
            action_value: '',
            sort_order: 0
        };
        // Preserve labels
        const opt = this.nodes.find(n=>n.id==nid).options.find(o=>o.id==oid);
        LOC.forEach(l=> d['label_'+l] = opt.translations?.[l]||'');
        
        const r = await this.api('save_option', d);
        if (r.success) { await this.loadAll(); this.openEditor(nid); }
    },

    esc(s) { const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
};

document.addEventListener('DOMContentLoaded', ()=>FB.init());
</script>
<?php require __DIR__ . '/partials/_delete_modal.php'; ?>
</body>
</html>
