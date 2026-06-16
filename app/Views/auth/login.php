<?php include VIEW_PATH . '/layouts/header.php'; ?>

<section class="section" style="background-color: var(--bg-color); min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div style="max-width: 400px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: var(--shadow-lg);">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: var(--primary-color); margin-bottom: 10px;"><?= __('login_title') ?></h1>
                <p><?= __('login_welcome') ?></p>
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

            <form action="<?= APP_URL ?>/login" method="POST">
                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px;"><?= __('form_email') ?></label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($_POST['email'] ?? $_COOKIE['remember_email'] ?? '') ?>" 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 8px;"><?= __('password') ?></label>
                    <input type="password" id="password" name="password" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" id="remember" name="remember" value="1" 
                               <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>
                               style="margin-right: 8px;">
                        <span style="font-size: 0.9em;"><?= __('remember_me') ?></span>
                    </label>
                    
                    <a href="<?= APP_URL ?>/forgot-password" style="font-size: 0.9em; color: var(--primary-color); text-decoration: none;">
                        <?= __('forgot_password') ?>
                    </a>
                </div>

                <button type="submit" class="btn" style="width: 100%;"><?= __('login_btn') ?></button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; font-size: 0.9em;">
                <?= __('no_account') ?> <a href="<?= APP_URL ?>/register"><?= __('register_here') ?></a>
            </p>
        </div>
    </div>
</section>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>