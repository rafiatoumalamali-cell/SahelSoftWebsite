<?php

namespace App\Models;

use App\Core\Model;

class Invoice extends Model {
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'invoice_number',
        'client_id',
        'project_id',
        'proposal_id',
        'title',
        'description',
        'items',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'currency',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'payment_method',
        'notes',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateInvoiceNumber() {
        // Generate invoice number in format: INV-YYYYMMDD-XXXX
        $date = date('Ymd');
        $prefix = 'INV-' . $date . '-';
        
        // Get the last invoice number for today
        $stmt = $this->pdo->prepare("SELECT invoice_number FROM {$this->table} WHERE invoice_number LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastInvoice = $stmt->fetch();
        
        if ($lastInvoice) {
            // Extract the sequence number and increment
            $sequence = (int) substr($lastInvoice['invoice_number'], -4);
            $sequence++;
        } else {
            $sequence = 1;
        }
        
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function createInvoice($data, $items = []) {
        $data['invoice_number'] = $this->generateInvoiceNumber();
        $data['issue_date'] = $data['issue_date'] ?? date('Y-m-d');
        $data['due_date'] = $data['due_date'] ?? date('Y-m-d', strtotime('+30 days'));
        $data['status'] = 'draft';
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        
        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $itemTotal = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
            $subtotal += $itemTotal;
        }
        
        $data['subtotal'] = $subtotal;
        $data['tax_rate'] = $data['tax_rate'] ?? 0;
        $data['tax_amount'] = $subtotal * ($data['tax_rate'] / 100);
        $data['total_amount'] = $subtotal + $data['tax_amount'];
        $data['line_items'] = json_encode($items); // Use 'line_items' as per schema
        
        // Insert invoice
        return $this->insert($data);
    }

    public function getInvoiceById($id) {
        $stmt = $this->pdo->prepare("
            SELECT i.*, 
                   c.full_name as client_name, c.email as client_email, c.phone as client_phone, c.company_name as client_company,
                   p.title as project_title,
                   pr.title as proposal_title,
                   u.full_name as created_by_name
            FROM {$this->table} i
            LEFT JOIN users c ON i.client_id = c.id
            LEFT JOIN projects p ON i.project_id = p.id
            LEFT JOIN proposals pr ON i.proposal_id = pr.id
            LEFT JOIN users u ON i.created_by = u.id
            WHERE i.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $invoice = $stmt->fetch();
        
        if ($invoice && !empty($invoice['line_items'])) {
            $invoice['items'] = json_decode($invoice['line_items'], true);
        } else {
            $invoice['items'] = [];
        }
        
        // Fetch associated payments
        $stmtPayments = $this->pdo->prepare("SELECT * FROM invoice_payments WHERE invoice_id = :id ORDER BY created_at DESC");
        $stmtPayments->execute(['id' => $id]);
        $invoice['payments'] = $stmtPayments->fetchAll();
        
        return $invoice;
    }

    public function getAllInvoices($filters = []) {
        $sql = "
            SELECT i.*, 
                   c.full_name as client_name, c.email as client_email,
                   p.title as project_title,
                   u.full_name as created_by_name
            FROM {$this->table} i
            LEFT JOIN users c ON i.client_id = c.id
            LEFT JOIN projects p ON i.project_id = p.id
            LEFT JOIN users u ON i.created_by = u.id
            WHERE i.deleted_at IS NULL
        ";
        
        $params = [];
        
        if (!empty($filters['client_id'])) {
            $sql .= " AND i.client_id = :client_id";
            $params['client_id'] = $filters['client_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND i.issue_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND i.issue_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY i.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll();
        
        // Add items to each invoice
        foreach ($invoices as &$invoice) {
            $invoice['items'] = !empty($invoice['line_items']) ? json_decode($invoice['line_items'], true) : [];
        }
        
        return $invoices;
    }

    public function getClientInvoices($clientId) {
        return $this->getAllInvoices(['client_id' => $clientId]);
    }

    public function updateInvoiceStatus($id, $status, $paymentMethod = null) {
        $data = ['status' => $status];
        
        if ($status === 'paid') {
            $data['paid_date'] = date('Y-m-d');
            if ($paymentMethod) {
                $data['payment_method'] = $paymentMethod;
            }
        }
        
        return $this->update($id, $data);
    }

    public function sendInvoice($id) {
        return $this->update($id, ['status' => 'sent']);
    }

    public function getOverdueInvoices() {
        $stmt = $this->pdo->prepare("
            SELECT i.*, c.full_name as client_name, c.email as client_email
            FROM {$this->table} i
            LEFT JOIN users c ON i.client_id = c.id
            WHERE i.status = 'sent' 
            AND i.due_date < CURDATE()
            ORDER BY i.due_date ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getInvoiceStats($filters = []) {
        $sql = "
            SELECT 
                COUNT(*) as total_invoices,
                COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_invoices,
                COUNT(CASE WHEN status = 'sent' THEN 1 END) as sent_invoices,
                COUNT(CASE WHEN status = 'overdue' THEN 1 END) as overdue_invoices,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN status != 'paid' THEN total_amount ELSE 0 END) as unpaid_amount
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['client_id'])) {
            $sql .= " AND client_id = :client_id";
            $params['client_id'] = $filters['client_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND issue_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND issue_date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getMonthlyRevenue($months = 12) {
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE_FORMAT(paid_date, '%Y-%m') as month,
                SUM(total_amount) as revenue,
                COUNT(*) as invoice_count
            FROM {$this->table}
            WHERE status = 'paid' 
            AND paid_date >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(paid_date, '%Y-%m')
            ORDER BY month DESC
        ");
        $stmt->execute(['months' => $months]);
        return $stmt->fetchAll();
    }

    public function createFromProposal($proposalId, $customizations = []) {
        $proposalModel = new \App\Models\Proposal();
        $proposal = $proposalModel->getProposalById($proposalId);
        
        if (!$proposal) {
            return false;
        }

        // Create invoice items from proposal
        $items = [
            [
                'name' => $proposal['title'],
                'description' => $proposal['description'],
                'quantity' => 1,
                'unit_price' => $proposal['total_amount'],
                'discount_percent' => 0,
                'tax_rate' => 0,
                'total_amount' => $proposal['total_amount']
            ]
        ];

        $invoiceData = [
            'client_id' => $proposal['client_id'],
            'project_id' => null, // Will be set when project is created
            'proposal_id' => $proposalId,
            'title' => 'Invoice for ' . $proposal['title'],
            'description' => 'Invoice generated from proposal #' . $proposal['id'],
            'tax_rate' => $customizations['tax_rate'] ?? 0,
            'currency' => $customizations['currency'] ?? 'XOF',
            'notes' => $customizations['notes'] ?? null
        ];

        return $this->createInvoice($invoiceData, $items);
    }

    public function createFromMilestones($projectId, $milestoneIds = []) {
        $milestoneModel = new \App\Models\PaymentMilestone();
        $projectModel = new \App\Models\Project();
        
        $project = $projectModel->find($projectId);
        if (!$project) {
            return false;
        }

        // Get milestones
        $milestones = [];
        if (empty($milestoneIds)) {
            $milestones = $milestoneModel->getMilestonesByProject($projectId);
        } else {
            foreach ($milestoneIds as $milestoneId) {
                $milestone = $milestoneModel->getMilestoneById($milestoneId);
                if ($milestone) {
                    $milestones[] = $milestone;
                }
            }
        }

        // Create invoice items
        $items = [];
        foreach ($milestones as $milestone) {
            $items[] = [
                'name' => $milestone['title'],
                'description' => $milestone['description'],
                'quantity' => 1,
                'unit_price' => $milestone['amount'],
                'discount_percent' => 0,
                'tax_rate' => 0,
                'total_amount' => $milestone['amount']
            ];
        }

        $invoiceData = [
            'client_id' => $project['client_id'],
            'project_id' => $projectId,
            'title' => 'Invoice for ' . $project['title'],
            'description' => 'Invoice for project milestones',
            'tax_rate' => 0,
            'currency' => 'XOF'
        ];

        return $this->createInvoice($invoiceData, $items);
    }

    public function deleteInvoice($id) {
        // Check if invoice has payments in invoice_payments table
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM invoice_payments WHERE invoice_id = :id");
        $stmt->execute(['id' => $id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            return false; // Cannot delete invoice with payments
        }

        // Soft delete
        return $this->update($id, [
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $_SESSION['user_id'] ?? null
        ]);
    }

    public function duplicateInvoice($id) {
        $originalInvoice = $this->getInvoiceById($id);
        
        if (!$originalInvoice) {
            return false;
        }

        // Create new invoice data
        $newInvoiceData = [
            'client_id' => $originalInvoice['client_id'],
            'project_id' => $originalInvoice['project_id'],
            'title' => $originalInvoice['title'] . ' (Copy)',
            'description' => $originalInvoice['description'],
            'tax_rate' => $originalInvoice['tax_rate'],
            'currency' => $originalInvoice['currency'],
            'notes' => $originalInvoice['notes'],
            'created_by' => $_SESSION['user_id'] ?? null
        ];

        return $this->createInvoice($newInvoiceData, $originalInvoice['items']);
    }
}
