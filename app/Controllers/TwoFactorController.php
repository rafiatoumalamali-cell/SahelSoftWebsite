<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\TwoFactorAuthService;
use App\Services\AuditService;

class TwoFactorController extends Controller {
    private $twoFactorService;
    private $auditService;

    public function __construct() {
        $this->twoFactorService = new TwoFactorAuthService();
        $this->auditService = AuditService::getInstance();
    }

    public function setup() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'];
        $is2FAEnabled = $this->twoFactorService->is2FAEnabled($userId);

        if ($is2FAEnabled) {
            $_SESSION['error'] = '2FA is already enabled for your account.';
            return $this->redirect('/profile');
        }

        // Generate new secret key
        $secretKey = $this->twoFactorService->generateSecretKey();
        
        // Store secret key temporarily in session
        $_SESSION['2fa_secret'] = $secretKey;
        
        // Generate QR code
        $qrCodeUrl = $this->twoFactorService->generateQRCode($userId);
        
        // Generate backup codes
        $backupCodes = $this->twoFactorService->generateBackupCodes();
        $_SESSION['2fa_backup_codes'] = $backupCodes;

        return $this->view('auth/2fa_setup', [
            'title' => 'Enable Two-Factor Authentication',
            'secretKey' => $secretKey,
            'qrCodeUrl' => $qrCodeUrl,
            'backupCodes' => $backupCodes
        ]);
    }

    public function enable() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/2fa/setup');
        }

        $userId = $_SESSION['user_id'];
        $code = $_POST['code'] ?? '';

        if (empty($code)) {
            $_SESSION['error'] = 'Verification code is required.';
            return $this->redirect('/2fa/setup');
        }

        // Verify the code
        $secretKey = $_SESSION['2fa_secret'] ?? '';
        if (empty($secretKey)) {
            $_SESSION['error'] = 'Setup session expired. Please try again.';
            return $this->redirect('/2fa/setup');
        }

        // Temporarily enable 2FA to verify code
        $this->twoFactorService->enable2FA($userId, $secretKey);
        
        if ($this->twoFactorService->verifyCode($userId, $code)) {
            // Save backup codes
            $backupCodes = $_SESSION['2fa_backup_codes'] ?? [];
            if (!empty($backupCodes)) {
                $this->twoFactorService->setBackupCodes($userId, $backupCodes);
            }
            
            // Log the action
            $this->auditService->logSecurityEvent('2fa_enabled', ['user_id' => $userId]);
            
            // Clean up session
            unset($_SESSION['2fa_secret'], $_SESSION['2fa_backup_codes']);
            
            $_SESSION['success'] = 'Two-factor authentication has been enabled for your account.';
            return $this->redirect('/profile');
        } else {
            // Disable 2FA since verification failed
            $this->twoFactorService->disable2FA($userId);
            
            $_SESSION['error'] = 'Invalid verification code. Please try again.';
            return $this->redirect('/2fa/setup');
        }
    }

    public function verify() {
        if (!isset($_SESSION['2fa_pending']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $userId = $_SESSION['2fa_pending']['user_id'];
        $email = $_SESSION['2fa_pending']['email'];
        $code = $_POST['code'] ?? '';
        $useBackup = $_POST['use_backup'] ?? '';

        if (empty($code)) {
            $_SESSION['error'] = 'Verification code is required.';
            return $this->view('auth/2fa_verify', [
                'title' => 'Two-Factor Authentication',
                'email' => $email
            ]);
        }

        $success = false;
        
        if ($useBackup) {
            // Verify backup code
            $success = $this->twoFactorService->verifyBackupCode($userId, $code);
            if ($success) {
                $this->auditService->logSecurityEvent('2fa_backup_used', ['user_id' => $userId]);
            }
        } else {
            // Verify TOTP code
            $success = $this->twoFactorService->verifyCode($userId, $code);
        }

        if ($success) {
            // Complete login
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = $_SESSION['2fa_pending']['role'];
            $_SESSION['full_name'] = $_SESSION['2fa_pending']['full_name'];
            
            // Log successful login with 2FA
            $this->auditService->logLogin($userId, $email, true);
            $this->twoFactorService->logLoginAttempt($userId, $email, true, true, true);
            
            // Clean up pending session
            unset($_SESSION['2fa_pending']);
            
            // Redirect to intended destination
            $redirectTo = $_SESSION['redirect_after_login'] ?? '/dashboard';
            unset($_SESSION['redirect_after_login']);
            
            $_SESSION['success'] = 'Login successful!';
            return $this->redirect($redirectTo);
        } else {
            // Log failed 2FA attempt
            $this->auditService->logSecurityEvent('2fa_failed', [
                'user_id' => $userId,
                'email' => $email,
                'use_backup' => $use_backup
            ]);
            
            $this->twoFactorService->logLoginAttempt($userId, $email, false, true, false);
            
            $_SESSION['error'] = 'Invalid verification code. Please try again.';
            return $this->view('auth/2fa_verify', [
                'title' => 'Two-Factor Authentication',
                'email' => $email
            ]);
        }
    }

    public function disable() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/profile');
        }

        $userId = $_SESSION['user_id'];
        $password = $_POST['password'] ?? '';

        if (empty($password)) {
            $_SESSION['error'] = 'Password is required to disable 2FA.';
            return $this->redirect('/profile');
        }

        // Verify password
        $userModel = new \App\Models\User();
        $user = $userModel->find($userId);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Invalid password.';
            return $this->redirect('/profile');
        }

        if ($this->twoFactorService->disable2FA($userId)) {
            $this->auditService->logSecurityEvent('2fa_disabled', ['user_id' => $userId]);
            $_SESSION['success'] = 'Two-factor authentication has been disabled for your account.';
        } else {
            $_SESSION['error'] = 'Failed to disable 2FA. Please try again.';
        }

        return $this->redirect('/profile');
    }

    public function showBackupCodes() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'];
        
        if (!$this->twoFactorService->is2FAEnabled($userId)) {
            $_SESSION['error'] = '2FA is not enabled for your account.';
            return $this->redirect('/profile');
        }

        // Generate new backup codes
        $backupCodes = $this->twoFactorService->generateBackupCodes();
        
        return $this->view('auth/2fa_backup_codes', [
            'title' => '2FA Backup Codes',
            'backupCodes' => $backupCodes
        ]);
    }

    public function regenerateBackupCodes() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/2fa/backup-codes');
        }

        $userId = $_SESSION['user_id'];
        $password = $_POST['password'] ?? '';

        if (empty($password)) {
            $_SESSION['error'] = 'Password is required to regenerate backup codes.';
            return $this->redirect('/2fa/backup-codes');
        }

        // Verify password
        $userModel = new \App\Models\User();
        $user = $userModel->find($userId);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Invalid password.';
            return $this->redirect('/2fa/backup-codes');
        }

        // Generate and save new backup codes
        $backupCodes = $this->twoFactorService->generateBackupCodes();
        if ($this->twoFactorService->setBackupCodes($userId, $backupCodes)) {
            $this->auditService->logSecurityEvent('2fa_backup_codes_regenerated', ['user_id' => $userId]);
            $_SESSION['success'] = 'Backup codes have been regenerated.';
        } else {
            $_SESSION['error'] = 'Failed to regenerate backup codes.';
        }

        return $this->view('auth/2fa_backup_codes', [
            'title' => '2FA Backup Codes',
            'backupCodes' => $backupCodes
        ]);
    }
}
