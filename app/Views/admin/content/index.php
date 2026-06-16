<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.content-management-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.content-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 30px;
}

.content-list {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.content-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.sidebar-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.content-table {
    width: 100%;
    border-collapse: collapse;
}

.content-table th {
    background: var(--bg-light);
    padding: 15px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid var(--border-color);
}

.content-table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
}

.content-table tr:hover {
    background: var(--bg-light);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-published {
    background: #d1fae5;
    color: #065f46;
}

.status-draft {
    background: #f3f4f6;
    color: #6b7280;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.85rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
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
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.recent-changes {
    max-height: 300px;
    overflow-y: auto;
}

.change-item {
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.change-item:last-child {
    border-bottom: none;
}

.change-title {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 4px;
}

.change-meta {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.search-box {
    position: relative;
    margin-bottom: 20px;
}

.search-input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.empty-state h3 {
    margin-bottom: 10px;
    color: var(--text-dark);
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

.page-key {
    font-family: monospace;
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<div class="content-management-container">
    <div class="container">
        <div class="content-header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1>Content Management</h1>
                    <p style="margin-top: 10px; color: var(--text-muted);">
                        Manage website content without touching code
                    </p>
                </div>
                <a href="<?= APP_URL ?>/admin/content/create" class="btn-sm btn-primary" style="padding: 10px 20px; font-size: 1rem;">
                    + Create New Page
                </a>
            </div>
        </div>

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

        <div class="content-grid">
            <div class="content-list">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <form method="GET" action="<?= APP_URL ?>/admin/content/search">
                        <input type="text" name="q" class="search-input" placeholder="Search content..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </form>
                </div>

                <?php if (empty($pages)): ?>
                    <div class="empty-state">
                        <h3>No content pages yet</h3>
                        <p>Create your first content page to get started</p>
                        <a href="<?= APP_URL ?>/admin/content/create" class="btn-sm btn-primary" style="margin-top: 15px;">Create Page</a>
                    </div>
                <?php else: ?>
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Page Title</th>
                                <th>Page Key</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($pages) && count($pages) > 0): ?>
                                <?php foreach ($pages as $page): ?>
                                    <?php if (is_array($page)): ?>
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: var(--text-dark);">
                                                    <?= htmlspecialchars($page['title'] ?? 'Untitled') ?>
                                                </div>
                                                <?php if (!empty($page['meta_description'])): ?>
                                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                                                        <?= htmlspecialchars(substr($page['meta_description'], 0, 100)) ?>...
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="page-key"><?= htmlspecialchars($page['page_key'] ?? 'N/A') ?></span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?= $page['status'] ?? 'draft' ?>">
                                                    <?= ucfirst($page['status'] ?? 'draft') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.85rem; color: var(--text-muted);">
                                                    <?= date('M j, Y', strtotime($page['updated_at'] ?? 'now')) ?>
                                                </div>
                                                <?php if (!empty($page['editor_name'])): ?>
                                                    <div style="font-size: 0.8rem; color: var(--text-muted);">
                                                        by <?= htmlspecialchars($page['editor_name']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?= APP_URL ?>/admin/content/edit?key=<?= htmlspecialchars($page['page_key'] ?? '') ?>" class="btn-sm btn-primary">
                                                        Edit
                                                    </a>
                                                    
                                                    <?php if (($page['status'] ?? 'draft') === 'draft'): ?>
                                                        <form method="POST" action="<?= APP_URL ?>/admin/content/publish" style="display: inline;">
                                                            <input type="hidden" name="page_key" value="<?= htmlspecialchars($page['page_key'] ?? '') ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                            <button type="submit" class="btn-sm btn-success" onclick="return confirm('Publish this page? It will be visible to visitors.')">
                                                                Publish
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form method="POST" action="<?= APP_URL ?>/admin/content/unpublish" style="display: inline;">
                                                            <input type="hidden" name="page_key" value="<?= htmlspecialchars($page['page_key'] ?? '') ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                            <button type="submit" class="btn-sm btn-warning" onclick="return confirm('Unpublish this page? It will no longer be visible to visitors.')">
                                                                Unpublish
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <?php 
                                                    $corePages = ['home', 'about', 'services'];
                                                    if (!in_array($page['page_key'] ?? null, $corePages)): 
                                                    ?>
                                                        <form method="POST" action="<?= APP_URL ?>/admin/content/delete" style="display: inline;">
                                                            <input type="hidden" name="page_key" value="<?= htmlspecialchars($page['page_key'] ?? '') ?>">
                                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                            <button type="submit" class="btn-sm btn-danger" onclick="return confirm('Delete this page? This action cannot be undone.')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="content-sidebar">
                <div class="sidebar-card">
                    <h3 style="margin-bottom: 15px;">Quick Stats</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: bold; color: var(--primary-color);">
                                <?= count($pages) ?>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Total Pages</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: bold; color: #10b981;">
                                <?= count(array_filter($pages, fn($p) => is_array($p) && ($p['status'] ?? 'draft') === 'published')) ?>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">Published</div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3 style="margin-bottom: 15px;">Recent Changes</h3>
                    <div class="recent-changes">
                        <?php if (empty($recentChanges)): ?>
                            <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                                No recent changes
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentChanges as $change): ?>
                                <?php if (is_array($change)): ?>
                                    <div class="change-item">
                                        <div class="change-title"><?= htmlspecialchars($change['title'] ?? 'Untitled') ?></div>
                                        <div class="change-meta">
                                            <?= date('M j, H:i', strtotime($change['updated_at'] ?? 'now')) ?>
                                            <?php if (!empty($change['editor_name'])): ?>
                                                • <?= htmlspecialchars($change['editor_name']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3 style="margin-bottom: 15px;">Content Tips</h3>
                    <ul style="margin: 0; padding-left: 20px; color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        <li style="margin-bottom: 8px;">Use descriptive page keys (e.g., 'about_us')</li>
                        <li style="margin-bottom: 8px;">Write SEO-friendly meta descriptions</li>
                        <li style="margin-bottom: 8px;">Keep content updated regularly</li>
                        <li style="margin-bottom: 8px;">Review content before publishing</li>
                        <li>Test pages after publishing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
