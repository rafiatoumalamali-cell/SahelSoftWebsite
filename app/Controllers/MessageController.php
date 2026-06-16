<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Notification;

class MessageController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    public function index() {
        $userModel = new User();
        $messageModel = new Message();
        
        $contacts = $userModel->getVisibleContacts($_SESSION['user_id'], $_SESSION['role']); 
        $recentContacts = $messageModel->getRecentContacts($_SESSION['user_id']);
        $unreadCounts = $messageModel->getUnreadCountsPerContact($_SESSION['user_id']);
        
        return $this->view('team/messages', [
            'title' => 'Team Messages',
            'contacts' => $contacts,
            'recentContacts' => $recentContacts,
            'unreadCounts' => $unreadCounts
        ]);
    }

    public function getChat() {
        $receiverId = $_GET['receiver_id'] ?? null;
        if (!$receiverId) {
            echo json_encode(['success' => false]);
            return;
        }

        $messageModel = new Message();
        $messages = $messageModel->getConversation($_SESSION['user_id'], $receiverId);
        
        // Mark as read
        $messageModel->markAsRead($receiverId, $_SESSION['user_id']);
        
        echo json_encode(['success' => true, 'messages' => $messages]);
    }

    public function send() {
        $receiverId = $_POST['receiver_id'] ?? null;
        $messageText = $_POST['message'] ?? null;
        
        if (!$receiverId || !$messageText) {
            echo json_encode(['success' => false]);
            return;
        }

        $messageModel = new Message();
        $success = $messageModel->sendMessage($_SESSION['user_id'], $receiverId, $messageText);
        
        if ($success) {
            // Send Notification to receiver
            $notificationModel = new Notification();
            $notificationModel->create(
                $receiverId,
                'New Message',
                'You have a new message from ' . ($_SESSION['full_name'] ?? 'a team member'),
                'info',
                APP_URL . '/team/messages?user_id=' . $_SESSION['user_id']
            );
        }
        
        echo json_encode(['success' => $success]);
    }
}
