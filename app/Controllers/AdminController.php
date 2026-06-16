<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\BlogPost;

class AdminController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/login');
        }
    }

    public function dashboard() {
        $userModel = new User();
        $projectModel = new Project();
        $settingModel = new Setting();
        $contactModel = new Contact();
        $proposalModel = new \App\Models\Proposal();
        $blogModel = new BlogPost();

        $users = $userModel->getAllUsers();
        $projects = $projectModel->getAllProjects();
        $settings = $settingModel->getAllSettings();
        $messages = $contactModel->orderBy('created_at', 'DESC')->findAll(); // Limit/Pagination in real app

        // Calculate Stats
        $stats = [
            'total_users' => count($users),
            'active_clients' => count(array_filter($users, fn($u) => ($u['role'] ?? '') === 'client')),
            'active_projects' => 0,
            'completed_projects' => 0,
            'revenue' => 0,
            'project_status_counts' => [],
            'user_role_counts' => [],
            'revenue_by_category' => [],
            'client_spending' => [],
            'average_project_budget' => 0
        ];
        
        // Detailed Project Stats
        foreach ($projects as $p) {
            $status = strtolower(trim($p['status'] ?? ''));
            $budget = $p['budget'] ?? 0;
            
            // Clean budget string
            if (is_string($budget)) {
                $budget = preg_replace('/[^0-9.]/', '', $budget);
            }

            // Count per status
            if (!isset($stats['project_status_counts'][$status])) {
                $stats['project_status_counts'][$status] = 0;
            }
            $stats['project_status_counts'][$status]++;

            if ($status === 'active') {
                $stats['active_projects']++;
            }
            
            // Revenue by Category
            $category = $p['category'] ?? 'Uncategorized';
            if (!empty($category)) {
                if (!isset($stats['revenue_by_category'][$category])) {
                    $stats['revenue_by_category'][$category] = 0;
                }
                if ($status === 'completed') {
                    $stats['revenue_by_category'][$category] += (float) $budget;
                }
            }

            // Top Clients Calculation (Accumulate budget per client)
            if (!empty($p['client_id'])) {
                if (!isset($stats['client_spending'][$p['client_id']])) {
                    $stats['client_spending'][$p['client_id']] = 0;
                }
                if ($status === 'completed') {
                    $stats['client_spending'][$p['client_id']] += (float) $budget;
                }
            }

            if ($status === 'completed') {
                $stats['completed_projects']++;
                $stats['revenue'] += (float) $budget;
            }
        }
        
        // Finalize specific stats
        $stats['average_project_budget'] = $stats['completed_projects'] > 0 ? $stats['revenue'] / $stats['completed_projects'] : 0;
        
        // Sort and limit Top Clients
        arsort($stats['client_spending']);
        $stats['top_clients'] = array_slice($stats['client_spending'], 0, 5, true);
        
        // Map client IDs to names for the view
        $clientNames = [];
        foreach($users as $user) {
            $clientNames[$user['id']] = $user['full_name'];
        }
        $stats['client_names'] = $clientNames;

        // Get proposal statistics
        $proposalStats = $proposalModel->getStats();
        $stats['proposals'] = $proposalStats;

        // Get payment milestone statistics
        $milestoneModel = new \App\Models\PaymentMilestone();
        $milestoneStats = $milestoneModel->getOverallStats();
        $stats['milestones'] = $milestoneStats;

        // Calculate conversion rate (contacts to projects)
        $totalContacts = count($messages);
        $convertedContacts = count(array_filter($messages, fn($m) => ($m['status'] ?? '') === 'converted'));
        $stats['conversion_rate'] = $totalContacts > 0 ? round(($convertedContacts / $totalContacts) * 100, 1) : 0;

        // Detailed User Stats
        foreach ($users as $u) {
            $role = strtolower(trim($u['role'] ?? 'user'));
            if (!isset($stats['user_role_counts'][$role])) {
                $stats['user_role_counts'][$role] = 0;
            }
            $stats['user_role_counts'][$role]++;
        }

        // Load blog posts for admin
        $blogPosts = $blogModel->getAllPosts(50, 0);

        return $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'users' => $users,
            'projects' => $projects,
            'settings' => $settings,
            'messages' => $messages,
            'blogPosts' => $blogPosts,
            'stats' => $stats
        ]);
    }

    // --- User Management ---
    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            // Basic validation
            if (empty($_POST['full_name']) || empty($_POST['email']) || empty($_POST['password'])) {
                // Handle error
            }
            
            $userModel->create($_POST);
            $this->redirect('/admin/dashboard#users'); // Redirect back to users tab
        }
    }

    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $id = $_POST['id'];
            $userModel->update($id, $_POST);
            $this->redirect('/admin/dashboard#users');
        }
    }

    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $id = $_POST['id'];
            $userModel->delete($id);
            $this->redirect('/admin/dashboard#users');
        }
    }

    // --- Project Management ---
    public function createProject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectModel = new Project();
            // Map form fields to DB fields
            $data = [
                'client_id' => $_POST['client_id'], // Ensure view provides this
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'budget' => $_POST['budget'] ?? 0,
                'status' => $_POST['status'] ?? 'proposed',
                'start_date' => $_POST['start_date'] ?? date('Y-m-d'),
                'deadline' => $_POST['deadline'] ?? null,
                // New fields (Ensure DB has these or use meta table)
                'category' => $_POST['category'] ?? null,
                'tags' => $_POST['tags'] ?? null,
                'live_url' => $_POST['live_url'] ?? null,
                'demo_url' => $_POST['demo_url'] ?? null,
                'progress' => $_POST['progress'] ?? 0,
                'best_case_completion' => !empty($_POST['best_case_completion']) ? $_POST['best_case_completion'] : null,
                'worst_case_completion' => !empty($_POST['worst_case_completion']) ? $_POST['worst_case_completion'] : null,
            ];
            
            // Handle specialized images
            $imageFields = ['image' => 'image_path', 'dashboard_img' => 'dashboard_img', 'product_page_img' => 'product_page_img', 'admin_panel_img' => 'admin_panel_img'];
            foreach ($imageFields as $formField => $dbField) {
                if (isset($_FILES[$formField]) && $_FILES[$formField]['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'public/uploads/projects/';
                    $absUploadDir = APP_ROOT . '/' . $uploadDir;
                    if (!is_dir($absUploadDir)) mkdir($absUploadDir, 0777, true);
                    
                    $fileName = time() . '_' . $formField . '_' . basename($_FILES[$formField]['name']);
                    if (move_uploaded_file($_FILES[$formField]['tmp_name'], $absUploadDir . $fileName)) {
                        $data[$dbField] = 'uploads/projects/' . $fileName;
                    }
                }
            }

            // Map other fields
            $data['problem'] = $_POST['problem'] ?? null;
            $data['solution'] = $_POST['solution'] ?? null;
            $data['results_impact'] = $_POST['results_impact'] ?? null;

            $projectModel->insert($data);
            
            // Notify Client
            if (!empty($data['client_id'])) {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $data['client_id'],
                    'New Project Started',
                    'Your project "' . $data['title'] . '" has been successfully initialized.',
                    'info',
                    APP_URL . '/client/dashboard'
                );
            }
            
            $this->redirect('/admin/dashboard#projects');
        }
    }

    public function updateProject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectModel = new Project();
            $id = $_POST['id'];
            
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'category' => $_POST['category'] ?? null,
                'tags' => $_POST['tags'] ?? null,
                'live_url' => $_POST['live_url'] ?? null,
                'demo_url' => $_POST['demo_url'] ?? null,
                'status' => $_POST['status'] ?? null,
                'progress' => $_POST['progress'] ?? 0,
                'problem' => $_POST['problem'] ?? null,
                'solution' => $_POST['solution'] ?? null,
                'results_impact' => $_POST['results_impact'] ?? null,
            ];

            // Handle specialized images
            $imageFields = ['image' => 'image_path', 'dashboard_img' => 'dashboard_img', 'product_page_img' => 'product_page_img', 'admin_panel_img' => 'admin_panel_img'];
            foreach ($imageFields as $formField => $dbField) {
                if (isset($_FILES[$formField]) && $_FILES[$formField]['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'public/uploads/projects/';
                    $absUploadDir = APP_ROOT . '/' . $uploadDir;
                    if (!is_dir($absUploadDir)) mkdir($absUploadDir, 0777, true);
                    
                    $fileName = time() . '_' . $formField . '_' . basename($_FILES[$formField]['name']);
                    if (move_uploaded_file($_FILES[$formField]['tmp_name'], $absUploadDir . $fileName)) {
                        $data[$dbField] = 'uploads/projects/' . $fileName;
                    }
                }
            }
            
            $projectModel->update($id, $data);

            // Notify Client of status change
            $project = $projectModel->find($id);
            if ($project && !empty($project['client_id'])) {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $project['client_id'],
                    'Project Status Updated',
                    'The status of your project "' . $project['title'] . '" has been updated to: ' . strtoupper($data['status']),
                    'info',
                    APP_URL . '/client/dashboard'
                );
            }

            $this->redirect('/admin/dashboard#projects');
        }
    }

    public function deleteProject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $projectModel = new Project();
            $id = $_POST['id'];
            $projectModel->delete($id);
            $this->redirect('/admin/dashboard#projects');
        }
    }

    public function manageProject() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/admin/dashboard#projects');
        }

        $projectModel = new Project();
        $project = $projectModel->getProjectById($id);

        if (!$project) {
            $this->redirect('/admin/dashboard#projects');
        }

        // Fetch Tasks (handle missing table)
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $tasks = [];
        try {
            $stmt = $pdo->prepare("SELECT * FROM project_tasks WHERE project_id = ? ORDER BY created_at DESC");
            $stmt->execute([$id]);
            $tasks = $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Tasks table doesn't exist yet
            error_log('Tasks table not found: ' . $e->getMessage());
        }

        // Fetch Payments (handle missing table)
        $payments = [];
        try {
            $stmt = $pdo->prepare("SELECT * FROM payments WHERE project_id = ? ORDER BY payment_date DESC");
            $stmt->execute([$id]);
            $payments = $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Payments table doesn't exist yet
            error_log('Payments table not found: ' . $e->getMessage());
        }

        // Fetch Dev/PM/Admin users for assignment
        $team = $pdo->query("SELECT * FROM users WHERE role IN ('developer', 'project_manager', 'admin') AND is_active = 1 ORDER BY full_name")->fetchAll();

        return $this->view('admin/project/manage', [
            'title' => 'Manage Project: ' . $project['title'],
            'project' => $project,
            'tasks' => $tasks,
            'payments' => $payments,
            'team' => $team
        ]);
    }

    public function updateProgress() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $progress = $_POST['progress'];
            
            $projectModel = new Project();
            $projectModel->update($id, ['progress' => $progress]);

            // Notify Client
            $project = $projectModel->find($id);
            if ($project && !empty($project['client_id'])) {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $project['client_id'],
                    'Project Progress Update',
                    'Work on "' . $project['title'] . '" is now ' . $progress . '% complete.',
                    'info',
                    APP_URL . '/client/dashboard'
                );
            }
            
            $this->redirect('/admin/project/manage?id=' . $id);
        }
    }

    // Task Actions
    public function createTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("INSERT INTO project_tasks (project_id, title, description, status, assigned_to, due_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['project_id'],
                $_POST['title'],
                $_POST['description'],
                $_POST['status'] ?? 'pending',
                !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null,
                !empty($_POST['due_date']) ? $_POST['due_date'] : null,
                $_SESSION['user_id']
            ]);

            // Notify Assigned Developer
            if (!empty($_POST['assigned_to'])) {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $_POST['assigned_to'],
                    'New Task Assigned',
                    'You have been assigned a new task: ' . $_POST['title'],
                    'info',
                    APP_URL . '/team/tasks'
                );
            }

            $this->redirect('/admin/project/manage?id=' . $_POST['project_id']);
        }
    }

    public function updateTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("UPDATE project_tasks SET title = ?, description = ?, status = ?, assigned_to = ?, due_date = ? WHERE id = ?");
            $stmt->execute([
                $_POST['title'],
                $_POST['description'],
                $_POST['status'],
                !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null,
                !empty($_POST['due_date']) ? $_POST['due_date'] : null,
                $_POST['id']
            ]);

            // Notify if assigned_to changed or remains set (optional: only notify if changed)
            if (!empty($_POST['assigned_to'])) {
                $notificationModel = new Notification();
                $notificationModel->create(
                    $_POST['assigned_to'],
                    'Task Updated',
                    'A task assigned to you has been updated: ' . $_POST['title'],
                    'info',
                    APP_URL . '/team/tasks'
                );
            }

            $this->redirect('/admin/project/manage?id=' . $_POST['project_id']);
        }
    }

    public function deleteTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("DELETE FROM project_tasks WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $this->redirect('/admin/project/manage?id=' . $_POST['project_id']);
        }
    }

    // Payment Actions
    public function createPayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("INSERT INTO payments (project_id, amount, description, status, payment_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['project_id'],
                $_POST['amount'],
                $_POST['description'] ?? '',
                $_POST['status'] ?? 'pending',
                !empty($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d H:i:s')
            ]);
            $this->redirect('/admin/project/manage?id=' . $_POST['project_id']);
        }
    }

    public function updatePayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("UPDATE payments SET amount = ?, description = ?, status = ?, payment_date = ? WHERE id = ?");
            $stmt->execute([
                $_POST['amount'],
                $_POST['description'],
                $_POST['status'],
                !empty($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d H:i:s'),
                $_POST['id']
            ]);

            // Notify Client if payment is marked as paid
            if (($_POST['status'] ?? '') === 'paid') {
                $projectModel = new Project();
                $project = $projectModel->find($_POST['project_id']);
                if ($project && !empty($project['client_id'])) {
                    $notificationModel = new Notification();
                    $notificationModel->create(
                        $project['client_id'],
                        'Payment Confirmed',
                        'We have confirmed your payment of ' . $_POST['amount'] . ' CFA for project "' . $project['title'] . '".',
                        'success',
                        APP_URL . '/client/dashboard'
                    );
                }
            }

            $this->redirect('/admin/project/manage?id=' . $_POST['project_id']);
        }
    }

    public function deletePayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $this->redirect('/admin/project/manage?id=' . $_POST['project_id']);
        }
    }

    // --- Settings ---
    public function updateSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel = new Setting();
            foreach ($_POST as $key => $value) {
                if ($key !== 'submit') {
                    $settingModel->set($key, $value);
                }
            }
            $this->redirect('/admin/dashboard#settings');
        }
    }

    // --- Project Request Lifecycle ---
    public function acceptRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $contactModel = new Contact();
            $projectModel = new Project();
            
            $request = $contactModel->where('id', $id)->first();
            if ($request) {
                // Update request status
                $contactModel->update($id, ['status' => 'accepted']);
                
                // Convert to Project if it has user_id
                if (!empty($request['user_id'])) {
                    $budget = $request['budget'] ?? 0;
                    if (!is_numeric($budget)) {
                        $budget = 0;
                    }
                    $projectData = [
                        'client_id' => $request['user_id'],
                        'title' => 'Project: ' . ($request['project_type'] ?? 'Request'),
                        'description' => $request['description'],
                        'budget' => $budget,
                        'status' => 'active',
                        'category' => $request['project_type'] ?? null,
                        'start_date' => date('Y-m-d')
                    ];
                    $projectModel->insert($projectData);

                    // Notify Client
                    $notificationModel = new Notification();
                    $notificationModel->create(
                        $request['user_id'],
                        'Project Request Accepted!',
                        'Your request for ' . ($request['project_type'] ?? 'a project') . ' has been accepted. We have started working on it!',
                        'success',
                        APP_URL . '/client/dashboard'
                    );
                }
            }
            $this->redirect('/admin/dashboard#messages');
        }
    }

    public function rejectRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $notes = $_POST['admin_notes'] ?? '';
            $contactModel = new Contact();
            
            $contactModel->update($id, [
                'status' => 'rejected',
                'admin_notes' => $notes
            ]);
            $this->redirect('/admin/dashboard#messages');
        }
    }
}
