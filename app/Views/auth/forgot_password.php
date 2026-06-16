<?php include VIEW_PATH . '/layouts/header.php'; ?>

<section class="section" style="background-color: var(--bg-color); min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: var(--shadow-lg);">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: var(--primary-color); margin-bottom: 10px;"><?= __('forgot_password') ?></h1>
                <p><?= __('forgot_password_desc') ?? 'Enter your email to reset your password.' ?></p>
            </div>
            
            <?php if (isset($error)): ?>
                <div style="background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div style="background-color: #e8f5e9; color: #2e7d32; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/forgot-password" method="POST">
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px;"><?= __('form_email') ?></label>
                    <input type="email" id="email" name="email" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <button type="submit" class="btn" style="width: 100%;"><?= __('reset_password_btn') ?? 'Send Reset Link' ?></button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 0.9em;">
                <a href="<?= APP_URL ?>/login"><?= __('back_to_login') ?? 'Back to Login' ?></a>
            </p>
        </div>
    </div>
</section>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
