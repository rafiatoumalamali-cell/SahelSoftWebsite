<?php

namespace App\Models;

use App\Core\Model;

class PaymentMilestone extends Model {
    protected $table = 'payment_milestones';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_id',
        'proposal_id',
        'title',
        'description',
        'amount',
        'due_date',
        'status',
        'payment_id',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getMilestonesByProject($projectId) {
        $stmt = $this->pdo->prepare("SELECT pm.*, p.title as project_title FROM payment_milestones pm LEFT JOIN projects p ON pm.project_id = p.id WHERE pm.project_id = :project_id ORDER BY pm.due_date ASC");
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll();
    }

    public function getMilestonesByProposal($proposalId) {
        $stmt = $this->pdo->prepare("SELECT pm.*, p.title as project_title FROM payment_milestones pm LEFT JOIN projects p ON pm.project_id = p.id WHERE pm.proposal_id = :proposal_id ORDER BY pm.due_date ASC");
        $stmt->execute(['proposal_id' => $proposalId]);
        return $stmt->fetchAll();
    }

    public function getMilestoneById($id) {
        $stmt = $this->pdo->prepare("SELECT pm.*, p.title as project_title, pr.title as proposal_title FROM payment_milestones pm LEFT JOIN projects p ON pm.project_id = p.id LEFT JOIN proposals pr ON pm.proposal_id = pr.id WHERE pm.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getAllMilestones() {
        $stmt = $this->pdo->query("SELECT pm.*, p.title as project_title, u.full_name as client_name FROM payment_milestones pm LEFT JOIN projects p ON pm.project_id = p.id LEFT JOIN users u ON p.client_id = u.id ORDER BY pm.due_date ASC");
        return $stmt->fetchAll();
    }

    public function getPendingMilestones() {
        $stmt = $this->pdo->query("SELECT pm.*, p.title as project_title, u.full_name as client_name FROM payment_milestones pm LEFT JOIN projects p ON pm.project_id = p.id LEFT JOIN users u ON p.client_id = u.id WHERE pm.status = 'pending' ORDER BY pm.due_date ASC");
        return $stmt->fetchAll();
    }

    public function getOverdueMilestones() {
        $stmt = $this->pdo->prepare("SELECT pm.*, p.title as project_title, u.full_name as client_name FROM payment_milestones pm LEFT JOIN projects p ON pm.project_id = p.id LEFT JOIN users u ON p.client_id = u.id WHERE pm.status = 'pending' AND pm.due_date < CURDATE() ORDER BY pm.due_date ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markAsPaid($milestoneId, $paymentId = null) {
        $sql = "UPDATE {$this->table} SET status = 'paid', payment_id = :payment_id, updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $milestoneId, 'payment_id' => $paymentId]);
    }

    public function checkOverdueMilestones() {
        $sql = "UPDATE {$this->table} SET status = 'overdue', updated_at = NOW() WHERE status = 'pending' AND due_date < CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    }

    public function getProjectMilestoneStats($projectId) {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'paid' => 0,
            'overdue' => 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'pending_amount' => 0
        ];

        $stmt = $this->pdo->prepare("SELECT status, COUNT(*) as count, SUM(amount) as total FROM payment_milestones WHERE project_id = :project_id GROUP BY status");
        $stmt->execute(['project_id' => $projectId]);
        $results = $stmt->fetchAll();

        foreach ($results as $row) {
            $stats['total'] += $row['count'];
            $stats[$row['status']] = $row['count'];
            $stats['total_amount'] += $row['total'];

            if ($row['status'] === 'paid') {
                $stats['paid_amount'] = $row['total'];
            } else {
                $stats['pending_amount'] += $row['total'];
            }
        }

        return $stats;
    }

    public function getOverallStats() {
        $stats = [
            'total_milestones' => 0,
            'pending_milestones' => 0,
            'paid_milestones' => 0,
            'overdue_milestones' => 0,
            'total_value' => 0,
            'paid_value' => 0,
            'pending_value' => 0
        ];

        $stmt = $this->pdo->query("SELECT status, COUNT(*) as count, SUM(amount) as total FROM payment_milestones GROUP BY status");
        $results = $stmt->fetchAll();

        foreach ($results as $row) {
            $stats['total_milestones'] += $row['count'];
            $stats[$row['status'] . '_milestones'] = $row['count'];
            $stats['total_value'] += $row['total'];

            if ($row['status'] === 'paid') {
                $stats['paid_value'] = $row['total'];
            } else {
                $stats['pending_value'] += $row['total'];
            }
        }

        return $stats;
    }

    public function createMilestonesFromProposal($proposalId, $projectId) {
        // Auto-create milestones based on proposal
        $proposalModel = new \App\Models\Proposal();
        $proposal = $proposalModel->getProposalById($proposalId);

        if (!$proposal) {
            return false;
        }

        $milestones = [];

        // Create deposit milestone if specified
        if ($proposal['deposit_amount'] > 0) {
            $milestones[] = [
                'project_id' => $projectId,
                'proposal_id' => $proposalId,
                'title' => 'Initial Deposit',
                'description' => 'Required deposit to begin project work',
                'amount' => $proposal['deposit_amount'],
                'due_date' => date('Y-m-d', strtotime('+1 week')), // Due in 1 week
                'status' => 'pending'
            ];
        }

        // Create final payment milestone
        $remainingAmount = $proposal['total_amount'] - $proposal['deposit_amount'];
        if ($remainingAmount > 0) {
            $milestones[] = [
                'project_id' => $projectId,
                'proposal_id' => $proposalId,
                'title' => 'Final Payment',
                'description' => 'Final payment upon project completion',
                'amount' => $remainingAmount,
                'due_date' => date('Y-m-d', strtotime('+' . $proposal['timeline_weeks'] . ' weeks')), // Due at project completion
                'status' => 'pending'
            ];
        }

        // Insert milestones
        foreach ($milestones as $milestone) {
            $this->insert($milestone);
        }

        return count($milestones);
    }
}
