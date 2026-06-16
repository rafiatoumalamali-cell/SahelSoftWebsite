<?php include VIEW_PATH . '/layouts/header.php'; ?>

<main class="blog-page">
    <section class="blog-hero">
        <div class="container">
            <div class="hero-content">
                <h1><?= __('blog_title') ?></h1>
                <p><?= __('blog_subtitle') ?></p>
            </div>
        </div>
    </section>

    <section class="blog-main">
        <div class="container">
            <div class="blog-layout">
                <!-- Blog Posts -->
                <div class="blog-posts">
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <article class="blog-card">
                                <?php if (!empty($post['featured_image'])): ?>
                                    <div class="blog-image">
                                        <img src="<?= APP_URL ?>/<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="blog-content">
                                    <div class="blog-meta">
                                        <span class="blog-date"><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                                        <?php if (!empty($post['category'])): ?>
                                            <span class="blog-category"><?= htmlspecialchars($post['category']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h2 class="blog-title">
                                        <a href="<?= APP_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h2>
                                    
                                    <p class="blog-excerpt">
                                        <?= htmlspecialchars($post['excerpt']) ?>
                                    </p>
                                    
                                    <a href="<?= APP_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="read-more">
                                        <?= __('read_more') ?> →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-posts">
                            <h3><?= __('no_posts_found') ?></h3>
                            <p><?= __('no_posts_message') ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($totalPosts > 6): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="<?= APP_URL ?>/blog?page=<?= $currentPage - 1 ?>" class="pagination-link prev">
                                    ← <?= __('previous') ?>
                                </a>
                            <?php endif; ?>
                            
                            <span class="pagination-info">
                                <?= __('page') ?> <?= $currentPage ?>
                            </span>
                            
                            <?php if ($currentPage * $limit < $totalPosts): ?>
                                <a href="<?= APP_URL ?>/blog?page=<?= $currentPage + 1 ?>" class="pagination-link next">
                                    <?= __('next') ?> →
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="blog-sidebar">
                    <!-- Recent Posts -->
                    <div class="sidebar-widget">
                        <h3><?= __('recent_posts') ?></h3>
                        <?php if (!empty($recentPosts)): ?>
                            <ul class="recent-posts-list">
                                <?php foreach ($recentPosts as $post): ?>
                                    <li>
                                        <a href="<?= APP_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                        <small><?= date('M j, Y', strtotime($post['created_at'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p><?= __('no_recent_posts') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar-widget">
                        <h3><?= __('categories') ?></h3>
                        <?php if (!empty($categories)): ?>
                            <ul class="categories-list">
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a href="<?= APP_URL ?>/blog?category=<?= urlencode($category) ?>">
                                            <?= htmlspecialchars($category) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p><?= __('no_categories') ?></p>
                        <?php endif; ?>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
