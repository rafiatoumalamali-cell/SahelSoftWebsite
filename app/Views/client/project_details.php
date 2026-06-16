<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Project Details Styles */
.project-details {
    margin-top: 80px;
    padding: 20px 0;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-light);
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 30px;
    transition: color 0.3s ease;
}

.back-link:hover {
    color: var(--primary-color);
}

.project-header {
    background: white;
    padding: 40px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.project-title-section {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 25px;
}

.project-title-main h1 {
    margin: 0 0 10px 0;
    color: var(--text-dark);
    font-size: 2.2rem;
}

.project-id {
    color: var(--text-light);
    font-size: 0.95rem;
    background: var(--bg-light);
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-block;
}

.project-stats {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.stat-box {
    text-align: right;
}

.stat-amount {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary-color);
    line-height: 1;
}

.stat-label {
    color: var(--text-light);
    font-size: 0.9rem;
    margin-top: 5px;
}

.project-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    background: var(--bg-light);
    padding: 25px;
    border-radius: var(--border-radius);
    margin: 25px 0;
}

.meta-item {
    display: flex;
    flex-direction: column;
}

.meta-label {
    font-size: 0.9rem;
    color: var(--text-light);
    margin-bottom: 5px;
}

.meta-value {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1.1rem;
}

/* Tabs Navigation */
.tabs-navigation {
    display: flex;
    gap: 1px;
    background: var(--border-color);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    overflow: hidden;
}

.tab-btn {
    flex: 1;
    background: var(--bg-light);
    border: none;
    padding: 18px;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-light);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.tab-btn:hover {
    background: white;
    color: var(--primary-color);
}

.tab-btn.active {
    background: white;
    color: var(--primary-color);
    position: relative;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-color);
}

.tab-content {
    display: none;
    background: white;
    padding: 40px;
    border-radius: 0 0 var(--border-radius) var(--border-radius);
    box-shadow: var(--shadow-md);
}

.tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

/* Overview Tab */
.project-description {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-color);
    margin-bottom: 30px;
}

.project-features {
    background: var(--bg-light);
    padding: 25px;
    border-radius: var(--border-radius);
    margin-bottom: 30px;
}

.project-features h4 {
    margin-bottom: 15px;
    color: var(--text-dark);
}

.features-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.feature-icon {
    color: var(--primary-color);
    font-size: 1.2rem;
}

/* Stepper Styles */
.milestone-stepper {
    position: relative;
    padding-left: 50px;
    margin-top: 30px;
}

.milestone-stepper::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 5px;
    bottom: 5px;
    width: 2px;
    background: var(--border-color);
}

.milestone-item {
    position: relative;
    margin-bottom: 30px;
}

.milestone-item:last-child {
    margin-bottom: 0;
}

.milestone-dot {
    position: absolute;
    left: -50px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: white;
    border: 3px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    z-index: 1;
    transition: all 0.3s ease;
    color: var(--text-light);
}

.milestone-item.active .milestone-dot {
    border-color: var(--primary-color);
    background: var(--primary-color);
    color: white;
    box-shadow: 0 0 0 5px rgba(14, 159, 110, 0.2);
}

.milestone-item.completed .milestone-dot {
    border-color: #059669;
    background: #059669;
    color: white;
}

.milestone-content {
    background: var(--bg-light);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.milestone-item.active .milestone-content {
    background: white;
    border-color: var(--primary-color);
    box-shadow: var(--shadow-md);
}

.milestone-title {
    font-weight: 700;
    font-size: 1.1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
    color: var(--text-dark);
}

.milestone-status {
    font-size: 0.75rem;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-planned { background: #f3f4f6; color: #6b7280; }
.status-in_progress { background: #fef3c7; color: #92400e; }
.status-completed { background: #d1fae5; color: #065f46; }

.milestone-desc {
    color: var(--text-light);
    font-size: 0.95rem;
    line-height: 1.4;
    font-size: 1.2rem;
}
    

/* Tasks Tab */
.tasks-list {
    display: grid;
    gap: 15px;
}

.task-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: var(--bg-light);
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.task-item:hover {
    background: white;
    box-shadow: var(--shadow-sm);
    transform: translateY(-2px);
}

.task-checkbox {
    width: 24px;
    height: 24px;
    border: 2px solid var(--border-color);
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.task-checkbox.checked {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.task-content {
    flex: 1;
}

.task-title {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.task-meta {
    display: flex;
    gap: 15px;
    font-size: 0.9rem;
    color: var(--text-light);
}

.no-tasks {
    text-align: center;
    padding: 40px;
    color: var(--text-light);
}

/* Payments Tab */
.payments-summary {
    background: var(--bg-light);
    padding: 25px;
    border-radius: var(--border-radius);
    margin-bottom: 30px;
}

.payment-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.payment-stat {
    text-align: center;
}

.payment-stat-amount {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
}

.payment-stat-label {
    font-size: 0.9rem;
    color: var(--text-light);
}

.payment-history {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.payment-item {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 20px;
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    align-items: center;
}

.payment-item:last-child {
    border-bottom: none;
}

.payment-status {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-paid { background: #d1fae5; color: #065f46; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-overdue { background: #fee2e2; color: #991b1b; }

.payment-amount {
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1.1rem;
}

/* Files Tab */
.files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.file-card {
    background: var(--bg-light);
    padding: 20px;
    border-radius: var(--border-radius);
    text-align: center;
    transition: all 0.3s ease;
    text-decoration: none;
    color: var(--text-dark);
}

.file-card:hover {
    background: white;
    box-shadow: var(--shadow-md);
    transform: translateY(-3px);
}

.file-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: var(--primary-color);
}

.file-name {
    font-weight: 600;
    margin-bottom: 5px;
    word-break: break-word;
}

.file-size {
    font-size: 0.85rem;
    color: var(--text-light);
}

/* Team Tab */
.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 20px;
}

.team-member-card {
    text-align: center;
}

.member-avatar {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
}

.member-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.member-role {
    font-size: 0.9rem;
    color: var(--text-light);
}

/* Progress Bar */
.progress-container {
    margin: 30px 0;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.progress-label {
    font-weight: 600;
    color: var(--text-dark);
}

.progress-percentage {
    font-weight: 700;
    color: var(--primary-color);
}

.progress-bar {
    height: 10px;
    background: var(--border-color);
    border-radius: 5px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 5px;
    transition: width 0.5s ease;
}

/* Action Buttons */
.project-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    flex-wrap: wrap;
    align-items: center;
}

.action-btn {
    padding: 12px 25px;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    min-width: fit-content;
    white-space: nowrap;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-secondary {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-secondary:hover {
    background: var(--primary-color);
    color: white;
}

.btn-accent {
    background: var(--gradient-accent);
    color: white;
}

.btn-accent:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-orange);
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .project-header {
        padding: 25px;
    }
    
    .project-title-section {
        flex-direction: column;
    }
    
    .project-stats {
        width: 100%;
        justify-content: space-between;
    }
    
    .stat-box {
        text-align: left;
    }
    
    .tabs-navigation {
        flex-direction: column;
    }
    
    .tab-btn {
        justify-content: flex-start;
        padding: 15px 20px;
    }
    
    .tab-content {
        padding: 25px;
    }
    
    .project-meta-grid {
        grid-template-columns: 1fr;
    }
    
    .payment-item {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .team-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}

@media (max-width: 480px) {
    .project-actions {
        flex-direction: column;
    }
    
    .action-btn {
        justify-content: center;
    }
    
    .files-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="project-details">
    <div class="container">
        <a href="<?= APP_URL ?>/client/dashboard" class="back-link">
            <span>←</span>
            <span><?= __('back_to_dashboard') ?></span>
        </a>
        
        <?php if (isset($_GET['success']) && $_GET['success'] === 'payment_submitted'): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 15px 20px; border-radius: 8px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; border-left: 5px solid #10b981;">
                <span style="font-size: 1.2rem;">✅</span>
                <div>
                    <strong><?= __('payment_submitted_success') ?></strong>
                </div>
            </div>
        <?php endif; ?>

        <!-- Project Header -->
        <div class="project-header">
            <div class="project-title-section">
                <div class="project-title-main">
                    <h1><?= htmlspecialchars($project['title']) ?></h1>
                    <span class="project-id"><?= __('project') ?> #<?= $project['id'] ?></span>
                </div>
                
                <div class="project-stats">
                    <div class="stat-box">
                        <div class="stat-amount"><?= number_format($project['budget'] ?? 0) ?> CFA</div>
                        <div class="stat-label"><?= __('total_budget') ?></div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-amount"><?= $project['progress'] ?? 0 ?>%</div>
                        <div class="stat-label"><?= __('progress') ?></div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="progress-header">
                    <span class="progress-label"><?= __('progress') ?></span>
                    <span class="progress-percentage"><?= $project['progress'] ?? 0 ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $project['progress'] ?? 0 ?>%"></div>
                </div>
            </div>

            <div class="project-meta-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
                <div class="meta-item">
                    <span class="meta-label"><?= __('project_status') ?></span>
                    <span class="meta-value" style="color: 
                        <?= $project['status'] == 'active' ? 'var(--status-in-progress)' : 
                           ($project['status'] == 'completed' ? 'var(--status-completed)' : 
                           ($project['status'] == 'on_hold' ? 'var(--status-pending)' : 'var(--text-light)')) ?>">
                        <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><?= __('start_date') ?></span>
                    <span class="meta-value">
                        <?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : __('to_be_determined') ?>
                    </span>
                </div>
                <!-- Estimations -->
                <div class="meta-item">
                    <span class="meta-label" title="Best case scenario for completion"><?= __('completion_best') ?></span>
                    <span class="meta-value" style="color: var(--primary-color);">
                        <?= !empty($project['best_case_completion']) ? date('d M Y', strtotime($project['best_case_completion'])) : __('pending') ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label" title="Worst case scenario for completion"><?= __('completion_worst') ?></span>
                    <span class="meta-value" style="color: #ef4444;">
                        <?= !empty($project['worst_case_completion']) ? date('d M Y', strtotime($project['worst_case_completion'])) : __('pending') ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><?= __('deadline') ?></span>
                    <span class="meta-value">
                        <?= $project['deadline'] ? date('d M Y', strtotime($project['deadline'])) : __('flexible') ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><?= __('last_updated') ?></span>
                    <span class="meta-value">
                        <?= $project['updated_at'] ? date('d M Y', strtotime($project['updated_at'])) : date('d M Y') ?>
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="project-actions">
                <a href="<?= APP_URL ?>/client/project/messages?id=<?= $project['id'] ?>" class="action-btn btn-primary">
                    <span>💬</span>
                    <span><?= __('message_team') ?></span>
                </a>
                <a href="<?= APP_URL ?>/client/project/payments?id=<?= $project['id'] ?>" class="action-btn btn-secondary">
                    <span>💰</span>
                    <span><?= __('make_payment') ?></span>
                </a>
                <?php if ($project['status'] == 'active'): ?>
                <a href="<?= APP_URL ?>/client/project/upload?id=<?= $project['id'] ?>" class="action-btn btn-accent">
                    <span>📁</span>
                    <span><?= __('upload_files') ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="tabs-navigation">
            <button class="tab-btn active" data-tab="overview">
                <span>📋</span>
                <span><?= __('overview') ?></span>
            </button>
            <button class="tab-btn" data-tab="tasks">
                <span>✅</span>
                <span><?= __('tasks') ?> (<?= $project['task_count'] ?? 0 ?>)</span>
            </button>
            <button class="tab-btn" data-tab="payments">
                <span>💰</span>
                <span><?= __('payments') ?></span>
            </button>
            <button class="tab-btn" data-tab="files">
                <span>📁</span>
                <span><?= __('files') ?></span>
            </button>
            <button class="tab-btn" data-tab="team">
                <span>👥</span>
                <span><?= __('team') ?></span>
            </button>
        </div>

        <!-- Overview Tab -->
        <div class="tab-content active" id="overview">
            <div class="project-description">
                <?= nl2br(htmlspecialchars($project['description'] ?? __('no_description_provided'))) ?>
            </div>

            <?php if (!empty($project['features'])): ?>
            <div class="project-features">
                <h4><?= __('project_features') ?></h4>
                <div class="features-list">
                    <?php foreach (explode("\n", $project['features']) as $feature): ?>
                        <?php if (trim($feature)): ?>
                        <div class="feature-item">
                            <span class="feature-icon">✓</span>
                            <span><?= htmlspecialchars(trim($feature)) ?></span>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Timeline -->
            <div>
                <h4><?= __('project_timeline') ?></h4>
                <div class="milestone-stepper">
                    <!-- Planning -->
                    <div class="milestone-item <?= $project['progress'] >= 25 ? 'completed' : ($project['progress'] >= 10 ? 'active' : '') ?>">
                        <div class="milestone-dot">
                            <?= $project['progress'] >= 25 ? '✓' : '1' ?>
                        </div>
                        <div class="milestone-content">
                            <div class="milestone-title">
                                <span><?= __('planning_analysis') ?></span>
                                <span class="milestone-status status-<?= $project['progress'] >= 25 ? 'completed' : ($project['progress'] >= 10 ? 'in_progress' : 'planned') ?>">
                                    <?= $project['progress'] >= 25 ? __('completed') : ($project['progress'] >= 10 ? __('in_progress') : __('planned')) ?>
                                </span>
                            </div>
                            <div class="milestone-desc"><?= __('process_desc_analysis') ?></div>
                        </div>
                    </div>

                    <!-- Design -->
                    <div class="milestone-item <?= $project['progress'] >= 50 ? 'completed' : ($project['progress'] >= 25 ? 'active' : '') ?>">
                        <div class="milestone-dot">
                            <?= $project['progress'] >= 50 ? '✓' : '2' ?>
                        </div>
                        <div class="milestone-content">
                            <div class="milestone-title">
                                <span><?= __('design_phase') ?></span>
                                <span class="milestone-status status-<?= $project['progress'] >= 50 ? 'completed' : ($project['progress'] >= 25 ? 'in_progress' : 'planned') ?>">
                                    <?= $project['progress'] >= 50 ? __('completed') : ($project['progress'] >= 25 ? __('in_progress') : __('planned')) ?>
                                </span>
                            </div>
                            <div class="milestone-desc"><?= __('process_desc_design') ?></div>
                        </div>
                    </div>

                    <!-- Development -->
                    <div class="milestone-item <?= $project['progress'] >= 90 ? 'completed' : ($project['progress'] >= 50 ? 'active' : '') ?>">
                        <div class="milestone-dot">
                            <?= $project['progress'] >= 90 ? '✓' : '3' ?>
                        </div>
                        <div class="milestone-content">
                            <div class="milestone-title">
                                <span><?= __('development') ?></span>
                                <span class="milestone-status status-<?= $project['progress'] >= 90 ? 'completed' : ($project['progress'] >= 50 ? 'in_progress' : 'planned') ?>">
                                    <?= $project['progress'] >= 90 ? __('completed') : ($project['progress'] >= 50 ? __('in_progress') : __('planned')) ?>
                                </span>
                            </div>
                            <div class="milestone-desc"><?= __('process_desc_dev') ?></div>
                        </div>
                    </div>

                    <!-- Project Progress -->
                    <div class="milestone-item <?= $project['progress'] >= 100 ? 'completed' : ($project['progress'] >= 90 ? 'active' : '') ?>">
                        <div class="milestone-dot">
                            <?= $project['progress'] >= 100 ? '✓' : '4' ?>
                        </div>
                        <div class="milestone-content">
                            <div class="milestone-title">
                                <span><?= __('project_progress') ?></span>
                                <span class="milestone-status status-<?= $project['progress'] >= 100 ? 'completed' : ($project['progress'] >= 90 ? 'in_progress' : 'planned') ?>">
                                    <?= $project['progress'] >= 100 ? __('completed') : ($project['progress'] >= 90 ? __('in_progress') : __('planned')) ?>
                                </span>
                            </div>
                            <div class="milestone-desc"><?= __('project_status_desc') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Tab -->
        <div class="tab-content" id="tasks">
            <?php if (!empty($tasks)): ?>
                <div class="tasks-list">
                    <?php foreach ($tasks as $task): ?>
                    <div class="task-item">
                        <div class="task-checkbox <?= $task['status'] === 'completed' ? 'checked' : '' ?>">
                            <?= $task['status'] === 'completed' ? '✓' : '' ?>
                        </div>
                        <div class="task-content">
                            <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                            <div class="task-meta">
                                <span><?= __('assigned_to') ?>: <?= htmlspecialchars($task['assigned_to'] ?? __('team')) ?></span>
                                <span><?= __('due') ?>: <?= $task['due_date'] ? date('d M Y', strtotime($task['due_date'])) : __('flexible') ?></span>
                            </div>
                        </div>
                        <span style="color: var(--text-light); font-size: 0.9rem;">
                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-tasks">
                    <i class="fas fa-tasks" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
                    <p><?= __('no_tasks_found') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Payments Tab -->
        <div class="tab-content" id="payments">
            <div class="payments-summary">
                <div class="payment-stats">
                    <div class="payment-stat">
                        <div class="payment-stat-amount"><?= number_format($paid_amount ?? 0) ?> CFA</div>
                        <div class="payment-stat-label"><?= __('paid') ?></div>
                    </div>
                    <div class="payment-stat">
                        <div class="payment-stat-amount"><?= number_format(($project['budget'] ?? 0) - ($paid_amount ?? 0)) ?> CFA</div>
                        <div class="payment-stat-label"><?= __('remaining') ?></div>
                    </div>
                    <div class="payment-stat">
                        <div class="payment-stat-amount"><?= $payment_count ?? 0 ?></div>
                        <div class="payment-stat-label"><?= __('payments_made') ?></div>
                    </div>
                </div>
                
                <div class="progress-bar" style="margin: 20px 0;">
                    <div class="progress-fill" style="width: <?= ($project['budget'] ?? 0) > 0 ? ($paid_amount ?? 0) / $project['budget'] * 100 : 0 ?>%"></div>
                </div>
            </div>

            <?php if (!empty($payments)): ?>
                <div class="payment-history">
                    <?php foreach ($payments as $payment): ?>
                    <div class="payment-item">
                        <div>
                            <div style="font-weight: 600;"><?= htmlspecialchars($payment['description']) ?></div>
                            <div style="color: var(--text-light); font-size: 0.9rem;">
                                <?= __('date') ?>: <?= date('d M Y', strtotime($payment['payment_date'])) ?>
                            </div>
                        </div>
                        <div class="payment-amount"><?= number_format($payment['amount']) ?> CFA</div>
                        <div class="payment-status status-<?= $payment['status'] ?>">
                            <?= __(strtolower($payment['status'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--text-light);">
                    <p><?= __('no_payments_found') ?></p>
                    <p><?= __('payments_processed_desc') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Files Tab -->
        <div class="tab-content" id="files">
            <?php if (!empty($files)): ?>
                <div class="files-grid">
                    <?php foreach ($files as $file): ?>
                    <a href="<?= APP_URL ?>/client/project/download?id=<?= $file['id'] ?>" class="file-card">
                        <div class="file-icon">
                            <?= strpos($file['type'], 'image') !== false ? '🖼️' : 
                               (strpos($file['type'], 'pdf') !== false ? '📄' : 
                               (strpos($file['type'], 'word') !== false ? '📝' : '📁')) ?>
                        </div>
                        <div class="file-name"><?= htmlspecialchars($file['name']) ?></div>
                        <div class="file-size"><?= formatFileSize($file['size']) ?></div>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--text-light);">
                    <p><?= __('no_files_found') ?></p>
                    <p><?= __('team_files_desc') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Team Tab -->
    <div class="tab-content" id="team">
        <div class="team-grid">
            <?php if (!empty($team)): ?>
                <?php foreach ($team as $member): ?>
                <div class="team-member-card">
                    <div class="member-avatar"><?= $member['initials'] ?></div>
                    <div class="member-name"><?= $member['name'] ?></div>
                    <div class="member-role"><?= $member['role'] ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: var(--text-light);">
                    <p><?= __('no_team_members_found') ?></p>
                    <p><?= __('team_will_be_assigned_desc') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
            </div>
            
            <div style="margin-top: 30px; padding: 20px; background: var(--bg-light); border-radius: var(--border-radius);">
                <h4><?= __('contact_team_prompt') ?></h4>
                <p><?= __('message_feature_desc') ?></p>
                <a href="<?= APP_URL ?>/client/project/messages?id=<?= $project['id'] ?>" class="action-btn btn-primary" style="display: inline-flex; margin-top: 10px;">
                    <span>💬</span>
                    <span><?= __('send_message') ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            // Store active tab in session storage
            sessionStorage.setItem('activeProjectTab', tabId);
        });
    });
    
    // Restore active tab from session storage
    const activeTab = sessionStorage.getItem('activeProjectTab');
    if (activeTab) {
        const btn = document.querySelector(`.tab-btn[data-tab="${activeTab}"]`);
        if (btn) {
            btn.click();
        }
    }
    
    // Task checkbox functionality
    const taskCheckboxes = document.querySelectorAll('.task-checkbox');
    taskCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('click', function() {
            if (!this.classList.contains('checked')) {
                this.classList.add('checked');
                this.innerHTML = '✓';
                
                // In real app, send AJAX request to mark task as complete
                console.log('Task marked as complete');
            }
        });
    });
    
    // Progress bar animation
    const progressFill = document.querySelector('.progress-fill');
    const targetWidth = progressFill.style.width;
    progressFill.style.width = '0%';
    
    setTimeout(() => {
        progressFill.style.width = targetWidth;
    }, 300);
});

// Helper function for file sizes
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>