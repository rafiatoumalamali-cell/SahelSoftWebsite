<?php include VIEW_PATH . '/layouts/header.php'; ?>

<section style="padding: 100px 0; text-align: center; margin-top: 80px; min-height: 80vh; background-color: #f8fafc;">
    <div class="container">
        <div style="background: white; padding: 60px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto;">
            <div style="font-size: 4rem; margin-bottom: 20px;">🚀</div>
            <h1 style="color: var(--primary-dark); margin-bottom: 15px;"><?= $title ?? __('coming_soon_title') ?></h1>
            <p style="color: var(--text-light); font-size: 1.2rem; max-width: 600px; margin: 0 auto 30px;">
                <?= __('coming_soon_desc') ?>
            </p>
            <a href="<?= APP_URL ?>/client/dashboard" class="btn-premium" style="display: inline-flex; text-decoration: none; border: none;">
                <?= __('back_to_dashboard') ?>
            </a>
        </div>
    </div>
</section>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
