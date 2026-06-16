<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;
use App\Models\Contact;

class ClientController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
            $this->redirect('/login');
        }
    }

    public function dashboard() {
        $projectModel = new Project();
        $contactModel = new Contact();
        
        $projects = $projectModel->getProjectsByClient($_SESSION['user_id']);
        $requests = $contactModel->where('user_id', $_SESSION['user_id'])->orderBy('created_at', 'DESC')->findAll();
        
        return $this->view('client/dashboard', [
            'title' => 'Client Dashboard',
            'projects' => $projects,
            'requests' => $requests
        ]);
    }

    public function projectDetails() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/client/dashboard');
        }

        $projectModel = new Project();
        $project = $projectModel->getProjectById($id);

        // Security check: ensure project belongs to logged-in client
        if (!$project || $project['client_id'] != $_SESSION['user_id']) {
             $this->redirect('/client/dashboard');
        }

        // Fetch tasks
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM project_tasks WHERE project_id = ? ORDER BY created_at DESC");
        $stmt->execute([$id]);
        $tasks = $stmt->fetchAll();

        // Fetch payments
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE project_id = ? ORDER BY payment_date DESC");
        $stmt->execute([$id]);
        $payments = $stmt->fetchAll();

        // Calculate paid amount
        $paid = 0;
        foreach ($payments as $p) {
            if ($p['status'] === 'verified') $paid += $p['amount'];
        }

        return $this->view('client/project_details', [
            'title' => $project['title'],
            'project' => $project,
            'tasks' => $tasks,
            'payments' => $payments,
            'paid_amount' => $paid,
            'payment_count' => count($payments)
        ]);
    }

    public function messages() {
        $id = $_GET['id'] ?? null;
        return $this->view('client/messages', [
            'title' => 'Contact Team',
            'project_id' => $id
        ]);
    }

    public function payments() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('/client/dashboard');
        
        $projectModel = new Project();
        $project = $projectModel->getProjectById($id);
        
        return $this->view('client/payments', [
            'title' => 'Make Payment',
            'project' => $project
        ]);
    }

    public function upload() {
        $id = $_GET['id'] ?? null;
        return $this->view('client/upload', [
            'title' => 'Upload Files',
            'project_id' => $id
        ]);
    }

    public function sendMessage() {
        // Redirect to messages view which now shows contact info
        $this->redirect('/client/project/messages?id=' . ($_POST['project_id'] ?? ''));
    }

    public function submitPayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $projectId = $_POST['project_id'];
            $amount = $_POST['amount'];
            $description = $_POST['description'] ?? 'Client Uploaded Receipt';
            
            $proofFile = null;
            if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'public/uploads/payments/';
                $absUploadDir = APP_ROOT . '/' . $uploadDir;
                if (!is_dir($absUploadDir)) mkdir($absUploadDir, 0777, true);
                
                $fileName = time() . '_' . basename($_FILES['receipt']['name']);
                if (move_uploaded_file($_FILES['receipt']['tmp_name'], $absUploadDir . $fileName)) {
                    $proofFile = 'uploads/payments/' . $fileName;
                }
            }

            // Try to insert with proof_file column first
            try {
                $stmt = $pdo->prepare("INSERT INTO payments (project_id, amount, description, status, proof_file, payment_date) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $projectId,
                    $amount,
                    $description,
                    'pending',
                    $proofFile,
                    date('Y-m-d H:i:s')
                ]);
            } catch (PDOException $e) {
                // If proof_file column doesn't exist, insert without it
                if (strpos($e->getMessage(), 'Unknown column \'proof_file\'') !== false) {
                    $stmt = $pdo->prepare("INSERT INTO payments (project_id, amount, description, status, payment_date) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $projectId,
                        $amount,
                        $description,
                        'pending',
                        date('Y-m-d H:i:s')
                    ]);
                } else {
                    throw $e; // Re-throw if it's a different error
                }
            }

            // Notify Admin
            $projectModel = new \App\Models\Project();
            $project = $projectModel->find($projectId);
            $notificationModel = new \App\Models\Notification();
            
            // Find admins to notify
            $userModel = new \App\Models\User();
            $admins = $userModel->where('role', 'admin')->findAll();
            
            foreach ($admins as $admin) {
                $notificationModel->create(
                    $admin['id'],
                    'New Payment Proof Uploaded',
                    ($_SESSION['full_name'] ?? 'A client') . ' uploaded a payment receipt for project "' . ($project['title'] ?? 'Unknown') . '".',
                    'info',
                    APP_URL . '/admin/project/manage?id=' . $projectId
                );
            }

            $this->redirect('/client/project?id=' . $projectId . '&success=payment_submitted');
        }
    }
    public function allMessages() {
        $projectModel = new Project();
        $projects = $projectModel->getProjectsByClient($_SESSION['user_id']);
        
        return $this->view('client/all_messages', [
            'title' => __('messages'),
            'projects' => $projects
        ]);
    }

    public function processUpload() {}
}
