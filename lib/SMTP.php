<?php
/**
 * PHPMailer SMTP class
 * Simplified version for SahelSoft
 */

namespace PHPMailer\PHPMailer;

class SMTP {
    const VERSION = '6.9.1';
    const DEFAULT_PORT = 25;
    const DEFAULT_SECURE_PORT = 465;
    const DEFAULT_TLS_PORT = 587;
    
    const MAX_LINE_LENGTH = 998;
    const MAX_REPLY_LINES = 512;
    
    private $socket = null;
    private $debugLevel = 0;
    private $lastReply = '';

    public function setDebugLevel($level = 0) {
        $this->debugLevel = $level;
    }

    public function connect($host, $port = null, $timeout = 30, $options = []) {
        $this->error = null;
        
        if ($port === null) {
            $port = self::DEFAULT_TLS_PORT;
        }
        
        $this->socket = fsockopen(
            $host,
            $port,
            $errno,
            $errstr,
            $timeout
        );
        
        if (!$this->socket) {
            $this->error = "Failed to connect to server: $errstr ($errno)";
            return false;
        }
        
        stream_set_timeout($this->socket, $timeout, 0);
        
        // Get server greeting
        $reply = $this->getLines();
        if (strpos($reply, '220') !== 0) {
            $this->error = 'Unexpected server greeting: ' . $reply;
            return false;
        }
        
        // Send HELO/EHLO
        if (!$this->sendHello('localhost')) {
            return false;
        }
        
        return true;
    }

    private function sendHello($hostname) {
        // Try EHLO first (for ESMTP)
        if ($this->sendCommand('EHLO', 'EHLO ' . $hostname, [250, 251])) {
            return true;
        }
        
        // Fall back to HELO
        return $this->sendCommand('HELO', 'HELO ' . $hostname, 250);
    }

    public function startTLS() {
        if (!extension_loaded('openssl')) {
            $this->error = 'OpenSSL extension not loaded';
            return false;
        }
        
        if (!$this->sendCommand('STARTTLS', 'STARTTLS', 220)) {
            return false;
        }
        
        $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;
        
        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
        }
        
        set_error_handler([$this, 'errorHandler']);
        $crypto_ok = stream_socket_enable_crypto($this->socket, true, $crypto_method);
        restore_error_handler();
        
        if (!$crypto_ok) {
            $this->error = 'STARTTLS failed';
            return false;
        }
        
        return $this->sendHello('localhost');
    }

    public function authenticate($username, $password) {
        // Try AUTH LOGIN first
        if (!$this->sendCommand('AUTH LOGIN', 'AUTH LOGIN', 334)) {
            return false;
        }
        
        // Send username (base64 encoded)
        if (!$this->sendCommand('Username', base64_encode($username), 334)) {
            return false;
        }
        
        // Send password (base64 encoded)
        if (!$this->sendCommand('Password', base64_encode($password), 235)) {
            return false;
        }
        
        return true;
    }

    public function mail($from) {
        return $this->sendCommand(
            'MAIL FROM',
            'MAIL FROM:<' . $from . '>',
            250
        );
    }

    public function recipient($to) {
        return $this->sendCommand(
            'RCPT TO',
            'RCPT TO:<' . $to . '>',
            [250, 251]
        );
    }

    public function data($data) {
        if (!$this->sendCommand('DATA', 'DATA', 354)) {
            return false;
        }
        
        // Dot-stuffing: lines starting with a dot need an extra dot
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $data));
        foreach ($lines as $line) {
            if (strpos($line, '.') === 0) {
                $line = '.' . $line;
            }
            $this->send($line . "\r\n");
        }
        
        return $this->sendCommand('DATA END', '.', 250);
    }

    public function quit() {
        return $this->sendCommand('QUIT', 'QUIT', 221);
    }

    public function close() {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
        }
    }

    private function sendCommand($command, $send, $expect) {
        $this->send($send . "\r\n");
        $this->lastReply = $this->getLines();
        
        $code = (int) substr($this->lastReply, 0, 3);
        
        if (is_array($expect)) {
            if (!in_array($code, $expect, true)) {
                $this->error = "$command command failed: {$this->lastReply}";
                return false;
            }
        } else {
            if ($code !== $expect) {
                $this->error = "$command command failed: {$this->lastReply}";
                return false;
            }
        }
        
        return true;
    }

    private function send($data) {
        if (!$this->socket) {
            return false;
        }
        
        $len = strlen($data);
        $sent = fwrite($this->socket, $data, $len);
        
        return $sent === $len;
    }

    private function getLines() {
        $data = '';
        $endtime = time() + 300; // 5 minute timeout
        
        do {
            if (time() > $endtime) {
                break;
            }
            
            $str = fgets($this->socket, 512);
            if ($str === false) {
                break;
            }
            
            $data .= $str;
            
            // Check if we have a complete response (line ends with space or is last line)
            if (substr($str, 3, 1) === ' ') {
                break;
            }
            
        } while (true);
        
        return $data;
    }

    public function getLastReply() {
        return $this->lastReply;
    }

    public function getError() {
        return $this->error;
    }

    protected function errorHandler($errno, $errmsg, $errfile = '', $errline = 0) {
        $notice = 'Connection failed.';
        $this->error = $notice;
        $this->edebug($notice);
        return false;
    }

    private function edebug($str) {
        if ($this->debugLevel > 0) {
            error_log($str);
            // Log to file for easy debugging
            $logFile = __DIR__ . '/../writable/smtp_debug.log';
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents($logFile, "[$timestamp] $str\n", FILE_APPEND);
            // Also output to browser for real-time debugging (in HTML comment)
            echo "<!-- SMTP DEBUG: $str -->\n";
        }
    }
}
