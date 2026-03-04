<!-- ═══ Universal Delete Confirmation Modal ═══ -->
<div id="deleteConfirmModal" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,0.75); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s ease;">
    <div style="background:linear-gradient(145deg, #1a2333, #141924); border:1px solid rgba(255,255,255,0.08); border-radius:24px; padding:0; width:90%; max-width:420px; box-shadow:0 25px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.03); transform:scale(0.92); transition:transform 0.25s cubic-bezier(0.34,1.56,0.64,1); overflow:hidden;">
        
        <!-- Header -->
        <div style="padding:28px 28px 0; text-align:center;">
            <div style="width:56px; height:56px; margin:0 auto 16px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); border-radius:16px; display:flex; align-items:center; justify-content:center;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
            </div>
            <h3 style="color:#fff; font-size:1.125rem; font-weight:700; margin:0 0 8px; letter-spacing:-0.01em;">Confirm Deletion</h3>
            <p style="color:#94a3b8; font-size:0.8rem; line-height:1.6; margin:0;">
                You are about to permanently delete<br>
                <span id="deleteItemName" style="color:#f87171; font-weight:600;"></span>
            </p>
            <p style="color:#64748b; font-size:0.7rem; margin:12px 0 0;">This action cannot be undone.</p>
        </div>

        <!-- Actions -->
        <div style="padding:24px 28px 28px; display:flex; gap:12px; margin-top:20px;">
            <button type="button" onclick="closeDeleteConfirm()" style="flex:1; padding:12px 20px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); border-radius:14px; color:#94a3b8; font-size:0.82rem; font-weight:600; cursor:pointer; font-family:inherit; transition:all 0.15s; letter-spacing:0.02em;">
                Cancel
            </button>
            <button type="button" id="deleteConfirmBtn" onclick="executeDelete()" style="flex:1; padding:12px 20px; background:linear-gradient(135deg, #ef4444, #dc2626); border:1px solid rgba(239,68,68,0.3); border-radius:14px; color:#fff; font-size:0.82rem; font-weight:700; cursor:pointer; font-family:inherit; transition:all 0.15s; letter-spacing:0.02em; box-shadow:0 4px 16px rgba(239,68,68,0.25);">
                Delete
            </button>
        </div>
    </div>
</div>

<style>
#deleteConfirmModal button:first-of-type:hover { background:rgba(255,255,255,0.1) !important; color:#fff !important; }
#deleteConfirmModal button:last-of-type:hover { box-shadow:0 6px 24px rgba(239,68,68,0.4) !important; }
</style>

<script>
// ═══ Unified Delete Confirmation System ═══
let _deleteAction = null;  // URL string for GET deletes, or form element for POST deletes

/**
 * Show the delete confirmation modal.
 * @param {string} itemName - Display name of the item to delete
 * @param {string|HTMLFormElement} action - URL for GET deletion, or a form element for POST deletion
 */
function showDeleteModal(itemName, action) {
    _deleteAction = action;
    document.getElementById('deleteItemName').textContent = itemName;
    const modal = document.getElementById('deleteConfirmModal');
    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
        modal.querySelector('div').style.transform = 'scale(1)';
    });
}

function closeDeleteConfirm() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.style.opacity = '0';
    modal.querySelector('div').style.transform = 'scale(0.92)';
    setTimeout(() => { modal.style.display = 'none'; }, 200);
    _deleteAction = null;
}

function executeDelete() {
    if (!_deleteAction) return;
    if (typeof _deleteAction === 'string') {
        const formEl = document.getElementById(_deleteAction);
        if (formEl && formEl.tagName === 'FORM') {
            formEl.submit();
        } else {
            // GET-based delete (redirect to URL)
            window.location.href = _deleteAction;
        }
    } else if (_deleteAction instanceof HTMLFormElement) {
        // POST-based delete (submit form)
        _deleteAction.submit();
    } else if (typeof _deleteAction === 'function') {
        // Callback function
        _deleteAction();
        closeDeleteConfirm();
    }
}

// Close on backdrop click
document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteConfirm();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('deleteConfirmModal').style.display === 'flex') {
        closeDeleteConfirm();
    }
});
</script>
