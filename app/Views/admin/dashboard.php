<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Admin Dashboard Styles */
.admin-dashboard {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
    overflow-x: auto;
}

.dashboard-container {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 20px;
}

.dashboard-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 30px;
}

.dashboard-sidebar {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 20px;
    height: fit-content;
}

.dashboard-main {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 30px;
}

.sidebar-nav ul {
    list-style: none;
    padding: 0;
}

.sidebar-nav li {
    margin-bottom: 5px;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 8px;
    color: var(--text-color);
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 500;
}

.sidebar-nav a:hover {
    background: rgba(14, 159, 110, 0.08);
    color: var(--primary-color);
}

.sidebar-nav a.active {
    background: var(--primary-color);
    color: white;
}

.nav-icon {
    font-size: 1.2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-top: 4px solid var(--primary-color);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
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
}

.stat-label {
    color: var(--text-light);
    font-size: 0.95rem;
    font-weight: 500;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin: 20px 0;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px;
}

.data-table th {
    background: var(--primary-color);
    color: white;
    text-align: left;
    padding: 18px 20px;
    font-weight: 600;
    border-bottom: 3px solid var(--primary-dark);
}

.data-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.data-table tr:hover {
    background: #f9f9f9;
}

.data-table tr:last-child td {
    border-bottom: none;
}

.role-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: capitalize;
}

.role-admin { background: #fee2e2; color: #991b1b; }
.role-developer { background: #dbeafe; color: #1e40af; }
.role-project_manager { background: #f3e8ff; color: #6b21a8; }
.role-client { background: #dcfce7; color: #166534; }
.role-user { background: #fef3c7; color: #92400e; }

/* Portfolio Management Styles */
.portfolio-manager {
    margin-top: 30px;
}

.add-project-btn {
    background: var(--gradient-accent);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.add-project-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2);
}

.portfolio-grid-admin {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.portfolio-card-admin {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
    position: relative;
}

.portfolio-card-admin:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.portfolio-image-admin {
    height: 180px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    position: relative;
}

.portfolio-actions {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.portfolio-card-admin:hover .portfolio-actions {
    opacity: 1;
}

.action-btn {
    background: white;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.action-btn.edit { color: #3b82f6; }
.action-btn.delete { color: #ef4444; }

.portfolio-content-admin {
    padding: 20px;
}

.portfolio-content-admin h3 {
    margin: 0 0 10px 0;
    font-size: 1.25rem;
    color: var(--text-dark);
}

.portfolio-content-admin p {
    color: var(--text-light);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 15px;
}

.portfolio-tags-admin {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.tag-admin {
    background: var(--bg-light);
    color: var(--primary-dark);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    padding: 40px;
    border-radius: 16px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-light);
}

.modal h2 {
    margin-bottom: 25px;
    color: var(--text-dark);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-dark);
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(14, 159, 110, 0.2);
}

.btn-secondary {
    background: transparent;
    color: var(--text-color);
    border: 2px solid #e5e7eb;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-sidebar {
        position: sticky;
        top: 80px;
        z-index: 100;
    }
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 20px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .portfolio-grid-admin {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        padding: 25px;
        width: 95%;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<section class="admin-dashboard">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome back, Administrator! Manage your platform, users, and portfolio projects.</p>
        </div>
        
        <div class="dashboard-grid">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="#dashboard" class="active">
                            <span class="nav-icon">📊</span>
                            <span>Dashboard</span>
                        </a></li>
                        <li><a href="#users">
                            <span class="nav-icon">👥</span>
                            <span>User Management</span>
                        </a></li>
                        <li><a href="#projects">
                            <span class="nav-icon">💼</span>
                            <span>Project Portfolio</span>
                        </a></li>
                        <li><a href="#settings">
                            <span class="nav-icon">⚙️</span>
                            <span>System Settings</span>
                        </a></li>
                        <li><a href="#analytics">
                            <span class="nav-icon">📈</span>
                            <span>Analytics</span>
                        </a></li>
                        <li><a href="<?= APP_URL ?>/admin/proposals">
                            <span class="nav-icon">📋</span>
                            <span>Proposals</span>
                        </a></li>
                        <li><a href="<?= APP_URL ?>/admin/content">
                            <span class="nav-icon">📝</span>
                            <span>Content</span>
                        </a></li>
                        <li><a href="#messages">
                            <span class="nav-icon">✉️</span>
                            <span>Messages</span>
                        </a></li>
                        <li><a href="<?= APP_URL ?>/admin/invoices">
                            <span class="nav-icon">🧾</span>
                            <span>Invoices</span>
                        </a></li>
                        <li><a href="#blog">
                            <span class="nav-icon">📝</span>
                            <span>Blog Management</span>
                        </a></li>
                    </ul>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <main class="dashboard-main">
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['total_users'] ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['active_clients'] ?></div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['completed_projects'] ?></div>
                        <div class="stat-label">Completed Projects</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['revenue']) ?> CFA</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['proposals']['total'] ?? 0 ?></div>
                        <div class="stat-label">Total Proposals</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['proposals']['accepted'] ?? 0 ?></div>
                        <div class="stat-label">Accepted Proposals</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['milestones']['total_milestones'] ?? 0 ?></div>
                        <div class="stat-label">Payment Milestones</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['conversion_rate'] ?>%</div>
                        <div class="stat-label">Conversion Rate</div>
                    </div>
                </div>
                
                <!-- Blog Management Section -->
                <section id="blog">
                    <div class="section-header">
                        <h2>Blog Management</h2>
                        <button class="btn-primary" onclick="openBlogModal()">
                            <span>➕</span>
                            New Blog Post
                        </button>
                    </div>
                    <p>Create, edit, and manage blog posts for your website.</p>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($blogPosts)): ?>
                                    <?php foreach ($blogPosts as $post): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= APP_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank">
                                                <?= htmlspecialchars($post['title']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($post['category'] ?? 'Uncategorized') ?></td>
                                        <td><?= htmlspecialchars($post['author'] ?? 'Admin') ?></td>
                                        <td>
                                            <?php 
                                            $status = $post['status'] ?? 'draft';
                                            $bgColors = [
                                                'published' => '#d1fae5',
                                                'draft' => '#fef3c7'
                                            ];
                                            $textColors = [
                                                'published' => '#065f46',
                                                'draft' => '#92400e'
                                            ];
                                            ?>
                                            <span class="status-badge" style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; background: <?= $bgColors[$status] ?>; color: <?= $textColors[$status] ?>;">
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($post['created_at'])) ?></td>
                                        <td style="display: flex; gap: 5px;">
                                            <button class="action-btn edit" title="Edit Post" onclick='editBlogPost(<?= json_encode($post, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                                <span>✏️</span>
                                            </button>
                                            <button class="action-btn delete" title="Delete Post" onclick="deleteBlogPost(<?= $post['id'] ?>)">
                                                <span>🗑️</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px;">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">📝</div>
                                                <h3>No Blog Posts</h3>
                                                <p>Start creating blog posts to engage your audience.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- User Management Section -->
                <section id="users">
                    <h2>User Management</h2>
                    <p>View and manage all registered users on the platform.</p>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Company</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?= $user['id'] ?></td>
                                    <td style="font-weight: bold;"><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="role-badge role-<?= str_replace('_', '', $user['role']) ?>">
                                            <?= ucwords(str_replace('_', ' ', $user['role'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($user['company_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                                    <td style="display: flex; gap: 5px;">
                                        <button class="action-btn edit" title="Edit User" onclick='openUserModal(<?= json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <span>✏️</span>
                                        </button>
                                        <form method="POST" action="<?= APP_URL ?>/admin/users/delete" onsubmit="return confirm('Delete user?');" style="margin:0;">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="action-btn delete" title="Delete User">
                                                <span>🗑️</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                
                <!-- Portfolio Management Section -->
                <section id="projects" class="portfolio-manager">
                    <h2>Portfolio Management</h2>
                    <p>Add, edit, or remove completed projects from your public portfolio.</p>
                    
                    <button class="add-project-btn" onclick="openModal()">
                        <span>+</span>
                        <span>Add New Project</span>
                    </button>
                    
                    <div class="portfolio-grid-admin">
                        <?php foreach ($projects as $project): ?>
                        <div class="portfolio-card-admin">
                            <div class="portfolio-image-admin" style="background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);">
                                <?php if(!empty($project['image_path'])): ?>
                                    <img src="<?= APP_URL ?>/<?= $project['image_path'] ?>" alt="Project" loading="lazy" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <span>💻</span>
                                <?php endif; ?>
                                <div class="portfolio-actions">
                                    <button class="action-btn edit" onclick='editProject(<?= json_encode($project, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                        <span>✏️</span>
                                    </button>
                                    <a href="<?= APP_URL ?>/admin/project/manage?id=<?= $project['id'] ?>" class="action-btn" title="Manage Project" style="background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <span>⚙️</span>
                                    </a>
                                    <form method="POST" action="<?= APP_URL ?>/admin/projects/delete" onsubmit="return confirm('Delete project?');" style="margin:0;">
                                        <input type="hidden" name="id" value="<?= $project['id'] ?>">
                                        <button class="action-btn delete">
                                            <span>🗑️</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="portfolio-content-admin">
                                <h3><?= htmlspecialchars($project['title']) ?></h3>
                                <p><?= htmlspecialchars(substr($project['description'], 0, 100)) ?>...</p>
                                <div class="portfolio-tags-admin">
                                    <span class="tag-admin"><?= htmlspecialchars($project['status']) ?></span>
                                    <?php if(!empty($project['category'])): ?>
                                        <span class="tag-admin"><?= htmlspecialchars($project['category']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <!-- System Settings Section -->
                <section id="settings" style="margin-top: 30px;">
                    <h2>System Settings</h2>
                    <p>Manage website configuration.</p>
                    
                    <form method="POST" action="<?= APP_URL ?>/admin/settings/update" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                        <div class="form-group">
                            <label>Site Name</label>
                            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? 'SahelSoft') ?>">
                        </div>
                        <div class="form-group">
                            <label>Contact Email</label>
                            <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Contact Phone</label>
                            <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>">
                        </div>
                         <div class="form-group">
                            <label>Maintenance Mode (1 = On, 0 = Off)</label>
                            <input type="number" name="maintenance_mode" class="form-control" value="<?= htmlspecialchars($settings['maintenance_mode'] ?? '0') ?>">
                        </div>
                        <div class="form-group">
                            <label>Client Satisfaction (%)</label>
                            <input type="number" name="client_satisfaction" class="form-control" value="<?= htmlspecialchars($settings['client_satisfaction'] ?? '100') ?>" min="0" max="100">
                        </div>
                        <button type="submit" class="btn-primary">Save Settings</button>
                    </form>
                </section>

                <!-- Analytics Section -->
                <section id="analytics" style="margin-top: 30px;">
                    <h2>Analytics</h2>
                    <p>Detailed breakdown of platform activity.</p>
                    
                    <div class="analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 20px;">
                        <!-- Project Distribution -->
                        <div class="analytics-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                            <h3 style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Project Status Distribution</h3>
                            <?php if(empty($stats['project_status_counts'])): ?>
                                <p>No projects data available.</p>
                            <?php else: ?>
                                <?php foreach($stats['project_status_counts'] as $status => $count): ?>
                                    <div style="margin-bottom: 15px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span style="text-transform: capitalize; font-weight: 500;"><?= $status ?></span>
                                            <span style="font-weight: bold;"><?= $count ?></span>
                                        </div>
                                        <div style="background: #f0f0f0; height: 10px; border-radius: 5px; overflow: hidden;">
                                            <?php $percent = ($count / count($projects)) * 100; ?>
                                            <div style="background: var(--primary-color); height: 100%; width: <?= $percent ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- User Demographics -->
                        <div class="analytics-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                            <h3 style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">User Roles Distribution</h3>
                             <?php if(empty($stats['user_role_counts'])): ?>
                                <p>No user data available.</p>
                            <?php else: ?>
                                <?php foreach($stats['user_role_counts'] as $role => $count): ?>
                                    <div style="margin-bottom: 15px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span style="text-transform: capitalize; font-weight: 500;"><?= str_replace('_', ' ', $role) ?></span>
                                            <span style="font-weight: bold;"><?= $count ?></span>
                                        </div>
                                        <div style="background: #f0f0f0; height: 10px; border-radius: 5px; overflow: hidden;">
                                            <?php $percent = ($count / count($users)) * 100; ?>
                                            <div style="background: #3b82f6; height: 100%; width: <?= $percent ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Revenue by Category -->
                        <div class="analytics-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                            <h3 style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Revenue by Category</h3>
                            <?php if(empty($stats['revenue_by_category'])): ?>
                                <p>No revenue data available.</p>
                            <?php else: ?>
                                <?php 
                                $maxRevenue = max($stats['revenue_by_category']); 
                                foreach($stats['revenue_by_category'] as $cat => $rev): 
                                ?>
                                    <div style="margin-bottom: 15px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span style="font-weight: 500;"><?= htmlspecialchars($cat) ?></span>
                                            <span style="font-weight: bold; color: var(--success);"><?= number_format($rev) ?> CFA</span>
                                        </div>
                                        <div style="background: #f0f0f0; height: 10px; border-radius: 5px; overflow: hidden;">
                                            <?php $percent = $maxRevenue > 0 ? ($rev / $maxRevenue) * 100 : 0; ?>
                                            <div style="background: var(--success); height: 100%; width: <?= $percent ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Top Clients -->
                        <div class="analytics-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                            <h3 style="margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Top Clients (by Project Value)</h3>
                            <?php if(empty($stats['top_clients'])): ?>
                                <p>No client transaction data available.</p>
                            <?php else: ?>
                                <ul style="list-style: none; padding: 0;">
                                <?php foreach($stats['top_clients'] as $clientId => $totalSpent): ?>
                                    <li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f9fafb;">
                                        <span style="font-weight: 500;"><?= htmlspecialchars($stats['client_names'][$clientId] ?? 'Unknown Client') ?></span>
                                        <span style="font-weight: bold;"><?= number_format($totalSpent) ?> CFA</span>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <!-- Messages Section -->
                <section id="messages" style="margin-top: 30px;">
                    <h2>Messages</h2>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Service</th>
                                    <th>Attachments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($messages)): ?>
                                    <tr><td colspan="5">No messages yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($messages as $msg): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($msg['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($msg['name']) ?></td>
                                        <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a></td>
                                        <td><?= htmlspecialchars($msg['project_type'] ?? 'General') ?></td>
                                        <td>
                                            <?php if(!empty($msg['attachments'])): ?>
                                                <?php $files = json_decode($msg['attachments'], true); ?>
                                                <?php foreach($files as $file): ?>
                                                    <a href="<?= APP_URL ?>/<?= $file ?>" target="_blank" title="View Attachment">📎</a>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $status = $msg['status'] ?? 'pending';
                                            $bgColors = [
                                                'accepted' => '#d1fae5',
                                                'rejected' => '#fee2e2', 
                                                'converted' => '#dbeafe',
                                                'pending' => '#fef3c7',
                                                'new' => '#3b82f6',
                                                'completed' => '#10b981'
                                            ];
                                            $textColors = [
                                                'accepted' => '#065f46',
                                                'rejected' => '#991b1b',
                                                'converted' => '#1e40af', 
                                                'pending' => '#92400e',
                                                'new' => '#ffffff',
                                                'completed' => '#ffffff'
                                            ];
                                        ?>
                                            <span class="status-badge status-<?= $status ?>" style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; background: <?= $bgColors[$status] ?>; color: <?= $textColors[$status] ?>;">
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </td>
                                        <td style="display: flex; gap: 5px;">
                                            <?php if(in_array($msg['status'] ?? 'pending', ['new', 'pending']) && ($msg['status'] ?? 'pending') !== 'converted'): ?>
                                                <form method="POST" action="<?= APP_URL ?>/admin/contacts/convert-to-project" style="margin:0;">
                                                    <input type="hidden" name="contact_id" value="<?= $msg['id'] ?>">
                                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                    <button type="submit" class="action-btn edit" title="Convert to Project" style="background: #10b981; color: white;" onclick="return confirm('Convert this contact inquiry to a project? A client account will be created if needed.')">
                                                        <span>🔄</span>
                                                    </button>
                                                </form>
                                                <button class="action-btn delete" title="Reject Request" onclick="openRejectModal(<?= $msg['id'] ?>)">
                                                    <span>❌</span>
                                                </button>
                                            <?php elseif($msg['status'] === 'converted'): ?>
                                                <?php 
                                                // Find the associated project for this converted contact
                                                $pdo = \App\Core\Database::getInstance()->getConnection();
                                                $stmt = $pdo->prepare("SELECT id FROM projects WHERE client_id IN (SELECT id FROM users WHERE email = ?) ORDER BY created_at DESC LIMIT 1");
                                                $stmt->execute([$msg['email']]);
                                                $project = $stmt->fetch();
                                                if ($project): 
                                                ?>
                                                    <a href="<?= APP_URL ?>/admin/project/manage?id=<?= $project['id'] ?>" class="action-btn" title="Manage Project" style="background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                                        <span>⚙️</span>
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: #9ca3af; font-size: 0.8rem;">No Project</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color: #9ca3af; font-size: 0.8rem;">Managed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</section>

<!-- Project Modal -->
<div id="projectModal" class="modal">
    <div class="modal-content">
        <button type="button" class="close-modal" onclick="closeModal()">&times;</button>
        <h2 id="projectModalTitle">Add New Project</h2>
        <form id="projectForm" method="POST" action="<?= APP_URL ?>/admin/projects/create" enctype="multipart/form-data">
            <input type="hidden" name="id" id="projectId">
            
            <div class="form-group">
                <label for="projectTitle">Title *</label>
                <input type="text" name="title" id="projectTitle" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="projectDescription">Short Description</label>
                <textarea name="description" id="projectDescription" class="form-control" rows="2"></textarea>
            </div>

            <div style="background: #f9fafb; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
                <h4 style="margin-bottom: 15px; color: var(--primary-color);">Case Study Details</h4>
                <div class="form-group">
                    <label for="projectProblem">The Challenge / Problem</label>
                    <textarea name="problem" id="projectProblem" class="form-control" rows="3" placeholder="Describe the problem this project solved..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="projectSolution">Our Solution</label>
                    <textarea name="solution" id="projectSolution" class="form-control" rows="3" placeholder="Describe the custom solution implemented..."></textarea>
                </div>

                <div class="form-group">
                    <label for="projectResults">Results & Impact</label>
                    <textarea name="results_impact" id="projectResults" class="form-control" rows="3" placeholder="Describe the successful outcome..."></textarea>
                </div>
            </div>
            
            <div class="form-group">
                <label for="projectClient">Client *</label>
                <!-- In a real app, select from $users where role=client -->
                <select name="client_id" id="projectClient" class="form-control" required>
                    <?php foreach($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['full_name']) ?> (<?= $u['email'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="projectStatus" class="form-control">
                    <option value="proposed">Proposed</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="on_hold">On Hold</option>
                </select>
            </div>

            <div class="form-group">
                <label>Progress (%)</label>
                <input type="number" name="progress" id="projectProgress" class="form-control" min="0" max="100" value="0">
            </div>

            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Best Case Completion</label>
                    <input type="date" name="best_case_completion" id="projectBestCase" class="form-control">
                </div>
                <div class="form-group">
                    <label>Worst Case Completion</label>
                    <input type="date" name="worst_case_completion" id="projectWorstCase" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="projectCategory" class="form-control">
            </div>

             <div class="form-group">
                <label>Tags (Technology Stack)</label>
                <input type="text" name="tags" id="projectTags" class="form-control" placeholder="e.g., PHP, MySQL, JavaScript, Bootstrap">
                <small style="color: var(--text-light); font-size: 0.85rem; margin-top: 5px; display: block;">
                    🏷️ Enter technologies used in this project, separated by commas. These will be displayed in the case study modal.
                </small>
            </div>

            <div class="form-group">
                <label>Budget</label>
                <input type="number" name="budget" id="projectBudget" class="form-control">
            </div>

             <div class="form-group">
                <label>Live URL</label>
                <input type="url" name="live_url" id="projectUrl" class="form-control">
            </div>

             <div class="form-group">
                <label>Demo Video URL (Optional)</label>
                <input type="url" name="demo_url" id="projectDemoUrl" class="form-control" placeholder="e.g., https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                <small style="color: var(--text-light); font-size: 0.85rem; margin-top: 5px; display: block;">
                    📹 Add a link to a demo video (YouTube, Vimeo, etc.). This will appear in the case study modal as "Click here to see the demo video of the project"
                </small>
            </div>
            
             <div style="background: #f0fdf4; padding: 15px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #dcfce7;">
                <h4 style="margin-bottom: 15px; color: var(--primary-color);">Project Visuals</h4>
                <div class="form-group">
                    <label>Main Project Image (Card Thumbnail)</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Dashboard Screenshot</label>
                        <input type="file" name="dashboard_img" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Product Page Screenshot</label>
                        <input type="file" name="product_page_img" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Admin Panel Screenshot</label>
                    <input type="file" name="admin_panel_img" class="form-control">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="projectSubmitBtn">Save Project</button>
                <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <button type="button" class="close-modal" onclick="closeUserModal()">&times;</button>
        <h2 id="userModalTitle">Add User</h2>
        <form id="userForm" method="POST" action="<?= APP_URL ?>/admin/users/create">
            <input type="hidden" name="id" id="userId">
            
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" id="userName" class="form-control" autocomplete="name" required>
            </div>
            
             <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" id="userEmail" class="form-control" autocomplete="email" required>
            </div>

             <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" id="userPhone" class="form-control" autocomplete="tel">
            </div>

             <div class="form-group">
                <label>Company</label>
                <input type="text" name="company_name" id="userCompany" class="form-control" autocomplete="organization">
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" id="userRole" class="form-control">
                    <option value="client">Client</option>
                    <option value="admin">Admin</option>
                    <option value="project_manager">Project Manager</option>
                    <option value="developer">Developer</option>
                </select>
            </div>
            
             <div class="form-group">
                <label>Password (Leave blank to keep current if editing)</label>
                <input type="password" name="password" class="form-control" autocomplete="current-password">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="userSubmitBtn">Save User</button>
                <button type="button" class="btn-secondary" onclick="closeUserModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Blog Modal -->
<div id="blogModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <button type="button" class="close-modal" onclick="closeBlogModal()">&times;</button>
        <h2 id="blogModalTitle">Add New Blog Post</h2>
        <form id="blogForm" method="POST" action="<?= APP_URL ?>/admin/blog/create" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Post Title *</label>
                    <input type="text" name="title" id="blogTitle" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category" id="blogCategory" class="form-control">
                        <option value="">Select Category</option>
                        <option value="Technology">Technology</option>
                        <option value="Business">Business</option>
                        <option value="Design">Design</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Tutorial">Tutorial</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" id="blogAuthor" class="form-control" value="Admin">
            </div>
            
            <div class="form-group">
                <label>Excerpt</label>
                <textarea name="excerpt" id="blogExcerpt" class="form-control" rows="3" placeholder="Brief description of post..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Content *</label>
                <textarea name="content" id="blogContent" class="form-control" rows="10" required placeholder="Write your blog post content here..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Featured Image</label>
                <input type="file" name="featured_image" id="blogImage" class="form-control" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="blogStatus" class="form-control">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="blog_id" id="blogId">
            
            <div class="form-actions">
                <button type="submit" class="btn-primary" id="blogSubmitBtn">Create Post</button>
                <button type="button" class="btn-secondary" onclick="closeBlogModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
// Project Modal
function openModal() {
    document.getElementById('projectModal').classList.add('active');
    document.getElementById('projectForm').reset();
    document.getElementById('projectForm').action = '<?= APP_URL ?>/admin/projects/create';
     document.getElementById('projectModalTitle').innerText = 'Add New Project';
      document.getElementById('projectSubmitBtn').innerText = 'Create Project';
}

function closeModal() {
    document.getElementById('projectModal').classList.remove('active');
}

function editProject(project) {
    openModal();
    document.getElementById('projectForm').action = '<?= APP_URL ?>/admin/projects/update';
    document.getElementById('projectId').value = project.id;
    document.getElementById('projectTitle').value = project.title;
    document.getElementById('projectDescription').value = project.description;
    if(project.client_id) document.getElementById('projectClient').value = project.client_id;
    if(project.status) document.getElementById('projectStatus').value = project.status;
    if(project.category) document.getElementById('projectCategory').value = project.category;
    if(project.tags) document.getElementById('projectTags').value = project.tags;
    if(project.budget) document.getElementById('projectBudget').value = project.budget;
    if(project.live_url) document.getElementById('projectUrl').value = project.live_url;
    if(project.demo_url) document.getElementById('projectDemoUrl').value = project.demo_url;
    if(project.progress) document.getElementById('projectProgress').value = project.progress;
    if(project.best_case_completion) document.getElementById('projectBestCase').value = project.best_case_completion;
    if(project.worst_case_completion) document.getElementById('projectWorstCase').value = project.worst_case_completion;
    
    // Case Study Fields
    if(project.problem) document.getElementById('projectProblem').value = project.problem;
    if(project.solution) document.getElementById('projectSolution').value = project.solution;
    if(project.results_impact) document.getElementById('projectResults').value = project.results_impact;

    document.getElementById('projectModalTitle').innerText = 'Edit Project';
    document.getElementById('projectSubmitBtn').innerText = 'Update Project';
}

// User Modal
function openUserModal(user = null) {
    document.getElementById('userModal').classList.add('active');
    const form = document.getElementById('userForm');
    form.reset();
    
    if (user) {
        form.action = '<?= APP_URL ?>/admin/users/update';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.full_name;
        document.getElementById('userEmail').value = user.email;
        if(user.phone) document.getElementById('userPhone').value = user.phone;
        if(user.company_name) document.getElementById('userCompany').value = user.company_name;
        if(user.role) document.getElementById('userRole').value = user.role;
        
        document.getElementById('userModalTitle').innerText = 'Edit User';
        document.getElementById('userSubmitBtn').innerText = 'Update User';
    } else {
        form.action = '<?= APP_URL ?>/admin/users/create';
        document.getElementById('userModalTitle').innerText = 'Add New User';
        document.getElementById('userSubmitBtn').innerText = 'Create User';
    }
}

function closeUserModal() {
    document.getElementById('userModal').classList.remove('active');
}

// Blog Modal Functions
function openBlogModal() {
    document.getElementById('blogModal').classList.add('active');
    document.getElementById('blogForm').reset();
    document.getElementById('blogForm').action = '<?= APP_URL ?>/admin/blog/create';
    document.getElementById('blogModalTitle').innerText = 'Add New Blog Post';
    document.getElementById('blogSubmitBtn').innerText = 'Create Post';
}

function closeBlogModal() {
    document.getElementById('blogModal').classList.remove('active');
}

function editBlogPost(post) {
    openBlogModal();
    document.getElementById('blogForm').action = '<?= APP_URL ?>/admin/blog/update';
    document.getElementById('blogId').value = post.id;
    document.getElementById('blogTitle').value = post.title;
    document.getElementById('blogCategory').value = post.category || '';
    document.getElementById('blogAuthor').value = post.author || 'Admin';
    document.getElementById('blogExcerpt').value = post.excerpt || '';
    document.getElementById('blogContent').value = post.content || '';
    document.getElementById('blogStatus').value = post.status || 'draft';
    
    document.getElementById('blogModalTitle').innerText = 'Edit Blog Post';
    document.getElementById('blogSubmitBtn').innerText = 'Update Post';
}

function deleteBlogPost(postId) {
    if (confirm('Are you sure you want to delete this blog post? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= APP_URL ?>/admin/blog/delete';
        
        const postIdInput = document.createElement('input');
        postIdInput.type = 'hidden';
        postIdInput.name = 'blog_id';
        postIdInput.value = postId;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= csrf_token() ?>';
        
        form.appendChild(postIdInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
<!-- Reject Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <button type="button" class="close-modal" onclick="closeRejectModal()">&times;</button>
        <h2>Reject Request</h2>
        <form method="POST" action="<?= APP_URL ?>/admin/requests/reject">
            <input type="hidden" name="id" id="rejectId">
            <div class="form-group">
                <label>Admin Notes / Feedback for Client</label>
                <textarea name="admin_notes" class="form-control" rows="4" placeholder="Explain why the request was rejected..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary" style="background: #ef4444;">Reject Request</button>
                <button type="button" class="btn-secondary" onclick="closeRejectModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id) {
    document.getElementById('rejectModal').classList.add('active');
    document.getElementById('rejectId').value = id;
}
function closeUserModal() {
    document.getElementById('userModal').classList.remove('active');
}
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>