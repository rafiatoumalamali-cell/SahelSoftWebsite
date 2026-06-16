<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;
use App\Models\Message;
use App\Models\Proposal;
use App\Models\Invoice;
use App\Services\NotificationService;

class ClientPortalController extends Controller {
    private $projectModel;
    private $messageModel;
    private $proposalModel;
    private $invoiceModel;
    private $notificationService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        if ($_SESSION['role'] !== 'client') {
            $_SESSION['error'] = 'Access denied. Client portal only.';
            $this->redirect('/dashboard');
        }
        
        $this->projectModel = new Project();
        $this->messageModel = new Message();
        $this->proposalModel = new Proposal();
        $this->invoiceModel = new Invoice();
        $this->notificationService = new NotificationService();
    }

    public function dashboard() {
        $userId = $_SESSION['user_id'];
        
        // Get user's portal settings
        $settings = $this->getClientPortalSettings($userId);
        
        // Get dashboard widgets
        $widgets = $this->getClientDashboardWidgets($userId);
        
        // Get quick actions
        $quickActions = $this->getClientQuickActions($userId);
        
        // Get recent activity
        $recentActivity = $this->getClientActivityTimeline($userId, 10);
        
        // Get unread counts
        $unreadMessages = $this->messageModel->getUnreadCount($userId);
        $unreadNotifications = $this->notificationService->getUnreadCount($userId);
        
        // Get project statistics
        $projectStats = $this->getProjectStatistics($userId);
        
        // Get user's projects and requests
        $projects = $this->projectModel->where('client_id', $userId)->findAll();
        
        // Get user's email to find their contact requests
        $userModel = new \App\Models\User();
        $user = $userModel->find($userId);
        $userEmail = $user['email'] ?? '';
        
        // Find contact requests by email instead of user_id
        $contactModel = new \App\Models\Contact();
        $requests = [];
        if ($userEmail) {
            $requests = $contactModel->where('email', $userEmail)->findAll();
        }
        
        // Log portal visit
        $this->logPortalVisit($userId, 'dashboard');
        
        return $this->view('client/dashboard', [
            'title' => 'Client Dashboard',
            'settings' => $settings,
            'widgets' => $widgets,
            'quickActions' => $quickActions,
            'recentActivity' => $recentActivity,
            'unreadMessages' => $unreadMessages,
            'unreadNotifications' => $unreadNotifications,
            'projectStats' => $projectStats,
            'projects' => $projects ?? [],
            'requests' => $requests ?? []
        ]);
    }

    public function projects() {
        $userId = $_SESSION['user_id'];
        $status = $_GET['status'] ?? 'all';
        
        $projects = $this->projectModel->where('client_id', $userId)->findAll();
        
        // Filter by status if specified
        if ($status !== 'all') {
            $projects = array_filter($projects, function($project) use ($status) {
                return $project['status'] === $status;
            });
        }
        
        // Log portal visit
        $this->logPortalVisit($userId, 'projects');
        
        return $this->view('client/projects/index', [
            'title' => 'My Projects',
            'projects' => $projects,
            'status' => $status
        ]);
    }

    public function projectView() {
        $projectId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$projectId) {
            $_SESSION['error'] = 'Project ID not provided.';
            return $this->redirect('/client/projects');
        }
        
        $project = $this->projectModel->find($projectId);
        
        if (!$project || $project['client_id'] != $userId) {
            $_SESSION['error'] = 'Project not found or access denied.';
            return $this->redirect('/client/projects');
        }
        
        // Get project timeline
        $timeline = $this->getProjectTimeline($projectId);
        
        // Get project messages
        $messages = $this->messageModel->getProjectMessages($projectId);
        
        // Get project files
        $files = $this->getProjectFiles($projectId);
        
        // Get project team members
        $teamMembers = [];
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT u.id, u.full_name, u.email, u.role, u.phone, u.company_name 
            FROM users u 
            INNER JOIN project_team pt ON u.id = pt.user_id 
            WHERE pt.project_id = ? AND u.role IN ('admin', 'staff', 'project_manager', 'developer')
        ");
        $stmt->execute([$projectId]);
        $teamMembers = $stmt->fetchAll();
        
        // Log portal visit
        $this->logPortalVisit($userId, 'project_view', ['project_id' => $projectId]);
        
        return $this->view('client/projects/view', [
            'title' => $project['title'],
            'project' => $project,
            'timeline' => $timeline,
            'messages' => $messages,
            'files' => $files,
            'team' => $teamMembers
        ]);
    }

    public function messages() {
        $userId = $_SESSION['user_id'];
        
        $messages = $this->messageModel->getUserMessages($userId);
        
        // Log portal visit
        $this->logPortalVisit($userId, 'messages');
        
        return $this->view('client/messages/index', [
            'title' => 'Messages',
            'messages' => $messages
        ]);
    }

    public function messageThread() {
        $messageId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$messageId) {
            $_SESSION['error'] = 'Message ID not provided.';
            return $this->redirect('/client/messages');
        }
        
        $message = $this->messageModel->getMessageWithThread($messageId, $userId);
        
        if (!$message) {
            $_SESSION['error'] = 'Message not found or access denied.';
            return $this->redirect('/client/messages');
        }
        
        // Mark messages as read
        $this->messageModel->markThreadAsRead($messageId, $userId);
        
        // Log portal visit
        $this->logPortalVisit($userId, 'message_thread', ['message_id' => $messageId]);
        
        return $this->view('client/messages/thread', [
            'title' => 'Message Thread',
            'message' => $message,
            'thread' => $message['thread'] ?? []
        ]);
    }

    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/client/messages');
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/client/messages');
        }
        
        $messageData = [
            'sender_id' => $_SESSION['user_id'],
            'recipient_id' => $_POST['recipient_id'],
            'subject' => $_POST['subject'] ?? '',
            'content' => $_POST['content'] ?? '',
            'project_id' => $_POST['project_id'] ?? null,
            'message_type' => 'client'
        ];
        
        $messageId = $this->messageModel->create($messageData);
        
        if ($messageId) {
            // Send notification to recipient
            $this->notificationService->sendNotification(
                $messageData['recipient_id'],
                'New Message from Client',
                $messageData['subject'],
                [
                    'category' => 'message',
                    'action_url' => APP_URL . "/admin/messages/view?id={$messageId}",
                    'action_text' => 'View Message'
                ]
            );
            
            // Log activity
            $this->logClientActivity($_SESSION['user_id'], 'message_sent', 'Message sent', 'message', $messageId);
            
            $_SESSION['success'] = 'Message sent successfully.';
            return $this->redirect('/client/messages/thread?id=' . $messageId);
        } else {
            $_SESSION['error'] = 'Failed to send message.';
            return $this->redirect('/client/messages');
        }
    }

    public function proposals() {
        $userId = $_SESSION['user_id'];
        $status = $_GET['status'] ?? 'all';
        
        $proposals = $this->proposalModel->where('client_id', $userId)->findAll();
        
        // Filter by status if specified
        if ($status !== 'all') {
            $proposals = array_filter($proposals, function($proposal) use ($status) {
                return $proposal['status'] === $status;
            });
        }
        
        // Log portal visit
        $this->logPortalVisit($userId, 'proposals');
        
        return $this->view('client/proposals/index', [
            'title' => 'Proposals',
            'proposals' => $proposals,
            'status' => $status
        ]);
    }

    public function proposalView() {
        $proposalId = $_GET['id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$proposalId) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/client/proposals');
        }
        
        $proposal = $this->proposalModel->find($proposalId);
        
        if (!$proposal || $proposal['client_id'] != $userId) {
            $_SESSION['error'] = 'Proposal not found or access denied.';
            return $this->redirect('/client/proposals');
        }
        
        // Log portal visit
        $this->logPortalVisit($userId, 'proposal_view', ['proposal_id' => $proposalId]);
        
        return $this->view('client/proposals/view', [
            'title' => 'Proposal: ' . $proposal['title'],
            'proposal' => $proposal
        ]);
    }

    public function acceptProposal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/client/proposals');
        }
        
        $proposalId = $_POST['proposal_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$proposalId) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/client/proposals');
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/client/proposals');
        }
        
        $proposal = $this->proposalModel->find($proposalId);
        
        if (!$proposal || $proposal['client_id'] != $userId) {
            $_SESSION['error'] = 'Proposal not found or access denied.';
            return $this->redirect('/client/proposals');
        }
        
        if ($this->proposalModel->update($proposalId, ['status' => 'accepted', 'response_date' => date('Y-m-d')])) {
            // Send notification to admin
            $this->notificationService->sendProposalNotification($proposal, 'accepted');
            
            // Log activity
            $this->logClientActivity($userId, 'proposal_accepted', 'Proposal accepted', 'proposal', $proposalId);
            
            $_SESSION['success'] = 'Proposal accepted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to accept proposal.';
        }
        
        return $this->redirect('/client/proposals/view?id=' . $proposalId);
    }

    public function rejectProposal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/client/proposals');
        }
        
        $proposalId = $_POST['proposal_id'] ?? null;
        $userId = $_SESSION['user_id'];
        $reason = $_POST['reason'] ?? '';
        
        if (!$proposalId) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/client/proposals');
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/client/proposals');
        }
        
        $proposal = $this->proposalModel->find($proposalId);
        
        if (!$proposal || $proposal['client_id'] != $userId) {
            $_SESSION['error'] = 'Proposal not found or access denied.';
            return $this->redirect('/client/proposals');
        }
        
        if ($this->proposalModel->update($proposalId, ['status' => 'rejected', 'response_date' => date('Y-m-d'), 'admin_notes' => $reason])) {
            // Send notification to admin
            $this->notificationService->sendProposalNotification($proposal, 'rejected');
            
            // Log activity
            $this->logClientActivity($userId, 'proposal_rejected', 'Proposal rejected: ' . $reason, 'proposal', $proposalId);
            
            $_SESSION['success'] = 'Proposal rejected.';
        } else {
            $_SESSION['error'] = 'Failed to reject proposal.';
        }
        
        return $this->redirect('/client/proposals');
    }

    public function payments() {
        $userId = $_SESSION['user_id'];
        
        $invoices = $this->invoiceModel->where('client_id', $userId)->findAll();
        
        // Log portal visit
        $this->logPortalVisit($userId, 'payments');
        
        return $this->view('client/payments/index', [
            'title' => 'Payments',
            'invoices' => $invoices
        ]);
    }

    public function profile() {
        $userId = $_SESSION['user_id'];
        
        $userModel = new \App\Models\User();
        $user = $userModel->find($userId);
        $settings = $this->getClientPortalSettings($userId);
        
        // Log portal visit
        $this->logPortalVisit($userId, 'profile');
        
        return $this->view('client/profile', [
            'title' => 'My Profile',
            'user' => $user,
            'settings' => $settings
        ]);
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/client/profile');
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/client/profile');
        }
        
        $userId = $_SESSION['user_id'];
        $userModel = new \App\Models\User();
        
        $userData = [
            'full_name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'company_name' => $_POST['company_name'] ?? '',
            'address' => $_POST['address'] ?? ''
        ];
        
        if ($userModel->update($userId, $userData)) {
            // Update session data
            $_SESSION['full_name'] = $userData['full_name'];
            $_SESSION['email'] = $userData['email'];
            
            // Log activity
            $this->logClientActivity($userId, 'profile_updated', 'Profile updated', 'user', $userId);
            
            $_SESSION['success'] = 'Profile updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update profile.';
        }
        
        return $this->redirect('/client/profile');
    }

    public function updateSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/client/profile');
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/client/profile');
        }
        
        $userId = $_SESSION['user_id'];
        
        $settings = [
            'theme' => $_POST['theme'] ?? 'auto',
            'language' => $_POST['language'] ?? 'en',
            'timezone' => $_POST['timezone'] ?? 'UTC',
            'dashboard_layout' => $_POST['dashboard_layout'] ?? 'default',
            'notifications_enabled' => isset($_POST['notifications_enabled']),
            'email_notifications' => isset($_POST['email_notifications']),
            'auto_refresh_interval' => intval($_POST['auto_refresh_interval'] ?? 30),
            'preferred_date_format' => $_POST['preferred_date_format'] ?? 'Y-m-d',
            'preferred_currency' => $_POST['preferred_currency'] ?? 'XOF'
        ];
        
        if ($this->updateClientPortalSettings($userId, $settings)) {
            $_SESSION['success'] = 'Settings updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update settings.';
        }
        
        return $this->redirect('/client/profile');
    }

    public function support() {
        $userId = $_SESSION['user_id'];
        
        $tickets = $this->getSupportTickets($userId);
        
        // Log portal visit
        $this->logPortalVisit($userId, 'support');
        
        return $this->view('client/support/index', [
            'title' => 'Support',
            'tickets' => $tickets
        ]);
    }

    public function createSupportTicket() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->view('client/support/create', [
                'title' => 'Create Support Ticket'
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/client/support/create');
            }
            
            $userId = $_SESSION['user_id'];
            
            $ticketData = [
                'user_id' => $userId,
                'subject' => $_POST['subject'] ?? '',
                'description' => $_POST['description'] ?? '',
                'category' => $_POST['category'] ?? 'general',
                'priority' => $_POST['priority'] ?? 'medium',
                'status' => 'open',
                'created_by' => $userId
            ];
            
            $ticketId = $this->saveSupportTicket($ticketData);
            
            if ($ticketId) {
                // Send notification to admin
                $this->notificationService->sendNotification(
                    1, // Send to admin user
                    'New Support Ticket',
                    "New support ticket: {$ticketData['subject']}",
                    [
                        'category' => 'support',
                        'action_url' => APP_URL . "/admin/support/tickets?id={$ticketId}",
                        'action_text' => 'View Ticket'
                    ]
                );
                
                $_SESSION['success'] = 'Support ticket created successfully.';
                return $this->redirect('/client/support');
            } else {
                $_SESSION['error'] = 'Failed to create support ticket.';
                return $this->redirect('/client/support/create');
            }
        }
    }

    public function knowledgeBase() {
        $userId = $_SESSION['user_id'];
        $category = $_GET['category'] ?? 'all';
        $search = $_GET['search'] ?? '';
        
        $articles = $this->getKBArticles($category, $search);
        
        // Log portal visit
        $this->logPortalVisit($userId, 'knowledge_base');
        
        return $this->view('client/kb/index', [
            'title' => 'Knowledge Base',
            'articles' => $articles,
            'category' => $category,
            'search' => $search
        ]);
    }

    public function kbArticle() {
        $slug = $_GET['slug'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$slug) {
            $_SESSION['error'] = 'Article not found.';
            return $this->redirect('/client/knowledge-base');
        }
        
        $article = $this->getKBArticleBySlug($slug);
        
        if (!$article || !$article['is_published']) {
            $_SESSION['error'] = 'Article not found.';
            return $this->redirect('/client/knowledge-base');
        }
        
        // Increment view count
        $this->incrementKBArticleViews($article['id']);
        
        // Log portal visit
        $this->logPortalVisit($userId, 'kb_article', ['article_id' => $article['id']]);
        
        return $this->view('client/kb/article', [
            'title' => $article['title'],
            'article' => $article
        ]);
    }

    // Helper methods
    private function getClientPortalSettings($userId) {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT * FROM client_portal_settings WHERE client_id = :client_id");
        $stmt->execute(['client_id' => $userId]);
        return $stmt->fetch() ?? [];
    }

    private function updateClientPortalSettings($userId, $settings) {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("
            INSERT INTO client_portal_settings 
            (client_id, theme, primary_color, logo_url, company_name, welcome_message, allowed_features, custom_css)
            VALUES (:client_id, :theme, :primary_color, :logo_url, :company_name, :welcome_message, :allowed_features, :custom_css)
            ON DUPLICATE KEY UPDATE
            theme = VALUES(theme),
            primary_color = VALUES(primary_color),
            logo_url = VALUES(logo_url),
            company_name = VALUES(company_name),
            welcome_message = VALUES(welcome_message),
            allowed_features = VALUES(allowed_features),
            custom_css = VALUES(custom_css),
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'client_id' => $userId,
            'theme' => $settings['theme'] ?? 'light',
            'primary_color' => $settings['primary_color'] ?? '#0f766e',
            'logo_url' => $settings['logo_url'] ?? null,
            'company_name' => $settings['company_name'] ?? null,
            'welcome_message' => $settings['welcome_message'] ?? null,
            'allowed_features' => json_encode($settings['allowed_features'] ?? []),
            'custom_css' => $settings['custom_css'] ?? null
        ]);
    }

    private function getClientDashboardWidgets($userId) {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT * FROM client_dashboard_widgets WHERE client_id = :client_id AND is_visible = TRUE ORDER BY position_y ASC, position_x ASC");
        $stmt->execute(['client_id' => $userId]);
        return $stmt->fetchAll();
    }

    private function getClientQuickActions($userId) {
        // Table doesn't exist in current schema, return empty array for now
        return [];
    }

    private function getClientActivityTimeline($userId, $limit = 10) {
        // Table doesn't exist in current schema, return empty array for now
        return [];
    }

    private function logClientActivity($userId, $activityType, $title, $entityType, $entityId, $metadata = null) {
        // Table doesn't exist in current schema, so just log to file for now
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'client_id' => $userId,
            'activity_type' => $activityType,
            'title' => $title,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata
        ];
        
        $logFile = __DIR__ . '/../../writable/client_activity_log.txt';
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
        
        return true;
    }

    private function logPortalVisit($userId, $pageType, $metadata = null) {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("
            INSERT INTO client_portal_analytics 
            (user_id, session_id, page_visited, page_type, device_type, browser, referrer)
            VALUES (:user_id, :session_id, :page_visited, :page_type, :device_type, :browser, :referrer)
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'session_id' => session_id(),
            'page_visited' => $_SERVER['REQUEST_URI'],
            'page_type' => $pageType,
            'device_type' => $this->detectDeviceType(),
            'browser' => $this->getBrowser(),
            'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
        ]);
    }

    private function getProjectStatistics($userId) {
        $projects = $this->projectModel->where('client_id', $userId)->findAll();
        
        $stats = [
            'total' => count($projects),
            'active' => 0,
            'completed' => 0,
            'pending' => 0
        ];
        
        foreach ($projects as $project) {
            switch ($project['status']) {
                case 'active':
                case 'in_progress':
                    $stats['active']++;
                    break;
                case 'completed':
                    $stats['completed']++;
                    break;
                case 'pending':
                    $stats['pending']++;
                    break;
            }
        }
        
        return $stats;
    }

    private function getProjectTimeline($projectId) {
        // This would need to be implemented with a proper timeline system
        return [];
    }

    private function getProjectFiles($projectId) {
        // This would integrate with the file management system
        return [];
    }

    private function getSupportTickets($userId) {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT * FROM client_support_tickets WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    private function saveSupportTicket($ticketData) {
        $pdo = $this->getPDO();
        $ticketNumber = 'TKT-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $stmt = $pdo->prepare("
            INSERT INTO client_support_tickets 
            (user_id, ticket_number, subject, description, category, priority, status, created_by)
            VALUES (:user_id, :ticket_number, :subject, :description, :category, :priority, :status, :created_by)
        ");
        
        return $stmt->execute([
            'user_id' => $ticketData['user_id'],
            'ticket_number' => $ticketNumber,
            'subject' => $ticketData['subject'],
            'description' => $ticketData['description'],
            'category' => $ticketData['category'],
            'priority' => $ticketData['priority'],
            'status' => $ticketData['status'],
            'created_by' => $ticketData['created_by']
        ]) ? $pdo->lastInsertId() : false;
    }

    private function getKBArticles($category, $search) {
        $pdo = $this->getPDO();
        $sql = "SELECT * FROM client_kb_articles WHERE is_published = TRUE";
        $params = [];
        
        if ($category !== 'all') {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        
        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR content LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY featured DESC, view_count DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getKBArticleBySlug($slug) {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("SELECT * FROM client_kb_articles WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    private function incrementKBArticleViews($articleId) {
        $pdo = $this->getPDO();
        $stmt = $pdo->prepare("UPDATE client_kb_articles SET view_count = view_count + 1 WHERE id = :id");
        return $stmt->execute(['id' => $articleId]);
    }

    private function getActivityIcon($activityType) {
        $icons = [
            'project_created' => '📁',
            'project_updated' => '✏️',
            'message_sent' => '📤',
            'message_received' => '📥',
            'payment_made' => '💳',
            'payment_received' => '💰',
            'proposal_sent' => '📋',
            'proposal_accepted' => '✅',
            'proposal_rejected' => '❌',
            'document_uploaded' => '📄',
            'login' => '🔑',
            'profile_updated' => '👤'
        ];
        
        return $icons[$activityType] ?? '📌';
    }

    private function getActivityColor($activityType) {
        $colors = [
            'project_created' => '#10b981',
            'project_updated' => '#3b82f6',
            'message_sent' => '#8b5cf6',
            'message_received' => '#8b5cf6',
            'payment_made' => '#f59e0b',
            'payment_received' => '#10b981',
            'proposal_sent' => '#3b82f6',
            'proposal_accepted' => '#10b981',
            'proposal_rejected' => '#ef4444',
            'document_uploaded' => '#6b7280',
            'login' => '#6b7280',
            'profile_updated' => '#3b82f6'
        ];
        
        return $colors[$activityType] ?? '#6b7280';
    }

    private function detectDeviceType() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet/i', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    private function getBrowser() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Edge/i', $userAgent)) return 'Edge';
        
        return 'Unknown';
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
