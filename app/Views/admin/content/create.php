<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.content-create-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.create-header {
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

.form-control.error {
    border-color: #ef4444;
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

.error-text {
    color: #ef4444;
    font-size: 0.85rem;
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
    min-height: 100px;
}

.validation-info {
    background: #fef3c7;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #f59e0b;
    margin-bottom: 20px;
}

.validation-info h4 {
    margin: 0 0 10px 0;
    color: #92400e;
}

.validation-info ul {
    margin: 0;
    padding-left: 20px;
    color: #92400e;
}

.validation-info li {
    margin-bottom: 5px;
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

<div class="content-create-container">
    <div class="container">
        <div class="create-header">
            <h1>Create New Page</h1>
            <p style="margin-top: 10px; color: var(--text-muted);">
                Add a new content page to your website
            </p>
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

            <div class="validation-info">
                <h4>Page Key Requirements</h4>
                <ul>
                    <li>Only letters, numbers, and underscores allowed</li>
                    <li>Must be unique (no duplicates)</li>
                    <li>Examples: 'about_us', 'services', 'contact_info'</li>
                    <li>Used in URLs: your-site.com/page/[page-key]</li>
                </ul>
            </div>

            <form method="POST" action="<?= APP_URL ?>/admin/content/create" id="create-form">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="page_key">Page Key <span class="required">*</span></label>
                        <input type="text" name="page_key" id="page_key" class="form-control" required
                               value="<?= htmlspecialchars($_POST['page_key'] ?? '') ?>"
                               placeholder="e.g., about_us"
                               pattern="[a-zA-Z0-9_]+"
                               title="Only letters, numbers, and underscores allowed">
                        <div class="form-help">Unique identifier for the page (letters, numbers, underscores only)</div>
                        <div id="page-key-error" class="error-text"></div>
                    </div>

                    <div class="form-group">
                        <label for="title">Page Title <span class="required">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required
                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                               placeholder="Enter page title">
                        <div class="form-help">This appears in the browser title and as the main heading</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Page Content <span class="required">*</span></label>
                    <textarea name="content" id="content" class="form-control" required
                              placeholder="Enter your page content using HTML..."><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                    <div class="form-help">Use HTML tags for formatting. This content will be displayed on the page.</div>
                    <div class="character-count" id="content-count">
                        <?= strlen($_POST['content'] ?? '') ?> characters
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" rows="3"
                                  placeholder="Brief description for SEO (150-160 characters recommended)"><?= htmlspecialchars($_POST['meta_description'] ?? '') ?></textarea>
                        <div class="form-help">Appears in search engine results</div>
                        <div class="character-count" id="meta-count">
                            <?= strlen($_POST['meta_description'] ?? '') ?> characters
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="meta_keywords">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                               value="<?= htmlspecialchars($_POST['meta_keywords'] ?? '') ?>"
                               placeholder="keyword1, keyword2, keyword3">
                        <div class="form-help">Comma-separated keywords for SEO</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" name="status" value="draft" id="status_draft" checked>
                            <label for="status_draft">Draft</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="status" value="published" id="status_published">
                            <label for="status_published">Published</label>
                        </div>
                    </div>
                    <div class="form-help">Draft pages are not visible to visitors</div>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">Create Page</button>
                    <a href="<?= APP_URL ?>/admin/content" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

            <div class="preview-section">
                <h4>Content Preview</h4>
                <div class="preview-content" id="preview-content">
                    <em>Start typing to see a preview of your content...</em>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Existing page keys for validation
const existingKeys = <?= json_encode($existingKeys) ?>;

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
    document.getElementById('preview-content').innerHTML = content || '<em>Start typing to see a preview of your content...</em>';
}

// Page key validation
document.getElementById('page_key').addEventListener('input', function() {
    const value = this.value;
    const errorDiv = document.getElementById('page-key-error');
    
    // Check if it matches the required pattern
    if (!/^[a-zA-Z0-9_]+$/.test(value) && value !== '') {
        errorDiv.textContent = 'Only letters, numbers, and underscores are allowed';
        this.classList.add('error');
    } else if (existingKeys.includes(value)) {
        errorDiv.textContent = 'This page key already exists';
        this.classList.add('error');
    } else {
        errorDiv.textContent = '';
        this.classList.remove('error');
    }
});

// Form validation before submission
document.getElementById('create-form').addEventListener('submit', function(e) {
    const pageKey = document.getElementById('page_key').value;
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    const errorDiv = document.getElementById('page-key-error');
    
    // Validate page key
    if (!/^[a-zA-Z0-9_]+$/.test(pageKey)) {
        errorDiv.textContent = 'Only letters, numbers, and underscores are allowed';
        e.preventDefault();
        return false;
    }
    
    if (existingKeys.includes(pageKey)) {
        errorDiv.textContent = 'This page key already exists';
        e.preventDefault();
        return false;
    }
    
    // Validate required fields
    if (!title.trim()) {
        alert('Page title is required');
        e.preventDefault();
        return false;
    }
    
    if (!content.trim()) {
        alert('Page content is required');
        e.preventDefault();
        return false;
    }
    
    return true;
});

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
