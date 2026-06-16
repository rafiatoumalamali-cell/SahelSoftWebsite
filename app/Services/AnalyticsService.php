<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Contact;
use App\Models\PaymentMilestone;
use App\Models\Invoice;
use App\Models\PaymentTransaction;

class AnalyticsService {
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

    public function getDashboardStats($filters = []) {
        $userModel = new User();
        $projectModel = new Project();
        $proposalModel = new Proposal();
        $contactModel = new Contact();
        $milestoneModel = new PaymentMilestone();
        $invoiceModel = new Invoice();
        $paymentModel = new PaymentTransaction();

        $users = $userModel->getAllUsers();
        $projects = $projectModel->getAllProjects();
        $contacts = $contactModel->findAll();
        $proposals = $proposalModel->getAllProposals();
        $milestones = $milestoneModel->getAllMilestones();
        $invoices = $invoiceModel->getAllInvoices();
        $payments = $paymentModel->getAllTransactions();

        $stats = [
            // User Statistics
            'total_users' => count($users),
            'active_clients' => count(array_filter($users, fn($u) => ($u['role'] ?? '') === 'client')),
            'new_users_this_month' => $this->getNewUsersThisMonth(),
            'user_growth_rate' => $this->getUserGrowthRate(),

            // Project Statistics
            'total_projects' => count($projects),
            'active_projects' => count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'active')),
            'completed_projects' => count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'completed')),
            'project_completion_rate' => $this->getProjectCompletionRate(),

            // Revenue Statistics
            'total_revenue' => $this->calculateTotalRevenue($projects),
            'revenue_this_month' => $this->getRevenueThisMonth(),
            'revenue_growth_rate' => $this->getRevenueGrowthRate(),
            'average_project_value' => $this->getAverageProjectValue(),

            // Proposal Statistics
            'total_proposals' => count($proposals),
            'accepted_proposals' => count(array_filter($proposals, fn($p) => ($p['status'] ?? '') === 'accepted')),
            'proposal_conversion_rate' => $this->getProposalConversionRate(),
            'proposal_success_rate' => $this->getProposalSuccessRate(),

            // Contact Statistics
            'total_contacts' => count($contacts),
            'converted_contacts' => count(array_filter($contacts, fn($c) => ($c['status'] ?? '') === 'converted')),
            'contact_conversion_rate' => $this->getContactConversionRate(),
            'new_contacts_this_month' => $this->getNewContactsThisMonth(),

            // Payment Statistics
            'total_milestones' => count($milestones),
            'paid_milestones' => count(array_filter($milestones, fn($m) => ($m['status'] ?? '') === 'paid')),
            'pending_payments' => $this->getPendingPayments(),
            'overdue_payments' => $this->getOverduePayments(),

            // Invoice Statistics
            'total_invoices' => count($invoices),
            'paid_invoices' => count(array_filter($invoices, fn($i) => ($i['status'] ?? '') === 'paid')),
            'outstanding_invoices' => $this->getOutstandingInvoices(),
            'invoice_payment_rate' => $this->getInvoicePaymentRate(),

            // Performance Metrics
            'average_project_duration' => $this->getAverageProjectDuration(),
            'client_satisfaction_rate' => $this->getClientSatisfactionRate(),
            'team_productivity' => $this->getTeamProductivity()
        ];

        return $stats;
    }

    public function getRevenueAnalytics($period = '12months') {
        $periods = [
            '1month' => 1,
            '3months' => 3,
            '6months' => 6,
            '12months' => 12,
            '24months' => 24
        ];

        $months = $periods[$period] ?? 12;

        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN status = 'completed' THEN budget ELSE 0 END) as revenue,
                COUNT(*) as project_count,
                AVG(CASE WHEN status = 'completed' THEN budget ELSE NULL END) as avg_project_value
            FROM projects
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['months' => $months]);
        $monthlyRevenue = $stmt->fetchAll();

        // Get revenue by category
        $categorySql = "
            SELECT 
                category,
                SUM(CASE WHEN status = 'completed' THEN budget ELSE 0 END) as revenue,
                COUNT(*) as project_count
            FROM projects
            WHERE status = 'completed' AND category IS NOT NULL
            GROUP BY category
            ORDER BY revenue DESC
        ";

        $stmt = $this->pdo->prepare($categorySql);
        $stmt->execute();
        $revenueByCategory = $stmt->fetchAll();

        // Get revenue by client
        $clientSql = "
            SELECT 
                u.full_name as client_name,
                SUM(p.budget) as total_revenue,
                COUNT(p.id) as project_count
            FROM projects p
            JOIN users u ON p.client_id = u.id
            WHERE p.status = 'completed'
            GROUP BY p.client_id, u.full_name
            ORDER BY total_revenue DESC
            LIMIT 10
        ";

        $stmt = $this->pdo->prepare($clientSql);
        $stmt->execute();
        $revenueByClient = $stmt->fetchAll();

        return [
            'monthly_revenue' => $monthlyRevenue,
            'revenue_by_category' => $revenueByCategory,
            'revenue_by_client' => $revenueByClient,
            'total_revenue' => array_sum(array_column($monthlyRevenue, 'revenue')),
            'growth_trend' => $this->calculateGrowthTrend($monthlyRevenue)
        ];
    }

    public function getProjectAnalytics() {
        $sql = "
            SELECT 
                status,
                COUNT(*) as count,
                AVG(budget) as avg_budget,
                SUM(budget) as total_budget
            FROM projects
            GROUP BY status
            ORDER BY count DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $projectsByStatus = $stmt->fetchAll();

        // Project timeline analytics
        $timelineSql = "
            SELECT 
                project_type,
                AVG(TIMESTAMPDIFF(DAY, start_date, deadline)) as avg_duration,
                COUNT(*) as project_count
            FROM projects
            WHERE start_date IS NOT NULL AND deadline IS NOT NULL
            GROUP BY project_type
            ORDER BY avg_duration ASC
        ";

        $stmt = $this->pdo->prepare($timelineSql);
        $stmt->execute();
        $projectTimelines = $stmt->fetchAll();

        // Project completion trends
        $completionSql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                COUNT(*) as total
            FROM projects
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";

        $stmt = $this->pdo->prepare($completionSql);
        $stmt->execute();
        $completionTrends = $stmt->fetchAll();

        return [
            'projects_by_status' => $projectsByStatus,
            'project_timelines' => $projectTimelines,
            'completion_trends' => $completionTrends,
            'completion_rate' => $this->getProjectCompletionRate(),
            'average_duration' => $this->getAverageProjectDuration()
        ];
    }

    public function getClientAnalytics() {
        // Client acquisition trends
        $acquisitionSql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as new_clients,
                SUM(CASE WHEN role = 'client' THEN 1 ELSE 0 END) as clients
            FROM users
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";

        $stmt = $this->pdo->prepare($acquisitionSql);
        $stmt->execute();
        $clientAcquisition = $stmt->fetchAll();

        // Client retention analysis
        $retentionSql = "
            SELECT 
                u.id,
                u.full_name,
                u.created_at as registration_date,
                COUNT(p.id) as project_count,
                SUM(p.budget) as total_value,
                MAX(p.created_at) as last_project_date
            FROM users u
            LEFT JOIN projects p ON u.id = p.client_id
            WHERE u.role = 'client'
            GROUP BY u.id, u.full_name, u.created_at
            HAVING project_count > 0
            ORDER BY total_value DESC
        ";

        $stmt = $this->pdo->prepare($retentionSql);
        $stmt->execute();
        $clientRetention = $stmt->fetchAll();

        // Client segmentation
        $segmentationSql = "
            SELECT 
                CASE 
                    WHEN total_projects = 1 THEN 'New'
                    WHEN total_projects BETWEEN 2 AND 5 THEN 'Regular'
                    WHEN total_projects > 5 THEN 'VIP'
                END as segment,
                COUNT(*) as count,
                AVG(total_value) as avg_value
            FROM (
                SELECT 
                    u.id,
                    COUNT(p.id) as total_projects,
                    SUM(p.budget) as total_value
                FROM users u
                LEFT JOIN projects p ON u.id = p.client_id
                WHERE u.role = 'client'
                GROUP BY u.id
            ) client_data
            GROUP BY segment
            ORDER BY avg_value DESC
        ";

        $stmt = $this->pdo->prepare($segmentationSql);
        $stmt->execute();
        $clientSegmentation = $stmt->fetchAll();

        return [
            'client_acquisition' => $clientAcquisition,
            'client_retention' => $clientRetention,
            'client_segmentation' => $clientSegmentation,
            'total_clients' => count($clientRetention),
            'average_client_value' => array_sum(array_column($clientRetention, 'total_value')) / max(count($clientRetention), 1)
        ];
    }

    public function getProposalAnalytics() {
        // Proposal funnel analysis
        $funnelSql = "
            SELECT 
                status,
                COUNT(*) as count,
                SUM(total_amount) as total_value
            FROM proposals
            GROUP BY status
            ORDER BY 
                CASE status 
                    WHEN 'draft' THEN 1
                    WHEN 'sent' THEN 2
                    WHEN 'accepted' THEN 3
                    WHEN 'rejected' THEN 4
                    ELSE 5
                END
        ";

        $stmt = $this->pdo->prepare($funnelSql);
        $stmt->execute();
        $proposalFunnel = $stmt->fetchAll();

        // Proposal success trends
        $trendsSql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accepted,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                COUNT(*) as total
            FROM proposals
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";

        $stmt = $this->pdo->prepare($trendsSql);
        $stmt->execute();
        $proposalTrends = $stmt->fetchAll();

        // Average proposal values
        $valueSql = "
            SELECT 
                status,
                AVG(total_amount) as avg_value,
                MIN(total_amount) as min_value,
                MAX(total_amount) as max_value,
                COUNT(*) as count
            FROM proposals
            WHERE total_amount > 0
            GROUP BY status
        ";

        $stmt = $this->pdo->prepare($valueSql);
        $stmt->execute();
        $proposalValues = $stmt->fetchAll();

        return [
            'proposal_funnel' => $proposalFunnel,
            'proposal_trends' => $proposalTrends,
            'proposal_values' => $proposalValues,
            'conversion_rate' => $this->getProposalConversionRate(),
            'success_rate' => $this->getProposalSuccessRate()
        ];
    }

    public function getFinancialAnalytics() {
        // Cash flow analysis
        $cashFlowSql = "
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as inflow,
                0 as outflow,
                COUNT(*) as transaction_count
            FROM payment_transactions
            WHERE status = 'completed'
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ";

        $stmt = $this->pdo->prepare($cashFlowSql);
        $stmt->execute();
        $cashFlow = $stmt->fetchAll();

        // Payment method analysis
        $paymentMethodsSql = "
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount,
                AVG(amount) as avg_amount
            FROM payment_transactions
            WHERE status = 'completed'
            GROUP BY payment_method
            ORDER BY total_amount DESC
        ";

        $stmt = $this->pdo->prepare($paymentMethodsSql);
        $stmt->execute();
        $paymentMethods = $stmt->fetchAll();

        // Invoice aging analysis
        $agingSql = "
            SELECT 
                CASE 
                    WHEN DATEDIFF(CURDATE(), due_date) <= 0 THEN 'Current'
                    WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 1 AND 30 THEN '1-30 Days'
                    WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 31 AND 60 THEN '31-60 Days'
                    WHEN DATEDIFF(CURDATE(), due_date) BETWEEN 61 AND 90 THEN '61-90 Days'
                    ELSE '90+ Days'
                END as aging_bucket,
                COUNT(*) as count,
                SUM(total_amount) as total_amount
            FROM invoices
            WHERE status IN ('sent', 'overdue')
            GROUP BY aging_bucket
            ORDER BY 
                CASE aging_bucket
                    WHEN 'Current' THEN 1
                    WHEN '1-30 Days' THEN 2
                    WHEN '31-60 Days' THEN 3
                    WHEN '61-90 Days' THEN 4
                    WHEN '90+ Days' THEN 5
                END
        ";

        $stmt = $this->pdo->prepare($agingSql);
        $stmt->execute();
        $invoiceAging = $stmt->fetchAll();

        return [
            'cash_flow' => $cashFlow,
            'payment_methods' => $paymentMethods,
            'invoice_aging' => $invoiceAging,
            'total_revenue' => array_sum(array_column($cashFlow, 'inflow')),
            'outstanding_amount' => array_sum(array_column($invoiceAging, 'total_amount'))
        ];
    }

    public function generateReport($reportType, $filters = []) {
        switch ($reportType) {
            case 'revenue':
                return $this->getRevenueAnalytics($filters['period'] ?? '12months');
            case 'projects':
                return $this->getProjectAnalytics();
            case 'clients':
                return $this->getClientAnalytics();
            case 'proposals':
                return $this->getProposalAnalytics();
            case 'financial':
                return $this->getFinancialAnalytics();
            case 'comprehensive':
                return [
                    'dashboard_stats' => $this->getDashboardStats($filters),
                    'revenue' => $this->getRevenueAnalytics($filters['period'] ?? '12months'),
                    'projects' => $this->getProjectAnalytics(),
                    'clients' => $this->getClientAnalytics(),
                    'proposals' => $this->getProposalAnalytics(),
                    'financial' => $this->getFinancialAnalytics()
                ];
            default:
                return ['error' => 'Invalid report type'];
        }
    }

    // Helper methods
    private function getNewUsersThisMonth() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    private function getUserGrowthRate() {
        $stmt = $this->pdo->prepare("
            SELECT 
                (COUNT(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN 1 END) * 100.0 / 
                NULLIF(COUNT(CASE WHEN MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN 1 END), 0)) as growth_rate
            FROM users
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['growth_rate'] ?? 0, 2);
    }

    private function getProjectCompletionRate() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM projects), 0) as rate FROM projects WHERE status = 'completed'");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['rate'] ?? 0, 2);
    }

    private function calculateTotalRevenue($projects) {
        return array_sum(array_map(function($project) {
            $budget = $project['budget'] ?? 0;
            return is_string($budget) ? (float) preg_replace('/[^0-9.]/', '', $budget) : (float) $budget;
        }, array_filter($projects, fn($p) => ($p['status'] ?? '') === 'completed')));
    }

    private function getRevenueThisMonth() {
        $stmt = $this->pdo->prepare("SELECT SUM(budget) as revenue FROM projects WHERE status = 'completed' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['revenue'] ?? 0);
    }

    private function getRevenueGrowthRate() {
        $stmt = $this->pdo->prepare("
            SELECT 
                (SUM(CASE WHEN status = 'completed' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN budget ELSE 0 END) * 100.0 / 
                NULLIF(SUM(CASE WHEN status = 'completed' AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN budget ELSE 0 END), 0)) as growth_rate
            FROM projects
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['growth_rate'] ?? 0, 2);
    }

    private function getAverageProjectValue() {
        $stmt = $this->pdo->prepare("SELECT AVG(budget) as avg_value FROM projects WHERE status = 'completed' AND budget > 0");
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) ($result['avg_value'] ?? 0);
    }

    private function getProposalConversionRate() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM proposals), 0) as rate FROM proposals WHERE status = 'accepted'");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['rate'] ?? 0, 2);
    }

    private function getProposalSuccessRate() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM proposals WHERE status IN ('accepted', 'rejected')), 0) as rate FROM proposals WHERE status = 'accepted'");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['rate'] ?? 0, 2);
    }

    private function getContactConversionRate() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM contacts), 0) as rate FROM contacts WHERE status = 'converted'");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['rate'] ?? 0, 2);
    }

    private function getNewContactsThisMonth() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM contacts WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    private function getPendingPayments() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count, SUM(amount) as total FROM payment_milestones WHERE status = 'pending'");
        $stmt->execute();
        $result = $stmt->fetch();
        return ['count' => $result['count'] ?? 0, 'total' => (float) ($result['total'] ?? 0)];
    }

    private function getOverduePayments() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count, SUM(amount) as total FROM payment_milestones WHERE status = 'pending' AND due_date < CURDATE()");
        $stmt->execute();
        $result = $stmt->fetch();
        return ['count' => $result['count'] ?? 0, 'total' => (float) ($result['total'] ?? 0)];
    }

    private function getOutstandingInvoices() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count, SUM(total_amount) as total FROM invoices WHERE status IN ('sent', 'overdue')");
        $stmt->execute();
        $result = $stmt->fetch();
        return ['count' => $result['count'] ?? 0, 'total' => (float) ($result['total'] ?? 0)];
    }

    private function getInvoicePaymentRate() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM invoices), 0) as rate FROM invoices WHERE status = 'paid'");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['rate'] ?? 0, 2);
    }

    private function getAverageProjectDuration() {
        $stmt = $this->pdo->prepare("SELECT AVG(DATEDIFF(deadline, start_date)) as avg_duration FROM projects WHERE start_date IS NOT NULL AND deadline IS NOT NULL AND status = 'completed'");
        $stmt->execute();
        $result = $stmt->fetch();
        return round($result['avg_duration'] ?? 0, 1);
    }

    private function getClientSatisfactionRate() {
        // This would be based on actual feedback/ratings in a real implementation
        return 95.5; // Placeholder
    }

    private function getTeamProductivity() {
        // This would be based on actual team performance metrics
        return 87.2; // Placeholder
    }

    private function calculateGrowthTrend($data) {
        if (count($data) < 2) return 'stable';
        
        $recent = array_slice($data, -3);
        $previous = array_slice($data, -6, -3);
        
        $recentSum = array_sum(array_column($recent, 'revenue'));
        $previousSum = array_sum(array_column($previous, 'revenue'));
        
        if ($previousSum == 0) return 'growing';
        
        $growth = (($recentSum - $previousSum) / $previousSum) * 100;
        
        if ($growth > 10) return 'growing';
        if ($growth < -10) return 'declining';
        return 'stable';
    }
}
