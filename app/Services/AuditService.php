<?php

namespace App\Services;

class AuditService {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function log($action, $resourceType = null, $resourceId = null, $oldValues = null, $newValues = null) {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $userEmail = $_SESSION['user_email'] ?? null;
            $ipAddress = $this->getClientIp();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            $logData = [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->insertLog($logData);
        } catch (\Exception $e) {
            error_log('Audit logging failed: ' . $e->getMessage());
        }
    }

    public function logLogin($userId, $userEmail, $success = true) {
        $this->log($success ? 'login_success' : 'login_failed', 'user', $userId, null, [
            'success' => $success,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public function logLogout($userId, $userEmail) {
        $this->log('logout', 'user', $userId);
    }

    public function logCreate($resourceType, $resourceId, $newValues) {
        $this->log('create', $resourceType, $resourceId, null, $newValues);
    }

    public function logUpdate($resourceType, $resourceId, $oldValues, $newValues) {
        $this->log('update', $resourceType, $resourceId, $oldValues, $newValues);
    }

    public function logDelete($resourceType, $resourceId, $oldValues) {
        $this->log('delete', $resourceType, $resourceId, $oldValues, null);
    }

    public function logView($resourceType, $resourceId) {
        $this->log('view', $resourceType, $resourceId);
    }

    public function logAccessDenied($resourceType, $resourceId = null) {
        $this->log('access_denied', $resourceType, $resourceId);
    }

    public function logPasswordChange($userId) {
        $this->log('password_change', 'user', $userId);
    }

    public function logEmailChange($userId, $oldEmail, $newEmail) {
        $this->log('email_change', 'user', $userId, ['email' => $oldEmail], ['email' => $newEmail]);
    }

    public function logRoleChange($userId, $oldRole, $newRole) {
        $this->log('role_change', 'user', $userId, ['role' => $oldRole], ['role' => $newRole]);
    }

    public function logProposalStatusChange($proposalId, $oldStatus, $newStatus) {
        $this->log('proposal_status_change', 'proposal', $proposalId, ['status' => $oldStatus], ['status' => $newStatus]);
    }

    public function logPaymentStatusChange($paymentId, $oldStatus, $newStatus) {
        $this->log('payment_status_change', 'payment', $paymentId, ['status' => $oldStatus], ['status' => $newStatus]);
    }

    public function logSecurityEvent($event, $details = null) {
        $this->log('security_' . $event, null, null, null, $details);
    }

    public function getAuditLogs($filters = []) {
        try {
            $pdo = $this->getPDO();
            
            $sql = "SELECT al.*, u.full_name FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id WHERE 1=1";
            $params = [];

            if (!empty($filters['user_id'])) {
                $sql .= " AND al.user_id = :user_id";
                $params['user_id'] = $filters['user_id'];
            }

            if (!empty($filters['action'])) {
                $sql .= " AND al.action = :action";
                $params['action'] = $filters['action'];
            }

            if (!empty($filters['resource_type'])) {
                $sql .= " AND al.resource_type = :resource_type";
                $params['resource_type'] = $filters['resource_type'];
            }

            if (!empty($filters['date_from'])) {
                $sql .= " AND al.created_at >= :date_from";
                $params['date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $sql .= " AND al.created_at <= :date_to";
                $params['date_to'] = $filters['date_to'];
            }

            $sql .= " ORDER BY al.created_at DESC";

            if (!empty($filters['limit'])) {
                $sql .= " LIMIT :limit";
                $params['limit'] = $filters['limit'];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            error_log('Failed to get audit logs: ' . $e->getMessage());
            return [];
        }
    }

    public function getSecurityEvents($limit = 100) {
        return $this->getAuditLogs([
            'action' => 'security_%',
            'limit' => $limit
        ]);
    }

    public function getUserActivity($userId, $limit = 50) {
        return $this->getAuditLogs([
            'user_id' => $userId,
            'limit' => $limit
        ]);
    }

    public function getRecentActivity($limit = 20) {
        return $this->getAuditLogs(['limit' => $limit]);
    }

    public function getFailedLogins($hours = 24) {
        try {
            $pdo = $this->getPDO();
            
            $sql = "SELECT * FROM audit_logs 
                    WHERE action = 'login_failed' 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL :hours HOUR)
                    ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['hours' => $hours]);
            
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            error_log('Failed to get failed logins: ' . $e->getMessage());
            return [];
        }
    }

    public function getLoginStats($days = 30) {
        try {
            $pdo = $this->getPDO();
            
            $sql = "SELECT 
                        DATE(created_at) as date,
                        COUNT(CASE WHEN action = 'login_success' THEN 1 END) as successful_logins,
                        COUNT(CASE WHEN action = 'login_failed' THEN 1 END) as failed_logins
                    FROM audit_logs 
                    WHERE action IN ('login_success', 'login_failed')
                    AND created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY date DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['days' => $days]);
            
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            error_log('Failed to get login stats: ' . $e->getMessage());
            return [];
        }
    }

    private function insertLog($logData) {
        $pdo = $this->getPDO();
        
        $sql = "INSERT INTO audit_logs (user_id, user_email, action, resource_type, resource_id, old_values, new_values, ip_address, user_agent, created_at) 
                VALUES (:user_id, :user_email, :action, :resource_type, :resource_id, :old_values, :new_values, :ip_address, :user_agent, :created_at)";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($logData);
    }

    private function getPDO() {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = new \PDO(
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
        return $pdo;
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

    public function cleanupOldLogs($days = 90) {
        try {
            $pdo = $this->getPDO();
            
            $sql = "DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(['days' => $days]);
            
            return $stmt->rowCount();
        } catch (\Exception $e) {
            error_log('Failed to cleanup old logs: ' . $e->getMessage());
            return 0;
        }
    }

    public function exportLogs($filters = [], $format = 'json') {
        $logs = $this->getAuditLogs($filters);
        
        switch ($format) {
            case 'csv':
                return $this->exportToCSV($logs);
            case 'json':
            default:
                return json_encode($logs, JSON_PRETTY_PRINT);
        }
    }

    private function exportToCSV($logs) {
        $csv = "ID,User,Email,Action,Resource Type,Resource ID,IP Address,User Agent,Created At\n";
        
        foreach ($logs as $log) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $log['id'],
                $log['full_name'] ?? 'N/A',
                $log['user_email'] ?? 'N/A',
                $log['action'],
                $log['resource_type'] ?? 'N/A',
                $log['resource_id'] ?? 'N/A',
                $log['ip_address'],
                str_replace(',', ';', $log['user_agent']),
                $log['created_at']
            );
        }
        
        return $csv;
    }
}
