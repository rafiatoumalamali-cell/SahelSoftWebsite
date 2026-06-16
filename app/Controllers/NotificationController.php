<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Notification;

class NotificationController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    public function index() {
        $notificationModel = new Notification();
        $notifications = $notificationModel->getAllByUser($_SESSION['user_id']);
        return $this->view('notifications/index', [
            'title' => 'My Notifications',
            'notifications' => $notifications
        ]);
    }

    public function markAsRead() {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            return;
        }

        $notificationModel = new Notification();
        $success = $notificationModel->markAsRead($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    public function markAllAsRead() {
        $notificationModel = new Notification();
        $success = $notificationModel->markAllAsRead($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    public function recent() {
        $notificationModel = new Notification();
        $notifications = $notificationModel->getAllByUser($_SESSION['user_id'], 10);
        $count = $notificationModel->getUnreadCount($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['notifications' => $notifications, 'unread_count' => $count]);
    }

    public function unreadCount() {
        $notificationModel = new Notification();
        $count = $notificationModel->getUnreadCount($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['unread_count' => $count]);
    }

    public function deleteRead() {
        $notificationModel = new Notification();
        $success = $notificationModel->deleteReadByUser($_SESSION['user_id']);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    public function cleanup() {
        // Only allow admin or system to trigger cleanup
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            return;
        }
        $notificationModel = new Notification();
        $success = $notificationModel->cleanup(30); // Clean older than 30 days
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }
}
