</main>
    
<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-column">
                    <div class="footer-logo">
                        <span class="logo-icon">⚡</span>
                        <span class="logo-text"><?= $site_name ?? 'SahelSoft' ?></span>
                    </div>
                    <p class="footer-description">
                        <?= __('footer_desc') ?>
                    </p>
                                    </div>

                <!-- Quick Links -->
                <div class="footer-column">
                    <h4 class="footer-title"><?= __('quick_links') ?></h4>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>"><?= __('home') ?></a></li>
                        <li><a href="<?= APP_URL ?>/services"><?= __('our_services') ?></a></li>
                        <li><a href="<?= APP_URL ?>/portfolio"><?= __('portfolio') ?></a></li>
                        <li><a href="<?= APP_URL ?>/about"><?= __('about') ?></a></li>
                        <li><a href="<?= APP_URL ?>/contact"><?= __('contact') ?></a></li>
                        <li><a href="<?= APP_URL ?>/help"><?= __('help') ?></a></li>
                        <li><a href="<?= APP_URL ?>/blog">Blog</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="footer-column">
                    <h4 class="footer-title"><?= __('our_services') ?></h4>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>/services#custom-software"><?= __('custom_software') ?></a></li>
                        <li><a href="<?= APP_URL ?>/services#ecommerce-platforms"><?= __('ecommerce_platforms') ?></a></li>
                        <li><a href="<?= APP_URL ?>/services#web-platforms"><?= __('web_platforms') ?></a></li>
                        <li><a href="<?= APP_URL ?>/services#mobile-apps"><?= __('mobile_apps') ?></a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-column">
                    <h4 class="footer-title"><?= __('contact_us') ?></h4>
                    <ul class="footer-contact">
                        <li class="contact-item">
                            <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                            <span><?= __('location_niger') ?><br><small><?= __('location_wa') ?></small></span>
                        </li>
                        <li class="contact-item">
                            <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                            <span>
                                <a href="mailto:<?= htmlspecialchars(getSetting('contact_email', 'sahelsoft38@gmail.com')) ?>"><?= htmlspecialchars(getSetting('contact_email', 'sahelsoft38@gmail.com')) ?></a><br>
                                <small><?= __('response_time') ?></small>
                            </span>
                        </li>
                        <li class="contact-item">
                            <span class="contact-icon"><i class="fas fa-phone-alt"></i></span>
                            <span>
                                <a href="tel:<?= htmlspecialchars(str_replace(' ', '', getSetting('contact_phone'))) ?>"><?= htmlspecialchars(getSetting('contact_phone')) ?></a><br>
                                <small><?= __('work_hours') ?></small>
                            </span>
                        </li>
                    </ul>
                    
                    <!-- Newsletter -->
                    <div class="newsletter">
                        <h5 class="newsletter-title"><?= __('stay_updated') ?></h5>
                        <p class="newsletter-text"><?= __('newsletter_desc') ?></p>
                        <form class="newsletter-form">
                            <div class="newsletter-input-group">
                                <input type="email" placeholder="<?= __('email_placeholder') ?>" required>
                                <button type="submit" class="newsletter-btn"><?= __('subscribe') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flag Divider -->
    <div class="footer-flag">
        <div class="flag-green"></div>
        <div class="flag-white"></div>
        <div class="flag-orange"></div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <div class="copyright">
                    &copy; <?= date('Y') ?> <?= __('copyright') ?>
                </div>
                <div class="footer-legal">
                    <a href="<?= APP_URL ?>/privacy"><?= __('privacy_policy') ?></a>
                    <a href="<?= APP_URL ?>/terms"><?= __('terms_service') ?></a>
                    <a href="<?= APP_URL ?>/cookies"><?= __('cookie_policy') ?></a>
                </div>
            </div>
            
            <!-- Made in Niger badge -->
            <div class="niger-badge">
                <span class="badge-flag">🇳🇪</span>
                <span class="badge-text"><?= __('made_in_niger') ?></span>
            </div>
        </div>
    </div>
</footer>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/<?= htmlspecialchars(preg_replace('/[^0-9]/', '', getSetting('contact_phone'))) ?>?text=Hello%20SahelSoft%2C%20I%20have%20a%20project%20in%20mind!" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="<?= __('whatsapp') ?>">
    <div class="whatsapp-icon">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.149-.67.149-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.123-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </div>
</a>

<style>
.whatsapp-float {
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 40px;
    right: 40px;
    background-color: #25d366;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    animation: bounceIn 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.whatsapp-float:hover {
    transform: scale(1.1);
    background-color: #128c7e;
    box-shadow: 2px 5px 15px rgba(0,0,0,0.4);
}

.whatsapp-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    60% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .whatsapp-float {
        width: 50px;
        height: 50px;
        bottom: 20px;
        right: 20px;
    }
}
</style>

<style>
/* Footer Styles */
.site-footer {
    background: var(--primary-dark);
    color: rgba(255, 255, 255, 0.9);
    margin-top: auto;
}

.footer-top {
    padding: 60px 0 40px;
    background: linear-gradient(135deg, var(--primary-dark) 0%, #083344 100%);
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
}

.footer-column {
    animation: fadeInUp 0.6s ease-out;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    font-size: 1.8rem;
    font-weight: 800;
    color: white;
}

.logo-icon {
    color: var(--accent-color);
    font-size: 2rem;
}

.logo-text {
    background: linear-gradient(135deg, #fff 0%, var(--primary-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.footer-description {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.7;
    margin-bottom: 25px;
    font-size: 0.95rem;
}

.footer-social {
    display: flex;
    gap: 15px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: var(--accent-color);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.social-icon {
    font-size: 1.2rem;
}

.footer-title {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 20px;
    font-weight: 700;
    position: relative;
    padding-bottom: 10px;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: var(--accent-color);
    border-radius: 2px;
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.footer-links a::before {
    content: '→';
    opacity: 0;
    transform: translateX(-5px);
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: var(--accent-color);
    padding-left: 5px;
}

.footer-links a:hover::before {
    opacity: 1;
    transform: translateX(0);
}

.footer-contact {
    list-style: none;
    padding: 0;
    margin-bottom: 30px;
}

.contact-item {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    align-items: flex-start;
}

.contact-icon {
    color: var(--accent-color);
    font-size: 1.2rem;
    margin-top: 2px;
}

.contact-item span:last-child {
    line-height: 1.5;
}

.contact-item a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-item a:hover {
    color: var(--accent-color);
}

.contact-item small {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.85rem;
}

/* Newsletter */
.newsletter {
    background: rgba(255, 255, 255, 0.05);
    padding: 25px;
    border-radius: var(--border-radius);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.newsletter-title {
    color: white;
    font-size: 1.1rem;
    margin-bottom: 10px;
    font-weight: 600;
}

.newsletter-text {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin-bottom: 20px;
    line-height: 1.5;
}

.newsletter-input-group {
    display: flex;
    gap: 10px;
    flex-direction: column;
}

.newsletter-input-group input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--border-radius);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.newsletter-input-group input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.newsletter-input-group input:focus {
    outline: none;
    border-color: var(--accent-color);
    background: rgba(255, 255, 255, 0.15);
}

.newsletter-btn {
    background: var(--gradient-accent);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: var(--border-radius);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.newsletter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Flag Divider */
.footer-flag {
    display: flex;
    height: 8px;
}

.flag-green {
    flex: 1;
    background: var(--primary-color);
}

.flag-white {
    flex: 1;
    background: white;
}

.flag-orange {
    flex: 1;
    background: var(--accent-color);
}

/* Footer Bottom */
.footer-bottom {
    background: rgba(0, 0, 0, 0.2);
    padding: 25px 0;
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.copyright {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
}

.footer-legal {
    display: flex;
    gap: 25px;
}

.footer-legal a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
    position: relative;
}

.footer-legal a:hover {
    color: var(--accent-color);
}

.footer-legal a:not(:last-child)::after {
    content: '•';
    position: absolute;
    right: -15px;
    color: rgba(255, 255, 255, 0.3);
}

/* Niger Badge */
.niger-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.1);
    padding: 10px 20px;
    border-radius: 30px;
    margin-top: 10px;
    animation: pulse 2s infinite;
}

.badge-flag {
    font-size: 1.5rem;
    animation: wave 3s ease-in-out infinite;
}

.badge-text {
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
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
        opacity: 0.8;
    }
}

@keyframes wave {
    0%, 100% {
        transform: rotate(0deg);
    }
    25% {
        transform: rotate(5deg);
    }
    75% {
        transform: rotate(-5deg);
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .footer-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
}

@media (max-width: 768px) {
    .footer-top {
        padding: 40px 0 30px;
    }
    
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .footer-legal {
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }
    
    .footer-legal a:not(:last-child)::after {
        display: none;
    }
    
    .newsletter-form {
        flex-direction: column;
    }
    
    .newsletter-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .footer-logo {
        font-size: 1.5rem;
    }
    
    .footer-title {
        font-size: 1.1rem;
    }
    
    .contact-item {
        flex-direction: column;
        gap: 8px;
    }
    
    .social-link {
        width: 40px;
        height: 40px;
    }
    
    .niger-badge {
        padding: 8px 15px;
        font-size: 0.8rem;
    }
}
</style>

<script>
// Newsletter form submission
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (!email) {
                showNotification('Please enter your email address', 'error');
                return;
            }
            
            if (!validateEmail(email)) {
                showNotification('Please enter a valid email address', 'error');
                return;
            }
            
            // Simulate submission
            emailInput.value = '';
            showNotification('Thank you for subscribing to our newsletter!', 'success');
            
            // In real app, send AJAX request here
            console.log('Newsletter subscription:', email);
        });
    }
    
    // Smooth scroll for footer links
    const footerLinks = document.querySelectorAll('.footer-links a[href^="#"]');
    footerLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId.startsWith('#')) {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
    
    // Add current year
    const yearElement = document.querySelector('.copyright');
    if (yearElement) {
        yearElement.innerHTML = yearElement.innerHTML.replace('<?= date("Y") ?>', new Date().getFullYear());
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === 'success' ? 'var(--primary-color)' : '#ef4444'};
        color: white;
        padding: 15px 25px;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        z-index: 1000;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add CSS for notification animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
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
document.head.appendChild(style);
</script>
</body>
</html>