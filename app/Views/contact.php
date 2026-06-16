<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Contact Page Styles */
.contact-hero {
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
    padding: 120px 0 80px;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-top: 70px;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(14, 159, 110, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 20px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: var(--text-light);
    max-width: 600px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px 100px;
}

@media (max-width: 1024px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

/* Contact Form */
.contact-form-container {
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 40px;
    border: 1px solid rgba(14, 159, 110, 0.1);
    position: relative;
    overflow: hidden;
}

.contact-form-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: var(--gradient-primary);
}

.form-header {
    margin-bottom: 30px;
    text-align: center;
}

.form-header h2 {
    color: var(--text-dark);
    font-size: 2rem;
    margin-bottom: 10px;
}

.form-header p {
    color: var(--text-light);
    font-size: 1.1rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.95rem;
}

.form-label i {
    color: var(--primary-color);
    font-size: 1rem;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: var(--font-main);
    font-size: 1rem;
    color: var(--text-color);
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.form-input:hover {
    border-color: var(--primary-light);
}

.form-input.error {
    border-color: #ef4444;
    background: #fef2f2;
}

.error-message {
    color: #ef4444;
    font-size: 0.85rem;
    margin-top: 5px;
    display: none;
}

.form-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: var(--font-main);
    font-size: 1rem;
    color: var(--text-color);
    transition: all 0.3s ease;
    resize: vertical;
    min-height: 120px;
    background: white;
}

.form-textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.form-select {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-family: var(--font-main);
    font-size: 1rem;
    color: var(--text-color);
    transition: all 0.3s ease;
    background: white;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
}

.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

/* Project Type Selection */
.project-types {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.project-type-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px 15px;
    background: var(--bg-light);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.project-type-btn:hover {
    background: white;
    border-color: var(--primary-light);
    transform: translateY(-2px);
}

.project-type-btn.active {
    background: white;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.project-type-icon {
    font-size: 2rem;
    color: var(--primary-color);
}

.project-type-text {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.9rem;
}

/* File Upload */
.file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    padding: 30px;
    text-align: center;
    margin-bottom: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--bg-light);
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(14, 159, 110, 0.05);
}

.file-upload-area.dragover {
    border-color: var(--primary-color);
    background: rgba(14, 159, 110, 0.1);
}

.upload-icon {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.upload-text {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.upload-hint {
    color: var(--text-light);
    font-size: 0.9rem;
}

.file-preview {
    display: none;
    margin-top: 15px;
}

.preview-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: white;
    border-radius: var(--border-radius);
    margin-bottom: 10px;
    border: 1px solid var(--border-color);
}

.preview-icon {
    font-size: 1.2rem;
    color: var(--primary-color);
}

.preview-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid var(--border-color);
}

.preview-info {
    flex: 1;
}

.preview-name {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.9rem;
}

.preview-size {
    font-size: 0.8rem;
    color: var(--text-light);
}

.remove-file {
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 5px;
    font-size: 1.2rem;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    padding: 18px;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.submit-btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Contact Info */
.contact-info-container {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: var(--border-radius-lg);
    padding: 40px;
    color: white;
    position: relative;
    overflow: hidden;
}

.contact-info-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
}

.info-header {
    margin-bottom: 40px;
    text-align: center;
    position: relative;
    z-index: 1;
}

.info-header h2 {
    color: white;
    font-size: 2rem;
    margin-bottom: 15px;
}

.info-header p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
}

.contact-details {
    display: flex;
    flex-direction: column;
    gap: 30px;
    margin-bottom: 40px;
    position: relative;
    z-index: 1;
}

.contact-details .contact-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
}

.contact-details .contact-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.contact-details .contact-content h4 {
    margin: 0 0 8px 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.contact-details .contact-content p {
    margin: 0;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.5;
}

.contact-details .contact-content a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-content a:hover {
    color: var(--accent-color);
}

/* Map Section */
.map-container {
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: var(--border-radius);
    overflow: hidden;
    position: relative;
    margin-top: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1;
}

.map-placeholder {
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
}

.map-placeholder i {
    font-size: 2.5rem;
    margin-bottom: 15px;
    display: block;
}

/* Social Links */
.social-links {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
    position: relative;
    z-index: 1;
}

.contact-info-container .social-link {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: var(--accent-color);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Success Message */
.success-message {
    display: none;
    background: #d1fae5;
    color: #065f46;
    padding: 20px;
    border-radius: var(--border-radius);
    margin-bottom: 25px;
    border-left: 4px solid #10b981;
    animation: slideIn 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .contact-hero {
        padding: 100px 0 60px;
        margin-top: 60px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .contact-form-container,
    .contact-info-container {
        padding: 30px;
    }
    
    .project-types {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .contact-form-container,
    .contact-info-container {
        padding: 25px;
    }
    
    .hero-title {
        font-size: 2rem;
    }
    
    .project-types {
        grid-template-columns: 1fr;
    }
    
    .form-header h2,
    .info-header h2 {
        font-size: 1.7rem;
    }
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Upload Progress */
.upload-progress {
    display: none;
    margin-top: 15px;
    padding: 15px;
    background: white;
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: var(--bg-light);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 10px;
}

.progress-fill {
    height: 100%;
    background: var(--gradient-primary);
    transition: width 0.3s ease;
    width: 0%;
}

.progress-text {
    font-size: 0.9rem;
    color: var(--text-dark);
    font-weight: 600;
}
</style>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?= __('contact_title') ?></h1>
            <p class="hero-subtitle">
                <?= __('contact_hero_subtitle') ?>
            </p>
        </div>
    </div>
</section>

<!-- Contact Grid -->
<div class="contact-grid">
    <!-- Contact Form -->
    <div class="contact-form-container">
        <div class="form-header">
            <h2><?= __('contact_title') ?></h2>
            <p><?= __('form_header_desc') ?></p>
        </div>

        <!-- Success Message (hidden by default) -->
        <div class="success-message" id="successMessage">
            <strong><?= __('success_msg_title') ?></strong>
            <p><?= __('success_msg_desc') ?></p>
        </div>

        <form action="<?= APP_URL ?>/contact/submit" method="POST" id="contactForm" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        <?= __('field_name') ?> *
                    </label>
                    <input type="text" name="name" class="form-input" required 
                           placeholder="<?= __('name_placeholder') ?>" id="nameInput" 
                           value="<?= isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : '' ?>">
                    <div class="error-message" id="nameError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-building"></i>
                        <?= __('field_org') ?>
                    </label>
                    <input type="text" name="organization" class="form-input" 
                           placeholder="<?= __('placeholder_org') ?>"
                           value="<?= isset($_SESSION['company_name']) ? htmlspecialchars($_SESSION['company_name']) : '' ?>">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i>
                        <?= __('field_email') ?> *
                    </label>
                    <input type="email" name="email" class="form-input" required 
                           placeholder="your@email.com" id="emailInput"
                           value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>">
                    <div class="error-message" id="emailError"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i>
                        <?= __('field_phone') ?>
                    </label>
                    <input type="tel" name="phone" class="form-input" 
                           placeholder="<?= __('placeholder_phone') ?>"
                           value="<?= isset($_SESSION['phone']) ? htmlspecialchars($_SESSION['phone']) : '' ?>">
                </div>
            </div>

            <!-- Project Type -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-project-diagram"></i>
                    <?= __('project_type_label') ?>
                </label>
                <div class="project-types">
                    <button type="button" class="project-type-btn active" data-type="web">
                        <span class="project-type-icon">🌐</span>
                        <span class="project-type-text"><?= __('proj_type_web') ?></span>
                    </button>
                    <button type="button" class="project-type-btn" data-type="ecommerce">
                        <span class="project-type-icon">🛒</span>
                        <span class="project-type-text"><?= __('proj_type_ecommerce') ?></span>
                    </button>
                    <button type="button" class="project-type-btn" data-type="mobile">
                        <span class="project-type-icon">📱</span>
                        <span class="project-type-text"><?= __('proj_type_mobile') ?></span>
                    </button>
                    <button type="button" class="project-type-btn" data-type="custom">
                        <span class="project-type-icon">🔧</span>
                        <span class="project-type-text"><?= __('proj_type_custom') ?></span>
                    </button>
                </div>
                <input type="hidden" name="project_type" id="projectType" value="web">
            </div>

            <!-- Budget -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-money-bill-wave"></i>
                    <?= __('field_budget') ?>
                </label>
                <select name="budget" class="form-select" id="budgetSelect">
                    <option value=""><?= __('budget_select_default') ?></option>
                    <option value="low"><?= __('price_small') ?> (100K - 500K CFA)</option>
                    <option value="medium"><?= __('price_medium') ?> (500K - 2M CFA)</option>
                    <option value="high"><?= __('price_enterprise') ?> (2M+ CFA)</option>
                    <option value="consultation"><?= __('budget_need_quote') ?></option>
                </select>
            </div>

            <!-- Project Description -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-left"></i>
                    <?= __('field_desc') ?> *
                </label>
                <textarea name="description" class="form-textarea" required 
                          placeholder="<?= __('placeholder_desc') ?>" 
                          id="descriptionInput"></textarea>
                <div class="error-message" id="descriptionError"></div>
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-paperclip"></i>
                    <?= __('attach_files') ?>
                </label>
                <div class="file-upload-area" id="fileUploadArea" role="button" tabindex="0" aria-label="Upload files">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">
                        <strong><?= __('drag_drop_text') ?></strong> <?= __('click_browse') ?>
                    </div>
                    <div class="upload-hint">
                        <?= __('file_supports') ?>
                    </div>
                </div>
                <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar" style="display: none;" id="fileInput">
                <div class="file-preview" id="filePreview"></div>
                <div class="upload-progress" id="uploadProgress">
                    <div class="progress-text">Uploading files...</div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-paper-plane"></i>
                <?= __('btn_submit') ?>
            </button>
        </form>
    </div>

    <!-- Contact Information -->
    <div class="contact-info-container">
        <div class="info-header">
            <h2><?= __('get_in_touch') ?></h2>
            <p><?= __('get_in_touch_desc') ?></p>
        </div>

        <div class="contact-details">
            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="contact-content">
                    <h4><?= __('email_us') ?></h4>
                    <p>
                        <a href="mailto:<?= htmlspecialchars(getSetting('contact_email', 'sahelsoft38@gmail.com')) ?>"><?= htmlspecialchars(getSetting('contact_email', 'sahelsoft38@gmail.com')) ?></a><br>
                        <small style="color: rgba(255, 255, 255, 0.7);"><?= __('response_time') ?></small>
                    </p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="contact-content">
                    <h4><?= __('call_us') ?></h4>
                    <p>
                        <a href="tel:<?= htmlspecialchars(str_replace(' ', '', getSetting('contact_phone'))) ?>"><?= htmlspecialchars(getSetting('contact_phone')) ?></a><br>
                        <small style="color: rgba(255, 255, 255, 0.7);"><?= __('office_hours') ?></small>
                    </p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="contact-content">
                    <h4><?= __('visit_us') ?></h4>
                    <p>
                        Niamey, Niger<br>
                        <small style="color: rgba(255, 255, 255, 0.7);"><?= __('remote_avail') ?></small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Map Placeholder -->
        <div class="map-container">
            <div class="map-placeholder">
                <i class="fas fa-map-marked-alt"></i>
                <div>Niamey, Niger 🇳🇪</div>
                <small style="font-size: 0.9rem; opacity: 0.7;"><?= __('west_africa') ?></small>
            </div>
        </div>

        <!-- Social Links -->
        <div class="social-links">
            <a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', getSetting('contact_phone'))) ?>" class="social-link" aria-label="WhatsApp" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="https://www.linkedin.com/in/malam-ali-rafiatou/" class="social-link" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="https://www.facebook.com/share/1AQymiEpK7/" class="social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://vm.tiktok.com/ZMHETDuofpHUU-u1Wva/" class="social-link" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-tiktok"></i>
            </a>
            <a href="https://www.twitter.com" class="social-link" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-twitter"></i>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const contactForm = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const successMessage = document.getElementById('successMessage');
    
    // Project type selection
    const projectTypeBtns = document.querySelectorAll('.project-type-btn');
    const projectTypeInput = document.getElementById('projectType');
    
    projectTypeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            projectTypeBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Update hidden input value
            projectTypeInput.value = this.getAttribute('data-type');
        });
    });
    
    // File upload functionality
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    
    // Detailed click handling
    if (fileUploadArea && fileInput) {
        fileUploadArea.onclick = function(e) {
            e.preventDefault();
            fileInput.click();
        };
        
        // Keyboard accessibility
        fileUploadArea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.click();
            }
        });

        // Extra fallback: click on any child of the area should also trigger it
        fileUploadArea.querySelectorAll('*').forEach(child => {
            child.onclick = function(e) {
                e.stopPropagation();
                fileInput.click();
            };
        });
    }
    
    // Drag and drop
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', () => {
        fileUploadArea.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelection();
        }
    });
    
    // File input change
    fileInput.addEventListener('change', function() {
        handleFileSelection();
    });
    
    function handleFileSelection() {
        if (!fileInput.files) return;
        
        filePreview.innerHTML = '';
        const files = Array.from(fileInput.files);
        
        if (files.length > 0) {
            filePreview.style.display = 'block';
        } else {
            filePreview.style.display = 'none';
        }
        
        files.forEach((file, index) => {
            // Validate file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                showNotification(`File "${file.name}" is too large. Maximum size is 10MB.`, 'error');
                return;
            }
            
            // Validate file type
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                                  'text/plain', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                                  'application/zip', 'application/x-rar-compressed', 'application/x-zip-compressed'];
            const allowedExtensions = ['.pdf', '.doc', '.docx', '.txt', '.jpg', '.jpeg', '.png', '.gif', '.zip', '.rar'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                showNotification(`File "${file.name}" has an unsupported format. Allowed: PDF, DOC, DOCX, TXT, Images, ZIP, RAR`, 'error');
                return;
            }
            
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            const icon = getFileIcon(file.name);
            const size = formatFileSize(file.size);
            
            // Check if file is an image for preview
            const isImage = file.type.startsWith('image/');
            
            // Escape filename for safe use in HTML
            const safeName = file.name.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            
            if (isImage) {
                // Create image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = safeName;
                    img.className = 'preview-thumbnail';
                    
                    const infoDiv = document.createElement('div');
                    infoDiv.className = 'preview-info';
                    
                    const nameDiv = document.createElement('div');
                    nameDiv.className = 'preview-name';
                    nameDiv.textContent = file.name;
                    
                    const sizeDiv = document.createElement('div');
                    sizeDiv.className = 'preview-size';
                    sizeDiv.textContent = size;
                    
                    infoDiv.appendChild(nameDiv);
                    infoDiv.appendChild(sizeDiv);
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-file';
                    removeBtn.setAttribute('data-index', index);
                    removeBtn.innerHTML = '&times;';
                    removeBtn.onclick = function(e) {
                        e.preventDefault();
                        const idx = parseInt(this.getAttribute('data-index'));
                        removeFile(idx);
                    };
                    
                    previewItem.appendChild(img);
                    previewItem.appendChild(infoDiv);
                    previewItem.appendChild(removeBtn);
                };
                reader.readAsDataURL(file);
            } else {
                // Use icon for non-image files
                const iconDiv = document.createElement('div');
                iconDiv.className = 'preview-icon';
                iconDiv.textContent = icon;
                
                const infoDiv = document.createElement('div');
                infoDiv.className = 'preview-info';
                
                const nameDiv = document.createElement('div');
                nameDiv.className = 'preview-name';
                nameDiv.textContent = file.name;
                
                const sizeDiv = document.createElement('div');
                sizeDiv.className = 'preview-size';
                sizeDiv.textContent = size;
                
                infoDiv.appendChild(nameDiv);
                infoDiv.appendChild(sizeDiv);
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-file';
                removeBtn.setAttribute('data-index', index);
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = function(e) {
                    e.preventDefault();
                    const idx = parseInt(this.getAttribute('data-index'));
                    removeFile(idx);
                };
                
                previewItem.appendChild(iconDiv);
                previewItem.appendChild(infoDiv);
                previewItem.appendChild(removeBtn);
            }
            
            filePreview.appendChild(previewItem);
        });
    }
    
    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = {
            pdf: '📄',
            doc: '📝',
            docx: '📝',
            jpg: '🖼️',
            jpeg: '🖼️',
            png: '🖼️',
            gif: '🖼️',
            zip: '📦',
            rar: '📦'
        };
        return icons[ext] || '📁';
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    function removeFile(index) {
        const dt = new DataTransfer();
        const { files } = fileInput;
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (index !== i) dt.items.add(file);
        }
        
        fileInput.files = dt.files;
        handleFileSelection();
    }
    
    // Form validation
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= htmlspecialchars(__('msg_sending'), ENT_QUOTES) ?>';
        
        // Validate form
        if (!validateForm()) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= htmlspecialchars(__('btn_submit'), ENT_QUOTES) ?>';
            return;
        }
        
        // Create FormData object to handle file uploads
        const formData = new FormData(contactForm);
        
        // Show progress if files are attached
        const uploadProgress = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const hasFiles = fileInput.files && fileInput.files.length > 0;
        
        if (hasFiles) {
            uploadProgress.style.display = 'block';
            progressFill.style.width = '0%';
        }
        
        // Use XMLHttpRequest for progress tracking
        const xhr = new XMLHttpRequest();
        
        // Debug logging
        console.log('=== Contact Form Submission Debug ===');
        console.log('Form action:', '<?= APP_URL ?>/contact/submit');
        console.log('Has files:', hasFiles);
        if (hasFiles) {
            console.log('File count:', fileInput.files.length);
            for (let i = 0; i < fileInput.files.length; i++) {
                console.log(`File ${i + 1}:`, fileInput.files[i].name, fileInput.files[i].size, 'bytes');
            }
        }
        console.log('FormData entries:');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(pair[0], ':', pair[1].name);
            } else {
                console.log(pair[0], ':', pair[1]);
            }
        }
        
        // Track upload progress
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable && hasFiles) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressFill.style.width = percentComplete + '%';
                console.log('Upload progress:', percentComplete.toFixed(2) + '%');
            }
        });
        
        // Handle completion
        xhr.addEventListener('load', () => {
            console.log('XHR Status:', xhr.status);
            console.log('XHR Response:', xhr.responseText);
            
            if (hasFiles) {
                uploadProgress.style.display = 'none';
            }
            
            try {
                const result = JSON.parse(xhr.responseText);
                console.log('Parsed result:', result);
                
                if (result.success) {
                    // Show success message
                    successMessage.style.display = 'block';
                    contactForm.reset();
                    
                    // Reset file preview
                    filePreview.innerHTML = '';
                    filePreview.style.display = 'none';
                    fileInput.value = '';
                    
                    // Reset project type to default
                    projectTypeBtns.forEach(b => b.classList.remove('active'));
                    projectTypeBtns[0].classList.add('active');
                    projectTypeInput.value = 'web';
                    
                    // Scroll to success message
                    successMessage.scrollIntoView({ behavior: 'smooth' });
                    
                    // Show notification
                    showNotification(result.message || '<?= htmlspecialchars(__('msg_sent_success'), ENT_QUOTES) ?>', 'success');
                    
                    // Hide success message after 10 seconds
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 10000);
                } else {
                    // Show error notification
                    showNotification(result.message || 'Failed to send message. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Parse Error:', error);
                showNotification('An error occurred processing the response.', 'error');
            }
            
            // Reset submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= htmlspecialchars(__('btn_submit'), ENT_QUOTES) ?>';
        });
        
        // Handle errors
        xhr.addEventListener('error', () => {
            console.error('XHR Error occurred');
            if (hasFiles) {
                uploadProgress.style.display = 'none';
            }
            showNotification('Network error. Please check your connection and try again.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= htmlspecialchars(__('btn_submit'), ENT_QUOTES) ?>';
        });
        
        xhr.addEventListener('abort', () => {
            console.error('XHR Aborted');
        });
        
        xhr.addEventListener('timeout', () => {
            console.error('XHR Timeout');
        });
        
        // Send request
        console.log('Sending XHR request...');
        xhr.open('POST', '<?= APP_URL ?>/contact/submit', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(formData);
        console.log('XHR request sent');
    });
    
    function validateForm() {
        let isValid = true;
        
        // Validate name
        const nameInput = document.getElementById('nameInput');
        const nameError = document.getElementById('nameError');
        if (!nameInput.value.trim()) {
            nameInput.classList.add('error');
            nameError.textContent = '<?= htmlspecialchars(__('error_enter_name'), ENT_QUOTES) ?>';
            nameError.style.display = 'block';
            isValid = false;
        } else {
            nameInput.classList.remove('error');
            nameError.style.display = 'none';
        }
        
        // Validate email
        const emailInput = document.getElementById('emailInput');
        const emailError = document.getElementById('emailError');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailInput.value.trim()) {
            emailInput.classList.add('error');
            emailError.textContent = '<?= htmlspecialchars(__('error_enter_email'), ENT_QUOTES) ?>';
            emailError.style.display = 'block';
            isValid = false;
        } else if (!emailRegex.test(emailInput.value)) {
            emailInput.classList.add('error');
            emailError.textContent = '<?= htmlspecialchars(__('error_valid_email'), ENT_QUOTES) ?>';
            emailError.style.display = 'block';
            isValid = false;
        } else {
            emailInput.classList.remove('error');
            emailError.style.display = 'none';
        }
        
        // Validate description
        const descriptionInput = document.getElementById('descriptionInput');
        const descriptionError = document.getElementById('descriptionError');
        if (!descriptionInput.value.trim()) {
            descriptionInput.classList.add('error');
            descriptionError.textContent = '<?= htmlspecialchars(__('error_enter_desc'), ENT_QUOTES) ?>';
            descriptionError.style.display = 'block';
            isValid = false;
        } else if (descriptionInput.value.trim().length < 20) {
            descriptionInput.classList.add('error');
            descriptionError.textContent = '<?= htmlspecialchars(__('error_desc_len'), ENT_QUOTES) ?>';
            descriptionError.style.display = 'block';
            isValid = false;
        } else {
            descriptionInput.classList.remove('error');
            descriptionError.style.display = 'none';
        }
        
        return isValid;
    }
    
    // Clear errors on input
    const inputs = document.querySelectorAll('.form-input, .form-textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error');
            const errorDiv = this.parentElement.querySelector('.error-message');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        });
    });
    
    // Budget select styling
    const budgetSelect = document.getElementById('budgetSelect');
    budgetSelect.addEventListener('change', function() {
        if (this.value) {
            this.style.color = 'var(--text-dark)';
        } else {
            this.style.color = 'var(--text-light)';
        }
    });
    
    // Phone number formatting (optional)
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = '+227 ' + value;
            }
            e.target.value = value.substring(0, 15);
        });
    }
    
    // Notification system
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Determine background color
        let bgColor = '#3b82f6'; // default blue
        if (type === 'success') {
            bgColor = 'var(--primary-color)';
        } else if (type === 'error') {
            bgColor = '#ef4444';
        }
        
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
    
    // Add CSS for animations
    const contactAnimationStyle = document.createElement('style');
    contactAnimationStyle.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .fa-spin {
            animation: fa-spin 1s linear infinite;
        }
        
        @keyframes fa-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(contactAnimationStyle);
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>