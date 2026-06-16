<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.payment-container {
    padding: 60px 0;
    background-color: #f8fafc;
}

.payment-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 40px;
    align-items: start;
}

@media (max-width: 992px) {
    .payment-grid {
        grid-template-columns: 1fr;
    }
}

.payment-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.payment-methods {
    display: grid;
    gap: 20px;
    margin: 30px 0;
}

.method-item {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.method-item:hover {
    border-color: var(--primary-color);
    background: #f0fdf4;
}

.method-icon {
    width: 50px;
    height: 50px;
    background: var(--bg-light);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: var(--primary-color);
}

.payment-form input, 
.payment-form select {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    margin-top: 8px;
    font-size: 1rem;
}

.payment-form label {
    font-weight: 600;
    color: var(--text-dark);
}

.form-group {
    margin-bottom: 25px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 30px;
    transition: color 0.3s ease;
}

.back-link:hover {
    color: var(--primary-color);
}
</style>

<div class="payment-container" style="margin-top: 80px;">
    <div class="container">
        <a href="<?= APP_URL ?>/client/project?id=<?= $project['id'] ?>" class="back-link">
            <span>←</span> <?= __('back_to_project') ?>
        </a>

        <div class="payment-grid">
            <!-- Left Column: Instructions -->
            <div class="payment-card">
                <h2 class="section-title" style="margin-bottom: 20px;">
                    <i class="fas fa-money-check-alt" style="color: var(--primary-color);"></i>
                    <?= __('payment_instructions') ?>
                </h2>
                <p style="color: var(--text-light); line-height: 1.7;">
                    <?= __('payment_reference_desc') ?>
                </p>

                <div class="payment-methods">
                    <div class="method-item">
                        <div class="method-icon">🏦</div>
                        <div>
                            <strong style="display: block; font-size: 1.1rem;"><?= __('bank_transfer_boa') ?></strong>
                            <p style="margin: 5px 0 0 0; color: var(--text-dark); font-family: monospace; font-size: 1.1rem; letter-spacing: 1px;"><?= htmlspecialchars(getSetting('bank_account', 'Contact support for details')) ?></p>
                        </div>
                    </div>
                    <div class="method-item">
                        <div class="method-icon">📱</div>
                        <div>
                            <strong style="display: block; font-size: 1.1rem;"><?= __('aman_mobile') ?></strong>
                            <p style="margin: 5px 0 0 0; color: var(--text-dark); font-family: monospace; font-size: 1.1rem;">+233 50 38 36 061</p>
                        </div>
                    </div>
                </div>

                <div style="background: #fffbeb; padding: 20px; border-radius: 12px; border-left: 4px solid var(--secondary-color);">
                    <p style="margin: 0; color: #854d0e; font-size: 0.95rem;">
                        <i class="fas fa-info-circle"></i> <?= __('payment_note') ?>
                    </p>
                </div>
            </div>

            <!-- Right Column: Form -->
            <div class="payment-card">
                <h3 style="margin-bottom: 30px;"><?= __('upload_receipt') ?></h3>
                
                <form action="<?= APP_URL ?>/client/project/submit-payment" method="POST" enctype="multipart/form-data" class="payment-form">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                    
                    <div class="form-group">
                        <label><?= __('payment_amount') ?></label>
                        <input type="number" name="amount" required step="0.01" placeholder="e.g. 500000">
                    </div>

                    <div class="form-group">
                        <label><?= __('payment_phase') ?></label>
                        <input type="text" name="description" required placeholder="e.g. Deposit / Design Phase">
                    </div>

                    <div class="form-group">
                        <label><?= __('photo_screenshot') ?></label>
                        <input type="file" name="receipt" required accept="image/*,.pdf" style="border: 2px dashed #e2e8f0; padding: 30px; text-align: center; width: 100%;">
                    </div>

                    <button type="submit" class="btn-premium" style="width: 100%; justify-content: center; padding: 15px; border: none; cursor: pointer;">
                        <?= __('submit_proof') ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
