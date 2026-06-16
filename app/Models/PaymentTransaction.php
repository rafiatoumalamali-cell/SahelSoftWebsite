<?php

namespace App\Models;

use App\Core\Model;

class PaymentTransaction extends Model {
    protected $table = 'payment_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'invoice_id',
        'client_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'gateway_response',
        'processed_by',
        'notes',
        'created_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    public function createTransaction($data) {
        $data['status'] = 'pending';
        $data['processed_by'] = $_SESSION['user_id'] ?? null;
        
        return $this->insert($data);
    }

    public function getTransactionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, 
                   c.full_name as client_name, c.email as client_email,
                   i.invoice_number, i.title as invoice_title,
                   u.full_name as processed_by_name
            FROM {$this->table} pt
            LEFT JOIN users c ON pt.client_id = c.id
            LEFT JOIN invoices i ON pt.invoice_id = i.id
            LEFT JOIN users u ON pt.processed_by = u.id
            WHERE pt.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getTransactionsByInvoice($invoiceId) {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, c.full_name as client_name
            FROM {$this->table} pt
            LEFT JOIN users c ON pt.client_id = c.id
            WHERE pt.invoice_id = :invoice_id
            ORDER BY pt.created_at DESC
        ");
        $stmt->execute(['invoice_id' => $invoiceId]);
        return $stmt->fetchAll();
    }

    public function getClientTransactions($clientId) {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, i.invoice_number, i.title as invoice_title
            FROM {$this->table} pt
            LEFT JOIN invoices i ON pt.invoice_id = i.id
            WHERE pt.client_id = :client_id
            ORDER BY pt.created_at DESC
        ");
        $stmt->execute(['clientId' => $clientId]);
        return $stmt->fetchAll();
    }

    public function getAllTransactions($filters = []) {
        $sql = "
            SELECT pt.*, 
                   c.full_name as client_name, c.email as client_email,
                   i.invoice_number, i.title as invoice_title,
                   u.full_name as processed_by_name
            FROM {$this->table} pt
            LEFT JOIN users c ON pt.client_id = c.id
            LEFT JOIN invoices i ON pt.invoice_id = i.id
            LEFT JOIN users u ON pt.processed_by = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['client_id'])) {
            $sql .= " AND pt.client_id = :client_id";
            $params['client_id'] = $filters['client_id'];
        }
        
        if (!empty($filters['invoice_id'])) {
            $sql .= " AND pt.invoice_id = :invoice_id";
            $params['invoice_id'] = $filters['invoice_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND pt.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['payment_method'])) {
            $sql .= " AND pt.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND pt.created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND pt.created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY pt.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $filters['limit'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateTransactionStatus($id, $status, $gatewayResponse = null) {
        $data = ['status' => $status];
        
        if ($gatewayResponse) {
            $data['gateway_response'] = json_encode($gatewayResponse);
        }
        
        return $this->update($id, $data);
    }

    public function markAsCompleted($id, $transactionId = null, $gatewayResponse = null) {
        $data = [
            'status' => 'completed',
            'transaction_id' => $transactionId
        ];
        
        if ($gatewayResponse) {
            $data['gateway_response'] = json_encode($gatewayResponse);
        }
        
        return $this->update($id, $data);
    }

    public function markAsFailed($id, $gatewayResponse = null) {
        $data = ['status' => 'failed'];
        
        if ($gatewayResponse) {
            $data['gateway_response'] = json_encode($gatewayResponse);
        }
        
        return $this->update($id, $data);
    }

    public function refundTransaction($id, $reason = null) {
        $transaction = $this->getTransactionById($id);
        
        if (!$transaction || $transaction['status'] !== 'completed') {
            return false;
        }

        // Update transaction status
        $data = [
            'status' => 'refunded',
            'notes' => ($transaction['notes'] ?? '') . "\n\nRefunded: " . ($reason ?? 'No reason provided')
        ];
        
        return $this->update($id, $data);
    }

    public function getPaymentStats($filters = []) {
        $sql = "
            SELECT 
                COUNT(*) as total_transactions,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_transactions,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_transactions,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_transactions,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount,
                SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_amount
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['client_id'])) {
            $sql .= " AND client_id = :client_id";
            $params['client_id'] = $filters['client_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getMonthlyRevenue($months = 12) {
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as revenue,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count,
                COUNT(*) as total_count
            FROM {$this->table}
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month DESC
        ");
        $stmt->execute(['months' => $months]);
        return $stmt->fetchAll();
    }

    public function getPaymentMethodStats() {
        $stmt = $this->pdo->prepare("
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as completed_amount
            FROM {$this->table}
            GROUP BY payment_method
            ORDER BY total_amount DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function processManualPayment($invoiceId, $amount, $paymentMethod, $notes = null) {
        // Get invoice details
        $invoiceModel = new \App\Models\Invoice();
        $invoice = $invoiceModel->getInvoiceById($invoiceId);
        
        if (!$invoice) {
            return false;
        }

        // Create transaction record
        $transactionData = [
            'invoice_id' => $invoiceId,
            'client_id' => $invoice['client_id'],
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'transaction_id' => 'MANUAL-' . date('YmdHis') . '-' . uniqid(),
            'notes' => $notes
        ];

        $transactionId = $this->createTransaction($transactionData);
        
        if ($transactionId) {
            // Mark transaction as completed
            $this->markAsCompleted($transactionId);
            
            // Update invoice status if fully paid
            $invoiceModel = new \App\Models\Invoice();
            $totalPaid = $this->getTotalPaidForInvoice($invoiceId);
            
            if ($totalPaid >= $invoice['total_amount']) {
                $invoiceModel->updateInvoiceStatus($invoiceId, 'paid', $paymentMethod);
            } else {
                $invoiceModel->updateInvoiceStatus($invoiceId, 'sent'); // Partial payment
            }
            
            return $transactionId;
        }
        
        return false;
    }

    public function getTotalPaidForInvoice($invoiceId) {
        $stmt = $this->pdo->prepare("
            SELECT SUM(amount) as total_paid
            FROM {$this->table}
            WHERE invoice_id = :invoice_id AND status = 'completed'
        ");
        $stmt->execute(['invoice_id' => $invoiceId]);
        $result = $stmt->fetch();
        
        return $result ? (float) $result['total_paid'] : 0;
    }

    public function getOutstandingBalance($invoiceId) {
        $invoiceModel = new \App\Models\Invoice();
        $invoice = $invoiceModel->getInvoiceById($invoiceId);
        
        if (!$invoice) {
            return 0;
        }
        
        $totalPaid = $this->getTotalPaidForInvoice($invoiceId);
        return $invoice['total_amount'] - $totalPaid;
    }

    public function searchTransactions($query, $filters = []) {
        $sql = "
            SELECT pt.*, 
                   c.full_name as client_name, c.email as client_email,
                   i.invoice_number, i.title as invoice_title
            FROM {$this->table} pt
            LEFT JOIN users c ON pt.client_id = c.id
            LEFT JOIN invoices i ON pt.invoice_id = i.id
            WHERE (pt.transaction_id LIKE :query OR i.invoice_number LIKE :query OR c.full_name LIKE :query OR c.email LIKE :query)
        ";
        
        $params = ['query' => '%' . $query . '%'];
        
        // Add additional filters
        if (!empty($filters['status'])) {
            $sql .= " AND pt.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['payment_method'])) {
            $sql .= " AND pt.payment_method = :payment_method";
            $params['payment_method'] = $filters['payment_method'];
        }
        
        $sql .= " ORDER BY pt.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function exportTransactions($filters = [], $format = 'csv') {
        $transactions = $this->getAllTransactions($filters);
        
        switch ($format) {
            case 'csv':
                return $this->exportToCSV($transactions);
            case 'json':
            default:
                return json_encode($transactions, JSON_PRETTY_PRINT);
        }
    }

    private function exportToCSV($transactions) {
        $csv = "ID,Invoice Number,Client Name,Amount,Payment Method,Status,Transaction ID,Created At\n";
        
        foreach ($transactions as $transaction) {
            $csv .= sprintf(
                "%d,%s,%s,%.2f,%s,%s,%s,%s\n",
                $transaction['id'],
                $transaction['invoice_number'] ?? 'N/A',
                $transaction['client_name'] ?? 'N/A',
                $transaction['amount'],
                $transaction['payment_method'],
                $transaction['status'],
                $transaction['transaction_id'] ?? 'N/A',
                $transaction['created_at']
            );
        }
        
        return $csv;
    }
}
