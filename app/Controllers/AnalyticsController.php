<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AnalyticsService;
use App\Services\AuditService;

class AnalyticsController extends Controller {
    private $analyticsService;
    private $auditService;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/login');
        }
        
        $this->analyticsService = new AnalyticsService();
        $this->auditService = AuditService::getInstance();
    }

    public function dashboard() {
        $filters = $this->getFiltersFromRequest();
        $stats = $this->analyticsService->getDashboardStats($filters);
        
        // Log dashboard view
        $this->auditService->logView('analytics_dashboard');
        
        return $this->view('admin/analytics/dashboard', [
            'title' => 'Advanced Analytics Dashboard',
            'stats' => $stats,
            'filters' => $filters
        ]);
    }

    public function revenue() {
        $period = $_GET['period'] ?? '12months';
        $filters = $this->getFiltersFromRequest();
        $analytics = $this->analyticsService->getRevenueAnalytics($period);
        
        // Log revenue analytics view
        $this->auditService->logView('analytics_revenue');
        
        return $this->view('admin/analytics/revenue', [
            'title' => 'Revenue Analytics',
            'analytics' => $analytics,
            'period' => $period,
            'filters' => $filters
        ]);
    }

    public function projects() {
        $filters = $this->getFiltersFromRequest();
        $analytics = $this->analyticsService->getProjectAnalytics();
        
        // Log project analytics view
        $this->auditService->logView('analytics_projects');
        
        return $this->view('admin/analytics/projects', [
            'title' => 'Project Analytics',
            'analytics' => $analytics,
            'filters' => $filters
        ]);
    }

    public function clients() {
        $filters = $this->getFiltersFromRequest();
        $analytics = $this->analyticsService->getClientAnalytics();
        
        // Log client analytics view
        $this->auditService->logView('analytics_clients');
        
        return $this->view('admin/analytics/clients', [
            'title' => 'Client Analytics',
            'analytics' => $analytics,
            'filters' => $filters
        ]);
    }

    public function proposals() {
        $filters = $this->getFiltersFromRequest();
        $analytics = $this->analyticsService->getProposalAnalytics();
        
        // Log proposal analytics view
        $this->auditService->logView('analytics_proposals');
        
        return $this->view('admin/analytics/proposals', [
            'title' => 'Proposal Analytics',
            'analytics' => $analytics,
            'filters' => $filters
        ]);
    }

    public function financial() {
        $filters = $this->getFiltersFromRequest();
        $analytics = $this->analyticsService->getFinancialAnalytics();
        
        // Log financial analytics view
        $this->auditService->logView('analytics_financial');
        
        return $this->view('admin/analytics/financial', [
            'title' => 'Financial Analytics',
            'analytics' => $analytics,
            'filters' => $filters
        ]);
    }

    public function reports() {
        $reportType = $_GET['type'] ?? 'comprehensive';
        $format = $_GET['format'] ?? 'view';
        $filters = $this->getFiltersFromRequest();
        
        // Log report generation
        $this->auditService->logSecurityEvent('report_generated', [
            'report_type' => $reportType,
            'format' => $format,
            'filters' => $filters
        ]);
        
        if ($format === 'export') {
            $exportFormat = $_GET['export_format'] ?? 'json';
            $report = $this->analyticsService->generateReport($reportType, $filters);
            
            // Set appropriate headers for file download
            $filename = $reportType . '_report_' . date('Y-m-d') . '.' . $exportFormat;
            
            if ($exportFormat === 'csv') {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                echo $report;
            } else {
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                echo $report;
            }
            exit;
        }
        
        $report = $this->analyticsService->generateReport($reportType, $filters);
        
        return $this->view('admin/analytics/reports', [
            'title' => ucfirst($reportType) . ' Report',
            'report' => $report,
            'reportType' => $reportType,
            'filters' => $filters
        ]);
    }

    public function kpi() {
        $kpiData = $this->getKPIData();
        
        // Log KPI view
        $this->auditService->logView('analytics_kpi');
        
        return $this->view('admin/analytics/kpi', [
            'title' => 'Key Performance Indicators',
            'kpiData' => $kpiData
        ]);
    }

    public function realtime() {
        // This would use WebSocket or similar for real-time updates
        // For now, return current data
        $stats = $this->analyticsService->getDashboardStats();
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $stats,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            exit;
        }
        
        return $this->view('admin/analytics/realtime', [
            'title' => 'Real-Time Analytics',
            'stats' => $stats
        ]);
    }

    public function compare() {
        $period1 = $_GET['period1'] ?? 'last_month';
        $period2 = $_GET['period2'] ?? 'this_month';
        
        $comparisonData = $this->getComparisonData($period1, $period2);
        
        // Log comparison view
        $this->auditService->logView('analytics_comparison');
        
        return $this->view('admin/analytics/compare', [
            'title' => 'Period Comparison',
            'comparisonData' => $comparisonData,
            'period1' => $period1,
            'period2' => $period2
        ]);
    }

    public function forecast() {
        $forecastData = $this->getForecastData();
        
        // Log forecast view
        $this->auditService->logView('analytics_forecast');
        
        return $this->view('admin/analytics/forecast', [
            'title' => 'Revenue Forecast',
            'forecastData' => $forecastData
        ]);
    }

    private function getFiltersFromRequest() {
        return [
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'client_id' => $_GET['client_id'] ?? null,
            'project_type' => $_GET['project_type'] ?? null,
            'status' => $_GET['status'] ?? null
        ];
    }

    private function getKPIData() {
        $stats = $this->analyticsService->getDashboardStats();
        
        return [
            'revenue_kpi' => [
                'current' => $stats['total_revenue'],
                'target' => $stats['total_revenue'] * 1.2, // 20% growth target
                'percentage' => ($stats['total_revenue'] / ($stats['total_revenue'] * 1.2)) * 100,
                'trend' => 'up'
            ],
            'conversion_kpi' => [
                'current' => $stats['contact_conversion_rate'],
                'target' => 25, // 25% conversion target
                'percentage' => ($stats['contact_conversion_rate'] / 25) * 100,
                'trend' => 'up'
            ],
            'completion_kpi' => [
                'current' => $stats['project_completion_rate'],
                'target' => 85, // 85% completion target
                'percentage' => ($stats['project_completion_rate'] / 85) * 100,
                'trend' => 'stable'
            ],
            'client_satisfaction_kpi' => [
                'current' => $stats['client_satisfaction_rate'],
                'target' => 90, // 90% satisfaction target
                'percentage' => ($stats['client_satisfaction_rate'] / 90) * 100,
                'trend' => 'up'
            ]
        ];
    }

    private function getComparisonData($period1, $period2) {
        $period1Filters = $this->getPeriodFilters($period1);
        $period2Filters = $this->getPeriodFilters($period2);
        
        $period1Data = $this->analyticsService->getDashboardStats($period1Filters);
        $period2Data = $this->analyticsService->getDashboardStats($period2Filters);
        
        return [
            'period1' => [
                'name' => $this->getPeriodName($period1),
                'data' => $period1Data
            ],
            'period2' => [
                'name' => $this->getPeriodName($period2),
                'data' => $period2Data
            ],
            'comparison' => $this->calculateComparison($period1Data, $period2Data)
        ];
    }

    private function getPeriodFilters($period) {
        $filters = [];
        
        switch ($period) {
            case 'this_month':
                $filters['date_from'] = date('Y-m-01');
                $filters['date_to'] = date('Y-m-t');
                break;
            case 'last_month':
                $filters['date_from'] = date('Y-m-01', strtotime('-1 month'));
                $filters['date_to'] = date('Y-m-t', strtotime('-1 month'));
                break;
            case 'this_quarter':
                $filters['date_from'] = date('Y-m-01', strtotime('-' . ((date('n') - 1) % 3) . ' months'));
                $filters['date_to'] = date('Y-m-t');
                break;
            case 'last_quarter':
                $filters['date_from'] = date('Y-m-01', strtotime('-' . ((date('n') - 1) % 3 + 3) . ' months'));
                $filters['date_to'] = date('Y-m-t', strtotime('-' . ((date('n') - 1) % 3 + 1) . ' months'));
                break;
            case 'this_year':
                $filters['date_from'] = date('Y-01-01');
                $filters['date_to'] = date('Y-12-31');
                break;
            case 'last_year':
                $filters['date_from'] = date('Y-01-01', strtotime('-1 year'));
                $filters['date_to'] = date('Y-12-31', strtotime('-1 year'));
                break;
        }
        
        return $filters;
    }

    private function getPeriodName($period) {
        $names = [
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'last_quarter' => 'Last Quarter',
            'this_year' => 'This Year',
            'last_year' => 'Last Year'
        ];
        
        return $names[$period] ?? $period;
    }

    private function calculateComparison($data1, $data2) {
        $comparison = [];
        
        foreach ($data1 as $key => $value1) {
            if (is_numeric($value1) && isset($data2[$key]) && is_numeric($data2[$key])) {
                $value2 = $data2[$key];
                $change = $value2 - $value1;
                $percentage = $value1 != 0 ? ($change / $value1) * 100 : 0;
                
                $comparison[$key] = [
                    'period1' => $value1,
                    'period2' => $value2,
                    'change' => $change,
                    'percentage' => round($percentage, 2),
                    'trend' => $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'stable')
                ];
            }
        }
        
        return $comparison;
    }

    private function getForecastData() {
        // Simple linear regression forecast
        $revenueData = $this->analyticsService->getRevenueAnalytics('12months');
        $monthlyRevenue = $revenueData['monthly_revenue'];
        
        if (count($monthlyRevenue) < 3) {
            return ['error' => 'Insufficient data for forecasting'];
        }
        
        $forecast = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        
        // Calculate average growth rate
        $growthRates = [];
        for ($i = 1; $i < count($monthlyRevenue); $i++) {
            $prev = $monthlyRevenue[$i-1]['revenue'] ?? 0;
            $curr = $monthlyRevenue[$i]['revenue'] ?? 0;
            if ($prev > 0) {
                $growthRates[] = (($curr - $prev) / $prev) * 100;
            }
        }
        
        $avgGrowthRate = count($growthRates) > 0 ? array_sum($growthRates) / count($growthRates) : 0;
        $lastRevenue = end($monthlyRevenue)['revenue'] ?? 0;
        
        // Generate forecast
        for ($i = 0; $i < 6; $i++) {
            $forecastRevenue = $lastRevenue * pow(1 + ($avgGrowthRate / 100), $i + 1);
            $forecast[] = [
                'month' => $months[$i],
                'forecast' => round($forecastRevenue, 2),
                'confidence_low' => round($forecastRevenue * 0.8, 2),
                'confidence_high' => round($forecastRevenue * 1.2, 2)
            ];
        }
        
        return [
            'forecast' => $forecast,
            'growth_rate' => round($avgGrowthRate, 2),
            'method' => 'Linear Regression',
            'confidence_level' => 80
        ];
    }

    public function exportData() {
        $type = $_GET['type'] ?? 'dashboard';
        $format = $_GET['format'] ?? 'json';
        $filters = $this->getFiltersFromRequest();
        
        $this->auditService->logSecurityEvent('data_exported', [
            'type' => $type,
            'format' => $format,
            'filters' => $filters
        ]);
        
        switch ($type) {
            case 'dashboard':
                $data = $this->analyticsService->getDashboardStats($filters);
                break;
            case 'revenue':
                $data = $this->analyticsService->getRevenueAnalytics($filters['period'] ?? '12months');
                break;
            case 'projects':
                $data = $this->analyticsService->getProjectAnalytics();
                break;
            case 'clients':
                $data = $this->analyticsService->getClientAnalytics();
                break;
            case 'proposals':
                $data = $this->analyticsService->getProposalAnalytics();
                break;
            case 'financial':
                $data = $this->analyticsService->getFinancialAnalytics();
                break;
            default:
                $data = ['error' => 'Invalid data type'];
        }
        
        $filename = $type . '_data_' . date('Y-m-d_H-i-s') . '.' . $format;
        
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $this->convertToCSV($data);
        } else {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo json_encode($data, JSON_PRETTY_PRINT);
        }
        exit;
    }

    private function convertToCSV($data) {
        if (is_array($data) && isset($data[0])) {
            // Array of objects/arrays
            $headers = array_keys($data[0]);
            $csv = implode(',', $headers) . "\n";
            
            foreach ($data as $row) {
                $csv .= implode(',', array_map(function($value) {
                    return is_numeric($value) ? $value : '"' . str_replace('"', '""', $value) . '"';
                }, $row)) . "\n";
            }
            
            return $csv;
        } else {
            // Single object/array
            $csv = "Key,Value\n";
            foreach ($data as $key => $value) {
                $csv .= '"' . $key . '","' . (is_numeric($value) ? $value : str_replace('"', '""', $value)) . "\"\n";
            }
            return $csv;
        }
    }
}
