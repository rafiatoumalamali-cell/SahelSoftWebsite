<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.messages-container {
    padding: 60px 0;
    margin-top: 80px;
    background: #f8fafc;
    min-height: calc(100vh - 160px);
}

.messages-grid {
    max-width: 900px;
    margin: 0 auto;
}

.messages-header {
    margin-bottom: 40px;
    text-align: center;
}

.messages-header h1 {
    font-size: 2.5rem;
    color: var(--text-dark);
    margin-bottom: 10px;
}

.project-message-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
    border-left: 5px solid var(--primary-color);
}

.project-message-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.project-info h3 {
    margin: 0 0 5px 0;
    color: var(--text-dark);
}

.project-info p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-top: 8px;
}

.status-active { background: #dcfce7; color: #166534; }
.status-completed { background: #dbeafe; color: #1e40af; }

.no-projects-box {
    text-align: center;
    padding: 80px 40px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.no-projects-box i {
    font-size: 4rem;
    color: #e2e8f0;
    margin-bottom: 20px;
}
</style>

<div class="messages-container">
    <div class="container">
        <div class="messages-grid">
            <div class="messages-header animate-up">
                <h1><?= __('messages') ?></h1>
                <p><?= __('select_project_message') ?></p>
            </div>

            <?php if (empty($projects)): ?>
                <div class="no-projects-box animate-up">
                    <i class="fas fa-comment-slash"></i>
                    <h2><?= __('no_active_projects') ?></h2>
                    <p><?= __('once_active_projects') ?></p>
                    <a href="<?= APP_URL ?>/contact" class="btn-premium" style="margin-top: 25px;">
                        <?= __('request_project') ?>
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <a href="<?= APP_URL ?>/client/project/messages?id=<?= $project['id'] ?>" style="text-decoration: none;">
                        <div class="project-message-card animate-up">
                            <div class="project-info">
                                <h3><?= htmlspecialchars($project['title']) ?></h3>
                                <p><?= __('role_assigned') ?>: <?= htmlspecialchars($project['assigned_team'] ?? 'SahelSoft Team') ?></p>
                                <span class="status-badge <?= $project['status'] === 'completed' ? 'status-completed' : 'status-active' ?>">
                                    <?= htmlspecialchars($project['status']) ?>
                                </span>
                            </div>
                            <div class="action-btn">
                                <span class="btn-secondary" style="padding: 10px 20px;">
                                    <i class="fas fa-comment-dots" style="margin-right: 8px;"></i>
                                    <?= __('open_chat') ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
