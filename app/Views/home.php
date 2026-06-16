<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* Homepage Styles */
.homepage-hero {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.4) 100%), 
                url('<?= APP_URL ?>/images/hero-bg.png');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding: 160px 0 120px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.homepage-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(14, 159, 110, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

.hero-content {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 4rem;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 25px;
    line-height: 1.1;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInUp 0.8s ease-out;
}

.hero-description {
    font-size: 1.3rem;
    color: #e2e8f0;
    margin-bottom: 40px;
    line-height: 1.6;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    animation: fadeInUp 1s ease-out;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
    animation: fadeInUp 1.2s ease-out;
}

.hero-btn-primary {
    background: var(--gradient-accent);
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
    box-shadow: 0 8px 25px rgba(249, 115, 22, 0.2);
}

.hero-btn-primary:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(249, 115, 22, 0.3);
    color: white;
}

.hero-btn-secondary {
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

.hero-btn-secondary:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(14, 159, 110, 0.2);
}

/* Niger Flag Decoration */
.niger-flag-hero {
    height: 8px;
    width: 300px;
    background: linear-gradient(90deg, 
        var(--primary-color) 0%, 
        var(--primary-color) 33%, 
        white 33%, 
        white 66%, 
        var(--accent-color) 66%, 
        var(--accent-color) 100%);
    margin: 40px auto;
    border-radius: 4px;
    animation: float 3s ease-in-out infinite;
}

/* Stats Section */
.stats-section {
    background: var(--gradient-primary);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    max-width: 1200px;
    margin: 0 auto;
}

.stat-item {
    animation: fadeInUp 0.6s ease-out;
}

.stat-number {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 10px;
    color: white;
    line-height: 1;
}

.stat-label {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

/* Services Section */
.services-section {
    padding: 100px 0;
    background: white;
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 60px;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: var(--gradient-accent);
    border-radius: 2px;
}

.section-subtitle {
    text-align: center;
    font-size: 1.2rem;
    color: var(--text-light);
    max-width: 700px;
    margin: 0 auto 60px;
    line-height: 1.6;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.service-card {
    background: white;
    padding: 40px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(14, 159, 110, 0.1);
    text-align: center;
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

.service-card:nth-child(2)::before {
    background: var(--gradient-accent);
}

.service-card:nth-child(3)::before {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.service-icon {
    font-size: 3.5rem;
    margin-bottom: 25px;
    color: var(--primary-color);
    display: inline-block;
    width: 100px;
    height: 100px;
    line-height: 100px;
    background: rgba(14, 159, 110, 0.1);
    border-radius: 50%;
    transition: all 0.3s ease;
}

.service-card:hover .service-icon {
    transform: scale(1.1) rotate(5deg);
    background: var(--primary-color);
    color: white;
}

.service-card h3 {
    font-size: 1.5rem;
    color: var(--text-dark);
    margin-bottom: 15px;
    font-weight: 700;
}

.service-card p {
    color: var(--text-color);
    line-height: 1.7;
    margin-bottom: 20px;
    font-size: 1.05rem;
}

.service-features {
    list-style: none;
    padding: 0;
    margin: 25px 0 0 0;
    text-align: left;
}

.service-features li {
    padding: 8px 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.service-features li::before {
    content: '✓';
    color: var(--primary-color);
    font-weight: bold;
    font-size: 1.1rem;
}

/* Why Choose Us Section */
.why-choose-section {
    padding: 100px 0;
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.feature-card {
    background: white;
    padding: 35px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, transparent, rgba(14, 159, 110, 0.03), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.feature-card:hover::before {
    transform: translateX(100%);
}

.feature-icon {
    width: 70px;
    height: 70px;
    background: var(--gradient-primary);
    border-radius: 50%;
    margin: 0 auto 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

.feature-card h3 {
    color: var(--primary-dark);
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.feature-card p {
    color: var(--text-color);
    line-height: 1.7;
    font-size: 1rem;
}

/* Portfolio Preview */
.portfolio-section {
    padding: 100px 0;
    background: white;
}

.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.portfolio-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    position: relative;
}

.portfolio-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.portfolio-image {
    height: 200px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
    position: relative;
    overflow: hidden;
}

.portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.portfolio-card:hover .portfolio-overlay {
    opacity: 1;
}

.view-project-btn {
    background: var(--accent-color);
    color: white;
    padding: 12px 25px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    transform: translateY(20px);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.portfolio-card:hover .view-project-btn {
    transform: translateY(0);
}

.portfolio-content {
    padding: 25px;
}

.portfolio-content h3 {
    margin: 0 0 10px 0;
    color: var(--text-dark);
    font-size: 1.3rem;
}

.portfolio-content p {
    color: var(--text-color);
    margin-bottom: 15px;
    line-height: 1.6;
    font-size: 0.95rem;
}

.portfolio-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.portfolio-tag {
    background: var(--bg-light);
    color: var(--primary-dark);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Process Section */
.process-section {
    padding: 100px 0;
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.process-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.process-step {
    text-align: center;
    position: relative;
}

.process-step::after {
    content: '→';
    position: absolute;
    right: -25px;
    top: 40px;
    color: var(--primary-color);
    font-size: 2rem;
    font-weight: bold;
    opacity: 0.5;
}

.process-step:last-child::after {
    display: none;
}

@media (max-width: 1024px) {
    .process-step::after {
        display: none;
    }
}

.step-number {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 auto 25px;
    box-shadow: var(--shadow-md);
}

.step-content h3 {
    color: var(--text-dark);
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.step-content p {
    color: var(--text-color);
    line-height: 1.6;
}

/* Testimonials */
.testimonials-section {
    padding: 100px 0;
    background: white;
}

.testimonial-slider {
    max-width: 800px;
    margin: 50px auto 0;
    position: relative;
}

.testimonial-card {
    background: white;
    padding: 40px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-lg);
    text-align: center;
    border: 1px solid rgba(14, 159, 110, 0.1);
}

.testimonial-text {
    font-size: 1.2rem;
    color: var(--text-color);
    line-height: 1.7;
    margin-bottom: 30px;
    font-style: italic;
}

.testimonial-author {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.author-avatar {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}

.author-info h4 {
    margin: 0 0 5px 0;
    color: var(--text-dark);
    font-size: 1.1rem;
}

.author-info p {
    margin: 0;
    color: var(--text-light);
    font-size: 0.9rem;
}

/* CTA Section */
.cta-section {
    padding: 100px 0;
    background: var(--gradient-primary);
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.cta-content h2 {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 20px;
    color: white;
}

.cta-content p {
    font-size: 1.3rem;
    color: rgba(255, 255, 255, 0.9);
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
    background: white;
    color: var(--primary-color);
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
    background: var(--accent-color);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}

.cta-btn-secondary {
    background: transparent;
    color: white;
    border: 3px solid white;
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
    background: white;
    color: var(--primary-color);
    transform: translateY(-3px);
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

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-title {
        font-size: 3rem;
    }
    
    .section-title {
        font-size: 2.2rem;
    }
    
    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .portfolio-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .homepage-hero {
        padding: 120px 0 80px;
        margin-top: 60px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-description {
        font-size: 1.1rem;
    }
    
    .hero-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .hero-btn-primary,
    .hero-btn-secondary {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
    }
    
    .portfolio-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cta-content h2 {
        font-size: 2.2rem;
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
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-number {
        font-size: 2.8rem;
    }
    
    .service-card {
        padding: 30px 20px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Hero Section -->
<section class="homepage-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?= __('hero_title') ?></h1>
            <p class="hero-description"><?= __('hero_desc') ?></p>
            
            <div class="niger-flag-hero"></div>
            
            <div class="hero-buttons">
                <a href="<?= APP_URL ?>/contact" class="hero-btn-primary">
                    <span>🚀</span>
                    <span><?= __('request_project') ?></span>
                </a>
                <a href="<?= APP_URL ?>/portfolio" class="hero-btn-secondary">
                    <span>👁️</span>
                    <span><?= __('view_work') ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" id="projectsCount"><?= isset($stats['projects']['total']) && $stats['projects']['total'] > 0 ? $stats['projects']['total'] : '0' ?></div>
                <div class="stat-label">Projects Proposed</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="clientsCount"><?= isset($stats['clients']) && $stats['clients'] > 0 ? $stats['clients'] : '0' ?>+</div>
                <div class="stat-label"><?= __('stats_clients') ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="teamCount"><?= isset($stats['team']) && $stats['team'] > 0 ? $stats['team'] : '0' ?></div>
                <div class="stat-label"><?= __('stats_team') ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="satisfactionCount"><?= htmlspecialchars($stats['satisfaction'] ?? '100%') ?></div>
                <div class="stat-label"><?= __('stats_satisfaction') ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2 class="section-title"><?= __('core_services') ?></h2>
        <p class="section-subtitle">
            <?= __('core_services_desc') ?>
        </p>
        
        <div class="services-grid">
            <!-- Custom Software -->
            <div class="service-card">
                <div class="service-icon">🔧</div>
                <h3><?= __('custom_software') ?></h3>
                <p><?= __('custom_software_desc') ?></p>
                
                <ul class="service-features">
                    <li><?= __('feat_biz_auto') ?></li>
                    <li><?= __('feat_erp_crm_short') ?></li>
                    <li><?= __('feat_inv_mgmt_short') ?></li>
                    <li><?= __('feat_rep_analytics_short') ?></li>
                </ul>
            </div>
            
            <!-- E-commerce Platforms -->
            <div class="service-card">
                <div class="service-icon">🛒</div>
                <h3>E-commerce Platforms</h3>
                <p>Build powerful online stores with secure payment processing and inventory management</p>
                
                <ul class="service-features">
                    <li>Custom store design</li>
                    <li>Payment gateway integration</li>
                    <li>Inventory management</li>
                    <li>Mobile commerce ready</li>
                </ul>
            </div>

            <!-- Web Platforms -->
            <div class="service-card">
                <div class="service-icon">🌐</div>
                <h3><?= __('web_platforms') ?></h3>
                <p><?= __('web_platforms_desc') ?></p>
                
                <ul class="service-features">
                    <li><?= __('feat_resp_web_short') ?></li>
                    <li><?= __('feat_ecom_plat_short') ?></li>
                    <li><?= __('feat_cms_short') ?></li>
                    <li><?= __('feat_api_int_short') ?></li>
                </ul>
            </div>
            
            <!-- Mobile Apps -->
            <div class="service-card">
                <div class="service-icon">📱</div>
                <h3><?= __('mobile_apps') ?></h3>
                <p><?= __('mobile_apps_desc') ?></p>
                
                <ul class="service-features">
                    <li><?= __('feat_ios_android_short') ?></li>
                    <li><?= __('feat_cross_plat_short') ?></li>
                    <li><?= __('feat_ui_mobile_short') ?></li>
                    <li><?= __('feat_app_deploy_short') ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-section">
    <div class="container">
        <h2 class="section-title"><?= __('why_choose_us') ?></h2>
        <p class="section-subtitle">
            <?= __('why_subtitle') ?>
        </p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🇳🇪</div>
                <h3><?= __('feature_1_title') ?></h3>
                <p><?= __('feature_1_desc') ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3><?= __('feature_2_title') ?></h3>
                <p><?= __('feature_2_desc') ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3><?= __('feature_3_title') ?></h3>
                <p><?= __('feature_3_desc') ?></p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3><?= __('feature_4_title') ?></h3>
                <p><?= __('feature_4_desc') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Portfolio Preview -->
<section class="portfolio-section">
    <div class="container">
        <h2 class="section-title"><?= __('featured_projects') ?></h2>
        <p class="section-subtitle">
            <?= __('featured_projects_desc') ?>
        </p>
        
        <div class="portfolio-grid">
            <?php if (empty($projects)): ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p><?= __('no_projects_found') ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <div class="portfolio-card">
                        <div class="portfolio-image">
                            <?php if (!empty($project['image_path'])): ?>
                                <img src="<?= APP_URL ?>/<?= htmlspecialchars($project['image_path']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <span><?= (($project['category'] ?? '') == 'mobile') ? '📱' : ((($project['category'] ?? '') == 'ecommerce') ? '🛒' : '💻') ?></span>
                            <?php endif; ?>
                            <div class="portfolio-overlay">
                                <a href="<?= APP_URL ?>/portfolio#<?= strtolower(str_replace(' ', '-', $project['category'] ?? 'project')) ?>" class="view-project-btn">
                                    <span>👁️</span>
                                    <span><?= __('view_project') ?></span>
                                </a>
                            </div>
                        </div>
                        <div class="portfolio-content">
                            <h3><?= htmlspecialchars($project['title']) ?></h3>
                            <p>
                                <?= htmlspecialchars(mb_strimwidth($project['problem'] ?? $project['description'] ?? '', 0, 120, "...")) ?>
                            </p>
                            <div class="portfolio-tags">
                                <?php if (!empty($project['tags'])): ?>
                                    <?php foreach (array_slice(explode(',', $project['tags']), 0, 3) as $tag): ?>
                                        <span class="portfolio-tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="portfolio-tag"><?= htmlspecialchars($project['category'] ?? 'Software') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 50px;">
            <a href="<?= APP_URL ?>/portfolio" class="hero-btn-secondary">
                <span>📁</span>
                <span><?= __('view_all_projects') ?></span>
            </a>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="process-section">
    <div class="container">
        <h2 class="section-title"><?= __('our_process') ?></h2>
        <p class="section-subtitle">
            <?= __('process_subtitle') ?>
        </p>
        
        <div class="process-steps">
            <div class="process-step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3><?= __('process_1_title') ?></h3>
                    <p><?= __('process_1_desc') ?></p>
                </div>
            </div>
            
            <div class="process-step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3><?= __('process_2_title') ?></h3>
                    <p><?= __('process_2_desc') ?></p>
                </div>
            </div>
            
            <div class="process-step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3><?= __('process_3_title') ?></h3>
                    <p><?= __('process_3_desc') ?></p>
                </div>
            </div>
            
            <div class="process-step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3><?= __('process_4_title') ?></h3>
                    <p><?= __('process_4_desc') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2><?= __('cta_text') ?></h2>
            <p><?= __('cta_desc') ?></p>
            
            <div class="cta-buttons">
                <a href="<?= APP_URL ?>/contact" class="cta-btn-primary">
                    <span>📞</span>
                    <span><?= __('cta_btn') ?></span>
                </a>
                <a href="tel:<?= htmlspecialchars(getSetting('contact_phone')) ?>" class="cta-btn-secondary">
                    <span>📱</span>
                    <span><?= __('call_us') ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats counters
    const stats = [
        { element: document.getElementById('projectsCount'), final: <?= isset($stats['projects']['total']) ? $stats['projects']['total'] : 0 ?>, suffix: '' },
        { element: document.getElementById('clientsCount'), final: <?= isset($stats['clients']) ? $stats['clients'] : 0 ?>, suffix: '+' },
        { element: document.getElementById('teamCount'), final: <?= isset($stats['team']) ? $stats['team'] : 0 ?>, suffix: '' },
        { element: document.getElementById('satisfactionCount'), final: parseInt('<?= htmlspecialchars($stats['satisfaction'] ?? '100') ?>', 10), suffix: '%' }
    ];
    
    stats.forEach(stat => {
        if (stat.element) {
            let current = 0;
            const increment = stat.final / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= stat.final) {
                    current = stat.final;
                    clearInterval(timer);
                }
                stat.element.textContent = Math.round(current) + stat.suffix;
            }, 30);
        }
    });
    
    // Animate elements on scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.service-card, .feature-card, .portfolio-card, .process-step, .stat-item');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };
    
    // Set initial states
    document.querySelectorAll('.service-card, .feature-card, .portfolio-card, .process-step').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    // Run on load and scroll
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
    
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
    
    // Portfolio card interactions
    const portfolioCards = document.querySelectorAll('.portfolio-card');
    portfolioCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const overlay = this.querySelector('.portfolio-overlay');
            const btn = this.querySelector('.view-project-btn');
            if (overlay) overlay.style.opacity = '1';
            if (btn) btn.style.transform = 'translateY(0)';
        });
        
        card.addEventListener('mouseleave', function() {
            const overlay = this.querySelector('.portfolio-overlay');
            const btn = this.querySelector('.view-project-btn');
            if (overlay) overlay.style.opacity = '0';
            if (btn) btn.style.transform = 'translateY(20px)';
        });
        
        // Make entire card clickable
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.view-project-btn') && !e.target.closest('a')) {
                const link = this.querySelector('.view-project-btn');
                if (link) {
                    window.location.href = link.href;
                }
            }
        });
    });
    
    // Process step animation
    const processSteps = document.querySelectorAll('.process-step');
    processSteps.forEach((step, index) => {
        step.style.animationDelay = `${index * 0.2}s`;
    });
    

    
    // Add floating animation to Niger flag
    const nigerFlag = document.querySelector('.niger-flag-hero');
    if (nigerFlag) {
        setInterval(() => {
            nigerFlag.style.transform = `translateY(${Math.sin(Date.now() / 1000) * 5}px)`;
        }, 100);
    }
    
    // CTA button animations
    const ctaButtons = document.querySelectorAll('.cta-btn-primary, .cta-btn-secondary');
    ctaButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>