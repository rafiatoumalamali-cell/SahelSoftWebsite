<?php

namespace App\Services;

// Load PHPMailer from lib folder
require_once __DIR__ . '/../../lib/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Email Service with PHPMailer SMTP support
 * Loads configuration from .env file
 */
class EmailService {
    private $fromEmail;
    private $fromName;
    private $replyToEmail;
    private $debugMode;
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $smtpEncryption;
    private $useSMTP;
    private $lastError = '';

    public function __construct() {
        // Load .env file
        $this->loadEnv();
        
        $this->fromEmail = $_ENV['SMTP_FROM'] ?? 'sahelsoft38@gmail.com';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'SahelSoft';
        $this->replyToEmail = $_ENV['SMTP_REPLY_TO'] ?? 'sahelsoft38@gmail.com';
        $this->debugMode = filter_var($_ENV['EMAIL_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
        
        // SMTP Configuration
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? '';
        $this->smtpPort = intval($_ENV['SMTP_PORT'] ?? 587);
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? '';
        // Remove spaces from app password if present
        $this->smtpPassword = str_replace(' ', '', $_ENV['SMTP_PASSWORD'] ?? '');
        $this->smtpEncryption = $_ENV['SMTP_ENCRYPTION'] ?? 'tls';
        
        // Enable SMTP if all required fields are set
        $this->useSMTP = !empty($this->smtpHost) && 
                         !empty($this->smtpUsername) && 
                         !empty($this->smtpPassword) &&
                         $this->smtpUsername !== 'your-email@gmail.com';
    }
    
    /**
     * Load environment variables from .env file
     */
    private function loadEnv() {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments and empty lines
                if (strpos($line, '#') === 0 || trim($line) === '') continue;
                if (strpos($line, '=') === false) continue;
                
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Only set if not already in $_ENV
                if (!isset($_ENV[$key]) && !isset($_SERVER[$key])) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }

    /**
     * Send email using PHPMailer with SMTP or fallback to mail()
     */
    public function sendEmail($to, $subject, $body, $attachments = [], $cc = [], $bcc = []) {
        // Always log for audit trail
        $this->logEmail($to, $subject, $body, $attachments, $cc, $bcc, 'attempt');
        
        // If debug mode is on and no SMTP configured, just log
        if ($this->debugMode && !$this->useSMTP) {
            $this->logEmail($to, $subject, $body, $attachments, $cc, $bcc, 'logged (debug mode)');
            return true;
        }

        try {
            $mail = new PHPMailer(true);
            
            // Use SMTP if configured
            if ($this->useSMTP) {
                $mail->isSMTP();
                $mail->Host = $this->smtpHost;
                $mail->Port = $this->smtpPort;
                $mail->SMTPAuth = true;
                $mail->Username = $this->smtpUsername;
                $mail->Password = $this->smtpPassword;
                $mail->SMTPSecure = $this->smtpEncryption;
                
                // Enable SMTP debugging to see connection details
                $mail->SMTPDebug = 2; // 0 = off, 1 = client, 2 = client and server
            }
            
            // Set sender
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addReplyTo($this->replyToEmail);
            
            // Add recipient
            $mail->addAddress($to);
            
            // Add CC recipients
            foreach ($cc as $ccEmail) {
                $mail->addCC($ccEmail);
            }
            
            // Add BCC recipients
            foreach ($bcc as $bccEmail) {
                $mail->addBCC($bccEmail);
            }
            
            // Add attachments
            foreach ($attachments as $attachment) {
                if (is_array($attachment)) {
                    $mail->addAttachment($attachment['path'], $attachment['name'] ?? '');
                } else {
                    $mail->addAttachment($attachment);
                }
            }
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->CharSet = 'UTF-8';
            
            // Send email
            $success = $mail->send();
            
            if ($success) {
                $this->logEmail($to, $subject, $body, $attachments, $cc, $bcc, 'sent via SMTP');
            } else {
                $this->logEmail($to, $subject, $body, $attachments, $cc, $bcc, 'failed: ' . $mail->ErrorInfo);
            }
            
            return $success;
            
        } catch (\Exception $e) {
            // Log error and fallback to mail() if SMTP fails
            $errorMsg = $e->getMessage();
            $this->logEmail($to, $subject, $body, $attachments, $cc, $bcc, 'SMTP error: ' . $errorMsg);
            
            // Also store error for display
            $this->lastError = $errorMsg;
            
            // Fallback to mail() for production reliability
            return $this->sendViaMail($to, $subject, $body, $cc, $bcc);
        }
    }
    
    /**
     * Fallback email sending using PHP mail() function
     */
    private function sendViaMail($to, $subject, $body, $cc = [], $bcc = []) {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $this->fromName . ' <' . $this->fromEmail . '>';
        $headers[] = 'Reply-To: ' . $this->replyToEmail;
        
        if (!empty($cc)) {
            $headers[] = 'Cc: ' . implode(', ', $cc);
        }
        
        if (!empty($bcc)) {
            $headers[] = 'Bcc: ' . implode(', ', $bcc);
        }

        $headersString = implode("\r\n", $headers);
        $success = mail($to, $subject, $body, $headersString);
        
        $status = $success ? 'sent via mail()' : 'failed (mail)';
        $this->logEmail($to, $subject, $body, [], $cc, $bcc, $status);
        
        return $success;
    }

    /**
     * Log email for debugging purposes
     */
    private function logEmail($to, $subject, $body, $attachments = [], $cc = [], $bcc = [], $status = 'logged') {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'to' => $to,
            'subject' => $subject,
            'cc' => $cc,
            'bcc' => $bcc,
            'attachments' => count($attachments),
            'status' => $status,
            'body_preview' => substr(strip_tags($body), 0, 200)
        ];
        
        $logFile = __DIR__ . '/../../writable/email_log.txt';
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset($email, $resetLink) {
        $subject = 'Password Reset Request';
        $body = $this->getEmailTemplate('password_reset', [
            'reset_link' => $resetLink,
            'email' => $email
        ]);
        
        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail($email, $fullName) {
        $subject = 'Welcome to SahelSoft';
        $body = $this->getEmailTemplate('welcome', [
            'full_name' => $fullName,
            'email' => $email,
            'login_url' => 'http://localhost/SahelSoftWebsite/public/login'
        ]);
        
        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Send notification email
     */
    public function sendNotification($email, $title, $message, $data = []) {
        $subject = $title;
        $body = $this->getEmailTemplate('notification', array_merge([
            'title' => $title,
            'message' => $message,
            'email' => $email
        ], $data));
        
        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Get email template
     */
    private function getEmailTemplate($template, $data = []) {
        $templates = [
            'password_reset' => '
                <html>
                <head><title>Password Reset</title></head>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Hello,</p>
                    <p>You requested a password reset for your SahelSoft account.</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href="{reset_link}">Reset Password</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you did not request this, please ignore this email.</p>
                    <p>Best regards,<br>SahelSoft Team</p>
                </body>
                </html>
            ',
            'welcome' => '
                <html>
                <head><title>Welcome to SahelSoft</title></head>
                <body>
                    <h2>Welcome to SahelSoft!</h2>
                    <p>Hello {full_name},</p>
                    <p>Thank you for registering with SahelSoft Business Management System.</p>
                    <p>Your account has been created successfully.</p>
                    <p>You can now log in using your email and password.</p>
                    <p><a href="{login_url}">Login to Your Account</a></p>
                    <p>If you have any questions, please contact our support team.</p>
                    <p>Best regards,<br>SahelSoft Team</p>
                </body>
                </html>
            ',
            'notification' => '
                <html>
                <head><title>{title}</title></head>
                <body>
                    <h2>{title}</h2>
                    <p>Hello,</p>
                    <p>{message}</p>
                    <p>This is an automated notification from SahelSoft.</p>
                    <p>Best regards,<br>SahelSoft Team</p>
                </body>
                </html>
            '
        ];

        $template = $templates[$template] ?? $templates['notification'];
        
        // Replace placeholders
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        
        return $template;
    }

    /**
     * Check if email service is available
     */
    public function isAvailable() {
        return true; // Always available with fallback
    }

    /**
     * Get last error message
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Get email configuration status
     */
    public function getConfigStatus() {
        $method = 'php_mail';
        if ($this->debugMode && !$this->useSMTP) {
            $method = 'debug_log';
        } elseif ($this->useSMTP) {
            $method = 'phpmailer_smtp';
        }
        
        return [
            'method' => $method,
            'from_email' => $this->fromEmail,
            'from_name' => $this->fromName,
            'smtp_host' => $this->smtpHost,
            'smtp_port' => $this->smtpPort,
            'smtp_configured' => $this->useSMTP,
            'debug_mode' => $this->debugMode,
            'status' => 'operational'
        ];
    }
}
