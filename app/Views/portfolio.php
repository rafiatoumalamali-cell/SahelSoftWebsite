<?php 
include VIEW_PATH . '/layouts/header.php'; 
?>

<style>
/* Portfolio Styles */
.portfolio-hero {
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
    padding: 120px 0 80px;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-top: 70px;
}

.portfolio-hero::before {
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
    animation: fadeInUp 0.8s ease-out;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: var(--text-light);
    max-width: 700px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

/* Filters */
.portfolio-filters {
    background: white;
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    margin: 0 auto 50px;
    max-width: 1200px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
}

.filter-btn {
    padding: 10px 25px;
    background: var(--bg-light);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    color: var(--text-color);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.filter-btn:hover {
    background: white;
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-2px);
}

.filter-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

/* Portfolio Grid */
.portfolio-grid-section {
    padding: 50px 0 100px;
    background: white;
}

.portfolio-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 40px;
    margin-bottom: 60px;
}

@media (max-width: 768px) {
    .portfolio-grid {
        grid-template-columns: 1fr;
    }
}

.portfolio-card {
    background: white;
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.4s ease;
    position: relative;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease-out forwards;
}

.portfolio-card.visible {
    opacity: 1;
    transform: translateY(0);
}

.portfolio-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.portfolio-image {
    height: 250px;
    position: relative;
    overflow: hidden;
}

.portfolio-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.portfolio-card:hover .portfolio-image img {
    transform: scale(1.05);
}

.image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.portfolio-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(14, 159, 110, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.portfolio-card:hover .portfolio-overlay {
    opacity: 1;
}

.overlay-content {
    text-align: center;
    padding: 20px;
}

.overlay-content h3 {
    color: white;
    margin-bottom: 15px;
    font-size: 1.5rem;
}

.view-details-btn {
    background: white;
    color: var(--primary-color);
    padding: 12px 30px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.view-details-btn:hover {
    background: var(--accent-color);
    color: white;
    transform: translateY(-3px);
}

.portfolio-content {
    padding: 30px;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.project-header h3 {
    margin: 0;
    color: var(--text-dark);
    font-size: 1.4rem;
    flex: 1;
}

.status-badge {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-client {
    background: #10b981;
    color: white;
}

.status-demo {
    background: #3b82f6;
    color: white;
}

.status-internal {
    background: #8b5cf6;
    color: white;
}

.project-category {
    display: inline-block;
    padding: 4px 12px;
    background: var(--bg-light);
    color: var(--primary-dark);
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 15px;
}

.project-problem {
    background: #fee2e2;
    padding: 15px;
    border-radius: var(--border-radius);
    margin-bottom: 15px;
    border-left: 4px solid #ef4444;
}

.problem-label {
    font-weight: 700;
    color: #991b1b;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.problem-text {
    color: var(--text-color);
    line-height: 1.6;
}

.project-features {
    background: #d1fae5;
    padding: 15px;
    border-radius: var(--border-radius);
    margin-bottom: 15px;
    border-left: 4px solid #10b981;
}

.features-label {
    font-weight: 700;
    color: #065f46;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
}

.features-list li {
    padding: 5px 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.features-list li::before {
    content: '✓';
    color: #10b981;
    font-weight: bold;
}

.project-tech {
    margin: 20px 0;
}

.tech-label {
    font-weight: 700;
    color: var(--primary-dark);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
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
}

.project-footer {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.view-case-btn {
    flex: 1;
    background: var(--primary-color);
    color: white;
    padding: 12px;
    border-radius: var(--border-radius);
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.view-case-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.live-demo-btn {
    flex: 1;
    background: transparent;
    color: var(--primary-color);
    padding: 12px;
    border-radius: var(--border-radius);
    border: 2px solid var(--primary-color);
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-align: center;
}

.live-demo-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-state-icon {
    font-size: 4rem;
    color: var(--border-color);
    margin-bottom: 20px;
}

.empty-state h3 {
    color: var(--text-dark);
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--text-light);
    max-width: 500px;
    margin: 0 auto 30px;
}

/* Portfolio Stats */
.portfolio-stats {
    background: var(--gradient-primary);
    color: white;
    padding: 60px 0;
    margin-top: 60px;
}

.stats-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 3rem;
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

/* Call to Action */
.portfolio-cta {
    padding: 100px 0;
    background: linear-gradient(135deg, rgba(14, 159, 110, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
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

/* Case Study Modal */
.case-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: var(--border-radius-lg);
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    animation: modalSlideIn 0.3s ease;
}

.close-modal {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-light);
    cursor: pointer;
    z-index: 1;
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    padding: 40px;
    color: white;
    position: relative;
    overflow: hidden;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
}

.modal-header-content {
    position: relative;
    z-index: 1;
}

.modal-category {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-bottom: 15px;
    font-weight: 600;
}

.modal-title {
    font-size: 2.2rem;
    font-weight: 800;
    margin-bottom: 15px;
    color: white;
}

.modal-client {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 20px;
}

.modal-body {
    padding: 40px;
}

.modal-section {
    margin-bottom: 40px;
}

.modal-section h3 {
    color: var(--primary-dark);
    margin-bottom: 20px;
    font-size: 1.5rem;
    position: relative;
    padding-bottom: 10px;
}

.modal-section h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

.modal-section p {
    color: var(--text-color);
    line-height: 1.7;
    margin-bottom: 15px;
}

.modal-tech-stack {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 20px 0;
}

.modal-tech-item {
    background: var(--bg-light);
    padding: 10px 20px;
    border-radius: var(--border-radius);
    font-weight: 500;
    color: var(--primary-dark);
    display: flex;
    align-items: center;
    gap: 8px;
}

.modal-images {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.modal-image {
    height: 150px;
    background: var(--bg-light);
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 2rem;
}

.modal-footer {
    padding: 30px 40px;
    background: var(--bg-light);
    border-top: 1px solid var(--border-color);
    text-align: center;
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

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-title {
        font-size: 2.8rem;
    }
    
    .portfolio-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .portfolio-hero {
        padding: 100px 0 60px;
        margin-top: 60px;
    }
    
    .hero-title {
        font-size: 2.2rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .portfolio-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-btn {
        text-align: center;
    }
    
    .portfolio-grid {
        grid-template-columns: 1fr;
    }
    
    .project-footer {
        flex-direction: column;
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
    
    .modal-content {
        max-height: 95vh;
    }
    
    .modal-header {
        padding: 30px 20px;
    }
    
    .modal-body {
        padding: 25px;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 1.8rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .modal-title {
        font-size: 1.8rem;
    }
}
</style>

<!-- Hero Section -->
<section class="portfolio-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title"><?= __('portfolio_title') ?></h1>
            <p class="hero-subtitle">
                <?= __('portfolio_intro') ?>
            </p>
        </div>
    </div>
</section>

<!-- Filters -->
<div class="portfolio-filters">
    <button class="filter-btn active" data-filter="all"><?= __('all_projects') ?></button>
    <button class="filter-btn" data-filter="ecommerce"><?= __('ecommerce_platforms') ?></button>
    <button class="filter-btn" data-filter="web"><?= __('web_platforms') ?></button>
    <button class="filter-btn" data-filter="mobile"><?= __('mobile_apps') ?></button>
    <button class="filter-btn" data-filter="enterprise"><?= __('enterprise_software') ?></button>
    <button class="filter-btn" data-filter="government"><?= __('government') ?></button>
</div>

<!-- Portfolio Grid -->
<section class="portfolio-grid-section">
    <div class="portfolio-container">
            <!-- Project 1 -->
        <div class="portfolio-grid" id="portfolioGrid">
            <?php if (empty($projects)): ?>
                <div class="empty-state" style="display: block; grid-column: 1 / -1;">
                    <div class="empty-state-icon">📂</div>
                    <h3><?= __('no_projects_found') ?></h3>
                    <p><?= __('no_projects_found') ?>. Admin hasn't added any showcase projects yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($projects as $index => $project): ?>
                    <div class="portfolio-card visible" data-category="<?= htmlspecialchars(strtolower($project['category'] ?? 'web')) ?> <?= htmlspecialchars(strtolower($project['tags'] ?? '')) ?>">
                        <div class="portfolio-image">
                            <?php if (!empty($project['image_path'])): ?>
                                <img src="<?= APP_URL ?>/<?= htmlspecialchars($project['image_path']) ?>" alt="<?= htmlspecialchars($project['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="image-placeholder">
                                    <span><?= (($project['category'] ?? '') == 'mobile') ? '📱' : ((($project['category'] ?? '') == 'ecommerce') ? '🛒' : '💻') ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="portfolio-overlay">
                                <div class="overlay-content">
                                    <h3><?= htmlspecialchars($project['title']) ?></h3>
                                    <button class="view-details-btn" onclick="openCaseStudy(<?= $project['id'] ?>)">
                                        <span>👁️</span>
                                        <span><?= __('view_case_study') ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="portfolio-content">
                            <div class="project-header">
                                <h3><?= htmlspecialchars($project['title']) ?></h3>
                                <span class="status-badge status-<?= strtolower($project['status'] ?? 'client') ?>">
                                    <?= htmlspecialchars($project['status'] ?? 'Active') ?>
                                </span>
                            </div>
                            
                            <span class="project-category"><?= htmlspecialchars($project['category'] ?? 'General') ?></span>
                            
                            <div class="project-problem">
                                <div class="problem-label">
                                    <span>📝</span>
                                    <span><?= __('project_overview') ?></span>
                                </div>
                                <p class="problem-text">
                                    <?= htmlspecialchars(mb_strimwidth($project['description'], 0, 150, "...")) ?>
                                </p>
                            </div>
                            
                            <?php if (!empty($project['tags'])): ?>
                            <div class="project-tech">
                                <div class="tech-label">
                                    <span>🛠️</span>
                                    <span><?= __('tech_stack') ?></span>
                                </div>
                                <div class="tech-tags">
                                    <?php foreach (explode(',', $project['tags']) as $tag): ?>
                                        <span class="tech-tag"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="project-footer">
                                <button class="view-case-btn" onclick="openCaseStudy(<?= $project['id'] ?>)">
                                    <span>📖</span>
                                    <span><?= __('view_case_study') ?></span>
                                </button>
                                <?php if (!empty($project['live_url'])): ?>
                                <a href="<?= htmlspecialchars($project['live_url']) ?>" class="live-demo-btn" target="_blank">
                                    <span>🌐</span>
                                    <span><?= __('live_demo') ?></span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Empty State (hidden when projects exist) -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-state-icon">🔍</div>
            <h3><?= __('no_projects_found') ?></h3>
            <p><?= __('no_projects_found') ?>. <?= __('try_different_filter') ?? 'Try selecting a different category or view all projects.' ?></p>
            <button class="filter-btn active" data-filter="all" style="margin-top: 20px;">
                <?= __('view_all_projects') ?>
            </button>
        </div>
    </div>
</section>

<!-- Portfolio Stats -->
<section class="portfolio-stats">
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" id="totalProjects">25+</div>
                <div class="stat-label">Projects Completed</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="satisfiedClients">15</div>
                <div class="stat-label">Happy Clients</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="technologiesUsed">12+</div>
                <div class="stat-label">Technologies</div>
            </div>
            
            <div class="stat-item">
                <div class="stat-number" id="avgSatisfaction">98%</div>
                <div class="stat-label">Client Satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="portfolio-cta">
    <div class="container">
        <div class="cta-content">
            <h2><?= __('have_project_mind') ?></h2>
            <p>
                <?= __('cta_text') ?>
            </p>
            <div class="cta-buttons">
                <a href="<?= APP_URL ?>/contact" class="cta-btn-primary">
                    <span>🚀</span>
                    <span><?= __('start_project_btn') ?></span>
                </a>
                <a href="<?= APP_URL ?>/services" class="cta-btn-secondary">
                    <span>🔧</span>
                    <span><?= __('view_services_btn') ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Case Study Modal -->
<div class="case-modal" id="caseModal">
    <div class="modal-content">
        <button class="close-modal" onclick="closeCaseStudy()">&times;</button>
        
        <div class="modal-header">
            <div class="modal-header-content">
                <span class="modal-category" id="modalCategory">E-commerce</span>
                <h2 class="modal-title" id="modalTitle">Niger E-commerce Platform</h2>
                <p class="modal-client" id="modalClient">Client: Local Retail Business • Niamey, Niger</p>
            </div>
        </div>
        
        <div class="modal-body">
            <div class="modal-section">
                <h3><?= __('project_overview') ?></h3>
                <p id="modalOverview">
                    A comprehensive e-commerce solution developed for a local Nigerien retailer 
                    to transform their traditional brick-and-mortar business into a modern 
                    digital storefront.
                </p>
            </div>
            
            <div class="modal-section" id="challengeSection">
                <h3><?= __('the_challenge') ?></h3>
                <p id="modalChallenge"></p>
            </div>
            
            <div class="modal-section" id="solutionSection">
                <h3><?= __('our_solution') ?></h3>
                <p id="modalSolution"></p>
                
                <div class="modal-images" id="modalImages">
                    <!-- Images will be injected here -->
                </div>
            </div>

            <div class="modal-section" id="resultsSection">
                <h3><?= __('results_impact') ?></h3>
                <p id="modalResults"></p>
            </div>
            
            <div class="modal-section">
                <h3><?= __('tech_stack') ?></h3>
                <div class="modal-tech-stack" id="modalTechStack">
                    <!-- Tech stack will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="modal-section" id="demoVideoSection" style="display: none;">
                <h3>🎬 <?= __('demo_video') ?? 'Demo Video' ?></h3>
                <p style="margin-bottom: 15px; color: var(--text-color);">
                    <?= __('demo_video_desc') ?? 'Watch a demonstration of this project in action:' ?>
                </p>
                <a href="#" id="demoVideoLink" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 10px; background: var(--gradient-accent); color: white; padding: 15px 30px; border-radius: var(--border-radius); text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                    <span style="font-size: 1.5rem;">▶️</span>
                    <span><?= __('click_demo_video') ?? 'Click here to see the demo video of the project' ?></span>
                </a>
            </div>


        </div>
        
        <div class="modal-footer">
            <a href="<?= APP_URL ?>/contact" class="cta-btn-primary" style="display: inline-flex;">
                <span>📞</span>
                <span><?= __('start_similar_project') ?></span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Portfolio filtering
    const portfolioFilterButtons = document.querySelectorAll('.filter-btn');
    const portfolioCards = document.querySelectorAll('.portfolio-card');
    const portfolioEmptyState = document.getElementById('emptyState');
    
    // Dynamic statistics from PHP backend
    const overallStats = {
        projects: '<?php echo $stats['projects_completed']; ?>',
        clients: '<?php echo $stats['happy_clients']; ?>',
        technologies: '<?php echo $stats['technologies']; ?>',
        satisfaction: '<?php echo $stats['client_satisfaction']; ?>'
    };

    // Service-specific statistics (calculated or estimated)
    const serviceStats = {
        all: overallStats,
        ecommerce: {
            projects: '1+',
            clients: '1',
            technologies: '6+',
            satisfaction: '95%'
        },
        web: {
            projects: '1+',
            clients: '1',
            technologies: '8+',
            satisfaction: '97%'
        },
        mobile: {
            projects: '0+',
            clients: '0',
            technologies: '4+',
            satisfaction: '96%'
        },
        enterprise: {
            projects: '0+',
            clients: '0',
            technologies: '9+',
            satisfaction: '99%'
        },
        government: {
            projects: '0+',
            clients: '0',
            technologies: '7+',
            satisfaction: '100%'
        }
    };
    
    portfolioFilterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            portfolioFilterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Update statistics based on selected service
            const stats = serviceStats[filter] || serviceStats.all;
            document.getElementById('totalProjects').textContent = stats.projects;
            document.getElementById('satisfiedClients').textContent = stats.clients;
            document.getElementById('technologiesUsed').textContent = stats.technologies;
            document.getElementById('avgSatisfaction').textContent = stats.satisfaction;
            
            // Re-animate stats with new values
            animateStats(stats);
            
            // Filter cards
            let visibleCount = 0;
            portfolioCards.forEach(card => {
                const categories = card.getAttribute('data-category');
                
                if (filter === 'all' || categories.includes(filter)) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, 10);
                    visibleCount++;
                } else {
                    card.classList.remove('visible');
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
            
            // Show/hide empty state
            if (visibleCount === 0) {
                portfolioEmptyState.style.display = 'block';
            } else {
                portfolioEmptyState.style.display = 'none';
            }
        });
    });
    
    // Animate stats counters function
    function animateStats(statsData) {
        const stats = [
            { element: document.getElementById('totalProjects'), final: parseInt(statsData.projects.replace(/\D/g, '')), suffix: statsData.projects.includes('+') ? '+' : '' },
            { element: document.getElementById('satisfiedClients'), final: parseInt(statsData.clients), suffix: '' },
            { element: document.getElementById('technologiesUsed'), final: parseInt(statsData.technologies.replace(/\D/g, '')), suffix: statsData.technologies.includes('+') ? '+' : '' },
            { element: document.getElementById('avgSatisfaction'), final: parseInt(statsData.satisfaction.replace(/\D/g, '')), suffix: '%' }
        ];
        
        stats.forEach(stat => {
            if (stat.element) {
                let current = 0;
                const increment = stat.final / 30;
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
    }
    
    // Initial stats data from PHP
    const initialStats = [
        { element: document.getElementById('totalProjects'), final: <?php echo (int)$stats['projects_completed']; ?>, suffix: '+' },
        { element: document.getElementById('satisfiedClients'), final: <?php echo (int)$stats['happy_clients']; ?>, suffix: '' },
        { element: document.getElementById('technologiesUsed'), final: <?php echo (int)$stats['technologies']; ?>, suffix: '+' },
        { element: document.getElementById('avgSatisfaction'), final: <?php echo (int)$stats['client_satisfaction']; ?>, suffix: '%' }
    ];
    
    // Initial stats animation
    initialStats.forEach(stat => {
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
    
    // Portfolio card hover effects
    portfolioCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        
        card.addEventListener('mouseleave', function() {
            if (this.style.display !== 'none') {
                this.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Case study data dynamically populated from PHP
    <?php 
    $caseStudiesArray = [];
    foreach ($projects as $project) {
        $caseStudiesArray[$project['id']] = [
            'category' => $project['category'] ?? 'General',
            'title' => $project['title'],
            'client' => $project['client_name'] ? "Client: " . $project['client_name'] : "Showcase Project",
            'overview' => $project['description'],
            'challenge' => $project['problem'] ?? "",
            'solution' => $project['solution'] ?? "",
            'results' => $project['results_impact'] ?? "The project was successfully delivered with high client satisfaction.",
            'techStack' => !empty($project['tags']) ? array_map('trim', explode(',', $project['tags'])) : ['Software', 'Digitalization'],
            'demo_url' => $project['demo_url'] ?? null,
            'dashboard_img' => $project['dashboard_img'] ?? null,
            'product_page_img' => $project['product_page_img'] ?? null,
            'admin_panel_img' => $project['admin_panel_img'] ?? null,
        ];
    }
    ?>
    const caseStudies = <?php echo json_encode($caseStudiesArray); ?>;
    
    // Modal functions
    window.openCaseStudy = function(id) {
        const caseStudy = caseStudies[id];
        if (!caseStudy) return;
        
        // Populate modal content
        document.getElementById('modalCategory').textContent = caseStudy.category;
        document.getElementById('modalTitle').textContent = caseStudy.title;
        document.getElementById('modalClient').textContent = caseStudy.client;
        document.getElementById('modalOverview').textContent = caseStudy.overview;
        document.getElementById('modalChallenge').textContent = caseStudy.challenge || '<?= __('the_challenge_default') ?>';
        document.getElementById('modalSolution').textContent = caseStudy.solution || '<?= __('our_solution_default') ?>';
        document.getElementById('modalResults').textContent = caseStudy.results;
        
        // Handle images injection
        const imagesContainer = document.getElementById('modalImages');
        imagesContainer.innerHTML = '';
        const visuals = [
            { label: 'Dashboard', path: caseStudy.dashboard_img, icon: '📊' },
            { label: 'Product Page', path: caseStudy.product_page_img, icon: '🛍️' },
            { label: 'Admin Panel', path: caseStudy.admin_panel_img, icon: '⚙️' }
        ];

        visuals.forEach(v => {
            if (v.path) {
                const div = document.createElement('div');
                div.className = 'modal-image-container';
                div.style.marginBottom = '25px';
                div.innerHTML = `
                    <div style="margin-bottom: 10px; font-weight: 600; color: var(--primary-dark); display: flex; align-items: center; gap: 8px;">
                        <span>${v.icon}</span> <span>${v.label}</span>
                    </div>
                    <img src="<?= APP_URL ?>/${v.path}" alt="${v.label}" loading="lazy" style="width:100%; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 1px solid #eee;">
                `;
                imagesContainer.appendChild(div);
            }
        });
        
        // Populate tech stack
        const techStackContainer = document.getElementById('modalTechStack');
        techStackContainer.innerHTML = '';
        caseStudy.techStack.forEach(tech => {
            const techItem = document.createElement('div');
            techItem.className = 'modal-tech-item';
            techItem.innerHTML = `<span>💻</span><span>${tech}</span>`;
            techStackContainer.appendChild(techItem);
        });
        
        // Handle demo video section
        const demoVideoSection = document.getElementById('demoVideoSection');
        const demoVideoLink = document.getElementById('demoVideoLink');
        
        if (caseStudy.demo_url && caseStudy.demo_url.trim() !== '') {
            demoVideoLink.href = caseStudy.demo_url;
            demoVideoSection.style.display = 'block';
        } else {
            demoVideoSection.style.display = 'none';
        }
        
        // Show modal
        const modal = document.getElementById('caseModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
    
    window.closeCaseStudy = function() {
        const modal = document.getElementById('caseModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };
    
    // Close modal when clicking outside
    const modal = document.getElementById('caseModal');
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCaseStudy();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCaseStudy();
        }
    });
    
    // Animate portfolio cards on scroll
    const animateCardsOnScroll = function() {
        const cards = document.querySelectorAll('.portfolio-card');
        
        cards.forEach(card => {
            const cardTop = card.getBoundingClientRect().top;
            const cardVisible = 150;
            
            if (cardTop < window.innerHeight - cardVisible && card.style.display !== 'none') {
                card.classList.add('visible');
            }
        });
    };
    
    // Initial animation
    setTimeout(animateCardsOnScroll, 100);
    
    // Animate on scroll
    window.addEventListener('scroll', animateCardsOnScroll);
    
    // Live demo button functionality
    const liveDemoButtons = document.querySelectorAll('.live-demo-btn');
    liveDemoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.textContent.includes('Private') || this.textContent.includes('App Store')) {
                e.preventDefault();
                showNotification('Access information available upon request', 'info');
            }
        });
    });
    
    // Notification system
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? 'var(--primary-color)' : 
                        type === 'error' ? '#ef4444' : '#3b82f6'};
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
});

// Add CSS for animations
const portfolioAnimationStyle = document.createElement('style');
portfolioAnimationStyle.textContent = `
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
`;
document.head.appendChild(portfolioAnimationStyle);
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>