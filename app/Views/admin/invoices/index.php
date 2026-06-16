<?php include APP_ROOT . '/app/Views/layouts/header.php'; ?>

<div class="invoice-dashboard">
    <div class="dashboard-container">
        <!-- Page Header -->
        <div class="page-header-premium">
            <div class="header-info">
                <h1 class="dashboard-title">Financial Operations</h1>
                <p class="dashboard-subtitle">Manage SahelSoft billings, client payments, and automated invoicing.</p>
            </div>
            <div class="header-actions">
                <a href="<?= APP_URL ?>/admin/invoices/create" class="btn-premium-accent">
                    <i class="fas fa-plus"></i>
                    <span>Generate New Invoice</span>
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid-premium">
            <div class="stat-card-premium">
                <div class="stat-icon-wrapper primary">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-label">Total Revenue</span>
                    <h3 class="stat-value"><?= number_format($stats['total_amount'] ?? 0) ?> <small>XOF</small></h3>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-icon-wrapper success">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-label">Paid & Verified</span>
                    <h3 class="stat-value text-success"><?= number_format($stats['paid_amount'] ?? 0) ?> <small>XOF</small></h3>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-icon-wrapper warning">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-label">Pending Payments</span>
                    <h3 class="stat-value text-warning"><?= number_format($stats['unpaid_amount'] ?? 0) ?> <small>XOF</small></h3>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-icon-wrapper danger">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-data">
                    <span class="stat-label">Overdue Invoices</span>
                    <h3 class="stat-value text-danger"><?= $stats['overdue_invoices'] ?? 0 ?></h3>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-card-premium">
            <form action="<?= APP_URL ?>/admin/invoices" method="GET" class="premium-filter-form">
                <div class="filter-group">
                    <label>Client</label>
                    <select name="client_id">
                        <option value="">All Clients</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>" <?= ($filters['client_id'] == $client['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Statuses</option>
                        <option value="draft" <?= ($filters['status'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                        <option value="sent" <?= ($filters['status'] == 'sent') ? 'selected' : '' ?>>Sent</option>
                        <option value="paid" <?= ($filters['status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                        <option value="overdue" <?= ($filters['status'] == 'overdue') ? 'selected' : '' ?>>Overdue</option>
                    </select>
                </div>
                <div class="filter-group date-range">
                    <label>Date Range</label>
                    <div class="date-inputs">
                        <input type="date" name="date_from" value="<?= $filters['date_from'] ?? '' ?>">
                        <span>to</span>
                        <input type="date" name="date_to" value="<?= $filters['date_to'] ?? '' ?>">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="<?= APP_URL ?>/admin/invoices" class="btn-reset" title="Reset Filters">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Invoices Table Container -->
        <div class="table-container-premium">
            <div class="table-header">
                <h3>Invoice Records</h3>
                <span class="badge-count"><?= count($invoices) ?> Total</span>
            </div>
            <div class="responsive-table-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Client Identity</th>
                            <th>Amount</th>
                            <th>Timeline</th>
                            <th>Status</th>
                            <th class="text-end">Management</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($invoices)): ?>
                            <tr>
                                <td colspan="6" class="empty-table-state">
                                    <div class="empty-icon">📂</div>
                                    <p>No financial records found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td>
                                        <a href="<?= APP_URL ?>/admin/invoices/view?id=<?= $invoice['id'] ?>" class="invoice-link">
                                            <?= $invoice['invoice_number'] ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="client-info-cell">
                                            <div class="client-avatar">
                                                <?= strtoupper(substr($invoice['client_name'], 0, 1)) ?>
                                            </div>
                                            <div class="client-text">
                                                <span class="client-name"><?= htmlspecialchars($invoice['client_name']) ?></span>
                                                <span class="client-email"><?= htmlspecialchars($invoice['client_email']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="amount-cell"><?= number_format($invoice['total_amount']) ?> <small>XOF</small></span>
                                    </td>
                                    <td>
                                        <div class="timeline-cell">
                                            <span>Issued: <?= date('M d', strtotime($invoice['issue_date'])) ?></span>
                                            <?php 
                                                $dueDate = strtotime($invoice['due_date']);
                                                $isOverdue = ($dueDate < time() && $invoice['status'] !== 'paid');
                                            ?>
                                            <span class="due-date <?= $isOverdue ? 'overdue-text' : '' ?>">
                                                Due: <?= date('M d, Y', $dueDate) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $statusLabel = strtoupper($invoice['status']);
                                            if ($isOverdue) $statusLabel = 'OVERDUE';
                                        ?>
                                        <span class="status-badge-premium status-<?= strtolower($invoice['status']) ?> <?= $isOverdue ? 'status-overdue' : '' ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="action-buttons-group">
                                            <a href="<?= APP_URL ?>/admin/invoices/view?id=<?= $invoice['id'] ?>" class="btn-action view" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($invoice['status'] === 'draft'): ?>
                                                <form action="<?= APP_URL ?>/admin/invoices/send" method="POST" class="d-inline">
                                                    <input type="hidden" name="id" value="<?= $invoice['id'] ?>">
                                                    <button type="submit" class="btn-action send" title="Send to Client">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="#" class="btn-action download" title="Download PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* Invoice Dashboard Styling */
.invoice-dashboard {
    padding-top: 100px;
    padding-bottom: 50px;
    background: var(--bg-light);
    min-height: 100vh;
}

.dashboard-container {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 20px;
}

/* Header Styling */
.page-header-premium {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 20px;
}

.dashboard-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--text-dark);
    margin: 0;
}

.dashboard-subtitle {
    color: var(--text-light);
    margin: 5px 0 0 0;
}

.btn-premium-accent {
    background: var(--gradient-accent);
    color: white;
    padding: 14px 28px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: var(--shadow-orange);
    transition: var(--transition);
}

.btn-premium-accent:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(249, 115, 22, 0.3);
    color: white;
}

/* Stats Grid */
.stats-grid-premium {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card-premium {
    background: var(--card-bg);
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 20px;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.stat-card-premium:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.stat-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon-wrapper.primary { background: rgba(14, 159, 110, 0.1); color: var(--primary-color); }
.stat-icon-wrapper.success { background: rgba(14, 159, 110, 0.15); color: #0e9f6e; }
.stat-icon-wrapper.warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
.stat-icon-wrapper.danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }

.stat-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-value {
    margin: 5px 0 0 0;
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--text-dark);
}

/* Filters Styling */
.filters-card-premium {
    background: var(--card-bg);
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    margin-bottom: 40px;
    border: 1px solid var(--border-color);
}

.premium-filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    align-items: flex-end;
}

.filter-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 10px;
}

.filter-group select, 
.filter-group input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-light);
    color: var(--text-color);
    font-family: inherit;
    transition: var(--transition);
}

.filter-group select:focus, 
.filter-group input:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 4px rgba(14, 159, 110, 0.1);
}

.date-inputs {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-inputs input { width: 50%; }

.filter-actions {
    display: flex;
    gap: 10px;
}

.btn-filter {
    background: var(--primary-color);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
}

.btn-filter:hover { background: var(--primary-dark); transform: scale(1.05); }

.btn-reset {
    background: #f3f4f6;
    color: var(--text-color);
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: var(--transition);
}

.btn-reset:hover { background: #e5e7eb; }

/* Table Styling */
.table-container-premium {
    background: var(--card-bg);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.table-header {
    padding: 25px 30px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h3 { margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-dark); }

.badge-count {
    background: rgba(14, 159, 110, 0.1);
    color: var(--primary-color);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
}

.premium-table {
    width: 100%;
    border-collapse: collapse;
}

.premium-table th {
    background: #f9fafb;
    padding: 20px 30px;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 2px solid var(--border-color);
}

.premium-table td {
    padding: 20px 30px;
    border-bottom: 1px solid var(--border-color);
    vertical-align: middle;
}

.invoice-link {
    font-weight: 800;
    color: var(--primary-color);
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
}

.client-info-cell {
    display: flex;
    align-items: center;
    gap: 15px;
}

.client-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gradient-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.client-text { display: flex; flex-direction: column; }

.client-name { font-weight: 700; color: var(--text-dark); font-size: 0.95rem; }

.client-email { font-size: 0.8rem; color: var(--text-light); }

.amount-cell { font-weight: 800; color: var(--text-dark); font-size: 1.1rem; }

.timeline-cell { display: flex; flex-direction: column; gap: 4px; font-size: 0.85rem; }

.due-date { font-weight: 600; color: var(--text-light); }

.overdue-text { color: #dc2626; }

.status-badge-premium {
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 800;
    display: inline-block;
}

.status-paid { background: #d1fae5; color: #065f46; }
.status-sent { background: #dbeafe; color: #1e40af; }
.status-draft { background: #fef3c7; color: #92400e; }
.status-overdue { background: #fee2e2; color: #991b1b; }

.action-buttons-group { display: flex; gap: 8px; justify-content: flex-end; }

.btn-action {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    background: #f3f4f6;
    color: var(--text-color);
}

.btn-action:hover { transform: translateY(-2px); }

.btn-action.view:hover { background: var(--primary-color); color: white; }

.btn-action.send:hover { background: #3b82f6; color: white; }

.btn-action.download:hover { background: #ef4444; color: white; }

.empty-table-state {
    padding: 100px 0;
    text-align: center;
}

.empty-icon { font-size: 3rem; margin-bottom: 15px; }

/* Responsive */
@media (max-width: 768px) {
    .page-header-premium { flex-direction: column; align-items: flex-start; }
    .btn-premium-accent { width: 100%; justify-content: center; }
    .stats-grid-premium { grid-template-columns: 1fr; }
    .premium-filter-form { grid-template-columns: 1fr; }
}
</style>

<?php include APP_ROOT . '/app/Views/layouts/footer.php'; ?>

