<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.help-hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    padding: 120px 0 80px;
    text-align: center;
    color: white;
    margin-top: 70px;
}

.help-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 60px 20px;
}

.faq-section {
    margin-top: 40px;
}

.faq-item {
    background: white;
    border-radius: 15px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item:hover {
    border-color: var(--primary-color);
}

.faq-question {
    padding: 25px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1.1rem;
}

.faq-answer {
    padding: 0 25px 25px;
    color: var(--text-light);
    line-height: 1.7;
    display: none;
    border-top: 1px solid #f1f5f9;
    padding-top: 20px;
}

.faq-item.active .faq-answer {
    display: block;
    animation: fadeIn 0.4s ease;
}

.faq-toggle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: var(--primary-color);
}

.faq-item.active .faq-toggle {
    transform: rotate(180deg);
    background: var(--primary-color);
    color: white;
}

.contact-support {
    text-align: center;
    margin-top: 80px;
    padding: 50px;
    background: #fdf2f2;
    border-radius: 30px;
    border: 1px dashed #fee2e2;
}

.support-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 30px;
    flex-wrap: wrap;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<section class="help-hero">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold mb-3"><?= __('help_center_title') ?></h1>
        <p class="lead" style="opacity: 0.9;"><?= __('help_center_subtitle') ?></p>
    </div>
</section>

<div class="container help-container">
    <h2 class="text-center mb-5 font-weight-bold"><?= __('frequently_asked_questions') ?></h2>
    
    <div class="faq-section">
        <!-- FAQ 1 -->
        <div class="faq-item">
            <div class="faq-question">
                <span><?= __('faq_1_q') ?></span>
                <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="faq-answer">
                <p><?= __('faq_1_a') ?></p>
            </div>
        </div>

        <!-- FAQ 2 -->
        <div class="faq-item">
            <div class="faq-question">
                <span><?= __('faq_2_q') ?></span>
                <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="faq-answer">
                <p><?= __('faq_2_a') ?></p>
            </div>
        </div>

        <!-- FAQ 3 -->
        <div class="faq-item">
            <div class="faq-question">
                <span><?= __('faq_3_q') ?></span>
                <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="faq-answer">
                <p><?= __('faq_3_a') ?></p>
            </div>
        </div>

        <!-- FAQ 4 -->
        <div class="faq-item">
            <div class="faq-question">
                <span><?= __('faq_4_q') ?></span>
                <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="faq-answer">
                <p><?= __('faq_4_a') ?></p>
            </div>
        </div>

        <!-- FAQ 5 -->
        <div class="faq-item">
            <div class="faq-question">
                <span><?= __('faq_5_q') ?></span>
                <div class="faq-toggle"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="faq-answer">
                <p><?= __('faq_5_a') ?></p>
            </div>
        </div>
    </div>

    <div class="contact-support animate-up">
        <h3 class="font-weight-bold"><?= __('still_have_questions') ?></h3>
        <p><?= __('contact_support_text') ?></p>
        <div class="support-buttons">
            <a href="<?= APP_URL ?>/contact" class="btn-premium">
                <i class="fas fa-envelope"></i> <?= __('contact_us') ?>
            </a>
            <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', getSetting('contact_phone'))) ?>" class="btn-secondary">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="<?= APP_URL ?>" class="btn-link" style="color: var(--primary-color); font-weight: 600;">
            <i class="fas fa-arrow-left"></i> <?= __('back_to_home') ?>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            // Close other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });
            // Toggle current item
            item.classList.toggle('active');
        });
    });
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
