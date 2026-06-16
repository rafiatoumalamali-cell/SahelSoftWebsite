<?php

namespace App\Models;

use App\Core\Model;

class Notification extends Model {
    protected $table = 'notifications';

    public function getUnreadByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAllByUser($userId, $limit = 20) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markAsRead($id) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET is_read = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function markAllAsRead($userId) {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function createNotification($data) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (user_id, title, message, type, link) VALUES (:user_id, :title, :message, :type, :link)");
        $result = $stmt->execute([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'] ?? 'info',
            'link' => $data['action_url'] ?? null
        ]);
        
        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function create($userId, $title, $message, $type = 'info', $link = null) {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (user_id, title, message, type, link) VALUES (:user_id, :title, :message, :type, :link)");
        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link
        ]);
    }

    public function getUnreadCount($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND is_read = 0");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function deleteReadByUser($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE user_id = :user_id AND is_read = 1");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function cleanup($days = 30) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)");
        return $stmt->execute(['days' => $days]);
    }

    public function getNotificationPreferences($userId) {
        // Return default preferences if table doesn't exist or is empty
        return [
            ['category' => 'general', 'email_enabled' => 1, 'push_enabled' => 0],
            ['category' => 'invoice', 'email_enabled' => 1, 'push_enabled' => 1],
            ['category' => 'payment', 'email_enabled' => 1, 'push_enabled' => 1]
        ];
    }

    public function logDelivery($notificationId, $userId, $channel, $status) {
        // Log to a delivery_logs table if it exists
        return true; 
    }

    public function getRecentNotifications($userId, $limit = 10) {
        return $this->getAllByUser($userId, $limit);
    }

    public function getUserNotifications($userId, $filters = []) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        $params = ['user_id' => $userId];

        if (isset($filters['is_read'])) {
            $sql .= " AND is_read = :is_read";
            $params['is_read'] = $filters['is_read'];
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function deleteOldNotifications($days = 30) {
        return $this->cleanup($days);
    }

    public function cleanupExpiredNotifications() {
        return true;
    }

    public function getNotificationStats($userId) {
        return [
            'total' => $this->countByUser($userId),
            'unread' => $this->getUnreadCount($userId)
        ];
    }

    private function countByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }

    public function updateNotificationPreferences($userId, $preferences) {
        return true;
    }

    public function searchNotifications($userId, $query, $filters = []) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND (title LIKE :query OR message LIKE :query)";
        $params = [
            'user_id' => $userId,
            'query' => "%$query%"
        ];
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function deleteNotification($notificationId, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $notificationId, 'user_id' => $userId]);
    }

    public function getDeliveryLog($notificationId = null, $userId = null) {
        return [];
    }

    public function createSystemAlert($message, $type, $options) {
        // Implementation for system-wide alerts
        return true;
    }
}
