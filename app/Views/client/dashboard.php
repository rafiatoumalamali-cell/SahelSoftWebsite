<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Dashboard Specific Styles */
.dashboard-hero {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 60px 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.dashboard-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    padding: 35px;
    margin-bottom: 40px;
    border: 1px solid rgba(0,0,0,0.03);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--bg-light);
}

.card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-title i {
    color: var(--primary-color);
}

.project-grid {
    display: grid;
    gap: 20px;
}

.project-item {
    background: var(--bg-light);
    border-radius: 15px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid transparent;
    transition: all 0.3s ease;
}

.project-item:hover {
    background: white;
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.project-info h3 {
    margin: 0 0 8px 0;
    font-size: 1.2rem;
    color: var(--text-dark);
}

.status-badge {
    font-size: 0.8rem;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active { background: #dcfce7; color: #166534; }
.status-pending { background: #fef9c3; color: #854d0e; }
.status-completed { background: #dbeafe; color: #1e40af; }

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 4rem;
    color: var(--border-color);
    margin-bottom: 20px;
    display: block;
}

.btn-premium {
    background: var(--gradient-primary);
    color: white;
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 10px 20px rgba(14, 159, 110, 0.2);
}

.btn-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px rgba(14, 159, 110, 0.3);
    color: white;
}

.request-item {
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 25px;
    position: relative;
    background: white;
    transition: all 0.3s ease;
}

.request-item:hover {
    border-color: var(--secondary-color);
}

.admin-feedback {
    background: #fffbeb;
    padding: 20px;
    border-radius: 12px;
    border-left: 5px solid var(--secondary-color);
    margin-top: 20px;
}

/* Dash Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid rgba(0,0,0,0.03);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.primary { background: rgba(14, 159, 110, 0.1); color: var(--primary-color); }
.stat-icon.secondary { background: rgba(236, 157, 11, 0.1); color: var(--secondary-color); }

.stat-info h4 {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-light);
    font-weight: 600;
}

.stat-info .stat-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-dark);
}

/* Progress Visualizer */
.progress-container {
    margin-top: 15px;
    flex-grow: 1;
    max-width: 300px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-color);
}

.progress-bar-wrapper {
    height: 10px;
    background: #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 5px;
    transition: width 1s ease-out;
}

[data-theme="dark"] .stat-card {
    background: var(--card-bg);
    border-color: var(--border-color);
}

[data-theme="dark"] .progress-bar-wrapper {
    background: #334155;
}

/* Animations */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-up {
    animation: fadeInUp 0.5s ease forwards;
}
</style>

<div class="dashboard-hero">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div class="animate-up">
                <h1 class="dashboard-title"><?= __('welcome_back') ?>, <?= htmlspecialchars($_SESSION['full_name']) ?>! 👋</h1>
                <p style="color: var(--text-light); font-size: 1.1rem; margin-top: 10px;"><?= __('manage_projects_desc') ?></p>
            </div>
            <div class="animate-up" style="animation-delay: 0.1s;">
                <a href="<?= APP_URL ?>/contact" class="btn-premium">
                    <i class="fas fa-plus"></i>
                    <?= __('create_project') ?>
                </a>
            </div>
        </div>
    </div>
</div>

        <!-- Stats Overview -->
        <?php 
            $activeCount = count(array_filter($projects, fn($p) => strtolower($p['status']) === 'active' || strtolower($p['status']) === 'in_progress'));
            $pendingRequestCount = count(array_filter($requests, fn($r) => strtolower($r['status']) === 'pending'));
        ?>
        <div class="stats-grid animate-up" style="animation-delay: 0.1s;">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <h4><?= __('active_projects') ?></h4>
                    <div class="stat-value"><?= $activeCount ?></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon secondary">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-info">
                    <h4><?= __('pending_requests') ?></h4>
                    <div class="stat-value"><?= $pendingRequestCount ?></div>
                </div>
            </div>
        </div>

        <!-- My Projects Card -->
        <div class="dashboard-card animate-up" style="animation-delay: 0.2s;">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-rocket"></i>
                    <?= __('my_projects_title') ?>
                </h2>
            </div>
            
            <?php if (empty($projects)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p style="color: var(--text-light); font-size: 1.1rem;"><?= __('no_projects_yet') ?></p>
                    <a href="<?= APP_URL ?>/contact" style="color: var(--primary-color); font-weight: 600; text-decoration: none; margin-top: 15px; display: inline-block;">
                        <?= __('request_quote_link') ?> &rarr;
                    </a>
                </div>
            <?php else: ?>
                <div class="project-grid">
                    <?php foreach ($projects as $project): ?>
                        <div class="project-item">
                            <div class="project-info">
                                <h3 style="margin-bottom: 5px;"><?= htmlspecialchars($project['title']) ?></h3>
                                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                    <span class="status-badge status-<?= strtolower($project['status']) ?>">
                                        <?= __(strtolower($project['status'])) ?>
                                    </span>
                                    <?php if(isset($project['deadline'])): ?>
                                        <small style="color: var(--text-light);">
                                            <i class="far fa-calendar-check"></i> 
                                            <?= __('estimated_delivery') ?>: <?= date('M d, Y', strtotime($project['deadline'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Project Progress Visualizer -->
                            <?php 
                                $progress = $project['progress'] ?? 0;
                                if (strtolower($project['status']) === 'completed') $progress = 100;
                            ?>
                            <div class="progress-container">
                                <div class="progress-label">
                                    <span><?= __('project_progress') ?></span>
                                    <span><?= $progress ?>%</span>
                                </div>
                                <div class="progress-bar-wrapper">
                                    <div class="progress-bar-fill" style="width: <?= $progress ?>%"></div>
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <a href="<?= APP_URL ?>/client/project?id=<?= $project['id'] ?>" class="btn-outline" style="border-radius: 8px; padding: 10px 20px;">
                                    <?= __('view_details') ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Project Requests Card -->
        <div class="dashboard-card animate-up" style="animation-delay: 0.3s;">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-paper-plane"></i>
                    <?= __('project_requests') ?>
                </h2>
            </div>
            
            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p style="color: var(--text-light); font-size: 1.1rem;"><?= __('no_requests') ?></p>
                    <a href="<?= APP_URL ?>/contact" style="color: var(--primary-color); font-weight: 600; text-decoration: none; margin-top: 15px; display: inline-block;">
                        <?= __('request_quote_link') ?> &rarr;
                    </a>
                </div>
            <?php else: ?>
                <div class="project-grid">
                    <?php foreach ($requests as $request): ?>
                        <div class="request-item">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                <div>
                                    <h3 style="margin: 0; font-size: 1.3rem; color: var(--text-dark);"><?= htmlspecialchars($request['project_type'] ?? __('project_requests')) ?></h3>
                                    <small style="color: var(--text-light); display: flex; align-items: center; gap: 5px; margin-top: 5px;">
                                        <i class="far fa-calendar-alt"></i>
                                        <?= date('M d, Y', strtotime($request['created_at'])) ?>
                                    </small>
                                </div>
                                <span class="status-badge" style="background: <?= $request['status'] === 'accepted' ? '#dcfce7' : ($request['status'] === 'rejected' ? '#fee2e2' : '#fef9c3') ?>; color: <?= $request['status'] === 'accepted' ? '#166534' : ($request['status'] === 'rejected' ? '#991b1b' : '#854d0e') ?>;">
                                    <?= __($request['status'] ?? 'pending') ?>
                                </span>
                            </div>
                            <p style="margin: 0; color: var(--text-color); line-height: 1.6;">
                                <?= htmlspecialchars(substr($request['description'], 0, 200)) ?><?= strlen($request['description']) > 200 ? '...' : '' ?>
                            </p>
                            
                            <?php if(!empty($request['admin_notes'])): ?>
                                <div class="admin-feedback">
                                    <strong style="display: block; margin-bottom: 10px; color: #854d0e; font-size: 0.9rem;">
                                        <i class="fas fa-comment-dots"></i> <?= __('feedback_from_sahelsoft') ?>:
                                    </strong>
                                    <p style="margin: 0; font-style: italic; color: var(--text-dark);"><?= htmlspecialchars($request['admin_notes']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>