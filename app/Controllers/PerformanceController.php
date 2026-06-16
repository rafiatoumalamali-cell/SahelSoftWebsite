<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PerformanceService;

class PerformanceController extends Controller {
    private $performanceService;

    public function __construct() {
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/dashboard');
        }
        
        $this->performanceService = new PerformanceService();
    }

    public function index() {
        $summary = $this->performanceService->getPerformanceSummary(24);
        $slowPages = $this->performanceService->getSlowPages(10);
        $cachePerformance = $this->performanceService->getCachePerformance();
        $serverHealth = $this->performanceService->getServerHealth();
        $alerts = $this->performanceService->getPerformanceAlerts('high', false);

        return $this->view('admin/performance/index', [
            'title' => 'Performance & Scaling',
            'summary' => $summary,
            'slowPages' => $slowPages,
            'cachePerformance' => $cachePerformance,
            'serverHealth' => $serverHealth,
            'alerts' => $alerts
        ]);
    }

    public function metrics() {
        $hoursBack = intval($_GET['hours'] ?? 24);
        $metricType = $_GET['type'] ?? null;
        
        $sql = "
            SELECT * FROM performance_metrics 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :hours_back HOUR)
        ";
        
        $params = ['hours_back' => $hoursBack];
        
        if ($metricType) {
            $sql .= " AND metric_type = :metric_type";
            $params['metric_type'] = $metricType;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $metrics = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'metrics' => $metrics]);
        exit;
    }

    public function alerts() {
        $severity = $_GET['severity'] ?? null;
        $resolved = isset($_GET['resolved']) ? $_GET['resolved'] === 'true' : false;
        
        $alerts = $this->performanceService->getPerformanceAlerts($severity, $resolved);

        return $this->view('admin/performance/alerts', [
            'title' => 'Performance Alerts',
            'alerts' => $alerts,
            'severity' => $severity,
            'resolved' => $resolved
        ]);
    }

    public function resolveAlert() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $alertId = $_POST['alert_id'] ?? null;
        
        if (!$alertId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Alert ID not provided']);
            exit;
        }

        $result = $this->performanceService->resolveAlert($alertId, $_SESSION['user_id']);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function cache() {
        $cachePerformance = $this->performanceService->getCachePerformance();
        
        // Get cache entries
        $stmt = $this->pdo->prepare("
            SELECT * FROM cache_config 
            ORDER BY created_at DESC 
            LIMIT 100
        ");
        $stmt->execute();
        $cacheEntries = $stmt->fetchAll();

        return $this->view('admin/performance/cache', [
            'title' => 'Cache Management',
            'cachePerformance' => $cachePerformance,
            'cacheEntries' => $cacheEntries
        ]);
    }

    public function clearCache() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $key = $_POST['key'] ?? null;
        $type = $_POST['type'] ?? null;
        $tags = $_POST['tags'] ?? [];

        if ($key) {
            $result = $this->performanceService->cacheDelete($key, $type);
        } elseif (!empty($tags)) {
            $result = $this->performanceService->cacheClear($tags, $type);
        } else {
            $result = $this->performanceService->cacheClear([], $type);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function backgroundJobs() {
        $jobStats = $this->performanceService->getBackgroundJobStats();
        
        // Get recent jobs
        $stmt = $this->pdo->prepare("
            SELECT bj.*, u.full_name as created_by_name
            FROM background_jobs bj
            LEFT JOIN users u ON bj.created_by = u.id
            ORDER BY bj.created_at DESC
            LIMIT 50
        ");
        $stmt->execute();
        $jobs = $stmt->fetchAll();

        return $this->view('admin/performance/jobs', [
            'title' => 'Background Jobs',
            'jobStats' => $jobStats,
            'jobs' => $jobs
        ]);
    }

    public function queueJob() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $jobType = $_POST['job_type'] ?? '';
        $jobClass = $_POST['job_class'] ?? '';
        $jobData = json_decode($_POST['job_data'] ?? '{}', true) ?? [];
        $priority = $_POST['priority'] ?? 'normal';

        if (empty($jobType) || empty($jobClass)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Job type and class are required']);
            exit;
        }

        $result = $this->performanceService->queueJob($jobType, $jobClass, $jobData, $priority);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function processJobs() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $processedCount = $this->performanceService->processBackgroundJobs();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'processed_count' => $processedCount]);
        exit;
    }

    public function scaling() {
        $rules = $this->performanceService->getScalingRules();
        
        // Get recent scaling events
        $stmt = $this->pdo->prepare("
            SELECT * FROM performance_alerts 
            WHERE alert_type = 'scaling_rule' 
            ORDER BY created_at DESC 
            LIMIT 20
        ");
        $stmt->execute();
        $scalingEvents = $stmt->fetchAll();

        return $this->view('admin/performance/scaling', [
            'title' => 'Auto Scaling',
            'rules' => $rules,
            'scalingEvents' => $scalingEvents
        ]);
    }

    public function addScalingRule() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->view('admin/performance/scaling-rule', [
                'title' => 'Add Scaling Rule'
            ]);
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/performance/scaling');
        }

        $ruleData = [
            'rule_name' => $_POST['rule_name'] ?? '',
            'metric_type' => $_POST['metric_type'] ?? '',
            'threshold_value' => floatval($_POST['threshold_value'] ?? 0),
            'comparison_operator' => $_POST['comparison_operator'] ?? '',
            'action_type' => $_POST['action_type'] ?? '',
            'action_config' => json_decode($_POST['action_config'] ?? '{}', true) ?? [],
            'cooldown_minutes' => intval($_POST['cooldown_minutes'] ?? 5),
            'is_active' => isset($_POST['is_active'])
        ];

        $stmt = $this->pdo->prepare("
            INSERT INTO scaling_rules 
            (rule_name, metric_type, threshold_value, comparison_operator, action_type, action_config, cooldown_minutes, is_active, created_by)
            VALUES (:rule_name, :metric_type, :threshold_value, :comparison_operator, :action_type, :action_config, :cooldown_minutes, :is_active, :created_by)
        ");

        $result = $stmt->execute([
            'rule_name' => $ruleData['rule_name'],
            'metric_type' => $ruleData['metric_type'],
            'threshold_value' => $ruleData['threshold_value'],
            'comparison_operator' => $ruleData['comparison_operator'],
            'action_type' => $ruleData['action_type'],
            'action_config' => json_encode($ruleData['action_config']),
            'cooldown_minutes' => $ruleData['cooldown_minutes'],
            'is_active' => $ruleData['is_active'],
            'created_by' => $_SESSION['user_id']
        ]);

        if ($result) {
            $_SESSION['success'] = 'Scaling rule added successfully.';
        } else {
            $_SESSION['error'] = 'Failed to add scaling rule.';
        }

        return $this->redirect('/admin/performance/scaling');
    }

    public function database() {
        // Get database optimizations
        $stmt = $this->pdo->prepare("
            SELECT * FROM database_optimization 
            ORDER BY status DESC, performance_impact DESC
        ");
        $stmt->execute();
        $optimizations = $stmt->fetchAll();

        return $this->view('admin/performance/database', [
            'title' => 'Database Optimization',
            'optimizations' => $optimizations
        ]);
    }

    public function optimizeDatabase() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $optimizations = $this->performanceService->optimizeDatabase();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'optimizations' => $optimizations]);
        exit;
    }

    public function reports() {
        $reportType = $_GET['type'] ?? 'daily';
        
        // Get recent reports
        $stmt = $this->pdo->prepare("
            SELECT pr.*, u.full_name as generated_by_name
            FROM performance_reports pr
            LEFT JOIN users u ON pr.generated_by = u.id
            WHERE pr.report_type = :report_type
            ORDER BY pr.generated_at DESC
            LIMIT 20
        ");
        $stmt->execute(['report_type' => $reportType]);
        $reports = $stmt->fetchAll();

        return $this->view('admin/performance/reports', [
            'title' => 'Performance Reports',
            'reports' => $reports,
            'reportType' => $reportType
        ]);
    }

    public function generateReport() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $reportType = $_POST['report_type'] ?? 'daily';
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;

        $reportId = $this->performanceService->generatePerformanceReport($reportType, $startDate, $endDate);

        header('Content-Type: application/json');
        echo json_encode(['success' => $reportId > 0, 'report_id' => $reportId]);
        exit;
    }

    public function cleanup() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->view('admin/performance/cleanup', [
                'title' => 'System Cleanup'
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/performance/cleanup');
            }

            $daysToKeep = intval($_POST['days_to_keep'] ?? 30);
            $deletedCount = $this->performanceService->cleanupOldMetrics($daysToKeep);

            $_SESSION['success'] = "Cleaned up {$deletedCount} old metric records.";
            return $this->redirect('/admin/performance/cleanup');
        }
    }

    public function serverHealth() {
        $serverHealth = $this->performanceService->getServerHealth();
        
        // Get detailed server metrics
        $stmt = $this->pdo->prepare("
            SELECT * FROM server_monitoring 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $detailedMetrics = $stmt->fetchAll();

        return $this->view('admin/performance/server-health', [
            'title' => 'Server Health',
            'serverHealth' => $serverHealth,
            'detailedMetrics' => $detailedMetrics
        ]);
    }

    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get current settings
            $settings = $this->getPerformanceSettings();

            return $this->view('admin/performance/settings', [
                'title' => 'Performance Settings',
                'settings' => $settings
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/performance/settings');
            }

            $settings = [
                'monitoring_enabled' => isset($_POST['monitoring_enabled']),
                'alert_threshold_cpu' => floatval($_POST['alert_threshold_cpu'] ?? 80),
                'alert_threshold_memory' => floatval($_POST['alert_threshold_memory'] ?? 85),
                'alert_threshold_response_time' => floatval($_POST['alert_threshold_response_time'] ?? 2000),
                'cache_ttl_default' => intval($_POST['cache_ttl_default'] ?? 3600),
                'job_queue_enabled' => isset($_POST['job_queue_enabled']),
                'auto_scaling_enabled' => isset($_POST['auto_scaling_enabled']),
                'cleanup_days' => intval($_POST['cleanup_days'] ?? 30)
            ];

            $this->savePerformanceSettings($settings);

            $_SESSION['success'] = 'Performance settings updated successfully.';
            return $this->redirect('/admin/performance/settings');
        }
    }

    // Private helper methods
    private function getPerformanceSettings() {
        // This would typically be stored in a settings table
        // For now, return default values
        return [
            'monitoring_enabled' => true,
            'alert_threshold_cpu' => 80,
            'alert_threshold_memory' => 85,
            'alert_threshold_response_time' => 2000,
            'cache_ttl_default' => 3600,
            'job_queue_enabled' => true,
            'auto_scaling_enabled' => false,
            'cleanup_days' => 30
        ];
    }

    private function savePerformanceSettings($settings) {
        // This would save settings to a database table
        // For now, just return true
        return true;
    }

    private function getPDO() {
        return new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }
}
