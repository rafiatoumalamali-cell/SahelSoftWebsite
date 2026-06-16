<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.file-management-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.file-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.file-header h1 {
    margin: 0;
    color: var(--text-dark);
}

.file-actions {
    display: flex;
    gap: 15px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
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

.filters-section {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.filters-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.form-control {
    padding: 10px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.file-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
    transition: all 0.3s ease;
}

.file-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.file-preview {
    height: 150px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border-color);
}

.file-preview.image {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.file-info {
    padding: 20px;
}

.file-name {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.file-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-size: 0.85rem;
    color: var(--text-muted);
}

.file-size {
    font-weight: 500;
}

.file-date {
    color: var(--text-muted);
}

.file-description {
    font-size: 0.9rem;
    color: var(--text-dark);
    margin-bottom: 15px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.file-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-bottom: 15px;
}

.file-tag {
    background: #e5e7eb;
    color: #374151;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.file-actions {
    display: flex;
    gap: 10px;
}

.file-action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.file-action-btn.primary {
    background: var(--primary-color);
    color: white;
}

.file-action-btn.secondary {
    background: #f3f4f6;
    color: var(--text-dark);
}

.file-action-btn.danger {
    background: #fef2f2;
    color: #dc2626;
}

.file-action-btn:hover {
    transform: translateY(-1px);
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

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.empty-state h3 {
    margin-bottom: 15px;
    color: var(--text-dark);
    font-size: 1.5rem;
}

.category-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 10px;
}

.category-project {
    background: #dbeafe;
    color: #1e40af;
}

.category-proposal {
    background: #f3e8ff;
    color: #8b5cf6;
}

.category-invoice {
    background: #fef3c7;
    color: #d97706;
}

.category-document {
    background: #e0e7ff;
    color: #6366f1;
}

.category-image {
    background: #dcfce7;
    color: #22c55e;
}

.category-video {
    background: #fef2f2;
    color: #dc2626;
}

.category-other {
    background: #f3f4f6;
    color: #6b7280;
}

.search-box {
    position: relative;
    margin-bottom: 20px;
}

.search-input {
    width: 100%;
    padding: 12px 20px 12px 50px;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .file-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .file-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .files-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-form {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="file-management-container">
    <div class="container">
        <div class="file-header">
            <div>
                <h1>📁 File Management</h1>
                <p style="margin-top: 10px; color: var(--text-muted);">
                    Manage, organize, and share your files securely
                </p>
            </div>
            <div class="file-actions">
                <a href="<?= APP_URL ?>/admin/files/upload" class="btn btn-primary">
                    <span>⬆️</span> Upload Files
                </a>
                <a href="<?= APP_URL ?>/admin/files/shared" class="btn btn-secondary">
                    <span>🔗</span> Shared Files
                </a>
                <a href="<?= APP_URL ?>/admin/files/stats" class="btn btn-secondary">
                    <span>📊</span> Statistics
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

        <div class="filters-section">
            <form method="GET" class="filters-form">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        <option value="project" <?= ($filters['category'] ?? '') === 'project' ? 'selected' : '' ?>>Projects</option>
                        <option value="proposal" <?= ($filters['category'] ?? '') === 'proposal' ? 'selected' : '' ?>>Proposals</option>
                        <option value="invoice" <?= ($filters['category'] ?? '') === 'invoice' ? 'selected' : '' ?>>Invoices</option>
                        <option value="document" <?= ($filters['category'] ?? '') === 'document' ? 'selected' : '' ?>>Documents</option>
                        <option value="image" <?= ($filters['category'] ?? '') === 'image' ? 'selected' : '' ?>>Images</option>
                        <option value="video" <?= ($filters['category'] ?? '') === 'video' ? 'selected' : '' ?>>Videos</option>
                        <option value="other" <?= ($filters['category'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                           placeholder="Search files...">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?= APP_URL ?>/admin/files" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= number_format($stats['total_files']) ?></div>
                <div class="stat-label">Total Files</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $this->fileModel->formatFileSize($stats['total_size']) ?></div>
                <div class="stat-label">Total Storage</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format($stats['total_downloads']) ?></div>
                <div class="stat-label">Downloads</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['categories_used'] ?></div>
                <div class="stat-label">Categories</div>
            </div>
        </div>

        <?php if (empty($files)): ?>
            <div class="empty-state">
                <h3>No files found</h3>
                <p>Start by uploading your first file or adjust your search filters.</p>
                <div style="margin-top: 20px;">
                    <a href="<?= APP_URL ?>/admin/files/upload" class="btn btn-primary">
                        <span>⬆️</span> Upload Your First File
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="files-grid">
                <?php foreach ($files as $file): ?>
                    <div class="file-card">
                        <div class="file-preview <?= $this->getFilePreviewClass($file) ?>" 
                             style="<?= $this->getFilePreviewStyle($file) ?>">
                            <?= $this->getFileIcon($file) ?>
                        </div>
                        <div class="file-info">
                            <div class="category-badge category-<?= $file['category'] ?>">
                                <?= ucfirst($file['category']) ?>
                            </div>
                            <div class="file-name" title="<?= htmlspecialchars($file['original_name']) ?>">
                                <?= htmlspecialchars($file['original_name']) ?>
                            </div>
                            <div class="file-meta">
                                <span class="file-size"><?= $this->fileModel->formatFileSize($file['file_size']) ?></span>
                                <span class="file-date"><?= date('M j, Y', strtotime($file['created_at'])) ?></span>
                            </div>
                            <?php if (!empty($file['description'])): ?>
                                <div class="file-description">
                                    <?= htmlspecialchars($file['description']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($file['tags'])): ?>
                                <div class="file-tags">
                                    <?php foreach ($file['tags'] as $tag): ?>
                                        <span class="file-tag"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="file-actions">
                                <a href="<?= APP_URL ?>/admin/files/view?id=<?= $file['id'] ?>" class="file-action-btn primary">
                                    <span>👁️</span> View
                                </a>
                                <a href="<?= APP_URL ?>/admin/files/download?id=<?= $file['id'] ?>" class="file-action-btn secondary">
                                    <span>⬇️</span> Download
                                </a>
                                <?php if ($file['uploaded_by'] == $_SESSION['user_id']): ?>
                                    <a href="<?= APP_URL ?>/admin/files/edit?id=<?= $file['id'] ?>" class="file-action-btn secondary">
                                        <span>✏️</span> Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Helper methods for the view
private function getFileIcon($file) {
    $iconMap = [
        'application/pdf' => '📄',
        'application/msword' => '📝',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '📝',
        'application/vnd.ms-excel' => '📊',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '📊',
        'application/vnd.ms-powerpoint' => '📈',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '📈',
        'image/jpeg' => '🖼️',
        'image/png' => '🖼️',
        'image/gif' => '🖼️',
        'video/mp4' => '🎥',
        'video/avi' => '🎥',
        'video/mov' => '🎥',
        'audio/mpeg' => '🎵',
        'audio/wav' => '🎵',
        'application/zip' => '📦',
        'application/x-rar-compressed' => '📦',
        'text/plain' => '📄',
    ];
    
    return $iconMap[$file['mime_type']] ?? '📎';
}

private function getFilePreviewClass($file) {
    if (strpos($file['mime_type'], 'image/') === 0) {
        return 'image';
    }
    return '';
}

private function getFilePreviewStyle($file) {
    if (strpos($file['mime_type'], 'image/') === 0) {
        return 'background-image: url(/' . str_replace(APP_ROOT . '/', '', $file['file_path']) . ');';
    }
    return '';
}
?>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
