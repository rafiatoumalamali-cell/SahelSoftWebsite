<?php include VIEW_PATH . '/layouts/header.php'; ?>



<style>

.messages-container {

    padding: 80px 0;

    background-color: #f8fafc;

}



.contact-card {

    background: var(--card-bg);

    border-radius: 20px;

    padding: 50px;

    box-shadow: 0 15px 40px rgba(0,0,0,0.06);

    text-align: center;

    max-width: 800px;

    margin: 0 auto;

}



.contact-grid {

    display: grid;

    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));

    gap: 30px;

    margin-top: 50px;

}



.contact-grid .contact-item {

    padding: 40px 25px;

    border-radius: 20px;

    background: var(--card-bg);

    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);

    text-decoration: none;

    border: 1px solid var(--border-color);

    display: flex;

    flex-direction: column;

    align-items: center;

    position: relative;

    overflow: hidden;

}



.contact-grid .contact-item:hover {

    transform: translateY(-8px);

    box-shadow: 0 20px 40px rgba(0,0,0,0.08);

}



.contact-grid .contact-icon-wrapper {

    width: 70px;

    height: 70px;

    border-radius: 50%;

    background: var(--bg-light);

    display: flex;

    align-items: center;

    justify-content: center;

    margin-bottom: 20px;

    transition: all 0.3s ease;

}



.contact-grid .contact-item:hover .contact-icon-wrapper {

    transform: scale(1.1) rotate(10deg);

}



.contact-grid .contact-icon {

    font-size: 2.2rem;

}



.whatsapp-icon { color: #25D366; }

.call-icon { color: var(--primary-color); }

.email-icon { color: #ea4335; }



.contact-grid .contact-hint {

    margin-top: 15px;

    font-size: 0.85rem;

    color: var(--text-light);

    font-weight: 500;

}



.back-link {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    color: var(--text-dark);

    text-decoration: none;

    font-weight: 600;

    margin-bottom: 40px;

    transition: color 0.3s ease;

}



.back-link:hover {

    color: var(--primary-color);

}

</style>



<div class="messages-container" style="margin-top: 80px;">

    <div class="container">

        <a href="<?= APP_URL ?>/client/project?id=<?= $projectId ?>" class="back-link">

            <span>←</span> <?= __('back_to_project') ?>

        </a>



        <div class="contact-card animate-up">

            <h1 style="color: var(--text-dark); margin-bottom: 15px;"><?= __('contact_team') ?></h1>

            <p style="color: var(--text-light); font-size: 1.1rem;"><?= __('reach_out_desc') ?></p>



            <div class="contact-grid">

                <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', getSetting('contact_phone'))) ?>" class="contact-item" style="border-top: 4px solid #25D366;">

                    <div class="contact-icon-wrapper">

                        <i class="fab fa-whatsapp contact-icon whatsapp-icon"></i>

                    </div>

                    <h3 style="margin-bottom: 10px; color: var(--text-dark);"><?= __('whatsapp') ?></h3>

                    <p style="margin: 0; color: #16a34a; font-weight: 700;">+233 503 836 061</p>

                    <span class="contact-hint"><?= __('message_us_now') ?></span>

                </a>



                <a href="tel:<?= htmlspecialchars(str_replace(' ', '', getSetting('contact_phone'))) ?>" class="contact-item" style="border-top: 4px solid var(--primary-color);">

                    <div class="contact-icon-wrapper">

                        <i class="fas fa-phone-alt contact-icon call-icon"></i>

                    </div>

                    <h3 style="margin-bottom: 10px; color: var(--text-dark);"><?= __('call_support') ?></h3>

                    <p style="margin: 0; color: var(--primary-color); font-weight: 700;">+233 503 836 061</p>

                    <span class="contact-hint"><?= __('available_wa_hours') ?></span>

                </a>



                <a href="mailto:sahelsoft38@gmail.com" class="contact-item" style="border-top: 4px solid #ea4335;">

                    <div class="contact-icon-wrapper">

                        <i class="far fa-envelope contact-icon email-icon"></i>

                    </div>

                    <h3 style="margin-bottom: 10px; color: var(--text-dark);"><?= __('email_us') ?></h3>

                    <p style="margin: 0; color: #b91c1c; font-weight: 700; word-break: break-all;">sahelsoft38@gmail.com</p>

                    <span class="contact-hint"><?= __('reply_within_24h') ?></span>

                </a>

            </div>



            <div style="margin-top: 60px; padding: 25px; background: #fdf2f2; border-radius: 15px; border: 1px dashed #fee2e2;">

                <p style="color: #991b1b; font-weight: 600; margin: 0;">

                    <i class="far fa-clock" style="margin-right: 8px;"></i> <?= __('typical_response') ?>

                </p>

            </div>

        </div>

    </div>

</div>



<?php include VIEW_PATH . '/layouts/footer.php'; ?>

