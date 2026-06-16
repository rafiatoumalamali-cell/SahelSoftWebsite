<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.auth-container {
    padding: 120px 0 80px;
    background: #f8fafc;
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
}

.auth-card {
    max-width: 450px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-header h1 {
    font-size: 1.75rem;
    color: var(--text-dark);
    margin-bottom: 10px;
}

.auth-header p {
    color: var(--text-light);
}

.auth-form .form-group {
    margin-bottom: 20px;
}

.auth-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--text-dark);
}

.auth-form input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.auth-form input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.btn-auth {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    border: none;
    background: var(--gradient-primary);
    color: white;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-auth:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(14, 159, 110, 0.3);
}
</style>

<div class="auth-container">
    <div class="container">
        <div class="auth-card animate-up">
            <div class="auth-header">
                <h1><?= __('reset_password_title') ?></h1>
                <p><?= __('reset_password_desc_form') ?></p>
            </div>

            <?php if (isset($error)): ?>
                <div style="background-color: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/reset-password" method="POST" class="auth-form">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label><?= __('field_new_password') ?></label>
                    <input type="password" name="password" required minlength="6" autofocus>
                </div>

                <button type="submit" class="btn-auth">
                    <?= __('btn_reset_password_confirm') ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
