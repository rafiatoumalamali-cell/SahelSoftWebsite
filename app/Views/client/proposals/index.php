<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.client-proposals-container {
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

.proposals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.proposal-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.proposal-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.proposal-card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 20px;
}

.proposal-card-title {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.proposal-card-meta {
    opacity: 0.9;
    font-size: 0.9rem;
}

.proposal-card-body {
    padding: 25px;
}

.proposal-amount {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.proposal-timeline {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    margin-bottom: 15px;
}

.proposal-description {
    color: var(--text-dark);
    line-height: 1.6;
    margin-bottom: 20px;
    max-height: 100px;
    overflow: hidden;
    position: relative;
}

.proposal-description::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 30px;
    background: linear-gradient(transparent, white);
}

.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 15px;
}

.status-draft {
    background: #f3f4f6;
    color: #6b7280;
}

.status-sent {
    background: #dbeafe;
    color: #1e40af;
    animation: pulse 2s infinite;
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

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.proposal-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.empty-state h3 {
    margin-bottom: 15px;
    color: var(--text-dark);
    font-size: 1.5rem;
}

.empty-state p {
    color: var(--text-muted);
    margin-bottom: 25px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.stat-item {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.85rem;
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

.deposit-info {
    background: #f0f9ff;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 0.85rem;
    color: var(--text-muted);
}

.deposit-amount {
    color: var(--primary-color);
    font-weight: 600;
}
</style>

<div class="client-proposals-container">
    <div class="container">
        <div class="proposals-header">
            <h1>My Proposals</h1>
            <p>View and respond to project proposals from SahelSoft</p>
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

        <?php if (!empty($proposals)): ?>
            <!-- Statistics -->
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-number"><?= count($proposals) ?></div>
                    <div class="stat-label">Total Proposals</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= count(array_filter($proposals, fn($p) => $p['status'] === 'sent')) ?></div>
                    <div class="stat-label">Pending Response</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= count(array_filter($proposals, fn($p) => $p['status'] === 'accepted')) ?></div>
                    <div class="stat-label">Accepted</div>
                </div>
            </div>

            <!-- Proposals Grid -->
            <div class="proposals-grid">
                <?php foreach ($proposals as $proposal): ?>
                    <div class="proposal-card">
                        <div class="proposal-card-header">
                            <div class="proposal-card-title">
                                <?= htmlspecialchars($proposal['title']) ?>
                            </div>
                            <div class="proposal-card-meta">
                                Proposal #<?= str_pad($proposal['id'], 4, '0', STR_PAD_LEFT) ?> • 
                                Sent <?= date('M j, Y', strtotime($proposal['created_at'])) ?>
                            </div>
                        </div>
                        
                        <div class="proposal-card-body">
                            <span class="status-badge status-<?= $proposal['status'] ?>">
                                <?= ucfirst($proposal['status']) ?>
                            </span>

                            <div class="proposal-amount">
                                ₦<?= number_format($proposal['total_amount'], 2) ?>
                            </div>

                            <?php if ($proposal['deposit_amount'] > 0): ?>
                                <div class="deposit-info">
                                    Deposit required: <span class="deposit-amount">₦<?= number_format($proposal['deposit_amount'], 2) ?></span>
                                    (<?= round(($proposal['deposit_amount'] / $proposal['total_amount']) * 100, 1) ?>%)
                                </div>
                            <?php endif; ?>

                            <div class="proposal-timeline">
                                📅 <?= $proposal['timeline_weeks'] ?> weeks timeline
                            </div>

                            <?php if (!empty($proposal['description'])): ?>
                                <div class="proposal-description">
                                    <?= htmlspecialchars(substr($proposal['description'], 0, 150)) ?>...
                                </div>
                            <?php endif; ?>

                            <div class="proposal-actions">
                                <a href="<?= APP_URL ?>/client/proposals/view?id=<?= $proposal['id'] ?>" class="btn btn-primary">
                                    View Details
                                </a>
                                
                                <?php if ($proposal['status'] === 'sent'): ?>
                                    <form method="POST" action="<?= APP_URL ?>/client/proposals/accept" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $proposal['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Accept this proposal? We will contact you to start the project.')">
                                            ✓ Accept
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="<?= APP_URL ?>/client/proposals/reject" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= $proposal['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this proposal? You can always contact us for changes.')">
                                            ✗ Reject
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>No proposals yet</h3>
                <p>We haven't sent you any project proposals yet. When we create a proposal for your project, it will appear here for your review and response.</p>
                <a href="<?= APP_URL ?>/contact" class="btn btn-primary">Submit Project Request</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
