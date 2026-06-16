<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.proposals-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.proposals-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.proposals-table {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: var(--bg-light);
    padding: 15px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid var(--border-color);
}

.table td {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
}

.table tr:hover {
    background: var(--bg-light);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-draft {
    background: #f3f4f6;
    color: #6b7280;
}

.status-sent {
    background: #dbeafe;
    color: #1e40af;
}

.status-accepted {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.status-expired {
    background: #fef3c7;
    color: #92400e;
}

.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.85rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-secondary {
    background: #6b7280;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-success {
    background: #10b981;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.3s;
}

.btn-success:hover {
    background: #059669;
}

.amount {
    font-weight: 600;
    color: var(--primary-color);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.empty-state h3 {
    margin-bottom: 10px;
    color: var(--text-dark);
}
</style>

<div class="proposals-container">
    <div class="container">
        <div class="proposals-header">
            <h1>Proposals Management</h1>
            <p>Manage project proposals and track client responses</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Total Proposals</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['sent'] ?></div>
                <div class="stat-label">Pending Response</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['accepted'] ?></div>
                <div class="stat-label">Accepted</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">₦<?= number_format($stats['accepted_value'], 2) ?></div>
                <div class="stat-label">Accepted Value</div>
            </div>
        </div>

        <!-- Create New Proposal Button -->
        <div style="margin-bottom: 20px;">
            <a href="<?= APP_URL ?>/admin/proposals/create" class="btn-primary">+ Create New Proposal</a>
            <a href="<?= APP_URL ?>/admin/proposals" class="btn btn-secondary">← Back to Proposals</a>
        </div>

        <!-- Proposals Table -->
        <div class="proposals-table">
            <?php if (empty($proposals)): ?>
                <div class="empty-state">
                    <h3>No proposals yet</h3>
                    <p>Create your first proposal to get started</p>
                    <a href="<?= APP_URL ?>/admin/proposals/create" class="btn-primary" style="margin-top: 15px;">Create Proposal</a>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Valid Until</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proposals as $proposal): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($proposal['title']) ?></strong>
                                    <?php if (!empty($proposal['contact_name'])): ?>
                                        <br><small style="color: var(--text-muted);">From: <?= htmlspecialchars($proposal['contact_name']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($proposal['client_name'] ?? 'Not assigned') ?>
                                    <?php if (!empty($proposal['organization'])): ?>
                                        <br><small style="color: var(--text-muted);"><?= htmlspecialchars($proposal['organization']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="amount">₦<?= number_format($proposal['total_amount'], 2) ?></td>
                                <td><?= $proposal['valid_until'] ? date('M d, Y', strtotime($proposal['valid_until'])) : 'Not set' ?></td>
                                <td>
                                    <span class="status-badge status-<?= $proposal['status'] ?>">
                                        <?= ucfirst($proposal['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($proposal['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= APP_URL ?>/admin/proposals/view?id=<?= $proposal['id'] ?>" class="btn-sm btn-primary">View</a>
                                        <?php if ($proposal['status'] === 'draft'): ?>
                                            <a href="<?= APP_URL ?>/admin/proposals/edit?id=<?= $proposal['id'] ?>" class="btn-sm btn-secondary">Edit</a>
                                            <form method="POST" action="<?= APP_URL ?>/admin/proposals/send" style="display: inline;">
                                                <input type="hidden" name="id" value="<?= $proposal['id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <button type="submit" class="btn-sm btn-success" onclick="return confirm('Send this proposal to the client?')">Send</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
