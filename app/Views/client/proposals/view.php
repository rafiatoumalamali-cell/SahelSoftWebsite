<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.proposal-detail-container {
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
    margin-bottom: 40px;
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
    margin-bottom: 20px;
    font-size: 1.4rem;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.pricing-card {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    border: 2px solid var(--border-color);
    transition: transform 0.3s, border-color 0.3s;
}

.pricing-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-color);
}

.pricing-card.primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border-color: var(--primary-color);
}

.pricing-amount {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 15px 0;
}

.pricing-card.primary .pricing-amount {
    color: white;
}

.pricing-label {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.pricing-description {
    font-size: 0.9rem;
    opacity: 0.8;
    line-height: 1.5;
}

.timeline-card {
    background: #f0f9ff;
    padding: 25px;
    border-radius: 12px;
    border-left: 5px solid #3b82f6;
    margin-bottom: 25px;
}

.timeline-card h4 {
    color: #1e40af;
    margin-bottom: 15px;
    font-size: 1.2rem;
}

.timeline-details {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.timeline-weeks {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--primary-color);
}

.timeline-months {
    color: var(--text-muted);
    font-size: 1rem;
}

.description-card {
    background: #f8fafc;
    padding: 30px;
    border-radius: 12px;
    border-left: 5px solid var(--primary-color);
    line-height: 1.7;
    color: var(--text-dark);
}

.status-badge {
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 20px;
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

.action-buttons {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 30px;
}

.btn {
    padding: 15px 30px;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-block;
    text-align: center;
    min-width: 150px;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-2px);
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.alert {
    padding: 20px 25px;
    border-radius: 10px;
    margin-bottom: 25px;
    font-size: 1rem;
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

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.proposal-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.proposal-id {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.deposit-highlight {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 8px;
}

.next-steps {
    background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    padding: 25px;
    border-radius: 12px;
    border-left: 5px solid #6366f1;
}

.next-steps h4 {
    color: #4338ca;
    margin-bottom: 15px;
    font-size: 1.2rem;
}

.next-steps ul {
    margin: 0;
    padding-left: 20px;
    color: var(--text-dark);
}

.next-steps li {
    margin-bottom: 10px;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .pricing-grid {
        grid-template-columns: 1fr;
    }
    
    .timeline-details {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<div class="proposal-detail-container">
    <div class="container">
        <div class="proposal-header">
            <div class="proposal-meta">
                <div>
                    <h1><?= htmlspecialchars($proposal['title']) ?></h1>
                    <p style="margin-top: 10px; color: var(--text-muted);">
                        Proposal #<?= str_pad($proposal['id'], 4, '0', STR_PAD_LEFT) ?> • 
                        Created <?= date('F j, Y', strtotime($proposal['created_at'])) ?>
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
            <!-- Pricing Section -->
            <div class="proposal-section">
                <h3>💰 Investment Details</h3>
                
                <div class="pricing-grid">
                    <div class="pricing-card primary">
                        <div class="pricing-label">Total Project Cost</div>
                        <div class="pricing-amount">₦<?= number_format($proposal['total_amount'], 2) ?></div>
                        <div class="pricing-description">Complete project delivery including all features and support</div>
                    </div>
                    
                    <?php if ($proposal['deposit_amount'] > 0): ?>
                        <div class="pricing-card">
                            <div class="pricing-label">
                                Required Deposit
                                <span class="deposit-highlight">TO START</span>
                            </div>
                            <div class="pricing-amount">₦<?= number_format($proposal['deposit_amount'], 2) ?></div>
                            <div class="pricing-description">
                                <?= round(($proposal['deposit_amount'] / $proposal['total_amount']) * 100, 1) ?>% of total • 
                                Due before project commencement
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="proposal-section">
                <h3>📅 Project Timeline</h3>
                
                <div class="timeline-card">
                    <h4>Estimated Duration</h4>
                    <div class="timeline-details">
                        <div class="timeline-weeks"><?= $proposal['timeline_weeks'] ?> weeks</div>
                        <div class="timeline-months">(approximately <?= round($proposal['timeline_weeks'] / 4.33, 1) ?> months)</div>
                    </div>
                </div>
            </div>

            <!-- Project Description -->
            <?php if (!empty($proposal['description'])): ?>
                <div class="proposal-section">
                    <h3>📋 Project Description</h3>
                    
                    <div class="description-card">
                        <?= nl2br(htmlspecialchars($proposal['description'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Status Information -->
            <?php if ($proposal['status'] === 'sent'): ?>
                <div class="proposal-section">
                    <h3>📬 Proposal Status</h3>
                    
                    <div class="alert alert-info">
                        <strong>Waiting for your response</strong><br>
                        This proposal was sent to you on <?= date('F j, Y', strtotime($proposal['sent_date'])) ?>. 
                        Please review the details above and decide whether to accept or reject this proposal.
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($proposal['status'] === 'accepted'): ?>
                <div class="proposal-section">
                    <h3>🎉 Proposal Accepted!</h3>
                    
                    <div class="alert alert-success">
                        <strong>Thank you for accepting this proposal!</strong><br>
                        We received your acceptance on <?= date('F j, Y', strtotime($proposal['response_date'])) ?>. 
                        Our team will contact you shortly to discuss the next steps and project kickoff.
                    </div>
                    
                    <div class="next-steps">
                        <h4>What happens next?</h4>
                        <ul>
                            <li>Our team will contact you within 24-48 hours</li>
                            <li>We'll schedule a kickoff meeting to discuss project details</li>
                            <li>Deposit payment will be processed before work begins</li>
                            <li>You'll receive regular progress updates throughout the project</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($proposal['status'] === 'rejected'): ?>
                <div class="proposal-section">
                    <h3>❌ Proposal Rejected</h3>
                    
                    <div class="alert alert-warning">
                        <strong>Proposal rejected on <?= date('F j, Y', strtotime($proposal['response_date'])) ?></strong><br>
                        We understand this proposal may not meet your current needs. Please feel free to contact us to discuss modifications or alternative solutions.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <?php if ($proposal['status'] === 'sent'): ?>
            <div class="action-buttons">
                <form method="POST" action="<?= APP_URL ?>/client/proposals/accept">
                    <input type="hidden" name="id" value="<?= $proposal['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you ready to accept this proposal? We will contact you to start the project.')">
                        ✅ Accept Proposal
                    </button>
                </form>
                
                <form method="POST" action="<?= APP_URL ?>/client/proposals/reject">
                    <input type="hidden" name="id" value="<?= $proposal['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this proposal? You can always contact us to discuss changes.')">
                        ❌ Reject Proposal
                    </button>
                </form>
                
                <a href="<?= APP_URL ?>/client/proposals" class="btn btn-secondary">← Back to Proposals</a>
            </div>
        <?php else: ?>
            <div class="action-buttons">
                <a href="<?= APP_URL ?>/client/proposals" class="btn btn-secondary">← Back to Proposals</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
