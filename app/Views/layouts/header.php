<?php
// Refresh session data if missing keys but logged in
if (isset($_SESSION['user_id']) && (!isset($_SESSION['email']) || empty($_SESSION['email']))) {
    $userModel = new \App\Models\User();
    $user = \App\Core\Database::getInstance()->getConnection()->query("SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'])->fetch();
    if ($user) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'] ?? '';
        $_SESSION['company_name'] = $user['company_name'] ?? '';
        $_SESSION['full_name'] = $user['full_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?= getLang() ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $meta_description ?? 'SahelSoft - Leading software development company in Niger. We build professional web applications, mobile apps, and custom enterprise solutions tailored to your business needs.' ?>">
    <meta name="keywords" content="<?= $meta_keywords ?? 'software development Niger, web design Niamey, SahelSoft, mobile app development, e-commerce Niger, custom software West Africa' ?>">
    <meta name="author" content="SahelSoft">
    
    <!-- Open Graph Meta Tags for Social Sharing -->
    <meta property="og:title" content="<?= $title ?? 'SahelSoft' ?> | Professional Software Solutions">
    <meta property="og:description" content="<?= $meta_description ?? 'Transforming businesses with innovative digital solutions. Niger\'s premier software house.' ?>">
    <meta property="og:image" content="<?= APP_URL ?>/images/sahelsoft-og.jpg">
    <meta property="og:url" content="<?= APP_URL ?><?= $_SERVER['REQUEST_URI'] ?? '/' ?>">
    <meta property="og:site_name" content="SahelSoft">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $title ?? 'SahelSoft' ?> | Professional Software Solutions">
    <meta name="twitter:description" content="<?= $meta_description ?? 'Nigerien software development company providing world-class digital services.' ?>">
    <meta name="twitter:image" content="<?= APP_URL ?>/images/sahelsoft-twitter.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= APP_URL ?>/images/apple-touch-icon.png">
    <link rel="manifest" href="<?= APP_URL ?>/site.webmanifest">
    
    <title><?= isset($title) ? $title . ' | SahelSoft' : 'SahelSoft - Professional Software Solutions in Niger' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Preconnect to improve performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "SahelSoft",
        "url": "<?= APP_URL ?>",
        "logo": "<?= APP_URL ?>/images/logo.png",
        "description": "Nigerien software development company providing web development, e-commerce platforms, and custom software solutions",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Niamey",
            "addressCountry": "NE"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+227-XX-XX-XX-XX",
            "contactType": "customer service",
            "email": "sahelsoft38@gmail.com"
        }
    }
    </script>
    
    <!-- Theme Initialization Script (Prevents FOUC) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    
    <!-- Additional styles for header -->
    <style>
    /* Header Styles */
    .site-header {
        background: white;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
        border-bottom: 6px solid rgba(236, 157, 11, 1);
        border-top: 6px solid rgba(236, 157, 11, 1);
        transition: all 0.3s ease;
    }

    .site-header.scrolled {
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
        border-bottom: 2px solid var(--primary-color);
        border-top-width: 0;
    }

    .site-header.scrolled .header-container {
        padding: 4px 15px;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 15px;
        max-width: 1350px;
        margin: 0 auto;
    }

    /* Logo */
    .brand-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1.8rem;
        color: var(--primary-dark);
        transition: transform 0.3s ease;
    }

    .brand-logo:hover {
        transform: scale(1.02);
    }

    .logo-icon {
        background: var(--gradient-primary);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 15px rgba(14, 159, 110, 0.2);
    }

    .logo-text {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .logo-text span {
        color: var(--accent-color);
    }

    /* Navigation */
    .main-nav {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 5px;
        margin: 0;
        padding: 0;
    }

    .nav-item {
        position: relative;
    }

    .nav-link {
        text-decoration: none;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 0.9rem;
        padding: 10px 14px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        position: relative;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 8px;
        left: 18px;
        right: 18px;
        height: 2px;
        background: var(--gradient-primary);
        border-radius: 1px;
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .nav-link:hover {
        color: var(--primary-color);
        background: rgba(14, 159, 110, 0.05);
    }

    .nav-link:hover::after {
        transform: scaleX(1);
    }

    .nav-link.active {
        color: var(--primary-color);
        background: rgba(14, 159, 110, 0.1);
    }

    .nav-link.active::after {
        transform: scaleX(1);
    }

    /* Dropdown for services */
    .nav-item.has-dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        min-width: 220px;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 15px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        z-index: 1000;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .dropdown-item {
        display: block;
        padding: 12px 25px;
        color: var(--text-color);
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .dropdown-item:hover {
        color: var(--primary-color);
        background: rgba(14, 159, 110, 0.05);
        padding-left: 30px;
    }

    .dropdown-item i {
        width: 20px;
        text-align: center;
        margin-right: 10px;
        color: var(--primary-color);
    }

    /* Auth Buttons */
    .auth-buttons {
        display: flex;
        gap: 8px;
        margin-left: 10px;
    }

    .btn-login {
        padding: 10px 18px;
        background: transparent;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }

    .btn-login:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(14, 159, 110, 0.2);
    }

    .btn-dashboard {
        padding: 10px 18px;
        background: var(--gradient-accent);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-dashboard:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2);
        color: white;
    }

    /* User Menu */
    .user-menu {
        position: relative;
        margin-left: 10px;
    }

    .user-toggle {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: rgba(14, 159, 110, 0.05);
        border: none;
    }

    .user-toggle:hover {
        background: rgba(14, 159, 110, 0.1);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        background: var(--gradient-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .user-name {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9rem;
    }

    .user-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        min-width: 200px;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        padding: 10px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        z-index: 1000;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .user-menu:hover .user-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .user-dropdown a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: var(--text-color);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .user-dropdown a:hover {
        color: var(--primary-color);
        background: rgba(14, 159, 110, 0.05);
    }

    .user-dropdown a i {
        width: 20px;
        text-align: center;
        color: var(--primary-color);
    }

    /* Notification Dropdown Styles */
    .notification-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        width: 320px;
        border-radius: 12px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.3s ease;
        z-index: 1001;
        border: 1px solid rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .notification-dropdown.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(5px);
    }

    .notification-header {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-light);
    }

    .notification-content {
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f9fafb;
        transition: background 0.2s ease;
        text-decoration: none;
        display: block;
        color: var(--text-color);
    }

    .notification-item:hover {
        background: #f9fafb;
    }

    .notification-item.unread {
        background: rgba(14, 159, 110, 0.03);
    }

    .notification-item h4 {
        margin: 0 0 5px 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-dark);
    }

    .notification-item p {
        margin: 0 0 5px 0;
        font-size: 0.85rem;
        line-height: 1.4;
        color: var(--text-light);
    }

    .notification-time {
        font-size: 0.75rem;
        color: var(--text-light);
    }

    .no-notifications {
        padding: 30px 20px;
        text-align: center;
        color: var(--text-light);
        font-size: 0.9rem;
    }

    /* Language Switcher */
    .language-switcher {
        margin-left: 15px;
        border-left: 1px solid rgba(0, 0, 0, 0.1);
        padding-left: 15px;
    }

    .lang-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        background: var(--bg-light);
        border: none;
        border-radius: 8px;
        color: var(--text-dark);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .lang-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }

    .lang-flag {
        font-size: 1.1rem;
    }

    /* Mobile Menu Toggle */
    .mobile-toggle {
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-dark);
        cursor: pointer;
        padding: 5px;
        margin-left: 15px;
    }

    /* Mobile Menu */
    @media (max-width: 1024px) {
        .mobile-toggle {
            display: block;
        }

        .main-nav {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            padding: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 20px 20px;
            transform: translateY(-100%);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .main-nav.active {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .nav-menu {
            flex-direction: column;
            gap: 5px;
            width: 100%;
        }

        .nav-link {
            padding: 15px;
            justify-content: space-between;
        }

        .dropdown-menu {
            position: static;
            opacity: 1;
            visibility: visible;
            transform: none;
            box-shadow: none;
            border: none;
            padding-left: 20px;
            display: none;
        }

        .nav-item.has-dropdown:hover .dropdown-menu,
        .nav-item.has-dropdown.active .dropdown-menu {
            display: block;
        }

        .auth-buttons {
            margin: 20px 0 0 0;
            flex-direction: column;
        }

        .user-menu {
            margin: 10px 0;
        }

        .language-switcher {
            margin: 10px 0;
            border-left: none;
            padding-left: 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            padding-top: 10px;
        }
    }

    /* Header Animation */
    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .site-header {
        animation: slideDown 0.5s ease;
    }

    /* Active page indicator */
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    $pages = [
        'index.php' => 'home',
        'services.php' => 'services',
        'portfolio.php' => 'portfolio',
        'about.php' => 'about',
        'contact.php' => 'contact'
    ];
    ?>
    
    .nav-link[href*="<?= array_search('home', $pages) ?>"],
    .nav-link[href*="/"]:not([href*="dashboard"]):not([href*="login"]):not([href*="logout"]) {
        <?= (empty($_GET) || $_SERVER['REQUEST_URI'] == APP_URL . '/') ? 'color: var(--primary-color); background: rgba(14, 159, 110, 0.1);' : '' ?>
    }

    <?php foreach ($pages as $page => $text): ?>
        <?php if ($page != 'index.php'): ?>
            .nav-link[href*="<?= $page ?>"] {
                <?= strpos($_SERVER['REQUEST_URI'], $text) !== false ? 'color: var(--primary-color); background: rgba(14, 159, 110, 0.1);' : '' ?>
            }
        <?php endif; ?>
    <?php endforeach; ?>

    /* Back to Top Button */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: var(--gradient-primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 999;
        box-shadow: 0 8px 25px rgba(14, 159, 110, 0.3);
        border: none;
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .back-to-top:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(14, 159, 110, 0.4);
        color: white;
    }

    [data-theme="dark"] .site-header.scrolled {
        background: rgba(15, 23, 42, 0.95) !important;
    }
    </style>
</head>
<body>
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>
    
    <header class="site-header">
        <div class="header-container">
            <!-- Logo -->
            <a href="<?= APP_URL ?>/" class="brand-logo">
                <div class="logo-icon">⚡</div>
                <div class="logo-text">Sahel<span>Soft</span></div>
            </a>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-toggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Navigation -->
            <nav class="main-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/" class="nav-link">
                            <i class="fas fa-home"></i>
                            <span><?= __('home') ?></span>
                        </a>
                    </li>
                    
                    <li class="nav-item has-dropdown">
                        <a href="<?= APP_URL ?>/services" class="nav-link">
                            <i class="fas fa-code"></i>
                            <span><?= __('services') ?></span>
                            <i class="fas fa-chevron-down" style="margin-left: auto; font-size: 0.8rem;"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="<?= APP_URL ?>/services#custom-software" class="dropdown-item">
                                <i class="fas fa-code"></i>
                                <?= __('custom_software') ?>
                            </a>
                            <a href="<?= APP_URL ?>/services#ecommerce-platforms" class="dropdown-item">
                                <i class="fas fa-shopping-cart"></i>
                                <?= __('ecommerce_platforms') ?>
                            </a>
                            <a href="<?= APP_URL ?>/services#web-platforms" class="dropdown-item">
                                <i class="fas fa-globe"></i>
                                <?= __('web_platforms') ?>
                            </a>
                            <a href="<?= APP_URL ?>/services#mobile-apps" class="dropdown-item">
                                <i class="fas fa-mobile-alt"></i>
                                <?= __('mobile_apps') ?>
                            </a>
                        </div>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/portfolio" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            <span><?= __('portfolio') ?></span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/about" class="nav-link">
                            <i class="fas fa-info-circle"></i>
                            <span><?= __('about') ?></span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/contact" class="nav-link">
                            <i class="fas fa-envelope"></i>
                            <span><?= __('contact') ?></span>
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id']) && in_array($_SESSION['role'], ['admin', 'project_manager', 'developer'])): ?>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/team/messages" class="nav-link">
                            <i class="fas fa-comments"></i>
                            <span>Messages</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/help" class="nav-link">
                            <i class="fas fa-question-circle"></i>
                            <span><?= __('help') ?></span>
                        </a>
                    </li>
                </ul>

                <!-- Auth Section -->
                <div class="auth-buttons">
                    <!-- Theme Toggle (Accessible to all) -->
                    <div class="header-action-item">
                        <button class="action-btn" id="theme-toggle" title="<?= __('theme_toggle_dark') ?>">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-menu">
                            <!-- Notification Placeholder -->
                            <div class="header-action-item notification-wrapper" style="display: flex; align-items: center;">
                                <button class="action-btn" id="notification-btn" title="<?= __('notifications') ?>" style="position: relative; background: transparent; border: none; padding: 8px; cursor: pointer; font-size: 1.2rem; color: var(--text-dark);">
                                    <i class="fas fa-bell"></i>
                                    <span class="notification-badge" style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: white; font-size: 0.65rem; padding: 2px 5px; border-radius: 10px; font-weight: 700; min-width: 16px; text-align: center; border: 2px solid white; line-height: 1;">0</span>
                                </button>
                                <div class="notification-dropdown" id="notification-dropdown">
                                    <div class="notification-header">
                                        <?= __('notifications') ?>
                                    </div>
                                    <div class="notification-content">
                                        <p class="no-notifications"><?= __('no_notifications') ?></p>
                                    </div>
                                </div>
                            </div>

                            <button class="user-toggle">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="user-name"><?= $_SESSION['full_name'] ?? 'User' ?></span>
                                <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                            </button>
                            <div class="user-dropdown">
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <a href="<?= APP_URL ?>/admin/dashboard">
                                        <i class="fas fa-cog"></i>
                                        <span>Admin Dashboard</span>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= APP_URL ?>/dashboard">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span><?= __('dashboard') ?></span>
                                </a>
                                <a href="<?= APP_URL ?>/<?= $_SESSION['role'] == 'admin' ? 'admin/invoices' : 'client/invoices' ?>">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span>Invoices</span>
                                </a>
                                <a href="<?= APP_URL ?>/profile">
                                    <i class="fas fa-user"></i>
                                    <span>My Profile</span>
                                </a>
                                <a href="<?= APP_URL ?>/team/messages">
                                    <i class="fas fa-envelope"></i>
                                    <span>Messages</span>
                                </a>
                                <a href="<?= APP_URL ?>/logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span><?= __('logout') ?></span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i>
                            <?= __('login') ?>
                        </a>
                        <a href="<?= APP_URL ?>/register" class="btn-dashboard">
                            <i class="fas fa-user-plus"></i>
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Language Switcher - Direct Buttons -->
                <div class="language-switcher" style="display: flex; gap: 5px; border: none; padding-left: 10px;">
                    <a href="<?= getLangUrl('en') ?>" class="lang-btn <?= getLang() == 'en' ? 'active' : '' ?>" style="padding: 5px 10px; <?= getLang() == 'en' ? 'background: var(--primary-color); color: white;' : '' ?>">EN</a>
                    <a href="<?= getLangUrl('fr') ?>" class="lang-btn <?= getLang() == 'fr' ? 'active' : '' ?>" style="padding: 5px 10px; <?= getLang() == 'fr' ? 'background: var(--primary-color); color: white;' : '' ?>">FR</a>
                    <a href="<?= getLangUrl('ha') ?>" class="lang-btn <?= getLang() == 'ha' ? 'active' : '' ?>" style="padding: 5px 10px; <?= getLang() == 'ha' ? 'background: var(--primary-color); color: white;' : '' ?>">HA</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Main Content -->
    <main id="main-content">
    
    <script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileToggle = document.querySelector('.mobile-toggle');
        const mainNav = document.querySelector('.main-nav');
        const body = document.body;
        
        // Toggle mobile menu
        mobileToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.main-nav') && !event.target.closest('.mobile-toggle')) {
                mainNav.classList.remove('active');
                body.style.overflow = '';
            }
        });
        
        // Close mobile menu when clicking on a link
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                mainNav.classList.remove('active');
                body.style.overflow = '';
            });
        });
        
        // Header scroll effect
        const header = document.querySelector('.site-header');
        let lastScroll = 0;
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            // Add/remove scrolled class
            if (currentScroll > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            // Hide/show header on scroll
            if (currentScroll > lastScroll && currentScroll > 100) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            
            lastScroll = currentScroll;
        });
        
        // Language switcher dropdown
        const langBtn = document.querySelector('.lang-btn');
        const langDropdown = langBtn?.nextElementSibling;
        
        if (langBtn && langDropdown) {
            langBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                langDropdown.style.display = langDropdown.style.display === 'block' ? 'none' : 'block';
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                langDropdown.style.display = 'none';
            });
            
            langDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // User dropdown
        const userToggle = document.querySelector('.user-toggle');
        const userDropdown = document.querySelector('.user-dropdown');
        
        if (userToggle && userDropdown) {
            userToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                userDropdown.style.display = 'none';
            });
            
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // Services dropdown for mobile
        const serviceDropdown = document.querySelector('.nav-item.has-dropdown');
        if (window.innerWidth <= 1024) {
            serviceDropdown?.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown-menu')) {
                    e.preventDefault();
                    this.classList.toggle('active');
                }
            });
        }
        
        // Highlight current page
        const currentPath = window.location.pathname;
        const navLinksAll = document.querySelectorAll('.nav-link');
        
        navLinksAll.forEach(link => {
            const linkPath = new URL(link.href).pathname;
            if (currentPath === linkPath || 
                (currentPath === '/' && link.href.includes('/') && !link.href.includes('dashboard') && !link.href.includes('login'))) {
                link.classList.add('active');
            }
        });
        
        // Skip link focus
        const skipLink = document.querySelector('.skip-link');
        skipLink.addEventListener('click', function(e) {
            e.preventDefault();
            const mainContent = document.getElementById('main-content');
            if (mainContent) {
                mainContent.setAttribute('tabindex', '-1');
                mainContent.focus();
                setTimeout(() => mainContent.removeAttribute('tabindex'), 1000);
            }
        });
    });

    // Dark Mode Toggle Logic
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;
    
    if (themeToggle) {
        // Initial icon state
        const currentTheme = localStorage.getItem('theme') || 'light';
        updateThemeIcon(currentTheme);

        themeToggle.addEventListener('click', () => {
            const theme = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            updateThemeIcon(theme);
        });
    }

    function updateThemeIcon(theme) {
        const icon = themeToggle.querySelector('i');
        if (theme === 'dark') {
            icon.className = 'fas fa-sun';
            themeToggle.title = "<?= __('theme_toggle_light') ?>";
        } else {
            icon.className = 'fas fa-moon';
            themeToggle.title = "<?= __('theme_toggle_dark') ?>";
        }
    }

    // Notification Dropdown Toggle
    const notificationBtn = document.getElementById('notification-btn');
    const notificationDropdown = document.getElementById('notification-dropdown');

    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
            if (notificationDropdown.classList.contains('active')) {
                fetchNotifications();
            }
        });

        document.addEventListener('click', (e) => {
            if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });
    }

    function fetchNotifications() {
        fetch('<?= APP_URL ?>/admin/notifications/recent')
            .then(response => response.json())
            .then(data => {
                const container = document.querySelector('.notification-content');
                const badge = document.querySelector('.notification-badge');
                
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }

                if (data.notifications && data.notifications.length > 0) {
                    container.innerHTML = data.notifications.map(n => `
                        <a href="${n.link || '#'}" class="notification-item ${n.is_read == 0 ? 'unread' : ''}" onclick="markAsRead(${n.id})">
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: ${n.is_read == 0 ? 'var(--primary-color)' : '#e5e7eb'}; margin-top: 6px; flex-shrink: 0;"></div>
                                <div>
                                    <h4 style="${n.is_read == 0 ? 'font-weight: 700;' : 'font-weight: 500; opacity: 0.7;'}">${n.title}</h4>
                                    <p style="${n.is_read == 0 ? '' : 'opacity: 0.7;'}">${n.message}</p>
                                    <span class="notification-time">${new Date(n.created_at).toLocaleDateString()}</span>
                                </div>
                            </div>
                        </a>
                    `).join('');
                    
                    // Add footer link
                    container.innerHTML += `
                        <a href="<?= APP_URL ?>/admin/notifications" style="display: block; padding: 12px; text-align: center; border-top: 1px solid #f0f0f0; font-size: 0.85rem; font-weight: 600; color: var(--primary-color); text-decoration: none;">
                            View All Notifications
                        </a>
                    `;
                } else {
                    container.innerHTML = '<p class="no-notifications"><?= __("no_notifications") ?></p>';
                }
            });
    }

    function markAsRead(id) {
        const formData = new FormData();
        formData.append('id', id);
        fetch('<?= APP_URL ?>/admin/notifications/mark-read', {
            method: 'POST',
            body: formData
        });
    }

    // Initial fetch for badge count
    if (document.getElementById('notification-btn')) {
        setInterval(() => {
            fetch('<?= APP_URL ?>/admin/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                });
        }, 30000); // Check every 30 seconds
    }
    
    
    // Back to Top Logic
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
        
        // Compact header on scroll
        const header = document.querySelector('.site-header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    </script>
</body>
</html>