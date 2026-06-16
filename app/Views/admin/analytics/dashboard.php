<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.analytics-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.analytics-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.filters-section {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), #3b82f6);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.stat-title {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.stat-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: var(--text-dark);
    margin-bottom: 10px;
    line-height: 1;
}

.stat-change {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.85rem;
    font-weight: 600;
}

.stat-change.positive {
    color: #10b981;
}

.stat-change.negative {
    color: #ef4444;
}

.stat-change.neutral {
    color: #6b7280;
}

.charts-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.chart-card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.chart-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-dark);
}

.chart-container {
    height: 300px;
    position: relative;
}

.chart-placeholder {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border-radius: 8px;
    border: 2px dashed var(--border-color);
    color: var(--text-muted);
    font-size: 0.9rem;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.kpi-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.kpi-title {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 600;
}

.kpi-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--text-dark);
    margin-bottom: 10px;
}

.kpi-progress {
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}

.kpi-progress-bar {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.kpi-progress-bar.good {
    background: linear-gradient(90deg, #10b981, #059669);
}

.kpi-progress-bar.warning {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.kpi-progress-bar.danger {
    background: linear-gradient(90deg, #ef4444, #dc2626);
}

.kpi-label {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-align: right;
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

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
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

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f4f6;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .charts-section {
        grid-template-columns: 1fr;
    }
    
    .kpi-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-form {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="analytics-container">
    <div class="container">
        <div class="analytics-header">
            <h1>📊 Advanced Analytics Dashboard</h1>
            <p style="margin-top: 10px; color: var(--text-muted);">
                Real-time insights and comprehensive business metrics
            </p>
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
                    <label for="date_from">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" 
                           value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="date_to">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" 
                           value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="client_id">Client</label>
                    <select name="client_id" id="client_id" class="form-control">
                        <option value="">All Clients</option>
                        <?php
                        $userModel = new \App\Models\User();
                        $clients = $userModel->where('role', 'client')->findAll();
                        foreach ($clients as $client):
                        ?>
                            <option value="<?= $client['id'] ?>" <?= ($filters['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="<?= APP_URL ?>/admin/analytics/dashboard" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Users</div>
                    <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;">👥</div>
                </div>
                <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
                <div class="stat-change positive">
                    <span>↑</span>
                    <span><?= $stats['new_users_this_month'] ?> this month</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Active Clients</div>
                    <div class="stat-icon" style="background: #d1fae5; color: #10b981;">🤝</div>
                </div>
                <div class="stat-value"><?= number_format($stats['active_clients']) ?></div>
                <div class="stat-change <?= $stats['user_growth_rate'] >= 0 ? 'positive' : 'negative' ?>">
                    <span><?= $stats['user_growth_rate'] >= 0 ? '↑' : '↓' ?></span>
                    <span><?= abs($stats['user_growth_rate']) ?>% growth</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Total Revenue</div>
                    <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">💰</div>
                </div>
                <div class="stat-value">₦<?= number_format($stats['total_revenue']) ?></div>
                <div class="stat-change <?= $stats['revenue_growth_rate'] >= 0 ? 'positive' : 'negative' ?>">
                    <span><?= $stats['revenue_growth_rate'] >= 0 ? '↑' : '↓' ?></span>
                    <span><?= abs($stats['revenue_growth_rate']) ?>% growth</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Completed Projects</div>
                    <div class="stat-icon" style="background: #dcfce7; color: #22c55e;">✅</div>
                </div>
                <div class="stat-value"><?= number_format($stats['completed_projects']) ?></div>
                <div class="stat-change neutral">
                    <span>→</span>
                    <span><?= $stats['project_completion_rate'] ?>% completion rate</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Proposals</div>
                    <div class="stat-icon" style="background: #f3e8ff; color: #8b5cf6;">📋</div>
                </div>
                <div class="stat-value"><?= number_format($stats['total_proposals']) ?></div>
                <div class="stat-change <?= $stats['proposal_conversion_rate'] >= 0 ? 'positive' : 'negative' ?>">
                    <span>→</span>
                    <span><?= $stats['proposal_conversion_rate'] ?>% conversion</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Contact Inquiries</div>
                    <div class="stat-icon" style="background: #fee2e2; color: #ef4444;">📧</div>
                </div>
                <div class="stat-value"><?= number_format($stats['total_contacts']) ?></div>
                <div class="stat-change <?= $stats['contact_conversion_rate'] >= 0 ? 'positive' : 'negative' ?>">
                    <span>→</span>
                    <span><?= $stats['contact_conversion_rate'] ?>% converted</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Payment Milestones</div>
                    <div class="stat-icon" style="background: #e0e7ff; color: #6366f1;">💳</div>
                </div>
                <div class="stat-value"><?= number_format($stats['total_milestones']) ?></div>
                <div class="stat-change neutral">
                    <span>→</span>
                    <span><?= $stats['paid_milestones'] ?> paid</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Outstanding Amount</div>
                    <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">⏰</div>
                </div>
                <div class="stat-value">₦<?= number_format($stats['pending_payments']['total']) ?></div>
                <div class="stat-change negative">
                    <span>⚠</span>
                    <span><?= $stats['pending_payments']['count'] ?> pending</span>
                </div>
            </div>
        </div>

        <div class="charts-section">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Revenue Trend</h3>
                    <select id="revenue-period" class="form-control" style="width: 150px;">
                        <option value="3months">Last 3 Months</option>
                        <option value="6months">Last 6 Months</option>
                        <option value="12months" selected>Last 12 Months</option>
                    </select>
                </div>
                <div class="chart-container">
                    <div class="chart-placeholder">
                        Revenue chart will be displayed here
                        <br>
                        <small>Integration with Chart.js recommended</small>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Project Status Distribution</h3>
                    <button class="btn btn-primary" style="padding: 8px 15px; font-size: 0.85rem;">Export</button>
                </div>
                <div class="chart-container">
                    <div class="chart-placeholder">
                        Project status pie chart
                        <br>
                        <small>Active: <?= $stats['active_projects'] ?> | Completed: <?= $stats['completed_projects'] ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-title">Revenue Target</div>
                    <div class="stat-icon" style="background: #dbeafe; color: #3b82f6; width: 30px; height: 30px;">🎯</div>
                </div>
                <div class="kpi-value">₦<?= number_format($stats['total_revenue'] * 1.2) ?></div>
                <div class="kpi-progress">
                    <div class="kpi-progress-bar good" style="width: 83%;"></div>
                </div>
                <div class="kpi-label">83% of target achieved</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-title">Conversion Target</div>
                    <div class="stat-icon" style="background: #d1fae5; color: #10b981; width: 30px; height: 30px;">📈</div>
                </div>
                <div class="kpi-value">25%</div>
                <div class="kpi-progress">
                    <div class="kpi-progress-bar good" style="width: <?= min($stats['contact_conversion_rate'], 100) ?>%;"></div>
                </div>
                <div class="kpi-label"><?= $stats['contact_conversion_rate'] ?>% achieved</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-title">Project Completion</div>
                    <div class="stat-icon" style="background: #fef3c7; color: #f59e0b; width: 30px; height: 30px;">⚡</div>
                </div>
                <div class="kpi-value">85%</div>
                <div class="kpi-progress">
                    <div class="kpi-progress-bar <?= $stats['project_completion_rate'] >= 85 ? 'good' : 'warning' ?>" style="width: <?= min($stats['project_completion_rate'], 100) ?>%;"></div>
                </div>
                <div class="kpi-label"><?= $stats['project_completion_rate'] ?>% completed</div>
            </div>

            <div class="kpi-card">
                <div class="kpi-header">
                    <div class="kpi-title">Client Satisfaction</div>
                    <div class="stat-icon" style="background: #dcfce7; color: #22c55e; width: 30px; height: 30px;">😊</div>
                </div>
                <div class="kpi-value">90%</div>
                <div class="kpi-progress">
                    <div class="kpi-progress-bar good" style="width: <?= min($stats['client_satisfaction_rate'], 100) ?>%;"></div>
                </div>
                <div class="kpi-label"><?= $stats['client_satisfaction_rate'] ?>% satisfaction</div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= APP_URL ?>/admin/analytics/reports?type=comprehensive" class="btn btn-primary" style="margin-right: 10px;">
                📊 Generate Full Report
            </a>
            <a href="<?= APP_URL ?>/admin/analytics/exportData?type=dashboard&format=csv" class="btn btn-secondary">
                📥 Export Data
            </a>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
// Auto-refresh functionality
let autoRefreshInterval;

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        refreshDashboard();
    }, 30000); // Refresh every 30 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

function refreshDashboard() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'flex';
    
    fetch('/admin/analytics/realtime?ajax=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stats with new data
                updateStats(data.data);
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
        })
        .finally(() => {
            loadingOverlay.style.display = 'none';
        });
}

function updateStats(newStats) {
    // Update stat values
    const statValues = document.querySelectorAll('.stat-value');
    if (statValues.length >= 8) {
        statValues[0].textContent = newStats.total_users.toLocaleString();
        statValues[1].textContent = newStats.active_clients.toLocaleString();
        statValues[2].textContent = '₦' + newStats.total_revenue.toLocaleString();
        statValues[3].textContent = newStats.completed_projects.toLocaleString();
        statValues[4].textContent = newStats.total_proposals.toLocaleString();
        statValues[5].textContent = newStats.total_contacts.toLocaleString();
        statValues[6].textContent = newStats.total_milestones.toLocaleString();
        statValues[7].textContent = '₦' + newStats.pending_payments.total.toLocaleString();
    }
}

// Period selector for revenue chart
document.getElementById('revenue-period').addEventListener('change', function() {
    const period = this.value;
    window.location.href = `/admin/analytics/revenue?period=${period}`;
});

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();
});

// Stop auto-refresh when page is hidden
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        startAutoRefresh();
    }
});

// Handle filter form submission
document.querySelector('.filters-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    window.location.href = '<?= APP_URL ?>/admin/analytics/dashboard?' + params.toString();
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
