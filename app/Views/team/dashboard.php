<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Team Dashboard Styles */
.team-dashboard {
    margin-top: 80px;
    padding: 30px 0;
    background: var(--bg-light);
    min-height: 100vh;
    overflow-x: auto;
}

.dashboard-header {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.dashboard-title {
    display: flex;
    align-items: center;
    gap: 15px;
}

.dashboard-title h1 {
    margin: 0;
    color: var(--text-dark);
}

.user-role {
    background: var(--gradient-primary);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: capitalize;
}

.header-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

/* Stats Overview */
.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    text-align: center;
    border-top: 4px solid var(--primary-color);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.stat-card:nth-child(2) {
    border-top-color: var(--accent-color);
}

.stat-card:nth-child(3) {
    border-top-color: #3b82f6;
}

.stat-card:nth-child(4) {
    border-top-color: #10b981;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--primary-color);
    margin-bottom: 5px;
    line-height: 1;
}

.stat-label {
    color: var(--text-light);
    font-size: 0.95rem;
    font-weight: 500;
}

/* Filters */
.filters-bar {
    background: white;
    padding: 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.filter-group {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-select {
    padding: 10px 15px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    background: white;
    color: var(--text-dark);
    font-family: var(--font-main);
    font-weight: 500;
    min-width: 150px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.search-box {
    position: relative;
    min-width: 250px;
}

.search-input {
    width: 100%;
    padding: 10px 15px 10px 40px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: var(--font-main);
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-light);
}

/* Projects Table */
.projects-table-container {
    background: white;
    border-radius: var(--border-radius);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: var(--shadow-md);
    margin-bottom: 40px;
}

.projects-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1400px; /* Further increased to prevent any squeezing */
    table-layout: auto;
}

.projects-table th {
    background: var(--primary-color);
    color: white;
    text-align: left;
    padding: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap; /* Prevent headers from wrapping */
}

.projects-table td {
    padding: 18px 20px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}

/* Ensure specific columns don't squeeze or wrap vertically */
.projects-table td:not(:first-child) {
    white-space: nowrap;
}

/* Ensure the last column (Actions) has enough space and doesn't shrink */
.projects-table td:last-child {
    min-width: 250px;
}

/* Allow Project column (first column) to wrap and have a reasonable width */
.projects-table td:first-child {
    min-width: 300px;
    max-width: 500px;
    word-wrap: break-word;
    word-break: break-word;
}

.projects-table .action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: nowrap;
    align-items: center;
    width: max-content;
}

.projects-table tr:hover {
    background: var(--bg-light);
}

.projects-table tr:last-child td {
    border-bottom: none;
}

/* Status Badges */
.status-badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: capitalize;
    display: inline-block;
    min-width: 100px;
    text-align: center;
}

.status-active { background: #d1fae5; color: #065f46; }
.status-completed { background: #dbeafe; color: #1e40af; }
.status-on_hold { background: #fef3c7; color: #92400e; }
.status-pending { background: #f3e8ff; color: #6b21a8; }
.status-cancelled { background: #fee2e2; color: #991b1b; }

/* Priority Badges */
.priority-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
}

.priority-high { background: #fee2e2; color: #991b1b; }
.priority-medium { background: #fef3c7; color: #92400e; }
.priority-low { background: #d1fae5; color: #065f46; }

/* Action Buttons */


.projects-table .action-btn {
    padding: 8px 15px;
    border-radius: var(--border-radius);
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    flex-shrink: 0;
    width: auto; /* Override global 38px */
    height: auto; /* Override global 38px */
    line-height: normal;
}

.btn-view {
    background: var(--bg-light);
    color: var(--text-dark);
    border: 1px solid var(--border-color);
}

.btn-view:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.btn-edit {
    background: rgba(14, 159, 110, 0.1);
    color: var(--primary-color);
    border: 1px solid rgba(14, 159, 110, 0.2);
}

.btn-edit:hover {
    background: var(--primary-color);
    color: white;
}

.btn-tasks {
    background: var(--accent-color);
    color: white;
    border: 1px solid var(--accent-color);
}

.btn-tasks:hover {
    background: var(--accent-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-orange);
}

.btn-create {
    background: var(--gradient-accent);
    color: white;
    padding: 12px 25px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.btn-create:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-orange);
    color: white;
}

/* Progress Bar */
.progress-cell {
    min-width: 150px;
}

.progress-container {
    width: 100%;
    background: var(--border-color);
    border-radius: 10px;
    height: 10px;
    overflow: hidden;
    margin-bottom: 5px;
}

.progress-bar {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 10px;
    transition: width 0.5s ease;
}

.progress-text {
    font-size: 0.85rem;
    color: var(--text-light);
    font-weight: 500;
}

/* Quick Actions */
.quick-actions {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    margin-top: 40px;
}

.quick-actions h3 {
    margin-bottom: 20px;
    color: var(--text-dark);
}

.action-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.action-card {
    background: var(--bg-light);
    padding: 25px;
    border-radius: var(--border-radius);
    text-decoration: none;
    color: var(--text-dark);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    gap: 20px;
}

.action-card:hover {
    background: white;
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.action-icon {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.action-card:nth-child(2) .action-icon {
    background: var(--gradient-accent);
}

.action-card:nth-child(3) .action-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.action-content h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
}

.action-content p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state-icon {
    font-size: 4rem;
    color: var(--border-color);
    margin-bottom: 20px;
}

.empty-state h3 {
    color: var(--text-dark);
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--text-light);
    max-width: 500px;
    margin: 0 auto 30px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .projects-table-container {
        overflow-x: auto;
    }
    
    .stats-overview {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .action-cards {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .filters-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        flex-direction: column;
    }
    
    .filter-select {
        width: 100%;
    }
    
    .search-box {
        min-width: 100%;
    }
    
    .stats-overview {
        grid-template-columns: 1fr;
    }
    
    .action-cards {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        text-align: center;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .dashboard-header {
        padding: 20px;
    }
    
    .projects-table th,
    .projects-table td {
        padding: 12px 15px;
    }
    
    .action-card {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
}
</style>

<section class="team-dashboard">
    <div class="container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="dashboard-title">
                    <h1>Team Dashboard</h1>
                    <div class="user-role">
                        <?= ucfirst($_SESSION['role']) ?>
                    </div>
                </div>
                
                <div class="header-actions">
                    <?php if ($_SESSION['role'] !== 'developer'): ?>
                        <a href="<?= APP_URL ?>/team/project/create" class="btn-create">
                            <span>+</span>
                            <span>Create New Project</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] == 'project_manager' || $_SESSION['role'] == 'admin'): ?>
                        <a href="<?= APP_URL ?>/team/reports" class="btn" style="background: transparent; color: var(--primary-color); border: 2px solid var(--primary-color);">
                            <span>📊</span>
                            <span>Reports</span>
                        </a>
                        <a href="<?= APP_URL ?>/team/project/create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Project
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stats Overview Integrated into Header -->
            <div class="stats-overview" style="margin-top: 30px; margin-bottom: 0;">
                <div class="stat-card">
                    <div class="stat-number"><?= count($projects) ?></div>
                    <div class="stat-label">Total Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?= count(array_filter($projects, fn($p) => $p['status'] === 'active')) ?>
                    </div>
                    <div class="stat-label">Active Projects</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $overdue = 0;
                        foreach ($projects as $p) {
                            if ($p['deadline'] && strtotime($p['deadline']) < time() && $p['status'] !== 'completed') {
                                $overdue++;
                            }
                        }
                        echo $overdue;
                        ?>
                    </div>
                    <div class="stat-label">Overdue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php
                        $totalTasks = 0;
                        $completedTasks = 0;
                        foreach ($projects as $p) {
                            $totalTasks += $p['total_tasks'] ?? 0;
                            $completedTasks += $p['completed_tasks'] ?? 0;
                        }
                        echo $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) . '%' : '0%';
                        ?>
                    </div>
                    <div class="stat-label">Task Completion</div>
                </div>
            </div>
        </div>

        <?php if (!empty($recentTasks)): ?>
            <div class="tasks-overview-section" style="margin-bottom: 40px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-dark);">My Assigned Tasks</h2>
                    <a href="<?= APP_URL ?>/team/tasks" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">View All Tasks →</a>
                </div>
                <div class="task-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    <?php foreach ($recentTasks as $task): ?>
                        <div class="mini-task-card" style="background: white; padding: 20px; border-radius: var(--border-radius); border-left: 4px solid <?= $task['priority'] == 'high' ? '#ef4444' : ($task['priority'] == 'medium' ? '#f59e0b' : '#10b981') ?>; box-shadow: var(--shadow-sm);">
                            <div style="font-size: 0.8rem; color: var(--text-light); text-transform: uppercase; font-weight: 700; margin-bottom: 5px;">
                                <?= htmlspecialchars($task['project_title']) ?>
                            </div>
                            <h4 style="margin: 0 0 10px 0; font-size: 1.1rem;"><?= htmlspecialchars($task['title']) ?></h4>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="status-badge" style="font-size: 0.7rem; padding: 3px 10px; background: #f3f4f6; color: #4b5563;">
                                    <?= ucfirst($task['status']) ?>
                                </span>
                                <span style="font-size: 0.8rem; color: <?= strtotime($task['due_date']) < time() ? '#ef4444' : 'var(--text-light)' ?>; font-weight: 500;">
                                    <i class="far fa-calendar"></i> <?= date('d M', strtotime($task['due_date'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters-bar">
            <div class="filter-group">
                <select class="filter-select" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                    <option value="pending">Pending</option>
                </select>
                
                <select class="filter-select" id="priorityFilter">
                    <option value="all">All Priority</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium Priority</option>
                    <option value="low">Low Priority</option>
                </select>
                
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'project_manager'): ?>
                <select class="filter-select" id="teamFilter">
                    <option value="all">All Teams</option>
                    <option value="web">Web Development</option>
                    <option value="mobile">Mobile Team</option>
                    <option value="design">Design Team</option>
                </select>
                <?php endif; ?>
            </div>
            
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search projects..." id="projectSearch">
                <span class="search-icon">🔍</span>
            </div>
        </div>

        <!-- Projects Table -->
        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📁</div>
                <h3>No Projects Assigned</h3>
                <p>You don't have any projects assigned to you yet. Projects will appear here once they are assigned by a project manager.</p>
                <?php if ($_SESSION['role'] !== 'developer'): ?>
                    <a href="<?= APP_URL ?>/team/project/create" class="btn-create">
                        <span>+</span>
                        <span>Create Your First Project</span>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="projects-table-container">
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Progress</th>
                            <th>Deadline</th>
                            <th>Team</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                            <?php
                            // Calculate progress
                            $progress = $p['progress'] ?? 0;
                            $priority = $p['priority'] ?? 'medium';
                            $team = $p['team'] ?? 'Development';
                            
                            // Check if overdue
                            $isOverdue = $p['deadline'] && strtotime($p['deadline']) < time() && $p['status'] !== 'completed';
                            ?>
                            
                            <tr class="project-row" 
                                data-status="<?= $p['status'] ?>" 
                                data-priority="<?= $priority ?>"
                                data-team="<?= strtolower(str_replace(' ', '_', $team)) ?>">
                                <td>
                                    <div style="font-weight: bold; color: var(--text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        #<?= $p['id'] ?> - <?= htmlspecialchars($p['title']) ?>
                                    </div>
                                    <?php if (!empty($p['description'])): ?>
                                        <div style="font-size: 0.9rem; color: var(--text-light); margin-top: 5px;">
                                            <?= htmlspecialchars(substr($p['description'], 0, 80)) ?>...
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($p['client_name'] ?? 'N/A') ?></div>
                                    <?php if (!empty($p['client_company'])): ?>
                                        <div style="font-size: 0.85rem; color: var(--text-light);">
                                            <?= htmlspecialchars($p['client_company']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $p['status'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-badge priority-<?= $priority ?>">
                                        <?= ucfirst($priority) ?>
                                    </span>
                                </td>
                                <td class="progress-cell">
                                    <div class="progress-container">
                                        <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                                    </div>
                                    <div class="progress-text"><?= $progress ?>% Complete</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: <?= $isOverdue ? '#ef4444' : 'var(--text-dark)' ?>;">
                                        <?= $p['deadline'] ? date('d M Y', strtotime($p['deadline'])) : 'No deadline' ?>
                                    </div>
                                    <?php if ($isOverdue): ?>
                                        <div style="font-size: 0.8rem; color: #ef4444; font-weight: 500;">
                                            Overdue
                                        </div>
                                    <?php elseif ($p['deadline']): ?>
                                        <?php
                                        $daysLeft = floor((strtotime($p['deadline']) - time()) / (60 * 60 * 24));
                                        if ($daysLeft >= 0 && $daysLeft <= 7) {
                                            echo '<div style="font-size: 0.8rem; color: #f59e0b; font-weight: 500;">' . $daysLeft . ' days left</div>';
                                        }
                                        ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 8px; height: 8px; background: 
                                            <?= $team == 'Design' ? '#8b5cf6' : 
                                               ($team == 'Mobile' ? '#3b82f6' : 'var(--primary-color)') ?>; 
                                            border-radius: 50%;">
                                        </div>
                                        <span><?= $team ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= APP_URL ?>/team/project/view?id=<?= $p['id'] ?>" class="action-btn btn-view">
                                            <span>👁️</span>
                                            <span>View</span>
                                        </a>
                                        <?php if ($_SESSION['role'] !== 'developer'): ?>
                                            <a href="<?= APP_URL ?>/team/project/edit?id=<?= $p['id'] ?>" class="action-btn btn-edit">
                                                <span>✏️</span>
                                                <span>Edit</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= APP_URL ?>/team/tasks?project_id=<?= $p['id'] ?>" class="action-btn btn-tasks">
                                            <span>✅</span>
                                            <span>Tasks</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-cards">
                <a href="<?= APP_URL ?>/team/tasks" class="action-card">
                    <div class="action-icon">✅</div>
                    <div class="action-content">
                        <h4>Manage Tasks</h4>
                        <p>View and manage your assigned tasks</p>
                    </div>
                </a>
                
                <a href="<?= APP_URL ?>/team/messages" class="action-card">
                    <div class="action-icon">💬</div>
                    <div class="action-content">
                        <h4>Team Messages</h4>
                        <p>Communicate with your team members</p>
                    </div>
                </a>
                
                <a href="<?= APP_URL ?>/team/reports" class="action-card">
                    <div class="action-icon">📊</div>
                    <div class="action-content">
                        <h4>View Reports</h4>
                        <p>Project performance and analytics</p>
                    </div>
                </a>
                
                <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="<?= APP_URL ?>/admin/users" class="action-card">
                    <div class="action-icon">👥</div>
                    <div class="action-content">
                        <h4>Manage Users</h4>
                        <p>Add or remove team members</p>
                    </div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const teamFilter = document.getElementById('teamFilter');
    const searchInput = document.getElementById('projectSearch');
    const projectRows = document.querySelectorAll('.project-row');
    
    function filterProjects() {
        const status = statusFilter.value;
        const priority = priorityFilter.value;
        const team = teamFilter?.value || 'all';
        const searchTerm = searchInput.value.toLowerCase();
        
        projectRows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowPriority = row.getAttribute('data-priority');
            const rowTeam = row.getAttribute('data-team');
            const rowText = row.textContent.toLowerCase();
            
            const statusMatch = status === 'all' || rowStatus === status;
            const priorityMatch = priority === 'all' || rowPriority === priority;
            const teamMatch = team === 'all' || rowTeam === team;
            const searchMatch = rowText.includes(searchTerm);
            
            if (statusMatch && priorityMatch && teamMatch && searchMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    // Add event listeners
    [statusFilter, priorityFilter, teamFilter, searchInput].forEach(element => {
        if (element) {
            element.addEventListener('input', filterProjects);
            element.addEventListener('change', filterProjects);
        }
    });
    
    // Restore filter values from localStorage
    ['statusFilter', 'priorityFilter', 'teamFilter', 'projectSearch'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            const savedValue = localStorage.getItem(`teamDashboard_${id}`);
            if (savedValue !== null) {
                element.value = savedValue;
            }
            
            element.addEventListener('change', function() {
                localStorage.setItem(`teamDashboard_${id}`, this.value);
            });
            
            if (id === 'projectSearch') {
                element.addEventListener('input', function() {
                    localStorage.setItem(`teamDashboard_${id}`, this.value);
                });
            }
        }
    });
    
    // Apply filters on page load
    setTimeout(filterProjects, 100);
    
    // Progress bar animation
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 300);
    });
    
    // Highlight overdue projects
    const overdueRows = document.querySelectorAll('.project-row');
    overdueRows.forEach(row => {
        const deadlineCell = row.querySelector('td:nth-child(6)');
        if (deadlineCell && deadlineCell.textContent.includes('Overdue')) {
            row.style.backgroundColor = 'rgba(239, 68, 68, 0.05)';
            row.style.borderLeft = '3px solid #ef4444';
        }
    });
    
    // Sort table by deadline (optional enhancement)
    const tableHeaders = document.querySelectorAll('.projects-table th');
    tableHeaders.forEach((header, index) => {
        if (header.textContent.includes('Deadline')) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                sortTableByDate(index);
            });
        }
    });
    
    function sortTableByDate(columnIndex) {
        const table = document.querySelector('.projects-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aDateText = a.querySelector(`td:nth-child(${columnIndex + 1})`).textContent;
            const bDateText = b.querySelector(`td:nth-child(${columnIndex + 1})`).textContent;
            
            const aDate = getDateFromText(aDateText);
            const bDate = getDateFromText(bDateText);
            
            return aDate - bDate;
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }
    
    function getDateFromText(text) {
        // Extract date from text (assuming format like "15 Dec 2024")
        const dateMatch = text.match(/\d{1,2}\s+\w{3}\s+\d{4}/);
        if (dateMatch) {
            return new Date(dateMatch[0]).getTime();
        }
        return 0;
    }
    
    // Export projects functionality (optional)
    const exportBtn = document.querySelector('.header-actions .btn[href*="reports"]');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // In real app, this would trigger a report generation/download
            showNotification('Generating report...', 'info');
            setTimeout(() => {
                showNotification('Report ready for download', 'success');
            }, 2000);
        });
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? 'var(--primary-color)' : 
                        type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});

// Add CSS for animations
const dashboardAnimationStyle = document.createElement('style');
dashboardAnimationStyle.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(dashboardAnimationStyle);
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>