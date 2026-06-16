<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Services\EmailService;

class NotificationService {
    private $notificationModel;
    private $emailService;

    public function __construct() {
        $this->notificationModel = new Notification();
        $this->emailService = new EmailService();
    }

    public function sendNotification($userId, $title, $message, $options = []) {
        $data = array_merge([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => 'info',
            'category' => 'general'
        ], $options);

        $notificationId = $this->notificationModel->createNotification($data);
        
        if ($notificationId) {
            // Check user preferences for additional delivery methods
            $this->processDeliveryMethods($notificationId, $userId, $data);
        }

        return $notificationId;
    }

    public function sendFromTemplate($templateName, $userId, $variables = [], $options = []) {
        return $this->notificationModel->createFromTemplate($templateName, $userId, $variables, $options);
    }

    public function broadcastToRole($role, $templateName, $variables = [], $options = []) {
        return $this->notificationModel->broadcastToRole($role, $templateName, $variables, $options);
    }

    public function broadcastToAll($templateName, $variables = [], $options = []) {
        return $this->notificationModel->broadcastToAll($templateName, $variables, $options);
    }

    public function sendProposalNotification($proposal, $action) {
        $variables = [
            'proposal_title' => $proposal['title'],
            'client_name' => $proposal['client_name'] ?? 'Client'
        ];

        $options = [
            'related_id' => $proposal['id'],
            'related_type' => 'proposal',
            'action_url' => APP_URL . "/admin/proposals/view?id={$proposal['id']}",
            'action_text' => 'View Proposal'
        ];

        switch ($action) {
            case 'sent':
                // Notify admin
                $this->broadcastToRole('admin', 'proposal_sent', $variables, $options);
                break;
            case 'accepted':
                // Notify admin
                $this->broadcastToRole('admin', 'proposal_accepted', $variables, $options);
                // Notify client
                if (!empty($proposal['client_id'])) {
                    $this->sendFromTemplate('proposal_accepted', $proposal['client_id'], $variables, $options);
                }
                break;
            case 'rejected':
                // Notify admin
                $this->broadcastToRole('admin', 'proposal_rejected', $variables, $options);
                break;
        }
    }

    public function sendProjectNotification($project, $action, $userId = null) {
        $variables = [
            'project_title' => $project['title'],
            'client_name' => $project['client_name'] ?? 'Client'
        ];

        $options = [
            'related_id' => $project['id'],
            'related_type' => 'project',
            'action_url' => APP_URL . "/admin/project/manage?id={$project['id']}",
            'action_text' => 'View Project'
        ];

        switch ($action) {
            case 'created':
                $this->broadcastToRole('admin', 'project_created', $variables, $options);
                break;
            case 'completed':
                $this->broadcastToRole('admin', 'project_completed', $variables, $options);
                if (!empty($project['client_id'])) {
                    $options['action_url'] = APP_URL . "/client/project?id={$project['id']}";
                    $this->sendFromTemplate('project_completed', $project['client_id'], $variables, $options);
                }
                break;
            case 'updated':
                if ($userId) {
                    $this->sendNotification($userId, 'Project Updated', "Project '{$project['title']}' has been updated.", $options);
                }
                break;
        }
    }

    public function sendPaymentNotification($payment, $action) {
        $variables = [
            'amount' => '₦' . number_format($payment['amount'], 2),
            'project_title' => $payment['project_title'] ?? 'Project',
            'milestone_title' => $payment['title'] ?? 'Payment Milestone',
            'due_date' => date('M j, Y', strtotime($payment['due_date'] ?? 'now'))
        ];

        $options = [
            'related_id' => $payment['id'],
            'related_type' => 'payment',
            'action_url' => APP_URL . "/admin/payments",
            'action_text' => 'View Payments'
        ];

        switch ($action) {
            case 'received':
                $this->broadcastToRole('admin', 'payment_received', $variables, $options);
                if (!empty($payment['client_id'])) {
                    $options['action_url'] = APP_URL . "/client/payments";
                    $this->sendFromTemplate('payment_received', $payment['client_id'], $variables, $options);
                }
                break;
            case 'due':
                if (!empty($payment['client_id'])) {
                    $this->sendFromTemplate('milestone_due', $payment['client_id'], $variables, $options);
                }
                break;
            case 'overdue':
                $this->broadcastToRole('admin', 'payment_overdue', $variables, [
                    'important' => true,
                    'type' => 'warning'
                ]);
                break;
        }
    }

    public function sendContactNotification($contact, $action) {
        $variables = [
            'name' => $contact['name'],
            'email' => $contact['email'],
            'project_type' => $contact['project_type'] ?? 'General Inquiry'
        ];

        $options = [
            'related_id' => $contact['id'],
            'related_type' => 'contact',
            'action_url' => APP_URL . "/admin/contacts",
            'action_text' => 'View Contact'
        ];

        switch ($action) {
            case 'received':
                $this->broadcastToRole('admin', 'contact_received', $variables, $options);
                break;
            case 'converted':
                $this->broadcastToRole('admin', 'contact_converted', $variables, $options);
                break;
        }
    }

    public function sendFileNotification($file, $action, $sharedWith = null) {
        $variables = [
            'file_name' => $file['original_name'],
            'shared_by_name' => $file['uploaded_by_name'] ?? 'User'
        ];

        $options = [
            'related_id' => $file['id'],
            'related_type' => 'file',
            'action_url' => APP_URL . "/admin/files/view?id={$file['id']}",
            'action_text' => 'View File'
        ];

        switch ($action) {
            case 'uploaded':
                if ($file['uploaded_by']) {
                    $this->sendNotification($file['uploaded_by'], 'File Uploaded', "File '{$file['original_name']}' has been uploaded successfully.", $options);
                }
                break;
            case 'shared':
                if ($sharedWith) {
                    $this->sendFromTemplate('file_shared', $sharedWith, $variables, $options);
                }
                break;
        }
    }

    public function sendSecurityNotification($userId, $alertMessage, $type = 'warning') {
        $variables = [
            'alert_message' => $alertMessage
        ];

        $options = [
            'important' => true,
            'type' => $type,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days'))
        ];

        return $this->sendFromTemplate('security_alert', $userId, $variables, $options);
    }

    public function sendInvoiceNotification($invoice, $action) {
        $variables = [
            'invoice_number' => $invoice['invoice_number'],
            'amount' => '₦' . number_format($invoice['total_amount'], 2),
            'client_name' => $invoice['client_name'] ?? 'Client'
        ];

        $options = [
            'related_id' => $invoice['id'],
            'related_type' => 'invoice',
            'action_url' => APP_URL . "/admin/invoices",
            'action_text' => 'View Invoice'
        ];

        switch ($action) {
            case 'sent':
                $this->broadcastToRole('admin', 'invoice_sent', $variables, $options);
                if (!empty($invoice['client_id'])) {
                    $options['action_url'] = APP_URL . "/client/invoices";
                    $this->sendFromTemplate('invoice_sent', $invoice['client_id'], $variables, $options);
                }
                break;
            case 'paid':
                $this->broadcastToRole('admin', 'invoice_paid', $variables, $options);
                break;
            case 'overdue':
                $this->broadcastToRole('admin', 'invoice_overdue', $variables, [
                    'important' => true,
                    'type' => 'warning'
                ]);
                break;
        }
    }

    public function sendSystemAlert($message, $type = 'info', $targetUsers = null) {
        $options = [
            'important' => $type === 'error' || $type === 'warning',
            'type' => $type,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+3 days'))
        ];

        if ($targetUsers) {
            foreach ($targetUsers as $userId) {
                $this->sendNotification($userId, 'System Alert', $message, $options);
            }
        } else {
            $this->notificationModel->createSystemAlert($message, $type, $options);
        }
    }

    public function getUnreadCount($userId) {
        return $this->notificationModel->getUnreadCount($userId);
    }

    public function getRecentNotifications($userId, $limit = 10) {
        return $this->notificationModel->getRecentNotifications($userId, $limit);
    }

    public function markAsRead($notificationId, $userId) {
        return $this->notificationModel->markAsRead($notificationId, $userId);
    }

    public function markAllAsRead($userId) {
        return $this->notificationModel->markAllAsRead($userId);
    }

    public function getUserNotifications($userId, $filters = []) {
        return $this->notificationModel->getUserNotifications($userId, $filters);
    }

    private function processDeliveryMethods($notificationId, $userId, $data) {
        $preferences = $this->notificationModel->getNotificationPreferences($userId);
        $categoryPreference = $this->getPreferenceForCategory($preferences, $data['category']);

        // In-app notification (always created)
        $this->notificationModel->logDelivery($notificationId, $userId, 'in_app', 'sent');

        // Email notification
        if ($categoryPreference && $categoryPreference['email_enabled']) {
            $this->sendEmailNotification($userId, $data);
        }

        // Push notification (if enabled)
        if ($categoryPreference && $categoryPreference['push_enabled']) {
            $this->sendPushNotification($userId, $data);
        }
    }

    private function getPreferenceForCategory($preferences, $category) {
        foreach ($preferences as $pref) {
            if ($pref['category'] === $category) {
                return $pref;
            }
        }
        return null;
    }

    private function sendEmailNotification($userId, $data) {
        try {
            $userModel = new User();
            $user = $userModel->find($userId);
            
            if (!$user) {
                return false;
            }

            // Create a simple email notification
            $subject = $data['title'];
            $message = $data['message'];
            
            // This would integrate with the EmailService to send actual emails
            // For now, we'll just log it
            error_log("Email notification sent to {$user['email']}: {$subject}");
            
            return true;
        } catch (\Exception $e) {
            error_log('Failed to send email notification: ' . $e->getMessage());
            return false;
        }
    }

    private function sendPushNotification($userId, $data) {
        // This would integrate with a push notification service
        // For now, we'll just log it
        error_log("Push notification sent to user {$userId}: {$data['title']}");
        return true;
    }

    public function cleanupOldNotifications() {
        return $this->notificationModel->deleteOldNotifications(30);
    }

    public function cleanupExpiredNotifications() {
        return $this->notificationModel->cleanupExpiredNotifications();
    }

    public function getNotificationStats($userId) {
        return $this->notificationModel->getNotificationStats($userId);
    }

    public function updateNotificationPreferences($userId, $preferences) {
        return $this->notificationModel->updateNotificationPreferences($userId, $preferences);
    }

    public function getNotificationPreferences($userId) {
        return $this->notificationModel->getNotificationPreferences($userId);
    }

    public function searchNotifications($userId, $query, $filters = []) {
        return $this->notificationModel->searchNotifications($userId, $query, $filters);
    }

    public function deleteNotification($notificationId, $userId) {
        return $this->notificationModel->deleteNotification($notificationId, $userId);
    }

    public function getDeliveryLog($notificationId = null, $userId = null) {
        return $this->notificationModel->getDeliveryLog($notificationId, $userId);
    }
}
