<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.content-search-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.search-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.search-results {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.search-box {
    position: relative;
    margin-bottom: 25px;
}

.search-input {
    width: 100%;
    padding: 15px 20px 15px 50px;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    font-size: 1.1rem;
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

.result-item {
    padding: 25px;
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.3s;
}

.result-item:hover {
    background: var(--bg-light);
}

.result-item:last-child {
    border-bottom: none;
}

.result-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
    text-decoration: none;
    display: inline-block;
}

.result-title:hover {
    color: var(--primary-color);
}

.result-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.result-meta-item {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.page-key {
    font-family: monospace;
    background: #f3f4f6;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
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

.result-excerpt {
    color: var(--text-dark);
    line-height: 1.6;
    margin-bottom: 15px;
}

.highlight {
    background: #fef3c7;
    padding: 1px 3px;
    border-radius: 3px;
    font-weight: 600;
}

.result-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 8px 16px;
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

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: var(--text-muted);
}

.empty-state h3 {
    margin-bottom: 15px;
    color: var(--text-dark);
    font-size: 1.5rem;
}

.search-stats {
    background: #f0f9ff;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #3b82f6;
}

.search-stats strong {
    color: #1e40af;
}

@media (max-width: 768px) {
    .result-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .result-actions {
        flex-direction: column;
    }
    
    .btn-sm {
        width: 100%;
        text-align: center;
    }
}
</style>

<div class="content-search-container">
    <div class="container">
        <div class="search-header">
            <h1>Search Content</h1>
            <p style="margin-top: 10px; color: var(--text-muted);">
                Search through all your website content
            </p>
        </div>

        <div class="search-results">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <form method="GET" action="<?= APP_URL ?>/admin/content/search">
                    <input type="text" name="q" class="search-input" 
                           placeholder="Search content, titles, descriptions..." 
                           value="<?= htmlspecialchars($query) ?>"
                           autofocus>
                </form>
            </div>

            <?php if (!empty($query)): ?>
                <div class="search-stats">
                    Found <strong><?= count($results) ?></strong> result(s) for 
                    "<strong><?= htmlspecialchars($query) ?></strong>"
                </div>
            <?php endif; ?>

            <?php if (empty($query)): ?>
                <div class="empty-state">
                    <h3>Search Your Content</h3>
                    <p>Enter a search term above to find pages, titles, or descriptions</p>
                </div>
            <?php elseif (empty($results)): ?>
                <div class="empty-state">
                    <h3>No Results Found</h3>
                    <p>No content matches your search for "<?= htmlspecialchars($query) ?>"</p>
                    <div style="margin-top: 20px;">
                        <strong>Search tips:</strong>
                        <ul style="text-align: left; max-width: 400px; margin: 15px auto;">
                            <li>Try different keywords</li>
                            <li>Check for spelling mistakes</li>
                            <li>Use more general terms</li>
                            <li>Search for partial words</li>
                        </ul>
                    </div>
                    <div style="margin-top: 25px;">
                        <a href="<?= APP_URL ?>/admin/content/search" class="btn-sm btn-primary">Clear Search</a>
                        <a href="<?= APP_URL ?>/admin/content" class="btn-sm btn-secondary" style="margin-left: 10px;">Back to Content</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <div class="result-item">
                        <a href="<?= APP_URL ?>/admin/content/edit?key=<?= htmlspecialchars($result['page_key']) ?>" class="result-title">
                            <?= $this->highlightSearch($result['title'], $query) ?>
                        </a>
                        
                        <div class="result-meta">
                            <div class="result-meta-item">
                                <span class="page-key"><?= htmlspecialchars($result['page_key']) ?></span>
                            </div>
                            <div class="result-meta-item">
                                <span class="status-badge status-<?= $result['status'] ?>">
                                    <?= ucfirst($result['status']) ?>
                                </span>
                            </div>
                            <div class="result-meta-item">
                                Updated <?= date('M j, Y', strtotime($result['updated_at'])) ?>
                            </div>
                        </div>

                        <?php if (!empty($result['meta_description'])): ?>
                            <div class="result-excerpt">
                                <?= $this->highlightSearch($result['meta_description'], $query) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($result['content'])): ?>
                            <div class="result-excerpt">
                                <?= $this->highlightSearch(substr(strip_tags($result['content']), 0, 200), $query) ?>...
                            </div>
                        <?php endif; ?>

                        <div class="result-actions">
                            <a href="<?= APP_URL ?>/admin/content/edit?key=<?= htmlspecialchars($result['page_key']) ?>" class="btn-sm btn-primary">
                                Edit Page
                            </a>
                            <?php if ($result['status'] === 'draft'): ?>
                                <form method="POST" action="<?= APP_URL ?>/admin/content/publish" style="display: inline;">
                                    <input type="hidden" name="page_key" value="<?= htmlspecialchars($result['page_key']) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="btn-sm btn-success">Publish</button>
                                </form>
                            <?php else: ?>
                                <a href="<?= APP_URL ?>/<?= htmlspecialchars($result['page_key']) ?>" target="_blank" class="btn-sm btn-secondary">
                                    View Live
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Helper function to highlight search terms
function highlightSearch($text, $query) {
    if (empty($query)) return htmlspecialchars($text);
    
    $text = htmlspecialchars($text);
    $query = preg_quote($query, '/');
    return preg_replace("/($query)/i", '<span class="highlight">$1</span>', $text);
}
?>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
