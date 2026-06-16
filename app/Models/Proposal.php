<?php

namespace App\Models;

use App\Core\Model;

class Proposal extends Model {
    protected $table = 'proposals';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_id',
        'client_id',
        'title',
        'description',
        'proposal_template_id',
        'content',
        'total_amount',
        'currency',
        'status',
        'valid_until',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'created_by',
        'deleted_at',
        'deleted_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getProposalsByClient($clientId) {
        $stmt = $this->pdo->prepare("SELECT p.*, u.full_name as client_name, u.email as client_email FROM proposals p LEFT JOIN users u ON p.client_id = u.id WHERE p.client_id = :client_id AND p.deleted_at IS NULL ORDER BY p.created_at DESC");
        $stmt->execute(['client_id' => $clientId]);
        return $stmt->fetchAll();
    }

    public function getProposalById($id) {
        $stmt = $this->pdo->prepare("SELECT p.*, u.full_name as client_name, u.email as client_email FROM proposals p LEFT JOIN users u ON p.client_id = u.id WHERE p.id = :id AND p.deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getAllProposals() {
        $stmt = $this->pdo->query("SELECT p.*, u.full_name as client_name, u.email as client_email FROM proposals p LEFT JOIN users u ON p.client_id = u.id WHERE p.deleted_at IS NULL ORDER BY p.created_at DESC");
        return $stmt->fetchAll();
    }

    public function getPendingProposals() {
        $stmt = $this->pdo->query("SELECT p.*, u.full_name as client_name, u.email as client_email FROM proposals p LEFT JOIN users u ON p.client_id = u.id WHERE p.status = 'sent' AND p.deleted_at IS NULL ORDER BY p.sent_at ASC");
        return $stmt->fetchAll();
    }

    public function getProposalsByContact($contactId) {
        $stmt = $this->pdo->prepare("SELECT * FROM proposals WHERE contact_id = :contact_id ORDER BY created_at DESC");
        $stmt->execute(['contact_id' => $contactId]);
        return $stmt->fetchAll();
    }

    public function sendProposal($id) {
        $sql = "UPDATE {$this->table} SET status = 'sent', sent_date = CURDATE(), updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function acceptProposal($id) {
        $sql = "UPDATE {$this->table} SET status = 'accepted', response_date = CURDATE(), updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function rejectProposal($id) {
        $sql = "UPDATE {$this->table} SET status = 'rejected', response_date = CURDATE(), updated_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getStats() {
        $stats = [
            'total' => 0,
            'draft' => 0,
            'sent' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'expired' => 0,
            'total_value' => 0,
            'accepted_value' => 0
        ];

        $stmt = $this->pdo->query("SELECT status, COUNT(*) as count, SUM(total_amount) as value FROM proposals GROUP BY status");
        $results = $stmt->fetchAll();

        foreach ($results as $row) {
            $stats['total'] += $row['count'];
            $stats[$row['status']] = $row['count'];
            
            if ($row['status'] === 'accepted') {
                $stats['accepted_value'] = $row['value'];
            }
            $stats['total_value'] += $row['value'];
        }

        return $stats;
    }
}
