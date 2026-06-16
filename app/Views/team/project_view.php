<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.project-view-container {
    margin-top: 80px;
    padding: 40px 0;
    background-color: var(--bg-light);
    min-height: 100vh;
    overflow-x: auto;
}

.view-header {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 30px;
    border-left: 6px solid var(--primary-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-left h1 {
    margin: 0;
    color: var(--text-dark);
    font-size: 2rem;
    font-weight: 800;
}

.breadcrumb {
    display: flex;
    gap: 12px;
    color: var(--text-light);
    font-size: 0.95rem;
    margin-bottom: 15px;
    align-items: center;
}

.breadcrumb i {
    font-size: 0.8rem;
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

.view-grid {
    display: grid;
    grid-template-columns: 2.5fr 1fr;
    gap: 30px;
}

.view-card {
    background: white;
    padding: 35px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 30px;
    border: 1px solid var(--border-color);
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--bg-light);
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-title i {
    color: var(--primary-color);
}

.project-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    width: 100%;
}

.main-content {
    min-width: 0; /* Critical for grid/flex children to allow wrapping */
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-label {
    font-size: 0.8rem;
    color: var(--text-light);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1.05rem;
}

.status-badge {
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.status-active { background: #d1fae5; color: #065f46; }
.status-completed { background: #dbeafe; color: #1e40af; }
.status-on_hold { background: #fef3c7; color: #92400e; }
.status-pending { background: #f3e8ff; color: #6b21a8; }
.status-proposed { background: #f1f5f9; color: #475569; }

.progress-section {
    margin-top: 40px;
    padding: 25px;
    background: var(--bg-light);
    border-radius: var(--border-radius);
}

.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.progress-bar-container {
    height: 14px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 10px;
    transition: width 1s ease-in-out;
}

.sidebar-card {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 30px;
    border: 1px solid var(--border-color);
}

.client-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 15px;
    margin-bottom: 25px;
}

.client-avatar {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    box-shadow: var(--shadow-md);
}

.client-details h4 {
    margin: 0;
    font-size: 1.2rem;
    color: var(--text-dark);
}

.client-details p {
    margin: 5px 0 0;
    font-size: 0.9rem;
    color: var(--text-light);
}

.sidebar-action-btn {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-align: center;
    display: block;
    transition: var(--transition);
}

.btn-message {
    background: rgba(14, 159, 110, 0.1);
    color: var(--primary-color);
}

.btn-message:hover {
    background: var(--primary-color);
    color: white;
}

@media (max-width: 1200px) {
    .view-grid {
        grid-template-columns: 2fr 1fr;
    }
}

@media (max-width: 992px) {
    .view-grid {
        grid-template-columns: 1fr;
    }
    
    .view-header {
        text-align: center;
        flex-direction: column;
    }
}
</style>

<div class="project-view-container">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= APP_URL ?>/team/dashboard"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right"></i>
            <span>Projects</span>
            <i class="fas fa-chevron-right"></i>
            <span style="color: var(--text-dark); font-weight: 600;">#<?= $project['id'] ?></span>
        </div>

        <div class="view-header">
            <div class="header-left">
                <h1><?= htmlspecialchars($project['title']) ?></h1>
            </div>
            <div class="header-actions">
                <?php if ($_SESSION['role'] !== 'developer'): ?>
                    <a href="<?= APP_URL ?>/team/project/edit?id=<?= $project['id'] ?>" class="btn" style="background: var(--primary-color); color: white; padding: 12px 25px; border-radius: 10px;">
                        <i class="fas fa-edit"></i> Edit Project
                    </a>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/team/tasks?project_id=<?= $project['id'] ?>" class="btn" style="background: var(--accent-color); color: white; margin-left: 10px; padding: 12px 25px; border-radius: 10px;">
                    <i class="fas fa-tasks"></i> Manage Tasks
                </a>
            </div>
        </div>

        <div class="view-grid">
            <div class="main-content">
                <div class="view-card">
                    <div class="card-title">
                        <i class="fas fa-info-circle"></i> Project Information
                    </div>
                    <div class="project-details">
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">
                                <span class="status-badge status-<?= $project['status'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                                </span>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Budget</span>
                            <span class="detail-value"><?= number_format($project['budget'], 2) ?> CFA</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Start Date</span>
                            <span class="detail-value"><?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : 'N/A' ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Deadline</span>
                            <span class="detail-value"><?= $project['deadline'] ? date('d M Y', strtotime($project['deadline'])) : 'N/A' ?></span>
                        </div>
                    </div>

                    <div style="margin-top: 30px; border-top: 1px solid var(--bg-light); padding-top: 25px;">
                        <span class="detail-label">Project Description</span>
                        <div class="detail-value" style="margin-top: 12px; line-height: 1.8; color: var(--text-color); word-break: break-all; overflow-wrap: break-word;">
                            <?= nl2br(htmlspecialchars($project['description'] ?? 'No description provided.')) ?>
                        </div>
                    </div>

                    <div class="progress-section">
                        <div class="progress-header">
                            <span class="detail-label">Overall Progress</span>
                            <span class="detail-value"><?= $project['progress'] ?? 0 ?>%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: <?= $project['progress'] ?? 0 ?>%"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks Placeholder -->
                <div class="view-card">
                    <div class="card-title">
                        <i class="fas fa-list-check"></i> Recent Tasks
                    </div>
                    <p style="color: var(--text-light); text-align: center; padding: 20px;">
                        Task management for this project is available in the <a href="<?= APP_URL ?>/team/tasks?project_id=<?= $project['id'] ?>">Tasks section</a>.
                    </p>
                </div>
            </div>

            <div class="sidebar">
                <div class="sidebar-card">
                    <div class="card-title"><i class="fas fa-user-tie"></i> Client Information</div>
                    <div class="client-info">
                        <div class="client-avatar">
                            <?= strtoupper(substr($project['client_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="client-details">
                            <h4><?= htmlspecialchars($project['client_name'] ?? 'Unknown Client') ?></h4>
                            <p><?= htmlspecialchars($project['client_company'] ?? 'Niger Business') ?></p>
                        </div>
                    </div>
                    
                    <div style="background: var(--bg-light); padding: 20px; border-radius: 15px; margin-bottom: 20px;">
                        <?php if (!empty($project['client_email'])): ?>
                            <div class="detail-item" style="margin-bottom: 15px;">
                                <span class="detail-label">Email Address</span>
                                <span class="detail-value" style="font-size: 0.9rem;"><?= htmlspecialchars($project['client_email']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($project['client_phone'])): ?>
                            <div class="detail-item">
                                <span class="detail-label">Phone Number</span>
                                <span class="detail-value" style="font-size: 0.9rem;"><?= htmlspecialchars($project['client_phone']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <a href="mailto:<?= $project['client_email'] ?? '' ?>" class="sidebar-action-btn btn-message">
                        <i class="fas fa-envelope"></i> Send Email
                    </a>
                </div>

                <div class="sidebar-card">
                    <div class="card-title"><i class="fas fa-users"></i> Project Team</div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <div style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #3b82f6;">
                            <i class="fas fa-code"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($project['team'] ?? 'Dev Team') ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-light);">Primary Team Assigned</div>
                        </div>
                    </div>
                    <p style="font-size: 0.85rem; color: var(--text-light); line-height: 1.5; margin: 0;">
                        This project is handled by the dedicated development team for specialized software solutions.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
