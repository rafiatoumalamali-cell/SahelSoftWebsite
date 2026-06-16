<?php
/**
 * PHPMailer - PHP email transport class
 * Simplified version for SahelSoft
 */

namespace PHPMailer\PHPMailer;

class PHPMailer {
    public $CharSet = 'UTF-8';
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $isHTML = true;
    public $SMTPDebug = 0;
    
    private $to = [];
    private $cc = [];
    private $bcc = [];
    private $replyTo = [];
    private $attachments = [];
    
    // SMTP settings
    public $Host = '';
    public $Port = 587;
    public $SMTPSecure = 'tls';
    public $SMTPAuth = true;
    public $Username = '';
    public $Password = '';
    
    private $error = '';

    public function __construct($exceptions = false) {
        // Constructor
    }

    public function addAddress($email, $name = '') {
        $this->to[] = ['email' => $email, 'name' => $name];
    }

    public function addCC($email, $name = '') {
        $this->cc[] = ['email' => $email, 'name' => $name];
    }

    public function addBCC($email, $name = '') {
        $this->bcc[] = ['email' => $email, 'name' => $name];
    }

    public function addReplyTo($email, $name = '') {
        // Store reply-to for use in headers
        $this->replyTo = ['email' => $email, 'name' => $name];
    }

    public function addAttachment($path, $name = '') {
        $this->attachments[] = ['path' => $path, 'name' => $name];
    }

    public function isSMTP() {
        // Set to use SMTP
        return true;
    }

    public function isHTML($isHtml = true) {
        $this->isHTML = $isHtml;
    }

    public function setFrom($email, $name = '', $auto = true) {
        $this->From = $email;
        $this->FromName = $name;
    }

    public function send() {
        // If SMTP credentials are set, use SMTP
        if ($this->Host && $this->Username && $this->Password) {
            return $this->sendViaSMTP();
        }
        
        // Otherwise fall back to mail()
        return $this->sendViaMail();
    }

    private function sendViaMail() {
        $to = $this->formatAddressList($this->to);
        $subject = $this->Subject;
        $body = $this->Body;
        
        // Headers
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=' . $this->CharSet;
        $headers[] = 'From: ' . $this->encodeHeader($this->FromName) . ' <' . $this->From . '>';
        
        if (!empty($this->replyTo)) {
            $replyToName = $this->replyTo['name'] ?? '';
            $replyToEmail = $this->replyTo['email'];
            if ($replyToName) {
                $headers[] = 'Reply-To: ' . $this->encodeHeader($replyToName) . ' <' . $replyToEmail . '>';
            } else {
                $headers[] = 'Reply-To: ' . $replyToEmail;
            }
        }
        
        if (!empty($this->cc)) {
            $headers[] = 'Cc: ' . $this->formatAddressList($this->cc);
        }
        
        if (!empty($this->bcc)) {
            $headers[] = 'Bcc: ' . $this->formatAddressList($this->bcc);
        }
        
        $headersString = implode("\r\n", $headers);
        
        return mail($to, $subject, $body, $headersString);
    }

    private function sendViaSMTP() {
        try {
            $smtp = new SMTP();
            $smtp->setDebugLevel($this->SMTPDebug);
            
            // Connect to SMTP server
            if (!$smtp->connect($this->Host, $this->Port, 30)) {
                $this->error = 'SMTP connect() failed';
                return false;
            }
            
            // Start TLS if required
            if ($this->SMTPSecure === 'tls') {
                $smtp->startTLS();
            }
            
            // Authenticate
            if ($this->SMTPAuth) {
                if (!$smtp->authenticate($this->Username, $this->Password)) {
                    $this->error = 'SMTP authenticate() failed';
                    return false;
                }
            }
            
            // Send envelope
            if (!$smtp->mail($this->From)) {
                $this->error = 'SMTP mail() failed: ' . $smtp->getLastReply();
                return false;
            }
            
            foreach ($this->to as $recipient) {
                if (!$smtp->recipient($recipient['email'])) {
                    $this->error = 'SMTP recipient() failed for ' . $recipient['email'] . ': ' . $smtp->getLastReply();
                    return false;
                }
            }
            
            foreach ($this->cc as $recipient) {
                if (!$smtp->recipient($recipient['email'])) {
                    $this->error = 'SMTP recipient() failed for ' . $recipient['email'] . ': ' . $smtp->getLastReply();
                    return false;
                }
            }
            
            foreach ($this->bcc as $recipient) {
                if (!$smtp->recipient($recipient['email'])) {
                    $this->error = 'SMTP recipient() failed for ' . $recipient['email'] . ': ' . $smtp->getLastReply();
                    return false;
                }
            }
            
            // Create message data
            $data = $this->createMessageData();
            
            // Send data
            if (!$smtp->data($data)) {
                $this->error = 'SMTP data() failed: ' . $smtp->getLastReply();
                return false;
            }
            
            // Quit
            $smtp->quit();
            $smtp->close();
            
            return true;
            
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    private function createMessageData() {
        $lines = [];
        
        // Headers
        $lines[] = 'Date: ' . date('r');
        $lines[] = 'To: ' . $this->formatAddressList($this->to);
        $lines[] = 'From: ' . $this->encodeHeader($this->FromName) . ' <' . $this->From . '>';
        $lines[] = 'Subject: ' . $this->encodeHeader($this->Subject);
        $lines[] = 'MIME-Version: 1.0';
        $lines[] = 'Content-Type: text/html; charset=' . $this->CharSet;
        
        if (!empty($this->replyTo)) {
            $replyToName = $this->replyTo['name'] ?? '';
            $replyToEmail = $this->replyTo['email'];
            if ($replyToName) {
                $lines[] = 'Reply-To: ' . $this->encodeHeader($replyToName) . ' <' . $replyToEmail . '>';
            } else {
                $lines[] = 'Reply-To: ' . $replyToEmail;
            }
        }
        
        if (!empty($this->cc)) {
            $lines[] = 'Cc: ' . $this->formatAddressList($this->cc);
        }
        
        $lines[] = '';
        $lines[] = $this->Body;
        
        return implode("\r\n", $lines);
    }

    private function formatAddressList($addresses) {
        $formatted = [];
        foreach ($addresses as $addr) {
            if ($addr['name']) {
                $formatted[] = $this->encodeHeader($addr['name']) . ' <' . $addr['email'] . '>';
            } else {
                $formatted[] = $addr['email'];
            }
        }
        return implode(', ', $formatted);
    }

    private function encodeHeader($str) {
        return '=?UTF-8?B?' . base64_encode($str) . '?=';
    }

    public function ErrorInfo() {
        return $this->error;
    }
}
