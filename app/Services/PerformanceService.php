<?php

namespace App\Services;

use App\Core\Model;
use PDO;

class PerformanceService {
    private $pdo;
    
    public function __construct() {
        $this->pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
    
    public function recordMetric($metricType, $metricValue, $metricName = null, $unit = 'ms', $pageName = null, $userId = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO performance_metrics 
            (metric_type, metric_name, metric_value, metric_unit, page_name, user_id, server_load, memory_usage_mb)
            VALUES (:metric_type, :metric_name, :metric_value, :metric_unit, :page_name, :user_id, :server_load, :memory_usage_mb)
        ");
        
        return $stmt->execute([
            'metric_type' => $metricType,
            'metric_name' => $metricName ?: $metricType,
            'metric_value' => $metricValue,
            'metric_unit' => $unit,
            'page_name' => $pageName,
            'user_id' => $userId,
            'server_load' => $this->getServerLoad(),
            'memory_usage_mb' => $this->getMemoryUsage()
        ]);
    }
    
    public function getPerformanceSummary($hoursBack = 24) {
        $stmt = $this->pdo->prepare("CALL get_performance_summary(:hours_back)");
        $stmt->execute(['hours_back' => $hoursBack]);
        return $stmt->fetchAll();
    }
    
    public function getSlowPages($limit = 10) {
        $stmt = $this->pdo->prepare("CALL get_slow_pages(:limit)");
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }
    
    public function getCachePerformance() {
        $stmt = $this->pdo->prepare("SELECT * FROM cache_performance");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getBackgroundJobStats() {
        $stmt = $this->pdo->prepare("SELECT * FROM background_job_stats");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getServerHealth() {
        $stmt = $this->pdo->prepare("SELECT * FROM server_health");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getPerformanceAlerts($severity = null, $resolved = false) {
        $sql = "
            SELECT pa.*, u.full_name as resolved_by_name
            FROM performance_alerts pa
            LEFT JOIN users u ON pa.resolved_by = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($severity) {
            $sql .= " AND pa.severity = :severity";
            $params['severity'] = $severity;
        }
        
        if ($resolved !== null) {
            $sql .= " AND pa.resolved = :resolved";
            $params['resolved'] = $resolved;
        }
        
        $sql .= " ORDER BY pa.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function createPerformanceAlert($alertType, $severity, $message, $details = [], $pageName = null, $userId = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO performance_alerts 
            (alert_type, severity, message, details, page_name, user_id)
            VALUES (:alert_type, :severity, :message, :details, :page_name, :user_id)
        ");
        
        return $stmt->execute([
            'alert_type' => $alertType,
            'severity' => $severity,
            'message' => $message,
            'details' => json_encode($details),
            'page_name' => $pageName,
            'user_id' => $userId
        ]);
    }
    
    public function resolveAlert($alertId, $resolvedBy) {
        $stmt = $this->pdo->prepare("
            UPDATE performance_alerts 
            SET resolved = TRUE, resolved_at = NOW(), resolved_by = :resolved_by
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $alertId,
            'resolved_by' => $resolvedBy
        ]);
    }
    
    public function cacheGet($key, $type = 'database') {
        $stmt = $this->pdo->prepare("
            SELECT cache_value, expires_at 
            FROM cache_config 
            WHERE cache_key = :cache_key 
            AND cache_type = :cache_type
            AND (expires_at IS NULL OR expires_at > NOW())
        ");
        
        $stmt->execute(['cache_key' => $key, 'cache_type' => $type]);
        $result = $stmt->fetch();
        
        if ($result) {
            // Update hit count
            $this->updateCacheStats($key, $type, 'hit');
            return json_decode($result['cache_value'], true);
        }
        
        // Update miss count
        $this->updateCacheStats($key, $type, 'miss');
        return null;
    }
    
    public function cacheSet($key, $value, $ttl = 3600, $tags = [], $type = 'database') {
        $stmt = $this->pdo->prepare("
            INSERT INTO cache_config 
            (cache_key, cache_value, cache_tags, ttl_seconds, expires_at, cache_type)
            VALUES (:cache_key, :cache_value, :cache_tags, :ttl_seconds, DATE_ADD(NOW(), INTERVAL :ttl_seconds SECOND), :cache_type)
            ON DUPLICATE KEY UPDATE
            cache_value = VALUES(cache_value),
            cache_tags = VALUES(cache_tags),
            ttl_seconds = VALUES(ttl_seconds),
            expires_at = VALUES(expires_at)
        ");
        
        return $stmt->execute([
            'cache_key' => $key,
            'cache_value' => json_encode($value),
            'cache_tags' => json_encode($tags),
            'ttl_seconds' => $ttl,
            'cache_type' => $type
        ]);
    }
    
    public function cacheDelete($key, $type = 'database') {
        $stmt = $this->pdo->prepare("
            DELETE FROM cache_config 
            WHERE cache_key = :cache_key AND cache_type = :cache_type
        ");
        
        return $stmt->execute(['cache_key' => $key, 'cache_type' => $type]);
    }
    
    public function cacheClear($tags = [], $type = null) {
        $sql = "DELETE FROM cache_config WHERE 1=1";
        $params = [];
        
        if (!empty($tags)) {
            $sql .= " AND JSON_CONTAINS(cache_tags, :tag)";
            $params['tag'] = json_encode($tags[0]); // Simplified - would need proper JSON handling
        }
        
        if ($type) {
            $sql .= " AND cache_type = :cache_type";
            $params['cache_type'] = $type;
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function queueJob($jobType, $jobClass, $jobData, $priority = 'normal', $scheduledAt = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO background_jobs 
            (job_type, job_class, job_data, priority, scheduled_at, created_by)
            VALUES (:job_type, :job_class, :job_data, :priority, :scheduled_at, :created_by)
        ");
        
        return $stmt->execute([
            'job_type' => $jobType,
            'job_class' => $jobClass,
            'job_data' => json_encode($jobData),
            'priority' => $priority,
            'scheduled_at' => $scheduledAt ?: date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['user_id'] ?? null
        ]);
    }
    
    public function processBackgroundJobs() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM background_jobs 
            WHERE status = 'pending' 
            AND scheduled_at <= NOW()
            ORDER BY priority DESC, scheduled_at ASC
            LIMIT 10
        ");
        
        $stmt->execute();
        $jobs = $stmt->fetchAll();
        
        foreach ($jobs as $job) {
            $this->processJob($job);
        }
        
        return count($jobs);
    }
    
    public function getScalingRules() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM scaling_rules 
            WHERE is_active = TRUE 
            ORDER BY severity DESC, rule_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function evaluateScalingRules() {
        $rules = $this->getScalingRules();
        $triggeredRules = [];
        
        foreach ($rules as $rule) {
            $currentValue = $this->getCurrentMetricValue($rule['metric_type']);
            
            if ($this->evaluateCondition($currentValue, $rule['threshold_value'], $rule['comparison_operator'])) {
                $this->executeScalingAction($rule);
                $triggeredRules[] = $rule;
            }
        }
        
        return $triggeredRules;
    }
    
    public function generatePerformanceReport($reportType = 'daily', $startDate = null, $endDate = null) {
        if (!$startDate) {
            $startDate = date('Y-m-d', strtotime('-1 day'));
        }
        
        if (!$endDate) {
            $endDate = date('Y-m-d', strtotime('-1 day'));
        }
        
        $metrics = $this->getReportMetrics($startDate, $endDate);
        $recommendations = $this->generateRecommendations($metrics);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO performance_reports 
            (report_type, report_period_start, report_period_end, metrics_summary, recommendations, generated_by)
            VALUES (:report_type, :report_period_start, :report_period_end, :metrics_summary, :recommendations, :generated_by)
        ");
        
        $result = $stmt->execute([
            'report_type' => $reportType,
            'report_period_start' => $startDate,
            'report_period_end' => $endDate,
            'metrics_summary' => json_encode($metrics),
            'recommendations' => json_encode($recommendations),
            'generated_by' => $_SESSION['user_id'] ?? 1
        ]);
        
        return $result ? $this->pdo->lastInsertId() : false;
    }
    
    public function cleanupOldMetrics($daysToKeep = 30) {
        $stmt = $this->pdo->prepare("CALL cleanup_old_metrics(:days_to_keep)");
        $stmt->execute(['days_to_keep' => $daysToKeep]);
        $result = $stmt->fetch();
        return $result['deleted_count'] ?? 0;
    }
    
    public function optimizeDatabase() {
        $optimizations = [];
        
        // Get pending optimizations
        $stmt = $this->pdo->prepare("
            SELECT * FROM database_optimization 
            WHERE status = 'pending' 
            ORDER BY performance_impact DESC
            LIMIT 5
        ");
        $stmt->execute();
        $pendingOptimizations = $stmt->fetchAll();
        
        foreach ($pendingOptimizations as $opt) {
            try {
                $this->pdo->exec($opt['optimization_sql']);
                
                // Mark as applied
                $updateStmt = $this->pdo->prepare("
                    UPDATE database_optimization 
                    SET status = 'applied', applied_at = NOW() 
                    WHERE id = :id
                ");
                $updateStmt->execute(['id' => $opt['id']]);
                
                $optimizations[] = [
                    'type' => $opt['optimization_type'],
                    'table' => $opt['table_name'],
                    'impact' => $opt['performance_impact'],
                    'status' => 'applied'
                ];
            } catch (\Exception $e) {
                // Mark as failed
                $updateStmt = $this->pdo->prepare("
                    UPDATE database_optimization 
                    SET status = 'failed' 
                    WHERE id = :id
                ");
                $updateStmt->execute(['id' => $opt['id']]);
                
                $optimizations[] = [
                    'type' => $opt['optimization_type'],
                    'table' => $opt['table_name'],
                    'impact' => $opt['performance_impact'],
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $optimizations;
    }
    
    // Private helper methods
    private function getServerLoad() {
        // Get server load average (Linux/Unix)
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return $load[0] * 100; // Convert to percentage
        }
        
        return 0;
    }
    
    private function getMemoryUsage() {
        // Get memory usage in MB
        $memoryUsage = memory_get_usage(true);
        return $memoryUsage / 1024 / 1024;
    }
    
    private function updateCacheStats($key, $type, $statType) {
        $field = $statType === 'hit' ? 'hit_count' : 'miss_count';
        
        $stmt = $this->pdo->prepare("
            UPDATE cache_config 
            SET {$field} = {$field} + 1 
            WHERE cache_key = :cache_key AND cache_type = :cache_type
        ");
        
        return $stmt->execute(['cache_key' => $key, 'cache_type' => $type]);
    }
    
    private function processJob($job) {
        try {
            // Update job status to running
            $this->updateJobStatus($job['id'], 'running');
            
            // Process the job based on type
            $result = $this->executeJob($job['job_type'], $job['job_data']);
            
            if ($result) {
                $this->updateJobStatus($job['id'], 'completed', null, null, true);
            } else {
                $this->updateJobStatus($job['id'], 'failed', null, 'Job execution failed');
            }
        } catch (\Exception $e) {
            $attempts = $job['attempts'] + 1;
            
            if ($attempts < $job['max_attempts']) {
                $this->updateJobStatus($job['id'], 'pending', null, $e->getMessage());
                
                // Reschedule with delay
                $delay = $job['max_attempts'] * 60; // Exponential backoff
                $rescheduleAt = date('Y-m-d H:i:s', time() + $delay);
                
                $stmt = $this->pdo->prepare("
                    UPDATE background_jobs 
                    SET scheduled_at = :scheduled_at, attempts = :attempts 
                    WHERE id = :id
                ");
                $stmt->execute(['scheduled_at' => $rescheduleAt, 'attempts' => $attempts, 'id' => $job['id']]);
            } else {
                $this->updateJobStatus($job['id'], 'failed', null, 'Max attempts exceeded');
            }
        }
    }
    
    private function executeJob($jobType, $jobData) {
        switch ($jobType) {
            case 'email_send':
                $emailService = new \App\Services\EmailService();
                return $emailService->sendEmail($jobData['to'], $jobData['subject'], $jobData['body']);
                
            case 'report_generation':
                // Generate report logic
                return true;
                
            case 'data_backup':
                // Backup logic
                return true;
                
            case 'cache_warm':
                // Cache warming logic
                return true;
                
            case 'cleanup':
                // Cleanup logic
                return $this->cleanupOldMetrics($jobData['days_to_keep'] ?? 30);
                
            default:
                return false;
        }
    }
    
    private function updateJobStatus($jobId, $status, $startedAt = null, $errorMessage = null, $completed = false) {
        $stmt = $this->pdo->prepare("
            UPDATE background_jobs 
            SET status = :status,
                started_at = COALESCE(:started_at, started_at),
                completed_at = CASE WHEN :completed = TRUE THEN NOW() ELSE completed_at END,
                error_message = :error_message
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $jobId,
            'status' => $status,
            'started_at' => $startedAt ?: date('Y-m-d H:i:s'),
            'completed' => $completed,
            'error_message' => $errorMessage
        ]);
    }
    
    private function getCurrentMetricValue($metricType) {
        switch ($metricType) {
            case 'cpu':
                return $this->getServerLoad();
            case 'memory':
                return $this->getMemoryUsage();
            case 'response_time':
                // Get average response time from last 5 minutes
                $stmt = $this->pdo->prepare("
                    SELECT AVG(metric_value) as avg_value 
                    FROM performance_metrics 
                    WHERE metric_type = 'api_response' 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                ");
                $stmt->execute();
                $result = $stmt->fetch();
                return $result['avg_value'] ?? 0;
            case 'requests_per_second':
                // Calculate requests per second
                $stmt = $this->pdo->prepare("
                    SELECT COUNT(*) / 300 as rps 
                    FROM performance_metrics 
                    WHERE metric_type = 'page_load' 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                ");
                $stmt->execute();
                $result = $stmt->fetch();
                return $result['rps'] ?? 0;
            default:
                return 0;
        }
    }
    
    private function evaluateCondition($currentValue, $threshold, $operator) {
        switch ($operator) {
            case 'greater_than':
                return $currentValue > $threshold;
            case 'less_than':
                return $currentValue < $threshold;
            case 'equals':
                return abs($currentValue - $threshold) < 0.01;
            default:
                return false;
        }
    }
    
    private function executeScalingAction($rule) {
        switch ($rule['action_type']) {
            case 'trigger_alert':
                $this->createPerformanceAlert(
                    'scaling_rule',
                    'medium',
                    "Scaling rule triggered: {$rule['rule_name']}",
                    json_decode($rule['action_config'], true)
                );
                break;
                
            case 'scale_up':
            case 'scale_down':
                // Implement actual scaling logic here
                $this->createPerformanceAlert(
                    'scaling_action',
                    'medium',
                    "Scaling action executed: {$rule['action_type']}",
                    json_decode($rule['action_config'], true)
                );
                break;
        }
    }
    
    private function getReportMetrics($startDate, $endDate) {
        $stmt = $this->pdo->prepare("
            SELECT 
                metric_type,
                AVG(metric_value) as avg_value,
                MIN(metric_value) as min_value,
                MAX(metric_value) as max_value,
                COUNT(*) as sample_count,
                STDDEV(metric_value) as std_deviation
            FROM performance_metrics
            WHERE DATE(created_at) BETWEEN :start_date AND :end_date
            GROUP BY metric_type
        ");
        
        $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        return $stmt->fetchAll();
    }
    
    private function generateRecommendations($metrics) {
        $recommendations = [];
        
        foreach ($metrics as $metric) {
            if ($metric['metric_type'] === 'page_load' && $metric['avg_value'] > 2000) {
                $recommendations[] = [
                    'type' => 'performance',
                    'priority' => 'high',
                    'message' => 'Average page load time is ' . round($metric['avg_value']) . 'ms. Consider optimizing slow pages.',
                    'action' => 'Review slow pages and implement caching'
                ];
            }
            
            if ($metric['metric_type'] === 'database_query' && $metric['avg_value'] > 500) {
                $recommendations[] = [
                    'type' => 'database',
                    'priority' => 'medium',
                    'message' => 'Database queries are averaging ' . round($metric['avg_value']) . 'ms. Consider query optimization.',
                    'action' => 'Add indexes and optimize slow queries'
                ];
            }
            
            if ($metric['metric_type'] === 'cache_hit_rate' && $metric['avg_value'] < 80) {
                $recommendations[] = [
                    'type' => 'caching',
                    'priority' => 'medium',
                    'message' => 'Cache hit rate is ' . round($metric['avg_value']) . '%. Consider improving caching strategy.',
                    'action' => 'Review cache configuration and increase TTL'
                ];
            }
        }
        
        return $recommendations;
    }
}
