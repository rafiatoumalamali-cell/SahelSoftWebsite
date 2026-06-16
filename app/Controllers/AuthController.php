<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\TwoFactorAuthService;
use App\Services\AuditService;

class AuthController extends Controller {
    public function login() {
        // If already logged in, redirect based on role
        if (isset($_SESSION['user_id'])) {
            return $this->redirectBasedOnRole($_SESSION['role']);
        }
        
        // Check for query parameter messages (from forgot password flow)
        $data = ['title' => __('page_title_login')];
        
        if (isset($_GET['success']) && $_GET['success'] === 'password_reset_success') {
            $data['success'] = __('password_reset_success') ?? 'Your password has been reset successfully. You can now log in.';
        }
        
        if (isset($_GET['error'])) {
            $errorMessages = [
                'invalid_token' => __('invalid_token') ?? 'Invalid reset link.',
                'invalid_or_expired_token' => __('invalid_token') ?? 'Invalid or expired reset link.',
                'reset_failed' => __('reset_failed') ?? 'Password reset failed. The link may have expired or is invalid.',
            ];
            $data['error'] = $errorMessages[$_GET['error']] ?? 'An error occurred. Please try again.';
        }
        
        return $this->view('auth/login', $data);
    }

    public function loginPost() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Check rate limiting
        $twoFactorService = new TwoFactorAuthService();
        if ($twoFactorService->isRateLimited($email)) {
            return $this->view('auth/login', [
                'title' => __('page_title_login'), 
                'error' => 'Too many failed login attempts. Please try again later.'
            ]);
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && $userModel->verifyPassword($password, $user['password_hash'])) {
            // Check if 2FA is enabled
            if ($twoFactorService->is2FAEnabled($user['id'])) {
                // Store pending login data
                $_SESSION['2fa_pending'] = [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => $user['full_name']
                ];
                
                // Log login attempt requiring 2FA
                $twoFactorService->logLoginAttempt($user['id'], $email, false, true, false);
                
                // Redirect to 2FA verification
                return $this->view('auth/2fa_verify', [
                    'title' => 'Two-Factor Authentication',
                    'email' => $email
                ]);
            }

            // Login Success (no 2FA)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['phone'] = $user['phone'] ?? '';
            $_SESSION['company_name'] = $user['company_name'] ?? '';

            // Log successful login
            $auditService = AuditService::getInstance();
            $auditService->logLogin($user['id'], $user['email'], true);
            $twoFactorService->logLoginAttempt($user['id'], $email, true);

            if ($remember) {
                setcookie('remember_email', $email, time() + (86400 * 30), "/");
            } else {
                setcookie('remember_email', '', time() - 3600, "/");
            }

            return $this->redirectBasedOnRole($user['role']);
        } else {
            // Log failed login attempt
            $auditService = AuditService::getInstance();
            $auditService->logLogin($user['id'] ?? null, $email, false);
            $twoFactorService->logLoginAttempt($user['id'] ?? null, $email, false);
            
            return $this->view('auth/login', [
                'title' => __('page_title_login'), 
                'error' => __('error_invalid_credentials')
            ]);
        }
    }

    public function register() {
         if (isset($_SESSION['user_id'])) {
            return $this->redirectBasedOnRole($_SESSION['role']);
        }
        return $this->view('auth/register', ['title' => __('page_title_register')]);
    }

    public function registerPost() {
        $data = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'phone' => $_POST['phone'] ?? '',
            'company_name' => $_POST['company_name'] ?? '',
            'role' => 'client' 
        ];

        $userModel = new User();
        
        if ($userModel->findByEmail($data['email'])) {
            return $this->view('auth/register', [
                'title' => __('page_title_register'), 
                'error' => __('error_email_registered'),
                'old' => $_POST  
            ]);
        }

        if ($userModel->create($data)) {
            // Log the user in immediately
            $newUser = $userModel->findByEmail($data['email']);
            $_SESSION['user_id'] = $newUser['id'];
            $_SESSION['role'] = $newUser['role'];
            $_SESSION['full_name'] = $newUser['full_name'];
            $_SESSION['email'] = $newUser['email'];
            $_SESSION['phone'] = $newUser['phone'] ?? '';
            $_SESSION['company_name'] = $newUser['company_name'] ?? '';
            
            return $this->redirect('/client/dashboard');
        } else {
            return $this->view('auth/register', [
                'title' => __('page_title_register'), 
                'error' => __('error_reg_failed'),
                'old' => $_POST
            ]);
        }
    }

    public function forgotPassword() {
        return $this->view('auth/forgot_password', ['title' => __('page_title_forgot')]);
    }

    public function forgotPasswordPost() {
        $email = $_POST['email'] ?? '';
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token with expiration
            $pdo = \App\Core\Database::getInstance()->getConnection();
            
            // Delete any existing tokens for this email
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
            
            // Insert new token with expiration
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expiresAt]);
            
            // Generate reset link
            $resetLink = APP_URL . "/reset-password?token=" . $token;
            
            // Send email using EmailService
            $emailService = new \App\Services\EmailService();
            $emailSent = $emailService->sendPasswordReset($email, $resetLink);
            
            // In development mode, also show the link for easy testing
            $devNote = '';
            $showDebug = filter_var($_ENV['EMAIL_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
            if ($showDebug || true) { // Always show debug info while troubleshooting
                $errorInfo = $emailService->getLastError();
                $errorMsg = $errorInfo ? "<br><strong style='color:red;'>Error:</strong> " . htmlspecialchars($errorInfo) : '';
                $devNote = "<br><br><div style='background: #f1f5f9; padding: 10px; border-radius: 5px; font-size: 0.8rem;'><strong>Dev Note:</strong> Reset Link: <a href='$resetLink'>$resetLink</a><br>Email Status: " . ($emailSent ? 'Sent/Logged' : 'Failed') . $errorMsg . "</div>";
            }
            
            return $this->view('auth/forgot_password', [
                'title' => __('page_title_forgot'),
                'success' => str_replace('{email}', htmlspecialchars($email), __('success_forgot_password')) . $devNote
            ]);
        }
        
        // Always show success message to prevent email enumeration attacks
        return $this->view('auth/forgot_password', [
            'title' => __('page_title_forgot'),
            'success' => str_replace('{email}', htmlspecialchars($email), __('success_forgot_password'))
        ]);
    }

    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) return $this->redirect('/login?error=invalid_token');
        
        $pdo = \App\Core\Database::getInstance()->getConnection();
        
        // Clean up expired tokens first
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();
        
        // Get valid token that hasn't been used and hasn't expired
        $stmt = $pdo->prepare("SELECT email, expires_at, used_at FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();
        
        if (!$reset) {
            return $this->redirect('/login?error=invalid_or_expired_token');
        }
        
        return $this->view('auth/reset_password', [
            'title' => __('page_title_reset'),
            'token' => $token,
            'email' => $reset['email']
        ]);
    }

    public function resetPasswordPost() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validate password strength
        if (strlen($password) < 6) {
            return $this->view('auth/reset_password', [
                'title' => __('page_title_reset'),
                'token' => $token,
                'error' => 'Password must be at least 6 characters long.'
            ]);
        }
        
        $pdo = \App\Core\Database::getInstance()->getConnection();
        
        // Get valid token that hasn't been used and hasn't expired
        $stmt = $pdo->prepare("SELECT email, expires_at, used_at FROM password_resets WHERE token = ? AND expires_at > NOW() AND used_at IS NULL");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();
        
        if (!$reset) {
            return $this->redirect('/login?error=invalid_or_expired_token');
        }
        
        $userModel = new \App\Models\User();
        $user = $userModel->findByEmail($reset['email']);
        
        if ($user && $userModel->update($user['id'], ['password' => $password])) {
            // Mark token as used
            $stmt = $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE token = ?");
            $stmt->execute([$token]);
            
            // Log the password reset for security audit
            $auditService = new AuditService();
            $auditService->log($user['id'], 'password_change', 'Password reset via forgot password', $_SERVER['REMOTE_ADDR'] ?? null);
            
            return $this->redirect('/login?success=password_reset_success');
        }
        
        return $this->redirect('/login?error=reset_failed');
    }

    public function logout() {
        session_destroy();
        return $this->redirect('/');
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            return $this->redirect('/login');
        }
        return $this->redirectBasedOnRole($_SESSION['role']);
    }

    private function redirectBasedOnRole($role) {
        switch ($role) {
            case 'admin':
                return $this->redirect('/admin/dashboard');
            case 'project_manager':
            case 'developer':
                return $this->redirect('/team/dashboard');
            case 'client':
                return $this->redirect('/client/dashboard');
            default:
                return $this->redirect('/');
        }
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) return $this->redirect('/login');
        
        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);
        
        return $this->view('profile', [
            'title' => __('my_profile'),
            'user' => $user
        ]);
    }

    public function profileUpdate() {
        if (!isset($_SESSION['user_id'])) return $this->redirect('/login');
        
        $userModel = new User();
        $data = [
            'full_name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'company_name' => $_POST['company_name'] ?? ''
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        if ($userModel->update($_SESSION['user_id'], $data)) {
            // Update session data
            $_SESSION['full_name'] = $data['full_name'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['phone'] = $data['phone'];
            $_SESSION['company_name'] = $data['company_name'];
            
            return $this->redirect('/profile?success=profile_updated');
        } else {
            return $this->redirect('/profile?error=update_failed');
        }
    }
}
