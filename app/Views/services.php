<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Services Page Styles */
.services-hero {
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.1) 0%, rgba(255, 255, 255, 0.95) 100%);
    padding: 120px 0 80px;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-top: 70px;
}

.services-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(14, 159, 110, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.08) 0%, transparent 50%);
    pointer-events: none;
}

.hero-content {
    max-width: 900px;
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
    animation: fadeInUp 0.8s ease-out;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: var(--text-light);
    max-width: 700px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

/* Services Navigation */
.services-nav {
    background: white;
    padding: 20px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    margin: 40px auto 60px;
    max-width: 1200px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

.service-nav-btn {
    padding: 12px 25px;
    background: var(--bg-light);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    color: var(--text-color);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.service-nav-btn:hover {
    background: white;
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
}

.service-nav-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.service-nav-icon {
    font-size: 1.1rem;
}

/* Services Grid */
.services-section {
    padding: 50px 0 100px;
    background: white;
}

.service-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.service-category {
    margin-bottom: 80px;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease-out forwards;
}

.category-header {
    text-align: center;
    margin-bottom: 50px;
}

.category-icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 50%;
    margin: 0 auto 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: var(--shadow-md);
}

.category-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
}

.category-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: var(--gradient-accent);
    border-radius: 2px;
}

.category-description {
    font-size: 1.2rem;
    color: var(--text-light);
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Service Cards Grid */
.service-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
}

.service-card {
    background: white;
    padding: 40px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    border: 1px solid rgba(14, 159, 110, 0.1);
    position: relative;
    overflow: hidden;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--gradient-primary);
}

.service-card-alt::before {
    background: var(--gradient-accent);
}

.service-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.service-icon {
    width: 70px;
    height: 70px;
    background: rgba(14, 159, 110, 0.1);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 2rem;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.service-card:hover .service-icon {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1) rotate(5deg);
}

.service-title h3 {
    margin: 0 0 5px 0;
    color: var(--text-dark);
    font-size: 1.4rem;
}

.service-tag {
    display: inline-block;
    padding: 4px 12px;
    background: var(--bg-light);
    color: var(--primary-dark);
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.service-description {
    color: var(--text-color);
    line-height: 1.7;
    margin-bottom: 25px;
    font-size: 1.05rem;
}

.service-features {
    background: var(--bg-light);
    padding: 20px;
    border-radius: var(--border-radius);
    margin-bottom: 25px;
}

.features-title {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.features-list li {
    padding: 8px 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.features-list li:hover {
    color: var(--primary-color);
    padding-left: 5px;
}

.features-list li::before {
    content: '✓';
    color: var(--primary-color);
    font-weight: bold;
    font-size: 1.1rem;
}

.service-tech {
    margin-bottom: 25px;
}

.tech-title {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tech-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tech-tag {
    background: var(--bg-light);
    color: var(--primary-dark);
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.tech-tag:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.service-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.service-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.service-price span {
    font-size: 0.9rem;
    color: var(--text-light);
    font-weight: 500;
}

.service-btn {
    background: var(--primary-color);
    color: white;
    padding: 12px 30px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.service-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-3px);
    box-shadow: var(--shadow-sm);
    color: white;
}

/* Process Section */
.process-section {
    padding: 100px 0;
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.process-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.process-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 15px;
}

.process-subtitle {
    text-align: center;
    font-size: 1.2rem;
    color: var(--text-light);
    max-width: 700px;
    margin: 0 auto 60px;
    line-height: 1.6;
}

.process-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    counter-reset: step-counter;
}

.process-step {
    background: white;
    padding: 40px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    position: relative;
    text-align: center;
    transition: all 0.3s ease;
}

.process-step:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.process-step::before {
    counter-increment: step-counter;
    content: counter(step-counter);
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 40px;
    background: var(--gradient-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    box-shadow: var(--shadow-md);
}

.step-icon {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: var(--primary-color);
}

.process-step h3 {
    color: var(--text-dark);
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.process-step p {
    color: var(--text-color);
    line-height: 1.6;
}

/* Pricing Section */
.pricing-section {
    padding: 100px 0;
    background: var(--gradient-primary);
    color: white;
    position: relative;
    overflow: hidden;
}

.pricing-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
}

.pricing-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 1;
}

.pricing-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: white;
}

.pricing-subtitle {
    text-align: center;
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    max-width: 700px;
    margin: 0 auto 60px;
    line-height: 1.6;
}

.pricing-tabs {
    display: flex;
    justify-content: center;
    margin-bottom: 50px;
    flex-wrap: wrap;
    gap: 10px;
}

.pricing-tab {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 12px 30px;
    border-radius: var(--border-radius);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.pricing-tab:hover {
    background: rgba(255, 255, 255, 0.2);
}

.pricing-tab.active {
    background: white;
    color: var(--primary-color);
    border-color: white;
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.pricing-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--border-radius-lg);
    padding: 40px;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.pricing-card:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
}

.pricing-card.popular {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid var(--accent-color);
    transform: scale(1.05);
}

.pricing-card.popular:hover {
    transform: scale(1.05) translateY(-10px);
}

.popular-badge {
    position: absolute;
    top: 20px;
    right: -30px;
    background: var(--accent-color);
    color: white;
    padding: 8px 40px;
    font-size: 0.8rem;
    font-weight: 600;
    transform: rotate(45deg);
    width: 150px;
    text-align: center;
}

.pricing-header {
    margin-bottom: 30px;
}

.pricing-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: white;
}

.pricing-description {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    line-height: 1.5;
}

.pricing-price {
    margin-bottom: 30px;
}

.price-amount {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    line-height: 1;
    margin-bottom: 5px;
}

.price-period {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0 0 30px 0;
}

.pricing-features li {
    padding: 10px 0;
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.pricing-features li:last-child {
    border-bottom: none;
}

.pricing-features li.included::before {
    content: '✓';
    color: #34d399;
    font-weight: bold;
}

.pricing-features li.not-included::before {
    content: '✗';
    color: rgba(255, 255, 255, 0.3);
}

.pricing-btn {
    width: 100%;
    background: white;
    color: var(--primary-color);
    padding: 15px;
    border-radius: var(--border-radius);
    border: none;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.pricing-btn:hover {
    background: var(--accent-color);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.pricing-note {
    text-align: center;
    margin-top: 40px;
    color: rgba(255, 255, 255, 0.7);
    font-style: italic;
    font-size: 0.95rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

/* CTA Section */
.services-cta {
    padding: 100px 0;
    background: white;
    text-align: center;
}

.cta-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 0 20px;
}

.cta-container h2 {
    font-size: 2.5rem;
    color: var(--text-dark);
    margin-bottom: 20px;
}

.cta-container p {
    font-size: 1.2rem;
    color: var(--text-light);
    margin-bottom: 40px;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-btn-primary {
    background: var(--gradient-primary);
    color: white;
    padding: 18px 40px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

.cta-btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    color: white;
}

.cta-btn-secondary {
    background: transparent;
    color: var(--primary-color);
    border: 3px solid var(--primary-color);
    padding: 18px 40px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 12px;
}

.cta-btn-secondary:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-title {
        font-size: 2.8rem;
    }
    
    .service-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .pricing-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .pricing-card.popular {
        transform: none;
    }
    
    .pricing-card.popular:hover {
        transform: translateY(-10px);
    }
}

@media (max-width: 768px) {
    .services-hero {
        padding: 100px 0 60px;
        margin-top: 60px;
    }
    
    .hero-title {
        font-size: 2.2rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .services-nav {
        flex-direction: column;
        align-items: stretch;
    }
    
    .service-nav-btn {
        text-align: center;
        justify-content: center;
    }
    
    .service-cards-grid {
        grid-template-columns: 1fr;
    }
    
    .pricing-grid {
        grid-template-columns: 1fr;
    }
    
    .pricing-card {
        max-width: 400px;
        margin: 0 auto;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-btn-primary,
    .cta-btn-secondary {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 1.8rem;
    }
    
    .category-title {
        font-size: 1.8rem;
    }
    
    .service-card {
        padding: 30px 20px;
    }
    
    .service-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .process-steps {
        grid-template-columns: 1fr;
    }
}
    /* Project Estimator Styles */
    .project-estimator {
        padding: 80px 0;
        background: var(--bg-light);
    }

    .estimator-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .estimator-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        padding: 40px;
        margin-top: 40px;
    }

    .estimator-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    @media (max-width: 768px) {
        .estimator-grid {
            grid-template-columns: 1fr;
        }
    }

    .estimator-group {
        margin-bottom: 25px;
    }

    .estimator-group label {
        display: block;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--text-dark);
    }

    .estimator-select {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--bg-light);
    }

    .features-checklist {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .feature-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 10px;
        border-radius: 8px;
        background: var(--bg-light);
        transition: all 0.2s ease;
    }

    .feature-checkbox:hover {
        background: rgba(14, 159, 110, 0.05);
    }

    .feature-checkbox input {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-color);
    }

    .result-panel {
        background: var(--primary-dark);
        color: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .result-label {
        font-size: 1rem;
        opacity: 0.8;
        margin-bottom: 10px;
    }

    .result-amount {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--accent-color);
        margin-bottom: 10px;
    }

    .result-disclaimer {
        font-size: 0.8rem;
        opacity: 0.6;
        line-height: 1.4;
    }

    .estimator-cta {
        margin-top: 25px;
        width: 100%;
    }
</style>

<!-- Hero Section -->
<section class="services-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?= __('our_services') ?></h1>
            <p class="hero-subtitle">
                <?= __('services_intro') ?>
            </p>
        </div>
    </div>
</section>

<!-- Services Navigation -->
<div class="services-nav">
    <button class="service-nav-btn active" data-target="custom-software">
        <span class="service-nav-icon">🔧</span>
        <span><?= __('custom_software') ?></span>
    </button>
    <button class="service-nav-btn" data-target="ecommerce-platforms">
        <span class="service-nav-icon">🛒</span>
        <span><?= __('ecommerce_platforms') ?></span>
    </button>
    <button class="service-nav-btn" data-target="web-platforms">
        <span class="service-nav-icon">🌐</span>
        <span><?= __('web_platforms') ?></span>
    </button>
    <button class="service-nav-btn" data-target="mobile-apps">
        <span class="service-nav-icon">📱</span>
        <span><?= __('mobile_apps') ?></span>
    </button>
</div>

<!-- Services Grid -->
<section class="services-section">
    <div class="service-container">
        <!-- Custom Software Category -->
        <div class="service-category" id="custom-software" style="display: block;">
            <div class="category-header">
                <div class="category-icon">🔧</div>
                <h2 class="category-title"><?= __('custom_software') ?></h2>
                <p class="category-description">
                    <?= __('custom_software_desc') ?>
                </p>
            </div>
            
            <div class="service-cards-grid">
                <!-- Custom Web Applications -->
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">🌐</div>
                        <div class="service-title">
                            <h3><?= __('custom_web_apps') ?></h3>
                            <span class="service-tag"><?= __('most_popular') ?></span>
                        </div>
                    </div>
                    
                    <p class="service-description">
                        <?= __('custom_web_apps_desc') ?>
                    </p>
                    
                    <div class="service-features">
                        <div class="features-title">
                            <span>✨</span>
                            <span><?= __('key_features') ?></span>
                        </div>
                        <ul class="features-list">
                            <li><?= __('feat_responsive_design') ?></li>
                            <li><?= __('feat_user_auth') ?></li>
                            <li><?= __('feat_db_integration') ?></li>
                            <li><?= __('feat_api_dev') ?></li>
                            <li><?= __('feat_realtime') ?></li>
                            <li><?= __('feat_admin_dash') ?></li>
                        </ul>
                    </div>
                    
                    <div class="service-tech">
                        <div class="tech-title">
                            <span>💻</span>
                            <span><?= __('tech_stack') ?></span>
                        </div>
                        <div class="tech-tags">
                            <span class="tech-tag">PHP</span>
                            <span class="tech-tag">JavaScript</span>
                            <span class="tech-tag">MySQL</span>
                            <span class="tech-tag">PayStack API</span>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <div class="service-price">
                            500K+ CFA
                            <span><?= __('starting_from') ?></span>
                        </div>
                        <a href="<?= APP_URL ?>/contact?service=custom-web-app" class="service-btn">
                            <span>📞</span>
                            <span><?= __('get_quote') ?></span>
                        </a>
                    </div>
                </div>
                
                <!-- Desktop Software -->
                <div class="service-card service-card-alt">
                    <div class="service-header">
                        <div class="service-icon">💻</div>
                        <div class="service-title">
                            <h3><?= __('desktop_apps') ?></h3>
                            <span class="service-tag">Windows/Mac/Linux</span>
                        </div>
                    </div>
                    
                    <p class="service-description">
                        <?= __('desktop_apps_desc') ?>
                    </p>
                    
                    <div class="service-features">
                        <div class="features-title">
                            <span>✨</span>
                            <span><?= __('key_features') ?></span>
                        </div>
                        <ul class="features-list">
                            <li><?= __('feat_offline') ?></li>
                            <li><?= __('feat_system_tray') ?></li>
                            <li><?= __('feat_auto_updates') ?></li>
                            <li><?= __('feat_local_db') ?></li>
                            <li><?= __('feat_multi_platform') ?></li>
                            <li><?= __('feat_hardware_integ') ?></li>
                        </ul>
                    </div>
                    
                    <div class="service-tech">
                        <div class="tech-title">
                            <span>💻</span>
                            <span><?= __('tech_stack') ?></span>
                        </div>
                        <div class="tech-tags">
                            <span class="tech-tag">C#/.NET</span>
                            <span class="tech-tag">Electron.js</span>
                            <span class="tech-tag">Java</span>
                            <span class="tech-tag">Python</span>
                            <span class="tech-tag">SQLite</span>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <div class="service-price">
                            750K+ CFA
                            <span><?= __('starting_from') ?></span>
                        </div>
                        <a href="<?= APP_URL ?>/contact?service=desktop-app" class="service-btn">
                            <span>📞</span>
                            <span><?= __('get_quote') ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- E-commerce Platforms Category -->
        <div class="service-category" id="ecommerce-platforms" style="display: none;">
            <div class="category-header">
                <div class="category-icon">🛒</div>
                <h2 class="category-title">E-commerce Platforms</h2>
                <p class="category-description">
                    Build powerful online stores with secure payment processing and inventory management
                </p>
            </div>
            
            <div class="service-cards-grid">
                <!-- E-commerce Platforms Store -->
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">🛒</div>
                        <div class="service-title">
                            <h3>E-commerce Platforms</h3>
                            <span class="service-tag">Store</span>
                        </div>
                    </div>
                    
                    <p class="service-description">
                        Build powerful online stores with secure payment processing and inventory management
                    </p>
                    
                    <div class="service-features">
                        <div class="features-title">
                            <span>✨</span>
                            <span>Key Features</span>
                        </div>
                        <ul class="features-list">
                            <li>Product catalog management</li>
                            <li>Shopping cart & checkout</li>
                            <li>Payment gateway integration</li>
                            <li>Order management</li>
                            <li>Customer accounts</li>
                            <li>Shipping integration</li>
                        </ul>
                    </div>
                    
                    <div class="service-tech">
                        <div class="tech-title">
                            <span>💻</span>
                            <span>Technology Stack</span>
                        </div>
                        <div class="tech-tags">
                            <span class="tech-tag">PHP</span>
                            <span class="tech-tag">JavaScript</span>
                            <span class="tech-tag">MySQL</span>
                            <span class="tech-tag">PayStack API</span>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <div class="service-price">
                            800K+ CFA
                            <span><?= __('starting_from') ?></span>
                        </div>
                        <a href="<?= APP_URL ?>/contact?service=ecommerce" class="service-btn">
                            <span>📞</span>
                            <span><?= __('get_quote') ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Web Platforms Category -->
        <div class="service-category" id="web-platforms" style="display: none;">
            <div class="category-header">
                <div class="category-icon">🌐</div>
                <h2 class="category-title"><?= __('web_platforms') ?></h2>
                <p class="category-description">
                    <?= __('web_platforms_desc') ?>
                </p>
            </div>
            
            <div class="service-cards-grid">

                <!-- Content Management Systems -->
                <div class="service-card service-card-alt">
                    <div class="service-header">
                        <div class="service-icon">📝</div>
                        <div class="service-title">
                            <h3><?= __('cms_systems') ?></h3>
                            <span class="service-tag"><?= __('cms_tag') ?></span>
                        </div>
                    </div>
                    
                    <p class="service-description">
                        <?= __('cms_systems_desc') ?>
                    </p>
                    
                    <div class="service-features">
                        <div class="features-title">
                            <span>✨</span>
                            <span><?= __('key_features') ?></span>
                        </div>
                        <ul class="features-list">
                            <li><?= __('feat_content_edit') ?></li>
                            <li><?= __('feat_multilang') ?></li>
                            <li><?= __('feat_seo_opt') ?></li>
                            <li><?= __('feat_blog_mgmt') ?></li>
                            <li><?= __('feat_media_lib') ?></li>
                            <li><?= __('feat_user_perm') ?></li>
                        </ul>
                    </div>
                    
                    <div class="service-tech">
                        <div class="tech-title">
                            <span>💻</span>
                            <span><?= __('tech_stack') ?></span>
                        </div>
                        <div class="tech-tags">
                            <span class="tech-tag">WordPress</span>
                            <span class="tech-tag">PHP</span>
                            <span class="tech-tag">JavaScript</span>
                            <span class="tech-tag">HTML5/CSS3</span>
                            <span class="tech-tag">MySQL</span>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <div class="service-price">
                            300K+ CFA
                            <span><?= __('starting_from') ?></span>
                        </div>
                        <a href="<?= APP_URL ?>/contact?service=cms" class="service-btn">
                            <span>📞</span>
                            <span><?= __('get_quote') ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Apps Category -->
        <div class="service-category" id="mobile-apps" style="display: none;">
            <div class="category-header">
                <div class="category-icon">📱</div>
                <h2 class="category-title"><?= __('mobile_apps') ?></h2>
                <p class="category-description">
                    <?= __('mobile_apps_desc') ?>
                </p>
            </div>
            
            <div class="service-cards-grid">
                <!-- iOS & Android Apps -->
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">📱</div>
                        <div class="service-title">
                            <h3><?= __('native_mobile_apps') ?></h3>
                            <span class="service-tag"><?= __('mobile_native_tag') ?></span>
                        </div>
                    </div>
                    
                    <p class="service-description">
                        <?= __('native_mobile_apps_desc') ?>
                    </p>
                    
                    <div class="service-features">
                        <div class="features-title">
                            <span>✨</span>
                            <span><?= __('key_features') ?></span>
                        </div>
                        <ul class="features-list">
                            <li><?= __('feat_platform_ui') ?></li>
                            <li><?= __('feat_offline') ?></li>
                            <li><?= __('feat_push_notif') ?></li>
                            <li><?= __('feat_camera_sensor') ?></li>
                            <li><?= __('feat_app_store') ?></li>
                            <li><?= __('feat_in_app') ?></li>
                        </ul>
                    </div>
                    
                    <div class="service-tech">
                        <div class="tech-title">
                            <span>💻</span>
                            <span><?= __('tech_stack') ?></span>
                        </div>
                        <div class="tech-tags">
                            <span class="tech-tag">Swift/iOS</span>
                            <span class="tech-tag">Kotlin/Android</span>
                            <span class="tech-tag">Firebase</span>
                            <span class="tech-tag">REST APIs</span>
                            <span class="tech-tag">SQLite</span>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <div class="service-price">
                            1.2M+ CFA
                            <span><?= __('starting_from') ?></span>
                        </div>
                        <a href="<?= APP_URL ?>/contact?service=native-mobile" class="service-btn">
                            <span>📞</span>
                            <span><?= __('get_quote') ?></span>
                        </a>
                    </div>
                </div>
                
                <!-- Cross-Platform Apps -->
                <div class="service-card service-card-alt">
                    <div class="service-header">
                        <div class="service-icon">🔄</div>
                        <div class="service-title">
                            <h3><?= __('cross_platform_apps') ?></h3>
                            <span class="service-tag"><?= __('mobile_cross_tag') ?></span>
                        </div>
                    </div>
                    
                    <p class="service-description">
                        <?= __('cross_platform_apps_desc') ?>
                    </p>
                    
                    <div class="service-features">
                        <div class="features-title">
                            <span>✨</span>
                            <span><?= __('key_features') ?></span>
                        </div>
                        <ul class="features-list">
                            <li><?= __('feat_single_code') ?></li>
                            <li><?= __('feat_fast_dev') ?></li>
                            <li><?= __('feat_consistent_ux') ?></li>
                            <li><?= __('feat_native_perf') ?></li>
                            <li><?= __('feat_easy_maint') ?></li>
                            <li><?= __('feat_cost_effect') ?></li>
                        </ul>
                    </div>
                    
                    <div class="service-tech">
                        <div class="tech-title">
                            <span>💻</span>
                            <span><?= __('tech_stack') ?></span>
                        </div>
                        <div class="tech-tags">
                            <span class="tech-tag">React Native</span>
                            <span class="tech-tag">Flutter</span>
                            <span class="tech-tag">Firebase</span>
                            <span class="tech-tag">Redux</span>
                            <span class="tech-tag">Node.js</span>
                        </div>
                    </div>
                    
                    <div class="service-action">
                        <div class="service-price">
                            900K+ CFA
                            <span><?= __('starting_from') ?></span>
                        </div>
                        <a href="<?= APP_URL ?>/contact?service=cross-platform" class="service-btn">
                            <span>📞</span>
                            <span><?= __('get_quote') ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        

    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">25+</div>
                <div class="stat-label">Projects Completed</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">15</div>
                <div class="stat-label">Happy Clients</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">12+</div>
                <div class="stat-label">Technologies</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number">98%</div>
                <div class="stat-label">Client Satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="process-section">
    <div class="process-container">
        <h2 class="process-title"><?= __('service_process_title') ?></h2>
        <p class="process-subtitle">
            <?= __('service_process_desc') ?>
        </p>
        
        <div class="process-steps">
            <div class="process-step">
                <div class="step-icon">🔍</div>
                <h3><?= __('process_analysis') ?></h3>
                <p>
                    <?= __('process_desc_analysis') ?>
                </p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">📐</div>
                <h3><?= __('process_design') ?></h3>
                <p>
                   <?= __('process_desc_design') ?>
                </p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">💻</div>
                <h3><?= __('process_dev') ?></h3>
                <p>
                    <?= __('process_desc_dev') ?>
                </p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">🧪</div>
                <h3><?= __('process_testing') ?></h3>
                <p>
                    <?= __('process_desc_testing') ?>
                </p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">🚀</div>
                <h3><?= __('process_deploy') ?></h3>
                <p>
                    <?= __('process_desc_deploy') ?>
                </p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">🛡️</div>
                <h3><?= __('process_support') ?></h3>
                <p>
                    <?= __('process_desc_support_full') ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="pricing-section">
    <div class="pricing-container">
        <h2 class="pricing-title"><?= __('pricing_title') ?></h2>
        <p class="pricing-subtitle">
            <?= __('pricing_text') ?> <?= __('price_estimate_note') ?>
        </p>
        
        <div class="pricing-tabs">
            <button class="pricing-tab active" data-pricing="project"><?= __('price_type_project') ?></button>
            <button class="pricing-tab" data-pricing="monthly"><?= __('price_type_monthly') ?></button>
            <button class="pricing-tab" data-pricing="hourly"><?= __('price_type_hourly') ?></button>
        </div>
        
        <div class="pricing-grid">
            <!-- Small Project -->
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3 class="pricing-name">Small Systems</h3>
                    <p class="pricing-description">
                        Ideal for small businesses, startups, and MVPs. Basic features and standard support included.
                    </p>
                </div>
                
                <div class="pricing-price">
                    <div class="price-amount">100K - 500K XOF</div>
                    <div class="price-period">One-time project</div>
                </div>
                
                <ul class="pricing-features">
                    <li class="included">Basic features & functionality</li>
                    <li class="included">Responsive web design</li>
                    <li class="included">Standard support (3 months)</li>
                    <li class="included">Advanced features</li>
                    <li class="not-included">Basic mobile app</li>
                    <li class="not-included">Priority 24/7 support</li>
                </ul>
                
                <button class="pricing-btn" onclick="window.location.href='<?= APP_URL ?>/contact?package=small'">
                    <span>📞</span>
                    <span><?= __('btn_start_small') ?></span>
                </button>
            </div>
            
            <!-- Medium Project -->
            <div class="pricing-card popular">
                <div class="popular-badge">Most Popular</div>
                <div class="pricing-header">
                    <h3 class="pricing-name">Medium Systems</h3>
                    <p class="pricing-description">
                        Perfect for growing businesses. Includes advanced features, better support, and mobile responsiveness.
                    </p>
                </div>
                
                <div class="pricing-price">
                    <div class="price-amount">150K - 1.5M XOF</div>
                    <div class="price-period">3-6 months development</div>
                </div>
                
                <ul class="pricing-features">
                    <li class="included">Advanced features</li>
                    <li class="included">Mobile-responsive design</li>
                    <li class="included">Extended support (6 months)</li>
                    <li class="included">Basic mobile app</li>
                    <li class="not-included">Enterprise-grade features</li>
                </ul>
                
                <button class="pricing-btn" onclick="window.location.href='<?= APP_URL ?>/contact?package=medium'">
                    <span>🚀</span>
                    <span>Start Medium Project</span>
                </button>
            </div>
            
            <!-- Enterprise -->
            <div class="pricing-card">
                <div class="pricing-header">
                    <h3 class="pricing-name">Enterprise Systems</h3>
                    <p class="pricing-description">
                        Comprehensive solutions for large organizations. Custom features, integrations, and dedicated support.
                    </p>
                </div>
                
                <div class="pricing-price">
                    <div class="price-amount">40K+ XOF</div>
                    <div class="price-period">Per hour</div>
                </div>
                
                <ul class="pricing-features">
                    <li class="included">6+ months development</li>
                    <li class="included">Custom enterprise features</li>
                    <li class="included">Native mobile applications</li>
                    <li class="included">Priority 24/7 support</li>
                    <li class="included">Advanced integrations</li>
                    <li class="included">Dedicated project manager</li>
                    <li class="included">Ongoing maintenance</li>
                </ul>
                
                <button class="pricing-btn" onclick="window.location.href='<?= APP_URL ?>/contact?package=enterprise'">
                    <span>🏢</span>
                    <span>Start Enterprise Project</span>
                </button>
            </div>
        </div>
        
        <p class="pricing-note">
            <?= __('price_note') ?> <?= __('price_estimate_note') ?>
        </p>
    </div>
</section>

<!-- Project Estimator Section -->
<section class="project-estimator">
    <div class="estimator-container">
        <h2 class="section-title"><?= __('project_estimator_title') ?></h2>
        <p class="section-subtitle"><?= __('project_estimator_subtitle') ?></p>

        <div class="estimator-card">
            <div class="estimator-grid">
                <div class="estimator-inputs">
                    <!-- Project Type -->
                    <div class="estimator-group">
                        <label><?= __('select_project_type') ?></label>
                        <select id="projectType" class="estimator-select">
                            <option value="base_web"><?= __('web_app') ?></option>
                            <option value="base_mobile"><?= __('mobile_app') ?></option>
                            <option value="base_ecommerce"><?= __('ecommerce') ?></option>
                            <option value="base_custom"><?= __('custom_software') ?></option>
                        </select>
                    </div>

                    <!-- Complexity -->
                    <div class="estimator-group">
                        <label><?= __('design_complexity') ?></label>
                        <select id="complexity" class="estimator-select">
                            <option value="1"><?= __('simple') ?></option>
                            <option value="1.5"><?= __('standard') ?></option>
                            <option value="2.5"><?= __('premium') ?></option>
                        </select>
                    </div>

                    <!-- Features -->
                    <div class="estimator-group">
                        <label><?= __('features_needed') ?></label>
                        <div class="features-checklist">
                            <label class="feature-checkbox">
                                <input type="checkbox" value="100000" class="feature-item">
                                <span><?= __('feat_auth') ?></span>
                            </label>
                            <label class="feature-checkbox">
                                <input type="checkbox" value="150000" class="feature-item">
                                <span><?= __('feat_payments') ?></span>
                            </label>
                            <label class="feature-checkbox">
                                <input type="checkbox" value="200000" class="feature-item">
                                <span><?= __('feat_admin') ?></span>
                            </label>
                            <label class="feature-checkbox">
                                <input type="checkbox" value="120000" class="feature-item">
                                <span><?= __('feat_chat') ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="result-panel">
                    <div class="result-label"><?= __('estimated_total') ?></div>
                    <div class="result-amount" id="estimateResult">0 CFA</div>
                    <p class="result-disclaimer"><?= __('estimate_disclaimer') ?></p>
                    
                    <button class="action-btn btn-primary estimator-cta" onclick="window.location.href='<?= APP_URL ?>/contact?estimate=' + document.getElementById('estimateResult').innerText">
                        <span>🚀</span>
                        <span><?= __('request_project') ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="services-cta">
    <div class="cta-container">
        <h2><?= __('ready_transform') ?></h2>
        <p>
            <?= __('ready_transform_desc') ?>
        </p>
        <div class="cta-buttons">
            <a href="<?= APP_URL ?>/contact" class="cta-btn-primary">
                <span>📞</span>
                <span><?= __('get_free_consultation') ?></span>
            </a>
            <a href="<?= APP_URL ?>/portfolio" class="cta-btn-secondary">
                <span>👁️</span>
                <span><?= __('view_portfolio') ?></span>
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Service navigation
    const serviceNavBtns = document.querySelectorAll('.service-nav-btn');
    const serviceCategories = document.querySelectorAll('.service-category');
    
    // Project Estimator Logic
    const projectType = document.getElementById('projectType');
    const complexity = document.getElementById('complexity');
    const featureCheckboxes = document.querySelectorAll('.feature-item');
    const resultDisplay = document.getElementById('estimateResult');

    const basePrices = {
        'base_web': 250000,
        'base_mobile': 450000,
        'base_ecommerce': 400000,
        'base_custom': 600000
    };

    function calculateEstimate() {
        let total = basePrices[projectType.value];
        let multiplier = parseFloat(complexity.value);
        
        total *= multiplier;

        featureCheckboxes.forEach(cb => {
            if (cb.checked) {
                total += parseInt(cb.value);
            }
        });

        resultDisplay.innerText = new Intl.NumberFormat().format(total) + ' CFA';
    }

    if (projectType) {
        projectType.addEventListener('change', calculateEstimate);
        complexity.addEventListener('change', calculateEstimate);
        featureCheckboxes.forEach(cb => cb.addEventListener('change', calculateEstimate));
        calculateEstimate(); // Initial call
    }
    
    serviceNavBtns.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            serviceNavBtns.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show selected category
            const targetId = this.getAttribute('data-target');
            serviceCategories.forEach(category => {
                if (category.id === targetId) {
                    category.style.display = 'block';
                    setTimeout(() => {
                        category.style.opacity = '1';
                        category.style.transform = 'translateY(0)';
                    }, 10);
                } else {
                    category.style.opacity = '0';
                    category.style.transform = 'translateY(30px)';
                    setTimeout(() => {
                        category.style.display = 'none';
                    }, 300);
                }
            });
            
            // Scroll to category
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Pricing tabs
    const pricingTabs = document.querySelectorAll('.pricing-tab');
    const pricingModels = {
        project: {
            small: { amount: '100K - 500K CFA', period: 'One-time project' },
            medium: { amount: '500K - 2M CFA', period: 'One-time project' },
            enterprise: { amount: '2M+ CFA', period: 'Custom project' }
        },
        monthly: {
            small: { amount: '50K CFA', period: 'Monthly retainer' },
            medium: { amount: '100K CFA', period: 'Monthly retainer' },
            enterprise: { amount: '200K+ CFA', period: 'Monthly retainer' }
        },
        hourly: {
            small: { amount: '15K CFA', period: 'Per hour' },
            medium: { amount: '25K CFA', period: 'Per hour' },
            enterprise: { amount: '40K+ CFA', period: 'Per hour' }
        }
    };
    
    pricingTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            pricingTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const pricingType = this.getAttribute('data-pricing');
            const model = pricingModels[pricingType];
            
            // Update pricing cards
            const priceAmounts = document.querySelectorAll('.price-amount');
            const pricePeriods = document.querySelectorAll('.price-period');
            
            if (model) {
                // Small project card (index 0)
                priceAmounts[0].textContent = model.small.amount;
                pricePeriods[0].textContent = model.small.period;
                
                // Medium project card (index 1)
                priceAmounts[1].textContent = model.medium.amount;
                pricePeriods[1].textContent = model.medium.period;
                
                // Enterprise card (index 2)
                priceAmounts[2].textContent = model.enterprise.amount;
                pricePeriods[2].textContent = model.enterprise.period;
            }
        });
    });
    
    // Process steps animation
    const processSteps = document.querySelectorAll('.process-step');
    processSteps.forEach((step, index) => {
        step.style.animationDelay = `${index * 0.1}s`;
        step.style.opacity = '0';
        step.style.transform = 'translateY(30px)';
        step.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    // Animate process steps on scroll
    const animateProcessSteps = function() {
        processSteps.forEach(step => {
            const stepTop = step.getBoundingClientRect().top;
            const stepVisible = 150;
            
            if (stepTop < window.innerHeight - stepVisible) {
                step.style.opacity = '1';
                step.style.transform = 'translateY(0)';
            }
        });
    };
    
    setTimeout(animateProcessSteps, 100);
    window.addEventListener('scroll', animateProcessSteps);
    
    // Service card hover effects
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Tech tag hover effects
    const techTags = document.querySelectorAll('.tech-tag');
    techTags.forEach(tag => {
        tag.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        tag.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Pricing card hover effects
    const pricingCards = document.querySelectorAll('.pricing-card');
    pricingCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('popular')) {
                this.style.transform = 'translateY(-10px)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('popular')) {
                this.style.transform = 'translateY(0)';
            } else {
                this.style.transform = 'scale(1.05)';
            }
        });
    });
    
    // Update URL hash for service navigation
    const updateServiceFromHash = function() {
        const hash = window.location.hash.substring(1);
        if (hash && document.getElementById(hash)) {
            const button = document.querySelector(`.service-nav-btn[data-target="${hash}"]`);
            if (button) {
                button.click();
            }
        }
    };
    
    // Initial check for hash
    updateServiceFromHash();
    
    // Update hash when clicking service nav
    serviceNavBtns.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            window.history.replaceState(null, null, `#${targetId}`);
        });
    });
    
    // Listen for hash changes
    window.addEventListener('hashchange', updateServiceFromHash);
    
    // Contact button tracking
    const contactButtons = document.querySelectorAll('.service-btn, .pricing-btn');
    contactButtons.forEach(button => {
        button.addEventListener('click', function() {
            const service = this.closest('.service-card') 
                ? this.closest('.service-card').querySelector('h3').textContent 
                : 'General Inquiry';
            
            console.log(`Service inquiry: ${service}`);
            // In real app, you might want to track this with analytics
        });
    });
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>