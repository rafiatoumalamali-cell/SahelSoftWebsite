<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.proposal-view-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.proposal-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.proposal-content {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.proposal-section {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid var(--border-color);
}

.proposal-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.proposal-section h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.proposal-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-item {
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.info-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.info-value {
    color: var(--text-muted);
}

.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
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

.amount-display {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
    margin: 10px 0;
}

.timeline-display {
    font-size: 1.2rem;
    color: var(--text-dark);
    margin: 10px 0;
}

.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-block;
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

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
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

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.client-info {
    background: #f0f9ff;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
    margin-bottom: 20px;
}

.client-info h4 {
    margin: 0 0 10px 0;
    color: var(--text-dark);
}

.client-info p {
    margin: 5px 0;
    color: var(--text-muted);
}

.contact-origin {
    background: #fef3c7;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #f59e0b;
    margin-bottom: 20px;
}

.contact-origin h4 {
    margin: 0 0 10px 0;
    color: var(--text-dark);
}

.admin-notes {
    background: #f3f4f6;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #6b7280;
}

.empty-field {
    color: var(--text-muted);
    font-style: italic;
}
</style>

<div class="proposal-view-container">
    <div class="container">
        <div class="proposal-header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1><?= htmlspecialchars($proposal['title']) ?></h1>
                    <p style="margin-top: 10px; color: var(--text-muted);">
                        Proposal ID: #<?= str_pad($proposal['id'], 4, '0', STR_PAD_LEFT) ?>
                        • Created on <?= date('F j, Y', strtotime($proposal['created_at'])) ?>
                    </p>
                </div>
                <div>
                    <span class="status-badge status-<?= $proposal['status'] ?>">
                        <?= ucfirst($proposal['status']) ?>
                    </span>
                </div>
            </div>
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

        <div class="proposal-content">
            <!-- Client Information -->
            <div class="proposal-section">
                <h3>Client Information</h3>
                <?php if ($proposal['client_id']): ?>
                    <div class="client-info">
                        <h4><?= htmlspecialchars($proposal['client_name']) ?></h4>
                        <p><strong>Email:</strong> <?= htmlspecialchars($proposal['client_email'] ?? 'N/A') ?></p>
                        <?php if (!empty($proposal['organization'])): ?>
                            <p><strong>Organization:</strong> <?= htmlspecialchars($proposal['organization']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>No client assigned</strong> - Please assign a client before sending the proposal
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contact Origin (if applicable) -->
            <?php if ($proposal['project_id']): ?>
                <div class="proposal-section">
                    <h3>Original Contact Inquiry</h3>
                    <div class="contact-origin">
                        <h4><?= htmlspecialchars($proposal['contact_name']) ?></h4>
                        <?php if (!empty($proposal['organization'])): ?>
                            <p><strong>Organization:</strong> <?= htmlspecialchars($proposal['organization']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Project Details -->
            <div class="proposal-section">
                <h3>Project Details</h3>
                
                <div class="proposal-info-grid">
                    <div class="info-item">
                        <div class="info-label">Total Amount</div>
                        <div class="amount-display">₦<?= number_format($proposal['total_amount'], 2) ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Required Deposit</div>
                        <p><strong>Required Deposit:</strong> ₦<?= number_format($proposal['total_amount'] * 0.3, 2) ?></p>
                        <?php if ($proposal['total_amount'] > 0): ?>
                            <div style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">
                                <p><?= round(($proposal['total_amount'] * 0.3) / $proposal['total_amount'] * 100) ?>% of total</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Valid Until</div>
                        <p><strong>Valid Until:</strong> <?= date('F j, Y', strtotime($proposal['valid_until'])) ?></p>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <span class="status-badge status-<?= $proposal['status'] ?>">
                            <?= ucfirst($proposal['status']) ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($proposal['description'])): ?>
                    <div style="margin-top: 20px;">
                        <h4 style="margin-bottom: 10px;">Project Description</h4>
                        <div style="line-height: 1.6; color: var(--text-dark);">
                            <?= nl2br(htmlspecialchars($proposal['description'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Timeline Information -->
            <?php if ($proposal['status'] === 'sent'): ?>
                <div class="proposal-section">
                    <h3>Proposal Timeline</h3>
                    <div class="proposal-info-grid">
                        <div class="info-item">
                            <div class="info-label">Sent Date</div>
                            <div class="info-value">
                                <?= $proposal['sent_date'] ? date('F j, Y', strtotime($proposal['sent_date'])) : 'Not sent' ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Waiting for Response</div>
                            <div class="info-value">
                                <?php 
                                if ($proposal['sent_date']) {
                                    $daysWaiting = (new DateTime())->diff(new DateTime($proposal['sent_date']))->days;
                                    echo $daysWaiting . ' days';
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($proposal['status'], ['accepted', 'rejected']) && $proposal['response_date']): ?>
                <div class="proposal-section">
                    <h3>Client Response</h3>
                    <div class="proposal-info-grid">
                        <div class="info-item">
                            <div class="info-label">Response Date</div>
                            <div class="info-value">
                                <?= date('F j, Y', strtotime($proposal['response_date'])) ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Response</div>
                            <span class="status-badge status-<?= $proposal['status'] ?>">
                                <?= ucfirst($proposal['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Internal Notes -->
            <?php if (!empty($proposal['admin_notes'])): ?>
                <div class="proposal-section">
                    <h3>Internal Notes</h3>
                    <div class="admin-notes">
                        <?= nl2br(htmlspecialchars($proposal['admin_notes'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="<?= APP_URL ?>/admin/proposals" class="btn btn-secondary">← Back to Proposals</a>
            
            <?php if ($proposal['status'] === 'draft'): ?>
                <a href="<?= APP_URL ?>/admin/proposals/edit?id=<?= $proposal['id'] ?>" class="btn btn-primary">Edit Proposal</a>
                
                <?php if ($proposal['client_id']): ?>
                    <form method="POST" action="<?= APP_URL ?>/admin/proposals/send" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $proposal['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Send this proposal to the client? They will receive an email notification.')">
                            📤 Send to Client
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info" style="margin: 0; padding: 12px 20px;">
                        Assign a client before sending this proposal
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($proposal['status'] === 'accepted'): ?>
                <div class="alert alert-success" style="margin: 0; padding: 12px 20px;">
                    🎉 Proposal accepted! Consider converting this to a project.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
