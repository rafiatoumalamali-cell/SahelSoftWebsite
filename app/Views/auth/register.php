<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= __('register_meta_desc') ?? 'Register for a new SahelSoft account to access our professional software solutions.' ?>">
    <title><?= __('page_title_register') ?> - SahelSoft</title>
    <style>
        :root {
            --primary-color: #0e9f6e;
            --accent-color: #f97316;
            --text-dark: #1f2937;
            --bg-color: #f9fafb;
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 2rem;
        }
        
        .header p {
            color: #666;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        .required {
            color: #c62828;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(14, 159, 110, 0.1);
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            color: #666;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 0.85em;
        }
        
        .strength-bars {
            display: flex;
            gap: 4px;
            margin-bottom: 4px;
        }
        
        .strength-bar {
            flex: 1;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
        }
        
        .weak { color: #c62828; }
        .fair { color: #f57c00; }
        .good { color: #fbc02d; }
        .strong { color: #388e3c; }
        .match { color: #388e3c; }
        .mismatch { color: #c62828; }
        
        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            font-size: 0.9em;
            margin-bottom: 20px;
        }
        
        .terms-checkbox input {
            margin-right: 8px;
            margin-top: 3px;
        }
        
        .terms-link {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .terms-link:hover {
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin-bottom: 20px;
        }
        
        .submit-btn:hover {
            background-color: #ea580c;
        }
        
        .submit-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .social-login {
            text-align: center;
            margin-top: 30px;
        }
        
        .social-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .social-btn {
            flex: 1;
            padding: 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .social-btn:hover {
            background-color: #f8f9fa;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .lang-switcher {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .lang-btn {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            background: white;
            font-size: 0.9em;
            transition: all 0.3s;
            opacity: 0.7;
        }

        .lang-btn:hover {
            opacity: 1;
            border-color: var(--primary-color);
        }

        .lang-btn.active {
            opacity: 1;
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="lang-switcher">
        <a href="?lang=en" class="lang-btn <?= getLang() == 'en' ? 'active' : '' ?>">EN</a>
        <a href="?lang=fr" class="lang-btn <?= getLang() == 'fr' ? 'active' : '' ?>">FR</a>
        <a href="?lang=ha" class="lang-btn <?= getLang() == 'ha' ? 'active' : '' ?>">HA</a>
    </div>

    <main class="container">
        <div class="form-container">
            <div class="header">
                <h1><?= __('create_account') ?></h1>
                <p><?= __('register_welcome') ?></p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('/register') ?>" method="POST" id="registerForm">
                <?= csrf_field() ?>
                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name">
                        <?= __('form_name') ?> <span class="required">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" required 
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                           placeholder="<?= __('name_placeholder') ?>">
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        <?= __('form_email') ?> <span class="required">*</span>
                    </label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="<?= __('email_placeholder') ?>">
                    <small style="color: #666; font-size: 0.85em; display: block; margin-top: 4px;">
                        <?= __('email_hint') ?>
                    </small>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone"><?= __('form_phone') ?></label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                           placeholder="<?= __('placeholder_phone') ?>">
                </div>
                
                <!-- Company -->
                <div class="form-group">
                    <label for="company_name"><?= __('company_opt') ?></label>
                    <input type="text" id="company_name" name="company_name" 
                           value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>"
                           placeholder="<?= __('company_placeholder') ?>">
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">
                        <?= __('password') ?> <span class="required">*</span>
                    </label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            👁️
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bars">
                            <div class="strength-bar" id="strengthBar1"></div>
                            <div class="strength-bar" id="strengthBar2"></div>
                            <div class="strength-bar" id="strengthBar3"></div>
                            <div class="strength-bar" id="strengthBar4"></div>
                        </div>
                        <div id="passwordCriteria"><?= __('password_criteria') ?></div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm_password">
                        <?= __('confirm_password') ?> <span class="required">*</span>
                    </label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="toggle-password" id="toggleConfirmPassword">
                            👁️
                        </button>
                    </div>
                    <div id="passwordMatch"></div>
                </div>

                <!-- Terms -->
                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        <?= __('agree_terms') ?> <a href="<?= base_url('/terms') ?>" class="terms-link"><?= __('terms_of_service') ?></a> 
                        <?= __('and') ?> <a href="<?= base_url('/privacy') ?>" class="terms-link"><?= __('privacy_policy') ?></a>
                    </label>
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" class="submit-btn" id="submitBtn">
                    Create Account
                </button>
            </form>
            
            <!-- Already have account -->
            <div class="login-link">
                <p><?= __('already_account') ?> 
                    <a href="<?= base_url('/login') ?>"><?= __('login_link') ?></a>
                </p>
            </div>

            <!-- Social Login -->
            <div class="social-login">
                <p style="margin-bottom: 15px; color: #666; font-size: 0.9em;"><?= __('register_with') ?></p>
                <div class="social-buttons">
                    <button type="button" class="social-btn" onclick="socialLogin('google')">
                        <span>G</span>
                        <span>Google</span>
                    </button>
                    <button type="button" class="social-btn" onclick="socialLogin('github')">
                        <span>G</span>
                        <span>GitHub</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.textContent = '🙈';
            } else {
                passwordField.type = 'password';
                this.textContent = '👁️';
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmField = document.getElementById('confirm_password');
            if (confirmField.type === 'password') {
                confirmField.type = 'text';
                this.textContent = '🙈';
            } else {
                confirmField.type = 'password';
                this.textContent = '👁️';
            }
        });

        // Password strength
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const bars = [
                document.getElementById('strengthBar1'),
                document.getElementById('strengthBar2'),
                document.getElementById('strengthBar3'),
                document.getElementById('strengthBar4')
            ];
            const criteria = document.getElementById('passwordCriteria');
            
            // Reset
            bars.forEach(bar => bar.style.background = '#e0e0e0');
            
            // Check strength
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Color bars
            const colors = ['#c62828', '#f57c00', '#fbc02d', '#388e3c'];
            for (let i = 0; i < strength; i++) {
                bars[i].style.background = colors[i];
            }
            
            // Update text
            const strengthText = [
                '<?= __('pass_very_weak') ?>', 
                '<?= __('pass_weak') ?>', 
                '<?= __('pass_fair') ?>', 
                '<?= __('pass_strong_text') ?>', 
                '<?= __('pass_very_strong') ?>'
            ];
            criteria.textContent = strengthText[strength];
            criteria.className = ['weak', 'fair', 'good', 'strong'][strength - 1] || '';
            
            checkPasswordMatch();
        });

        // Password match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (!confirm) {
                matchDiv.textContent = '';
                return;
            }
            
            if (password === confirm) {
                matchDiv.textContent = '✓ <?= __('passwords_match') ?>';
                matchDiv.className = 'match';
            } else {
                matchDiv.textContent = '✗ <?= __('passwords_not_match') ?>';
                matchDiv.className = 'mismatch';
            }
        }

        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;
            const submitBtn = document.getElementById('submitBtn');
            
            // Basic validation
            if (password.length < 8) {
                alert('<?= __('password_too_short') ?>');
                e.preventDefault();
                return false;
            }
            
            if (password !== confirm) {
                alert('<?= __('passwords_not_match_alert') ?>');
                e.preventDefault();
                return false;
            }
            
            if (!terms) {
                alert('<?= __('agree_terms_alert') ?>');
                e.preventDefault();
                return false;
            }
            
            // Disable button during submission
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating Account...';
            
            return true;
        });

        function socialLogin(provider) {
            alert('Social login with ' + provider + ' would be implemented here');
            // window.location.href = '/auth/' + provider;
        }
    </script>
</body>
</html>