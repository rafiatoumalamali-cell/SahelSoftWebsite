<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.content-edit-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.edit-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.content-form {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-dark);
}

.form-group .required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

textarea.form-control {
    min-height: 300px;
    resize: vertical;
    font-family: 'Courier New', monospace;
}

.form-help {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 5px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-block;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-secondary {
    background: #6b7280;
    color: white;
    margin-left: 10px;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-success {
    background: #10b981;
    color: white;
    margin-left: 10px;
}

.btn-success:hover {
    background: #059669;
}

.page-info {
    background: #f0f9ff;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
    margin-bottom: 25px;
}

.page-info h4 {
    margin: 0 0 10px 0;
    color: var(--text-dark);
}

.page-info p {
    margin: 5px 0;
    color: var(--text-muted);
}

.status-indicator {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
    margin-left: 10px;
}

.status-published {
    background: #d1fae5;
    color: #065f46;
}

.status-draft {
    background: #f3f4f6;
    color: #6b7280;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 8px;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
}

.radio-option input[type="radio"] {
    margin: 0;
}

.character-count {
    font-size: 0.85rem;
    color: var(--text-muted);
    text-align: right;
    margin-top: 5px;
}

.preview-section {
    background: #f8fafc;
    padding: 25px;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
    margin-top: 20px;
}

.preview-section h4 {
    margin: 0 0 15px 0;
    color: var(--text-dark);
}

.preview-content {
    line-height: 1.6;
    color: var(--text-dark);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .radio-group {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<div class="content-edit-container">
    <div class="container">
        <div class="edit-header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1>Edit Content</h1>
                    <p style="margin-top: 10px; color: var(--text-muted);">
                        Page: <strong><?= htmlspecialchars($page['title']) ?></strong>
                        <span class="status-indicator status-<?= $page['status'] ?>">
                            <?= ucfirst($page['status']) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="content-form">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="page-info">
                <h4>Page Information</h4>
                <p><strong>Page Key:</strong> <code><?= htmlspecialchars($page['page_key']) ?></code></p>
                <p><strong>Last Updated:</strong> <?= date('F j, Y, g:i A', strtotime($page['updated_at'])) ?></p>
                <p><strong>Created:</strong> <?= date('F j, Y, g:i A', strtotime($page['created_at'])) ?></p>
            </div>

            <form method="POST" action="<?= APP_URL ?>/admin/content/update">
                <input type="hidden" name="page_key" value="<?= htmlspecialchars($page['page_key']) ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="form-group">
                    <label for="title">Page Title <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" required
                           value="<?= htmlspecialchars($page['title']) ?>"
                           placeholder="Enter page title">
                    <div class="form-help">This appears in the browser title and as the main heading</div>
                </div>

                <div class="form-group">
                    <label for="content">Page Content <span class="required">*</span></label>
                    <textarea name="content" id="content" class="form-control" required
                              placeholder="Enter your page content using HTML..."><?= htmlspecialchars($page['content']) ?></textarea>
                    <div class="form-help">Use HTML tags for formatting. This content will be displayed on the page.</div>
                    <div class="character-count" id="content-count">
                        <?= strlen($page['content']) ?> characters
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                  placeholder="Brief description for SEO (150-160 characters recommended)"><?= htmlspecialchars($page['meta_description']) ?></textarea>
                        <div class="form-help">Appears in search engine results</div>
                        <div class="character-count" id="meta-count">
                            <?= strlen($page['meta_description']) ?> characters
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="meta_keywords">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                               value="<?= htmlspecialchars($page['meta_keywords']) ?>"
                               placeholder="keyword1, keyword2, keyword3">
                        <div class="form-help">Comma-separated keywords for SEO</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" name="status" value="draft" id="status_draft" 
                                   <?= $page['status'] === 'draft' ? 'checked' : '' ?>>
                            <label for="status_draft">Draft</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="status" value="published" id="status_published"
                                   <?= $page['status'] === 'published' ? 'checked' : '' ?>>
                            <label for="status_published">Published</label>
                        </div>
                    </div>
                    <div class="form-help">Draft pages are not visible to visitors</div>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="<?= APP_URL ?>/admin/content" class="btn btn-secondary">Cancel</a>
                    <?php if ($page['status'] === 'draft'): ?>
                        <button type="button" class="btn btn-success" onclick="publishPage()">
                            Publish Page
                        </button>
                    <?php endif; ?>
                </div>
            </form>

            <div class="preview-section">
                <h4>Content Preview</h4>
                <div class="preview-content" id="preview-content">
                    <?= $page['content'] ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character count updates
document.getElementById('content').addEventListener('input', function() {
    document.getElementById('content-count').textContent = this.value.length + ' characters';
    updatePreview();
});

document.getElementById('meta_description').addEventListener('input', function() {
    document.getElementById('meta-count').textContent = this.value.length + ' characters';
});

// Live preview update
function updatePreview() {
    const content = document.getElementById('content').value;
    document.getElementById('preview-content').innerHTML = content;
}

// Publish page function
function publishPage() {
    if (confirm('Publish this page? It will be visible to visitors.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/content/publish';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?= csrf_token() ?>';
        
        const pageKey = document.createElement('input');
        pageKey.type = 'hidden';
        pageKey.name = 'page_key';
        pageKey.value = '<?= htmlspecialchars($page['page_key']) ?>';
        
        form.appendChild(csrfToken);
        form.appendChild(pageKey);
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-save draft (optional enhancement)
let autoSaveTimer;
document.getElementById('content').addEventListener('input', function() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(function() {
        // Auto-save logic could be implemented here
        console.log('Auto-save triggered');
    }, 30000); // Auto-save after 30 seconds of inactivity
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
