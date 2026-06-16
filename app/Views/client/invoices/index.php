<?php include APP_ROOT . '/app/Views/layouts/header.php'; ?>

<div class="client-portal-wrapper">
    <div class="portal-container">
        <!-- Page Header -->
        <div class="page-header-premium">
            <div class="header-info">
                <h1 class="dashboard-title">My Financial Center</h1>
                <p class="dashboard-subtitle">Review your invoices, payment history, and pending balances with SahelSoft.</p>
            </div>
            <div class="header-actions">
                <div class="balance-card">
                    <span class="balance-label">Total Balance Due</span>
                    <?php 
                        $unpaidSum = array_sum(array_column(array_filter($invoices, fn($i) => $i['status'] !== 'paid'), 'total_amount'));
                    ?>
                    <h3 class="balance-value"><?= number_format($unpaidSum) ?> <small>XOF</small></h3>
                </div>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="invoices-grid-premium">
            <?php if (empty($invoices)): ?>
                <div class="empty-portal-state">
                    <div class="empty-illustration">📄</div>
                    <h3>No Invoices Yet</h3>
                    <p>When we issue an invoice for your projects, they will appear here for payment.</p>
                </div>
            <?php else: ?>
                <div class="premium-table-card">
                    <div class="card-header-clean">
                        <h3>Invoice History</h3>
                        <span class="count-badge"><?= count($invoices) ?> Records</span>
                    </div>
                    <div class="table-responsive-wrapper">
                        <table class="portal-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Project Reference</th>
                                    <th>Amount</th>
                                    <th>Issue Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $invoice): ?>
                                    <tr class="invoice-row">
                                        <td class="id-cell">
                                            <span class="inv-num"><?= $invoice['invoice_number'] ?></span>
                                        </td>
                                        <td>
                                            <div class="project-info">
                                                <span class="project-title"><?= htmlspecialchars($invoice['title']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="amount-value"><?= number_format($invoice['total_amount']) ?> <small>XOF</small></span>
                                        </td>
                                        <td>
                                            <span class="date-text"><?= date('M d, Y', strtotime($invoice['issue_date'])) ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $status = strtolower($invoice['status']);
                                                $dueDate = strtotime($invoice['due_date']);
                                                $isOverdue = ($dueDate < time() && $status !== 'paid');
                                                $displayStatus = $isOverdue ? 'OVERDUE' : strtoupper($status);
                                            ?>
                                            <span class="portal-status-badge status-<?= $status ?> <?= $isOverdue ? 'status-overdue' : '' ?>">
                                                <?= $displayStatus ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?= APP_URL ?>/client/invoices/view?id=<?= $invoice['id'] ?>" class="btn-portal-action <?= ($status === 'paid') ? 'secondary' : 'primary' ?>">
                                                <?= ($status === 'paid') ? '<i class="fas fa-eye"></i> View' : '<i class="fas fa-credit-card"></i> Pay Now' ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.client-portal-wrapper {
    padding-top: 100px;
    padding-bottom: 80px;
    background: var(--bg-light);
    min-height: 100vh;
}

.portal-container {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 0 20px;
}

.page-header-premium {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 50px;
    flex-wrap: wrap;
    gap: 30px;
}

.balance-card {
    background: var(--gradient-primary);
    color: white;
    padding: 20px 30px;
    border-radius: 20px;
    box-shadow: var(--shadow-md);
    text-align: right;
}

.balance-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    opacity: 0.8;
    letter-spacing: 1px;
}

.balance-value {
    margin: 5px 0 0 0;
    font-size: 1.8rem;
    font-weight: 800;
}

/* Table Card */
.premium-table-card {
    background: white;
    border-radius: 24px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.card-header-clean {
    padding: 25px 35px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9fafb;
}

.card-header-clean h3 { margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--text-dark); }

.count-badge {
    background: rgba(14, 159, 110, 0.1);
    color: var(--primary-color);
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
}

.portal-table {
    width: 100%;
    border-collapse: collapse;
}

.portal-table th {
    padding: 20px 35px;
    text-align: left;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.portal-table td {
    padding: 25px 35px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.inv-num { font-weight: 800; color: var(--primary-color); font-size: 1rem; }

.project-title { font-weight: 700; color: var(--text-dark); }

.amount-value { font-weight: 800; color: var(--text-dark); font-size: 1.1rem; }

.portal-status-badge {
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 800;
    display: inline-block;
}

.status-paid { background: #d1fae5; color: #065f46; }
.status-sent { background: #dbeafe; color: #1e40af; }
.status-overdue { background: #fee2e2; color: #991b1b; }

.btn-portal-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    transition: var(--transition);
}

.btn-portal-action.primary { background: var(--gradient-accent); color: white; box-shadow: var(--shadow-orange); }
.btn-portal-action.secondary { background: #f3f4f6; color: var(--text-dark); }

.btn-portal-action:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

.empty-portal-state {
    text-align: center;
    padding: 100px 40px;
    background: white;
    border-radius: 24px;
    border: 2px dashed var(--border-color);
}

.empty-illustration { font-size: 4rem; margin-bottom: 20px; }

@media (max-width: 768px) {
    .page-header-premium { flex-direction: column; align-items: flex-start; }
    .balance-card { width: 100%; text-align: left; }
    .portal-table thead { display: none; }
    .portal-table td { display: block; padding: 10px 35px; text-align: left !important; border: none; }
    .invoice-row { display: block; padding: 20px 0; border-bottom: 5px solid #f3f4f6; }
    .btn-portal-action { width: 100%; justify-content: center; margin-top: 10px; }
}
</style>

<?php include APP_ROOT . '/app/Views/layouts/footer.php'; ?>
