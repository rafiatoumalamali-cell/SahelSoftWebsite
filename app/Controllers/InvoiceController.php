<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;

class InvoiceController extends Controller {
    private $invoiceModel;
    private $projectModel;
    private $notificationService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        $this->invoiceModel = new Invoice();
        $this->projectModel = new Project();
        $this->notificationService = new NotificationService();
    }

    // --- Admin Methods ---

    public function adminIndex() {
        if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'project_manager') {
            $this->redirect('/dashboard');
        }

        $filters = [
            'status' => $_GET['status'] ?? null,
            'client_id' => $_GET['client_id'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
        ];

        $invoices = $this->invoiceModel->getAllInvoices($filters);
        $stats = $this->invoiceModel->getInvoiceStats();
        $clients = (new User())->where('role', 'client')->findAll();

        return $this->view('admin/invoices/index', [
            'title' => 'Invoice Management',
            'invoices' => $invoices,
            'stats' => $stats,
            'clients' => $clients,
            'filters' => $filters
        ]);
    }

    public function create() {
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }

        $clients = (new User())->where('role', 'client')->findAll();
        $projects = $this->projectModel->findAll();

        return $this->view('admin/invoices/create', [
            'title' => 'Create New Invoice',
            'clients' => $clients,
            'projects' => $projects,
            'selectedClientId' => $_GET['client_id'] ?? null,
            'selectedProjectId' => $_GET['project_id'] ?? null
        ]);
    }

    public function store() {
        if ($_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/invoices');
        }

        if (!csrf_verify()) {
            $_SESSION['error'] = 'Security verification failed.';
            return $this->redirect('/admin/invoices/create');
        }

        $items = [];
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (!empty($item['name']) && !empty($item['unit_price'])) {
                    $items[] = [
                        'name' => $item['name'],
                        'description' => $item['description'] ?? '',
                        'quantity' => floatval($item['quantity'] ?? 1),
                        'unit_price' => floatval($item['unit_price'] ?? 0)
                    ];
                }
            }
        }

        if (empty($items)) {
            $_SESSION['error'] = 'At least one item is required.';
            return $this->redirect('/admin/invoices/create');
        }

        $data = [
            'client_id' => $_POST['client_id'],
            'project_id' => !empty($_POST['project_id']) ? $_POST['project_id'] : null,
            'title' => $_POST['title'] ?? 'Invoice',
            'description' => $_POST['description'] ?? '',
            'tax_rate' => floatval($_POST['tax_rate'] ?? 0),
            'currency' => $_POST['currency'] ?? 'XOF',
            'due_date' => $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days')),
            'notes' => $_POST['notes'] ?? ''
        ];

        $invoiceId = $this->invoiceModel->createInvoice($data, $items);

        if ($invoiceId) {
            $_SESSION['success'] = 'Invoice created as draft.';
            return $this->redirect('/admin/invoices/view?id=' . $invoiceId);
        } else {
            $_SESSION['error'] = 'Failed to create invoice.';
            return $this->redirect('/admin/invoices/create');
        }
    }

    public function viewInvoice() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('/admin/invoices');

        $invoice = $this->invoiceModel->getInvoiceById($id);
        if (!$invoice) {
            $_SESSION['error'] = 'Invoice not found.';
            return $this->redirect('/admin/invoices');
        }

        // Check permissions
        if ($_SESSION['role'] === 'client' && $invoice['client_id'] != $_SESSION['user_id']) {
            $this->redirect('/dashboard');
        }

        $viewPath = ($_SESSION['role'] === 'client') ? 'client/invoices/view' : 'admin/invoices/view';

        return $this->view($viewPath, [
            'title' => 'Invoice ' . $invoice['invoice_number'],
            'invoice' => $invoice
        ]);
    }

    public function send() {
        if ($_SESSION['role'] !== 'admin') $this->redirect('/admin/invoices');

        $id = $_POST['id'] ?? null;
        if ($this->invoiceModel->updateInvoiceStatus($id, 'sent')) {
            $invoice = $this->invoiceModel->getInvoiceById($id);
            
            // Notify Client
            $this->notificationService->sendNotification(
                $invoice['client_id'],
                'New Invoice Received',
                "A new invoice ({$invoice['invoice_number']}) for '{$invoice['title']}' has been issued.",
                [
                    'category' => 'invoice',
                    'type' => 'info',
                    'action_url' => APP_URL . "/client/invoices/view?id={$id}"
                ]
            );

            $_SESSION['success'] = 'Invoice sent to client.';
        }
        $this->redirect('/admin/invoices/view?id=' . $id);
    }

    public function approvePayment() {
        if ($_SESSION['role'] !== 'admin') $this->redirect('/admin/invoices');

        $invoiceId = $_POST['invoice_id'] ?? null;
        $paymentId = $_POST['payment_id'] ?? null;

        $pdo = \App\Core\Database::getInstance()->getConnection();
        
        try {
            $pdo->beginTransaction();

            // Update Payment Status
            $stmt = $pdo->prepare("UPDATE invoice_payments SET status = 'completed', processed_by = ? WHERE id = ?");
            $stmt->execute([$_SESSION['user_id'], $paymentId]);

            // Update Invoice Status
            $this->invoiceModel->updateInvoiceStatus($invoiceId, 'paid', 'verified');

            $pdo->commit();

            $invoice = $this->invoiceModel->getInvoiceById($invoiceId);
            
            // Notify Client
            $this->notificationService->sendNotification(
                $invoice['client_id'],
                'Payment Confirmed',
                "Your payment for invoice {$invoice['invoice_number']} has been verified. Thank you!",
                [
                    'category' => 'invoice',
                    'type' => 'success',
                    'action_url' => APP_URL . "/client/invoices/view?id={$invoiceId}"
                ]
            );

            $_SESSION['success'] = 'Payment approved and invoice marked as paid.';
        } catch (\Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Failed to approve payment: ' . $e->getMessage();
        }

        $this->redirect('/admin/invoices/view?id=' . $invoiceId);
    }

    // --- Client Methods ---

    public function clientIndex() {
        if ($_SESSION['role'] !== 'client') $this->redirect('/dashboard');

        $invoices = $this->invoiceModel->getClientInvoices($_SESSION['user_id']);

        return $this->view('client/invoices/index', [
            'title' => 'My Invoices',
            'invoices' => $invoices
        ]);
    }

    public function submitPayment() {
        if ($_SESSION['role'] !== 'client' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/client/invoices');
        }

        $invoiceId = $_POST['invoice_id'];
        $invoice = $this->invoiceModel->getInvoiceById($invoiceId);

        if (!$invoice || $invoice['client_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Invalid invoice.';
            return $this->redirect('/client/invoices');
        }

        $proofFile = null;
        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'public/uploads/payments/';
            $absUploadDir = APP_ROOT . '/' . $uploadDir;
            if (!is_dir($absUploadDir)) mkdir($absUploadDir, 0777, true);
            
            $fileName = 'INV_' . $invoice['invoice_number'] . '_' . time() . '_' . basename($_FILES['receipt']['name']);
            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $absUploadDir . $fileName)) {
                $proofFile = 'uploads/payments/' . $fileName;
            }
        }

        if (!$proofFile) {
            $_SESSION['error'] = 'Please upload a valid receipt image.';
            return $this->redirect('/client/invoices/view?id=' . $invoiceId);
        }

        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO invoice_payments (invoice_id, payment_amount, payment_method, payment_reference, proof_file, payment_date, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $invoiceId,
            $invoice['total_amount'],
            $_POST['payment_method'] ?? 'bank_transfer',
            $_POST['reference'] ?? '',
            $proofFile,
            date('Y-m-d'),
            'pending'
        ]);

        // Notify Admins
        $admins = (new User())->where('role', 'admin')->findAll();
        foreach ($admins as $admin) {
            $this->notificationService->sendNotification(
                $admin['id'],
                'New Payment Proof',
                "{$_SESSION['full_name']} uploaded a receipt for invoice {$invoice['invoice_number']}.",
                [
                    'category' => 'invoice',
                    'type' => 'info',
                    'action_url' => APP_URL . "/admin/invoices/view?id={$invoiceId}"
                ]
            );
        }

        $_SESSION['success'] = 'Payment receipt uploaded. Our team will verify it shortly.';
        $this->redirect('/client/invoices/view?id=' . $invoiceId);
    }
}
