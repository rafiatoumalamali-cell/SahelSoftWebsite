<?php

namespace App\Models;

use App\Core\Model;

class Message extends Model {
    protected $table = 'team_messages';

    public function getConversation($user1, $user2) {
        $stmt = $this->pdo->prepare("SELECT m.*, u.full_name as sender_name 
                                   FROM {$this->table} m 
                                   JOIN users u ON m.sender_id = u.id
                                   WHERE (m.sender_id = :u1 AND m.receiver_id = :u2) 
                                   OR (m.sender_id = :u2 AND m.receiver_id = :u1) 
                                   ORDER BY m.created_at ASC");
        $stmt->execute(['u1' => $user1, 'u2' => $user2]);
        return $stmt->fetchAll();
    }

    public function getRecentContacts($userId) {
        // Fetch users the current user has chatted with
        $stmt = $this->pdo->prepare("SELECT DISTINCT u.id, u.full_name, u.role, u.avatar 
                                   FROM users u 
                                   JOIN {$this->table} m ON (u.id = m.sender_id OR u.id = m.receiver_id) 
                                   WHERE (m.sender_id = :userId OR m.receiver_id = :userId) 
                                   AND u.id != :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    public function sendMessage($senderId, $receiverId, $message) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (sender_id, receiver_id, message) VALUES (:sender, :receiver, :msg)");
        return $stmt->execute([
            'sender' => $senderId,
            'receiver' => $receiverId,
            'msg' => $message
        ]);
    }

    public function getUnreadCount($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE receiver_id = :userId AND is_read = 0");
        $stmt->execute(['userId' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function markAsRead($senderId, $receiverId) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET is_read = 1 WHERE sender_id = :sender AND receiver_id = :receiver AND is_read = 0");
        return $stmt->execute(['sender' => $senderId, 'receiver' => $receiverId]);
    }

    public function getUnreadCountsPerContact($userId) {
        $stmt = $this->pdo->prepare("SELECT sender_id, COUNT(*) as count FROM {$this->table} WHERE receiver_id = :userId AND is_read = 0 GROUP BY sender_id");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
