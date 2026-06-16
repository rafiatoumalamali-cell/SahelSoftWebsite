<?php include VIEW_PATH . '/layouts/header.php'; ?>

<main class="blog-single">
    <section class="blog-post">
        <div class="container">
            <article class="post-content">
                <!-- Post Header -->
                <header class="post-header">
                    <?php if (!empty($post['featured_image'])): ?>
                        <div class="post-image">
                            <img src="<?= APP_URL ?>/<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-meta">
                        <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
                        
                        <div class="post-info">
                            <span class="post-date">
                                <i class="fas fa-calendar"></i>
                                <?= date('F j, Y', strtotime($post['created_at'])) ?>
                            </span>
                            
                            <?php if (!empty($post['author'])): ?>
                                <span class="post-author">
                                    <i class="fas fa-user"></i>
                                    <?= htmlspecialchars($post['author']) ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($post['category'])): ?>
                                <span class="post-category">
                                    <i class="fas fa-tag"></i>
                                    <?= htmlspecialchars($post['category']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </header>

                <!-- Post Content -->
                <div class="post-body">
                    <?= $post['content'] ?>
                </div>

                <!-- Post Footer -->
                <footer class="post-footer">
                    <div class="post-actions">
                        <a href="<?= APP_URL ?>/blog" class="back-to-blog">
                            ← <?= __('back_to_blog') ?>
                        </a>
                        
                        <div class="share-buttons">
                            <span><?= __('share_post') ?>:</span>
                            <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode(APP_URL . '/blog/' . $post['slug']) ?>" target="_blank">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(APP_URL . '/blog/' . $post['slug']) ?>&text=<?= urlencode($post['title']) ?>" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://linkedin.com/sharing/share-offsite/?url=<?= urlencode(APP_URL . '/blog/' . $post['slug']) ?>" target="_blank">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </footer>
            </article>

            <!-- Related Posts -->
            <?php if (!empty($relatedPosts)): ?>
                <section class="related-posts">
                    <h3><?= __('related_posts') ?></h3>
                    <div class="related-posts-grid">
                        <?php foreach ($relatedPosts as $relatedPost): ?>
                            <div class="related-post-card">
                                <?php if (!empty($relatedPost['featured_image'])): ?>
                                    <div class="related-post-image">
                                        <img src="<?= APP_URL ?>/<?= htmlspecialchars($relatedPost['featured_image']) ?>" alt="<?= htmlspecialchars($relatedPost['title']) ?>" loading="lazy">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="related-post-content">
                                    <h4>
                                        <a href="<?= APP_URL ?>/blog/<?= htmlspecialchars($relatedPost['slug']) ?>">
                                            <?= htmlspecialchars($relatedPost['title']) ?>
                                        </a>
                                    </h4>
                                    <p><?= htmlspecialchars($relatedPost['excerpt']) ?></p>
                                    <small><?= date('M j, Y', strtotime($relatedPost['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
