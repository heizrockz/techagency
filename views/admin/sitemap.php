<?php
$title = "Sitemap Settings";
ob_start();
?>

<div class="admin-header">
    <div class="header-left">
        <h1>Sitemap Manager</h1>
        <p>Generate and edit your search engine structure.</p>
    </div>
    <div class="header-actions">
        <form method="POST" style="display:inline;">
            <input type="hidden" name="action" value="regenerate">
            <button type="submit" class="btn btn-secondary" onclick="return confirm('This will reload the sitemap from the current database records. Any manual changes will be lost. Proceed?')">
                <i class="lucide-refresh"></i> Regenerate Now
            </button>
        </form>
    </div>
</div>

<?php if ($saved): ?>
    <div class="alert alert-success">Sitemap has been updated successfully.</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">sitemap.xml Editor</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="save">
            <div class="form-group">
                <label for="sitemap_content">Raw XML Content</label>
                <textarea name="sitemap_content" id="sitemap_content" class="form-control" 
                          style="min-height: 500px; font-family: monospace; background: #1e1e1e; color: #d4d4d4; line-height: 1.5; padding: 20px;"
                ><?= htmlspecialchars($currentContent) ?></textarea>
            </div>
            
            <div class="form-actions" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Save Manual Changes</button>
                <a href="<?= baseUrl('sitemap.xml') ?>" target="_blank" class="btn btn-secondary">View Live XML</a>
            </div>
        </form>
    </div>
</div>

<style>
#sitemap_content {
    resize: vertical;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.1);
}
#sitemap_content:focus {
    outline: none;
    border-color: var(--neon-emerald);
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/admin_layout.php';
?>
