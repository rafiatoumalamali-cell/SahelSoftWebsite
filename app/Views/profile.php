<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.profile-container {
    padding: 60px 0;
    margin-top: 80px;
    background: #f8fafc;
    min-height: calc(100vh - 160px);
}

.profile-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    align-items: start;
}

@media (max-width: 992px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

.profile-sidebar {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    text-align: center;
}

.profile-avatar-large {
    width: 100px;
    height: 100px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin: 0 auto 20px;
    box-shadow: 0 10px 20px rgba(14, 159, 110, 0.2);
}

.profile-info h2 {
    font-size: 1.5rem;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.profile-info p {
    color: var(--text-light);
    font-size: 0.95rem;
    margin-bottom: 25px;
}

.profile-nav {
    list-style: none;
    padding: 0;
    margin: 0;
    text-align: left;
}

.profile-nav li {
    margin-bottom: 10px;
}

.profile-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    border-radius: 10px;
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.profile-nav a:hover, .profile-nav a.active {
    background: rgba(14, 159, 110, 0.05);
    color: var(--primary-color);
}

.profile-main {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.form-section {
    margin-bottom: 40px;
}

.form-section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f1f5f9;
}

.profile-form .form-group {
    margin-bottom: 20px;
}

.profile-form label {
    display: block;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.profile-form input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.profile-form input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.success-alert {
    background: #d1fae5;
    color: #065f46;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.error-alert {
    background: #fee2e2;
    color: #991b1b;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}
</style>

<div class="profile-container">
    <div class="container">
        <div class="profile-grid">
            <!-- Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-avatar-large">
                    <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($user['full_name']) ?></h2>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>
                
                <ul class="profile-nav">
                    <li>
                        <a href="<?= APP_URL ?>/profile" class="active">
                            <i class="fas fa-user-cog"></i> <?= __('account_settings') ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= APP_URL ?>/client/dashboard">
                            <i class="fas fa-tasks"></i> <?= __('my_projects') ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= APP_URL ?>/messages">
                            <i class="fas fa-envelope"></i> <?= __('messages') ?>
                        </a>
                    </li>
                    <li style="margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <a href="<?= APP_URL ?>/logout" style="color: #ef4444;">
                            <i class="fas fa-sign-out-alt"></i> <?= __('logout') ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="profile-main">
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-alert animate-up">
                        <i class="fas fa-check-circle"></i>
                        <?= __('profile_updated_success') ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="error-alert animate-up">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= __('profile_update_error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/profile/update" method="POST" class="profile-form">
                    <!-- Basic Info -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-id-card" style="color: var(--primary-color);"></i>
                            <?= __('personal_info') ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= __('field_name') ?></label>
                                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= __('field_email') ?></label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= __('field_phone') ?></label>
                                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= __('field_org') ?></label>
                                    <input type="text" name="company_name" value="<?= htmlspecialchars($user['company_name'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-shield-alt" style="color: var(--primary-color);"></i>
                            <?= __('security') ?>
                        </h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?= __('new_password') ?></label>
                                    <input type="password" name="password" placeholder="<?= __('leave_blank_password') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn-premium" style="padding: 15px 40px; border: none; cursor: pointer;">
                            <i class="fas fa-save" style="margin-right: 8px;"></i> <?= __('save_changes') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
