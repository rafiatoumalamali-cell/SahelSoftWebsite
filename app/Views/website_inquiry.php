<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Enhanced Contact Page Styles */
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

.form-container-full {
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    padding: 40px;
    border: 1px solid rgba(14, 159, 110, 0.1);
    position: relative;
    overflow: hidden;
    max-width: 1000px;
    margin: 40px auto 100px;
}

.form-container-full::before {
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

.form-header h1 {
    color: var(--text-dark);
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.form-header p {
    color: var(--text-light);
    font-size: 1.1rem;
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 2px solid var(--bg-light);
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title i {
    font-size: 1.6rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-grid.full {
    grid-template-columns: 1fr;
}

.form-grid.three-cols {
    grid-template-columns: repeat(3, 1fr);
}

@media (max-width: 768px) {
    .form-grid,
    .form-grid.three-cols {
        grid-template-columns: 1fr;
    }
}

.form-group {
    display: flex;
    flex-direction: column;
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

.form-label.required::after {
    content: ' *';
    color: #ef4444;
    font-weight: 700;
}

.form-label i {
    color: var(--primary-color);
    font-size: 1rem;
}

.form-input,
.form-textarea,
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
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
    padding-right: 40px;
}

/* Checkbox and Radio Styles */
.checkbox-group,
.radio-group {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.checkbox-item,
.radio-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.checkbox-item:hover,
.radio-item:hover {
    background: var(--bg-light);
}

.checkbox-item input[type="checkbox"],
.radio-item input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--primary-color);
}

.checkbox-item label,
.radio-item label {
    cursor: pointer;
    flex: 1;
    margin-bottom: 0;
}

/* Feature Selection */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-bottom: 15px;
}

.feature-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.3s ease;
}

.feature-checkbox:hover {
    border-color: var(--primary-light);
    background: var(--bg-light);
}

.feature-checkbox input[type="checkbox"]:checked + label {
    color: var(--primary-color);
    font-weight: 600;
}

.feature-checkbox input[type="checkbox"]:checked {
    accent-color: var(--primary-color);
}

.feature-checkbox label {
    margin-bottom: 0;
    font-size: 0.95rem;
}

/* Color Input */
.color-input-group {
    display: flex;
    gap: 10px;
    align-items: center;
}

.color-input-group input[type="color"] {
    width: 50px;
    height: 50px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    cursor: pointer;
}

.color-input-group input[type="text"] {
    flex: 1;
}

/* URL Input List */
.url-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.url-item {
    display: flex;
    gap: 10px;
}

.url-item input {
    flex: 1;
}

.url-item button {
    padding: 10px 15px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.3s ease;
}

.url-item button:hover {
    background: #dc2626;
}

.add-url-btn {
    padding: 10px 15px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.add-url-btn:hover {
    background: var(--primary-dark);
}

/* File Upload */
.file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    padding: 30px;
    text-align: center;
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

.preview-name {
    font-weight: 600;
    color: var(--text-dark);
    flex: 1;
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
    margin-top: 20px;
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

.error-message {
    color: #ef4444;
    font-size: 0.85rem;
    margin-top: 5px;
    display: none;
}

.info-text {
    font-size: 0.9rem;
    color: var(--text-light);
    margin-top: 5px;
    font-style: italic;
}

@media (max-width: 768px) {
    .form-container-full {
        padding: 25px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .section-title {
        font-size: 1.2rem;
    }
}

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
</style>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Website Project Inquiry</h1>
            <p class="hero-subtitle">
                Tell us about your website project and we'll provide you with a detailed proposal
            </p>
        </div>
    </div>
</section>

<!-- Main Form -->
<div class="form-container-full">
    <div class="form-header">
        <h1>Comprehensive Website Project Brief</h1>
        <p>Please fill out all relevant sections to help us understand your project better</p>
    </div>

    <!-- Success Message -->
    <div class="success-message" id="successMessage">
        <strong>Thank You!</strong>
        <p>Your project inquiry has been submitted successfully. We'll review your requirements and contact you shortly.</p>
    </div>

    <form action="<?= APP_URL ?>/contact/submit" method="POST" id="projectForm" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- SECTION 1: Contact Information -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-user-circle"></i>
                Contact Information
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-user"></i>
                        Full Name
                    </label>
                    <input type="text" name="name" class="form-input" required 
                           placeholder="Your full name" 
                           value="<?= isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : '' ?>">
                    <div class="error-message" id="nameError"></div>
                </div>

                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" name="email" class="form-input" required 
                           placeholder="your@email.com"
                           value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>">
                    <div class="error-message" id="emailError"></div>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-phone"></i>
                        Phone Number
                    </label>
                    <input type="tel" name="phone" class="form-input" 
                           placeholder="+227 XXXX XXXX"
                           value="<?= isset($_SESSION['phone']) ? htmlspecialchars($_SESSION['phone']) : '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-building"></i>
                        Organization/Company Name
                    </label>
                    <input type="text" name="organization" class="form-input" 
                           placeholder="Your company name"
                           value="<?= isset($_SESSION['company_name']) ? htmlspecialchars($_SESSION['company_name']) : '' ?>">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Business Information -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-briefcase"></i>
                Business Information
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-industry"></i>
                        Industry/Business Type
                    </label>
                    <select name="business_type" class="form-select">
                        <option value="">Select your industry...</option>
                        <option value="ecommerce">E-Commerce</option>
                        <option value="saas">SaaS/Software</option>
                        <option value="consulting">Consulting</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="education">Education</option>
                        <option value="real_estate">Real Estate</option>
                        <option value="finance">Finance</option>
                        <option value="marketing">Marketing/Advertising</option>
                        <option value="tech">Technology</option>
                        <option value="hospitality">Hospitality</option>
                        <option value="nonprofit">Non-Profit</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-globe"></i>
                        Do you have an existing website?
                    </label>
                    <select name="existing_website" class="form-select" id="existingWebsite">
                        <option value="">Select...</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>

            <div class="form-grid" id="existingUrlField" style="display: none;">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-link"></i>
                        Current Website URL
                    </label>
                    <input type="url" name="existing_url" class="form-input" 
                           placeholder="https://your-website.com">
                    <p class="info-text">Provide the URL so we can review your current site</p>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-bullseye"></i>
                        What is the primary purpose of your website?
                    </label>
                    <textarea name="website_purpose" class="form-textarea" 
                              placeholder="Describe the main goals and purpose of your website..."></textarea>
                    <p class="info-text">Example: Generate leads, sell products, provide information, build community, etc.</p>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-users"></i>
                        Target Audience
                    </label>
                    <textarea name="target_audience" class="form-textarea" 
                              placeholder="Describe your target audience..."></textarea>
                    <p class="info-text">Include demographics, interests, location, and user behavior</p>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Website Features -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-cogs"></i>
                Required Features
            </h2>

            <label class="form-label">
                <i class="fas fa-list-check"></i>
                Select the features you need (check all that apply)
            </label>

            <div class="features-grid">
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="ecommerce" id="feat_ecommerce">
                    <label for="feat_ecommerce">E-Commerce Functionality</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="blog" id="feat_blog">
                    <label for="feat_blog">Blog/Content Management</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="booking" id="feat_booking">
                    <label for="feat_booking">Booking System</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="membership" id="feat_membership">
                    <label for="feat_membership">Membership/User Accounts</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="newsletter" id="feat_newsletter">
                    <label for="feat_newsletter">Email Newsletter</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="contact_form" id="feat_contact">
                    <label for="feat_contact">Contact Forms</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="crm" id="feat_crm">
                    <label for="feat_crm">CRM Integration</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="live_chat" id="feat_chat">
                    <label for="feat_chat">Live Chat Support</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="social_media" id="feat_social">
                    <label for="feat_social">Social Media Integration</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="api_integration" id="feat_api">
                    <label for="feat_api">API Integration</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="video_hosting" id="feat_video">
                    <label for="feat_video">Video Hosting</label>
                </div>
                <div class="feature-checkbox">
                    <input type="checkbox" name="required_features[]" value="search_function" id="feat_search">
                    <label for="feat_search">Advanced Search</label>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-plus"></i>
                        Additional Features Required
                    </label>
                    <textarea name="additional_features_text" class="form-textarea" 
                              placeholder="Describe any other features needed..."></textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Design & Branding -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-palette"></i>
                Design & Branding
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-pen-fancy"></i>
                        Design Style Preference
                    </label>
                    <select name="design_style" class="form-select">
                        <option value="">Select a style...</option>
                        <option value="modern">Modern & Minimalist</option>
                        <option value="corporate">Corporate & Professional</option>
                        <option value="creative">Creative & Bold</option>
                        <option value="elegant">Elegant & Luxury</option>
                        <option value="playful">Playful & Casual</option>
                        <option value="tech">Tech-Focused</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-font"></i>
                        Preferred Fonts
                    </label>
                    <input type="text" name="branding_fonts" class="form-input" 
                           placeholder="e.g., Montserrat, Open Sans, etc.">
                    <p class="info-text">Or leave blank for our recommendation</p>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-palette"></i>
                        Brand Colors (if available)
                    </label>
                    <input type="text" name="branding_colors" class="form-input" 
                           placeholder="e.g., #0E9F6E, #F97316 or color names">
                    <p class="info-text">Share your brand colors or color preferences</p>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-clipboard-list"></i>
                        Design Inspiration
                    </label>
                    <textarea name="design_inspiration" class="form-textarea" 
                              placeholder="Describe design elements, layouts, or websites you like..."></textarea>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-link"></i>
                        Competitor/Reference Websites
                    </label>
                    <textarea name="competitor_urls" class="form-textarea" 
                              placeholder="Share URLs of websites you like or want to compare with..."></textarea>
                    <p class="info-text">One URL per line - these help us understand your vision</p>
                </div>
            </div>
        </div>

        <!-- SECTION 5: Technical Requirements -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-server"></i>
                Technical Requirements
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-database"></i>
                        CMS Preference
                    </label>
                    <select name="cms_preference" class="form-select">
                        <option value="">No preference</option>
                        <option value="wordpress">WordPress</option>
                        <option value="custom">Custom Built</option>
                        <option value="shopify">Shopify</option>
                        <option value="wix">Wix</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-mobile-alt"></i>
                        Mobile Responsive Design
                    </label>
                    <select name="mobile_responsive" class="form-select">
                        <option value="yes" selected>Yes (Recommended)</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-globe"></i>
                        Multilingual Support Needed?
                    </label>
                    <select name="multilingual" class="form-select" id="multilingualSelect">
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>

                <div class="form-group" id="languagesField" style="display: none;">
                    <label class="form-label">
                        <i class="fas fa-language"></i>
                        Which Languages?
                    </label>
                    <input type="text" name="languages" class="form-input" 
                           placeholder="e.g., French, English, Arabic">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-chart-line"></i>
                        Analytics Tracking Required?
                    </label>
                    <select name="analytics_tracking" class="form-select">
                        <option value="yes" selected>Yes (Google Analytics)</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-lock"></i>
                        SSL Certificate (HTTPS)
                    </label>
                    <select name="ssl_certificate" class="form-select">
                        <option value="yes" selected>Yes (Recommended)</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-plug"></i>
                        Payment Gateway Integration
                    </label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="payment_integrations[]" value="stripe" id="pay_stripe">
                            <label for="pay_stripe">Stripe</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="payment_integrations[]" value="paypal" id="pay_paypal">
                            <label for="pay_paypal">PayPal</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="payment_integrations[]" value="mobile_money" id="pay_mm">
                            <label for="pay_mm">Mobile Money (MTN, Airtel, etc.)</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="payment_integrations[]" value="bank_transfer" id="pay_bank">
                            <label for="pay_bank">Bank Transfer</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="payment_integrations[]" value="crypto" id="pay_crypto">
                            <label for="pay_crypto">Cryptocurrency</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tools"></i>
                        Third-Party Integrations Needed
                    </label>
                    <textarea name="integrations_needed" class="form-textarea" 
                              placeholder="e.g., Email service (Mailchimp), CRM (HubSpot), Analytics tools, etc."></textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 6: Hosting & Domain -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-server"></i>
                Hosting & Domain
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-globe"></i>
                        Domain Name
                    </label>
                    <input type="text" name="domain_name" class="form-input" 
                           placeholder="your-domain.com or leave empty if you need help">
                    <p class="info-text">Do you already have a domain? If not, we can help you register one</p>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-server"></i>
                        Hosting Preference
                    </label>
                    <select name="hosting_requirements" class="form-select">
                        <option value="">No preference / Let us recommend</option>
                        <option value="shared">Shared Hosting</option>
                        <option value="vps">VPS Hosting</option>
                        <option value="dedicated">Dedicated Server</option>
                        <option value="cloud">Cloud Hosting</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 7: SEO & Marketing -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-search"></i>
                SEO & Marketing
            </h2>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-key"></i>
                        SEO Keywords & Requirements
                    </label>
                    <textarea name="seo_requirements" class="form-textarea" 
                              placeholder="What keywords do you want to rank for? Any specific SEO goals?"></textarea>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tools"></i>
                        Current Marketing Tools
                    </label>
                    <textarea name="current_marketing_tools" class="form-textarea" 
                              placeholder="What email or marketing tools are you currently using?"></textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 8: Timeline & Budget -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Timeline & Budget
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar"></i>
                        Project Start Date (Preferred)
                    </label>
                    <input type="date" name="timeline_start" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-flag-checkered"></i>
                        Project Deadline
                    </label>
                    <input type="date" name="timeline_deadline" class="form-input">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-money-bill-wave"></i>
                        Budget Range
                    </label>
                    <select name="budget" class="form-select" required>
                        <option value="">Select your budget...</option>
                        <option value="low">Small Budget (100K - 500K CFA)</option>
                        <option value="medium">Medium Budget (500K - 2M CFA)</option>
                        <option value="high">Large Budget (2M - 5M CFA)</option>
                        <option value="enterprise">Enterprise (5M+ CFA)</option>
                        <option value="consultation">Need a Quote / Consultation</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-info-circle"></i>
                        Project Type
                    </label>
                    <select name="project_type" class="form-select">
                        <option value="web">Website</option>
                        <option value="ecommerce">E-Commerce</option>
                        <option value="mobile">Mobile App</option>
                        <option value="redesign">Website Redesign</option>
                        <option value="custom">Custom Project</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- SECTION 9: Support & Maintenance -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-headset"></i>
                Support & Maintenance
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tools"></i>
                        Ongoing Support Needed?
                    </label>
                    <select name="ongoing_support_needed" class="form-select" id="supportNeeded">
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>

                <div class="form-group" id="supportLevelField" style="display: none;">
                    <label class="form-label">
                        <i class="fas fa-bars"></i>
                        Support Level Needed
                    </label>
                    <select name="support_level" class="form-select">
                        <option value="">Select support level...</option>
                        <option value="basic">Basic (Bug fixes & updates)</option>
                        <option value="standard">Standard (Maintenance & monitoring)</option>
                        <option value="premium">Premium (24/7 support & optimization)</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-book"></i>
                        Staff Training Needed?
                    </label>
                    <select name="training_needed" class="form-select" id="trainingNeeded">
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>

                <div class="form-group" id="trainingDetailsField" style="display: none;">
                    <label class="form-label">
                        <i class="fas fa-info-circle"></i>
                        Training Details
                    </label>
                    <textarea name="training_details" class="form-textarea" 
                              placeholder="What should staff be trained on?"></textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 10: Project Description & Additional Notes -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-file-alt"></i>
                Project Description & Notes
            </h2>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label required">
                        <i class="fas fa-align-left"></i>
                        Detailed Project Description
                    </label>
                    <textarea name="description" class="form-textarea" required
                              placeholder="Provide a detailed description of your project, its scope, and any special requirements..."></textarea>
                    <p class="info-text">This helps us better understand your needs and provide an accurate quote</p>
                    <div class="error-message" id="descriptionError"></div>
                </div>
            </div>

            <div class="form-grid full">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sticky-note"></i>
                        Additional Notes
                    </label>
                    <textarea name="additional_notes" class="form-textarea" 
                              placeholder="Anything else we should know about your project?"></textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 11: File Uploads -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-paperclip"></i>
                File Attachments
            </h2>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-cloud-upload-alt"></i>
                    Upload Relevant Files
                </label>
                <div class="file-upload-area" id="fileUploadArea" role="button" tabindex="0" aria-label="Upload files">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">
                        <strong>Drag and drop files here</strong> or click to browse
                    </div>
                    <div class="upload-hint">
                        Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, ZIP (Max 10MB per file)
                    </div>
                </div>
                <input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.zip,.rar" style="display: none;" id="fileInput">
                <div class="file-preview" id="filePreview"></div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn" id="submitBtn">
            <i class="fas fa-paper-plane"></i>
            Submit Project Inquiry
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('projectForm');
    const fileInput = document.getElementById('fileInput');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const filePreview = document.getElementById('filePreview');
    const successMessage = document.getElementById('successMessage');

    // Show/Hide existing URL field
    document.getElementById('existingWebsite').addEventListener('change', function() {
        document.getElementById('existingUrlField').style.display = this.value === 'yes' ? 'grid' : 'none';
    });

    // Show/Hide multilingual languages field
    document.getElementById('multilingualSelect').addEventListener('change', function() {
        document.getElementById('languagesField').style.display = this.value === 'yes' ? 'grid' : 'none';
    });

    // Show/Hide support level field
    document.getElementById('supportNeeded').addEventListener('change', function() {
        document.getElementById('supportLevelField').style.display = this.value === 'yes' ? 'grid' : 'none';
    });

    // Show/Hide training details field
    document.getElementById('trainingNeeded').addEventListener('change', function() {
        document.getElementById('trainingDetailsField').style.display = this.value === 'yes' ? 'grid' : 'none';
    });

    // File upload handling
    fileUploadArea.addEventListener('click', () => fileInput.click());
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
        fileInput.files = e.dataTransfer.files;
        updateFilePreview();
    });

    fileInput.addEventListener('change', updateFilePreview);

    function updateFilePreview() {
        const files = fileInput.files;
        filePreview.innerHTML = '';

        if (files.length > 0) {
            filePreview.style.display = 'block';
            Array.from(files).forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = `
                    <div class="preview-icon">📄</div>
                    <div>
                        <div class="preview-name">${file.name}</div>
                        <div class="preview-size">${(file.size / 1024).toFixed(2)} KB</div>
                    </div>
                    <button type="button" class="remove-file" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                filePreview.appendChild(item);
            });

            document.querySelectorAll('.remove-file').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const index = parseInt(this.dataset.index);
                    const dataTransfer = new DataTransfer();
                    Array.from(files).forEach((file, i) => {
                        if (i !== index) dataTransfer.items.add(file);
                    });
                    fileInput.files = dataTransfer.files;
                    updateFilePreview();
                });
            });
        } else {
            filePreview.style.display = 'none';
        }
    }

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                successMessage.style.display = 'block';
                form.reset();
                filePreview.innerHTML = '';
                filePreview.style.display = 'none';
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Project Inquiry';
        }
    });
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
