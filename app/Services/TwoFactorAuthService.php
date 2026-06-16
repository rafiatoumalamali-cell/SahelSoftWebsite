<?php

namespace App\Services;

class TwoFactorAuthService {
    private $pdo;

    public function __construct() {
        $this->pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }

    public function generateSecretKey() {
        // Generate a 16-byte base32 encoded secret
        $secret = '';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[random_int(0, 31)];
        }
        return $secret;
    }

    public function enable2FA($userId, $secretKey) {
        try {
            // Check if 2FA already exists
            $stmt = $this->pdo->prepare("SELECT id FROM user_2fa WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            
            if ($stmt->fetch()) {
                // Update existing record
                $sql = "UPDATE user_2fa SET secret_key = :secret_key, enabled = TRUE, updated_at = NOW() WHERE user_id = :user_id";
            } else {
                // Insert new record
                $sql = "INSERT INTO user_2fa (user_id, secret_key, enabled, created_at) VALUES (:user_id, :secret_key, TRUE, NOW())";
            }
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['user_id' => $userId, 'secret_key' => $secretKey]);
        } catch (\Exception $e) {
            error_log('Failed to enable 2FA: ' . $e->getMessage());
            return false;
        }
    }

    public function disable2FA($userId) {
        try {
            $sql = "UPDATE user_2fa SET enabled = FALSE, updated_at = NOW() WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['user_id' => $userId]);
        } catch (\Exception $e) {
            error_log('Failed to disable 2FA: ' . $e->getMessage());
            return false;
        }
    }

    public function is2FAEnabled($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT enabled FROM user_2fa WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch();
            
            return $result && $result['enabled'];
        } catch (\Exception $e) {
            error_log('Failed to check 2FA status: ' . $e->getMessage());
            return false;
        }
    }

    public function getSecretKey($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT secret_key FROM user_2fa WHERE user_id = :user_id AND enabled = TRUE");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch();
            
            return $result ? $result['secret_key'] : null;
        } catch (\Exception $e) {
            error_log('Failed to get secret key: ' . $e->getMessage());
            return null;
        }
    }

    public function verifyCode($userId, $code) {
        try {
            $secretKey = $this->getSecretKey($userId);
            if (!$secretKey) {
                return false;
            }

            // Get current time window
            $timeWindow = $this->getTimeWindow(time());
            
            // Check current time window and adjacent windows (±1)
            for ($i = -1; $i <= 1; $i++) {
                $calculatedCode = $this->calculateCode($secretKey, $timeWindow + $i);
                if ($calculatedCode === $code) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            error_log('Failed to verify 2FA code: ' . $e->getMessage());
            return false;
        }
    }

    public function generateBackupCodes($count = 10) {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = $this->generateBackupCode();
        }
        return $codes;
    }

    public function setBackupCodes($userId, $codes) {
        try {
            $sql = "UPDATE user_2fa SET backup_codes = :backup_codes, updated_at = NOW() WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'backup_codes' => json_encode($codes)
            ]);
        } catch (\Exception $e) {
            error_log('Failed to set backup codes: ' . $e->getMessage());
            return false;
        }
    }

    public function verifyBackupCode($userId, $code) {
        try {
            $stmt = $this->pdo->prepare("SELECT backup_codes FROM user_2fa WHERE user_id = :user_id AND enabled = TRUE");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch();
            
            if (!$result || !$result['backup_codes']) {
                return false;
            }
            
            $backupCodes = json_decode($result['backup_codes'], true);
            
            if (in_array($code, $backupCodes)) {
                // Remove used backup code
                $remainingCodes = array_diff($backupCodes, [$code]);
                $this->setBackupCodes($userId, array_values($remainingCodes));
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log('Failed to verify backup code: ' . $e->getMessage());
            return false;
        }
    }

    public function getQRCodeUrl($userId, $appName = 'SahelSoft') {
        try {
            $stmt = $this->pdo->prepare("SELECT u.email FROM users u JOIN user_2fa 2fa ON u.id = 2fa.user_id WHERE u.id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $user = $stmt->fetch();
            
            if (!$user || !$this->is2FAEnabled($userId)) {
                return null;
            }
            
            $secretKey = $this->getSecretKey($userId);
            $email = urlencode($user['email']);
            $appName = urlencode($appName);
            $secret = urlencode($secretKey);
            
            return "otpauth://totp/{$appName}:{$email}?secret={$secret}&issuer={$appName}";
        } catch (\Exception $e) {
            error_log('Failed to generate QR code URL: ' . $e->getMessage());
            return null;
        }
    }

    public function generateQRCode($userId, $appName = 'SahelSoft') {
        $qrUrl = $this->getQRCodeUrl($userId, $appName);
        
        if (!$qrUrl) {
            return null;
        }
        
        // Use Google Charts API to generate QR code
        $qrUrl = urlencode($qrUrl);
        $size = 200;
        $chartUrl = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$qrUrl}&choe=UTF-8";
        
        return $chartUrl;
    }

    private function getTimeWindow($timestamp) {
        return floor($timestamp / 30); // 30-second time windows
    }

    private function calculateCode($secretKey, $timeWindow) {
        // Simple TOTP implementation (for production, use a proper library)
        $secret = base32_decode($secretKey);
        $time = pack('N*', $timeWindow);
        $hash = hash_hmac('sha1', $time, $secret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $binary = unpack('N', substr($hash, $offset, 4))[1];
        $binary = $binary & 0x7FFFFFFF;
        return $binary % 1000000;
    }

    private function generateBackupCode() {
        return sprintf('%06d', random_int(0, 999999));
    }

    public function logLoginAttempt($userId, $email, $success, $twoFactorRequired = false, $twoFactorVerified = false) {
        try {
            $sql = "INSERT INTO login_attempts (user_id, email, ip_address, user_agent, success, two_factor_required, two_factor_verified, created_at) 
                    VALUES (:user_id, :email, :ip_address, :user_agent, :success, :two_factor_required, :two_factor_verified, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'user_id' => $userId,
                'email' => $email,
                'ip_address' => $this->getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                'success' => $success,
                'two_factor_required' => $twoFactorRequired,
                'two_factor_verified' => $twoFactorVerified
            ]);
        } catch (\Exception $e) {
            error_log('Failed to log login attempt: ' . $e->getMessage());
            return false;
        }
    }

    public function getRecentFailedAttempts($email, $minutes = 15) {
        try {
            $sql = "SELECT COUNT(*) as attempts FROM login_attempts 
                    WHERE email = :email AND success = FALSE 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['email' => $email, 'minutes' => $minutes]);
            $result = $stmt->fetch();
            
            return $result ? (int)$result['attempts'] : 0;
        } catch (\Exception $e) {
            error_log('Failed to get failed attempts: ' . $e->getMessage());
            return 0;
        }
    }

    public function isRateLimited($email) {
        $failedAttempts = $this->getRecentFailedAttempts($email);
        return $failedAttempts >= 5; // 5 failed attempts in 15 minutes
    }

    private function getClientIp() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
}

// Helper function for base32 decoding
if (!function_exists('base32_decode')) {
    function base32_decode($secret) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $secret = str_replace(['=', ' '], '', $secret);
        
        $bits = '';
        foreach (str_split($secret) as $char) {
            if (($index = strpos($alphabet, $char)) !== false) {
                $bits .= sprintf('%05d', decbin($index));
            }
        }
        
        $bytes = '';
        for ($i = 0; $i + 8 <= strlen($bits); $i += 8) {
            $bytes .= chr(bindec(substr($bits, $i, 8)));
        }
        
        return $bytes;
    }
}
