<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
/* About Page Styles */
.about-hero {
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.1) 0%, rgba(255, 255, 255, 0.95) 100%);
    padding: 120px 0 80px;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-top: 70px;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(14, 159, 110, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.1) 0%, transparent 50%);
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

.niger-flag {
    height: 8px;
    width: 200px;
    background: linear-gradient(90deg, var(--primary-color) 0%, white 33%, var(--accent-color) 66%);
    margin: 30px auto;
    border-radius: 4px;
    animation: float 3s ease-in-out infinite;
}

/* Mission & Vision Section */
.mission-vision {
    padding: 100px 0;
    background: white;
}

.section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
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

.mission-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 40px;
    margin-bottom: 80px;
}

.mission-card {
    background: white;
    padding: 40px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(14, 159, 110, 0.1);
}

.mission-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.mission-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--gradient-primary);
}

.mission-card:nth-child(2)::before {
    background: var(--gradient-accent);
}

.mission-icon {
    font-size: 3rem;
    margin-bottom: 20px;
    color: var(--primary-color);
}

.mission-card h3 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: var(--text-dark);
}

.mission-text {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--text-color);
    margin-bottom: 20px;
}

/* Values Section */
.values-section {
    padding: 100px 0;
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.value-card {
    background: white;
    padding: 40px 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.value-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
}

.value-card::before {
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

.value-card:hover::before {
    transform: translateX(100%);
}

.value-icon {
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
    transition: all 0.3s ease;
}

.value-card:hover .value-icon {
    transform: scale(1.1) rotate(5deg);
}

.value-card h3 {
    color: var(--primary-dark);
    margin-bottom: 15px;
    font-size: 1.4rem;
}

.value-card p {
    color: var(--text-color);
    line-height: 1.7;
    font-size: 1rem;
}

/* Team Section */
.team-section {
    padding: 100px 0;
    background: white;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    margin-top: 50px;
}

.team-card {
    background: white;
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    position: relative;
}

.team-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.team-header {
    padding: 40px 30px 30px;
    text-align: center;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    position: relative;
}

.team-avatar {
    width: 120px;
    height: 120px;
    background: white;
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 3rem;
    font-weight: 700;
    border: 5px solid white;
    box-shadow: var(--shadow-md);
}

.team-name {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.team-role {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

.team-body {
    padding: 30px;
}

.team-description {
    color: var(--text-color);
    line-height: 1.7;
    margin-bottom: 25px;
    font-size: 1rem;
}

.team-skills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 25px;
}

.skill-tag {
    background: var(--bg-light);
    color: var(--primary-dark);
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.team-contact {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.contact-link {
    width: 40px;
    height: 40px;
    background: var(--bg-light);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    text-decoration: none;
    transition: all 0.3s ease;
}

.contact-link:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-3px);
}

/* Journey Section */
.journey-section {
    padding: 100px 0;
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
    position: relative;
    overflow: hidden;
}

.journey-timeline {
    max-width: 800px;
    margin: 60px auto 0;
    position: relative;
}

.journey-timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--gradient-primary);
    border-radius: 2px;
}

.timeline-item {
    display: flex;
    margin-bottom: 50px;
    position: relative;
}

.timeline-year {
    background: var(--gradient-primary);
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    flex-shrink: 0;
    margin-right: 30px;
    position: relative;
    z-index: 1;
    box-shadow: var(--shadow-md);
}

.timeline-content {
    background: white;
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    flex: 1;
    border-left: 4px solid var(--primary-color);
}

.timeline-content h4 {
    color: var(--primary-dark);
    margin-bottom: 10px;
    font-size: 1.3rem;
}

.timeline-content p {
    color: var(--text-color);
    line-height: 1.6;
}

/* Stats Section */
.stats-section {
    padding: 80px 0;
    background: var(--gradient-primary);
    color: white;
    text-align: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    max-width: 1000px;
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

/* CTA Section */
.cta-section {
    padding: 100px 0;
    background: white;
    text-align: center;
}

.cta-content {
    max-width: 700px;
    margin: 0 auto;
}

.cta-content h2 {
    font-size: 2.5rem;
    color: var(--text-dark);
    margin-bottom: 20px;
}

.cta-content p {
    font-size: 1.1rem;
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

.btn-primary-lg {
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

.btn-primary-lg:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    color: white;
}

.btn-secondary-lg {
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

.btn-secondary-lg:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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
        font-size: 2.8rem;
    }
    
    .section-title {
        font-size: 2.2rem;
    }
    
    .mission-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .about-hero {
        padding: 100px 0 60px;
        margin-top: 60px;
    }
    
    .hero-title {
        font-size: 2.2rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .mission-card {
        padding: 30px;
    }
    
    .values-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .team-grid {
        grid-template-columns: 1fr;
    }
    
    .journey-timeline::before {
        left: 20px;
    }
    
    .timeline-year {
        width: 50px;
        height: 50px;
        font-size: 1rem;
        margin-right: 20px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-primary-lg,
    .btn-secondary-lg {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .values-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-number {
        font-size: 2.8rem;
    }
    
    .team-header {
        padding: 30px 20px 25px;
    }
    
    .team-avatar {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
    }
}
</style>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?= __('about_title') ?></h1>
            <p class="hero-subtitle">
                <?= __('hero_subtitle') ?>
            </p>
            <div class="niger-flag"></div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="mission-vision">
    <div class="section-container">
        <div class="mission-grid">
            <div class="mission-card">
                <div class="mission-icon">🎯</div>
                <h3><?= __('our_mission') ?></h3>
                <p class="mission-text">
                    <?= __('mission_text_full') ?>
                </p>
                <p style="color: var(--primary-color); font-weight: 600;">
                    <?= __('mission_quote') ?>
                </p>
            </div>
            
            <div class="mission-card">
                <div class="mission-icon">🚀</div>
                <h3><?= __('our_vision') ?></h3>
                <p class="mission-text">
                    <?= __('vision_text') ?>
                </p>
                <p style="color: var(--accent-color); font-weight: 600;">
                    <?= __('vision_quote') ?>
                </p>
            </div>
        </div>
        
        <h2 class="section-title"><?= __('our_journey') ?></h2>
        <div class="journey-timeline">
            <div class="timeline-item">
                <div class="timeline-year">2024</div>
                <div class="timeline-content">
                    <h4><?= __('journey_1_title') ?></h4>
                    <p><?= __('journey_1_text') ?></p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2025</div>
                <div class="timeline-content">
                    <h4><?= __('journey_2_title') ?></h4>
                    <p><?= __('journey_2_text') ?></p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-year">2026</div>
                <div class="timeline-content">
                    <h4><?= __('journey_3_title') ?></h4>
                    <p><?= __('journey_3_text') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="values-section">
    <div class="section-container">
        <h2 class="section-title"><?= __('core_values_title') ?></h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🏆</div>
                <h3><?= __('val_excellence') ?></h3>
                <p><?= __('val_excellence_desc') ?></p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3><?= __('val_collaboration') ?></h3>
                <p><?= __('val_collaboration_desc') ?></p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">💡</div>
                <h3><?= __('val_innovation') ?></h3>
                <p><?= __('val_innovation_desc') ?></p>
            </div>
            
            <div class="value-card">
                <div class="value-icon">🇳🇪</div>
                <h3><?= __('val_impact') ?></h3>
                <p><?= __('val_impact_desc') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= $stats['projects'] ?></div>
                <div class="stat-label"><?= __('stats_projects') ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number"><?= $stats['clients'] ?></div>
                <div class="stat-label"><?= __('stats_clients') ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number"><?= $stats['satisfaction'] ?></div>
                <div class="stat-label"><?= __('stats_satisfaction') ?></div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number"><?= $stats['team'] ?></div>
                <div class="stat-label"><?= __('stats_team') ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="section-container">
        <h2 class="section-title"><?= __('meet_team') ?></h2>
        <p style="text-align: center; max-width: 800px; margin: 0 auto 50px; color: var(--text-light); font-size: 1.1rem;">
            <?= __('team_intro') ?>
        </p>
        
        <div class="team-grid">
            <!-- Team Member 1 -->
            <div class="team-card">
                <div class="team-header">
                    <div class="team-avatar">R</div>
                    <h3 class="team-name"><?= __('team_member_1_name') ?></h3>
                    <p class="team-role"><?= __('team_member_1_role') ?></p>
                </div>
                <div class="team-body">
                    <p class="team-description">
                        <?= __('team_member_1_desc') ?> <?= __('team_bio_rafiatou') ?>
                    </p>
                    
                    <div class="team-skills">
                        <span class="skill-tag">PHP/Laravel</span>
                        <span class="skill-tag">Project Management</span>
                        <span class="skill-tag">System Architecture</span>
                        <span class="skill-tag">Business Analysis</span>
                    </div>
                    
                    <div class="team-contact">
                        <a href="mailto:sahelsoft38@gmail.com" class="contact-link" title="Email">
                            <span>✉️</span>
                        </a>
                        <a href="#" class="contact-link" title="LinkedIn">
                            <span>💼</span>
                        </a>
                        <a href="#" class="contact-link" title="GitHub">
                            <span>💻</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Team Member 2 -->
            <div class="team-card">
                <div class="team-header">
                    <div class="team-avatar">F</div>
                    <h3 class="team-name"><?= __('team_member_2_name') ?></h3>
                    <p class="team-role"><?= __('team_member_2_role') ?></p>
                </div>
                <div class="team-body">
                    <p class="team-description">
                        <?= __('team_member_2_desc') ?> <?= __('team_bio_fannareme') ?>
                    </p>
                    
                    <div class="team-skills">
                        <span class="skill-tag">React/Vue.js</span>
                        <span class="skill-tag">UI/UX Design</span>
                        <span class="skill-tag">Frontend Development</span>
                        <span class="skill-tag">Mobile Apps</span>
                    </div>
                    
                    <div class="team-contact">
                        <a href="#" class="contact-link" title="Email">
                            <span>✉️</span>
                        </a>
                        <a href="#" class="contact-link" title="Dribbble">
                            <span>🎨</span>
                        </a>
                        <a href="#" class="contact-link" title="GitHub">
                            <span>💻</span>
                        </a>
                    </div>
                </div>
            </div>     
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2><?= __('cta_transform_title') ?></h2>
            <p>
                <?= __('cta_transform_text') ?>
            </p>
            <div class="cta-buttons">
                <a href="<?= APP_URL ?>/contact" class="btn-primary-lg">
                    <span>📞</span>
                    <span><?= __('hero_btn_primary') ?></span>
                </a>
                <a href="<?= APP_URL ?>/portfolio" class="btn-secondary-lg">
                    <span>👁️</span>
                    <span><?= __('view_work') ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate elements on scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.mission-card, .value-card, .team-card, .timeline-item, .stat-item');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.style.animation = 'fadeInUp 0.6s ease-out';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };
    
    // Set initial states
    document.querySelectorAll('.mission-card, .value-card, .team-card, .timeline-item, .stat-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });
    
    // Run on load and scroll
    animateOnScroll();
    window.addEventListener('scroll', animateOnScroll);
    
    // Animate stats counter
    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        let currentValue = 0;
        const increment = finalValue / 50;
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            stat.textContent = Math.round(currentValue) + (stat.textContent.includes('%') ? '%' : '+');
        }, 50);
    });
    
    // Team card hover effect enhancement
    const teamCards = document.querySelectorAll('.team-card');
    teamCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Add Nigeria flag animation to Niger badge
    const nigerFlag = document.querySelector('.niger-flag');
    if (nigerFlag) {
        setInterval(() => {
            nigerFlag.style.transform = `translateY(${Math.sin(Date.now() / 1000) * 5}px)`;
        }, 100);
    }
    
    // Value cards wave animation
    const valueCards = document.querySelectorAll('.value-card');
    valueCards.forEach((card, index)