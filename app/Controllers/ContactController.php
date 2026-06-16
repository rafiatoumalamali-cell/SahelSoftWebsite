<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Services\EmailService;

class ContactController extends Controller {
    
    public function submit() {
        // Check if it's POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isAjax()) {
                return $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            }
            return $this->redirect('/contact');
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            if ($this->isAjax()) {
                return $this->jsonResponse(['success' => false, 'message' => 'Invalid security token. Please refresh the page and try again.'], 400);
            }
            return $this->redirect('/contact');
        }

        // Validate required fields
        $errors = $this->validateForm();
        if (!empty($errors)) {
            if ($this->isAjax()) {
                return $this->jsonResponse(['success' => false, 'message' => implode(', ', $errors)], 400);
            }
            return $this->view('contact', ['title' => 'Contact Us', 'errors' => $errors]);
        }

        $userId = $_SESSION['user_id'] ?? null;
        
        // Fallback: If no user_id in session, try to find user by email
        if (!$userId && !empty($_POST['email'])) {
            $userModel = new \App\Models\User();
            $user = $userModel->findByEmail($_POST['email']);
            if ($user) {
                $userId = $user['id'];
            }
        }

        // Prepare data - Use 'new' status (matches database ENUM)
        $data = [
            'user_id'      => $userId,
            'name'         => trim($_POST['name'] ?? ''),
            'organization' => !empty($_POST['organization']) ? trim($_POST['organization']) : null,
            'email'        => trim($_POST['email'] ?? ''),
            'phone'        => !empty($_POST['phone']) ? trim($_POST['phone']) : null,
            'project_type' => !empty($_POST['project_type']) ? $_POST['project_type'] : null,
            'budget'       => !empty($_POST['budget']) ? $_POST['budget'] : null,
            'description'  => trim($_POST['description'] ?? ''),
            'status'       => 'new',
            
            // Business Information
            'business_type' => !empty($_POST['business_type']) ? $_POST['business_type'] : null,
            'website_purpose' => !empty($_POST['website_purpose']) ? trim($_POST['website_purpose']) : null,
            'target_audience' => !empty($_POST['target_audience']) ? trim($_POST['target_audience']) : null,
            'existing_website' => !empty($_POST['existing_website']) ? $_POST['existing_website'] : null,
            'existing_url' => !empty($_POST['existing_url']) ? trim($_POST['existing_url']) : null,
            'industry' => !empty($_POST['industry']) ? $_POST['industry'] : null,
            
            // Design & Branding
            'design_style' => !empty($_POST['design_style']) ? $_POST['design_style'] : null,
            'branding_colors' => !empty($_POST['branding_colors']) ? trim($_POST['branding_colors']) : null,
            'branding_fonts' => !empty($_POST['branding_fonts']) ? trim($_POST['branding_fonts']) : null,
            'competitor_urls' => !empty($_POST['competitor_urls']) ? trim($_POST['competitor_urls']) : null,
            
            // Technical Requirements
            'required_features' => !empty($_POST['required_features']) ? json_encode($_POST['required_features']) : null,
            'cms_preference' => !empty($_POST['cms_preference']) ? $_POST['cms_preference'] : null,
            'mobile_responsive' => !empty($_POST['mobile_responsive']) ? $_POST['mobile_responsive'] : 'yes',
            'multilingual' => !empty($_POST['multilingual']) ? $_POST['multilingual'] : 'no',
            'languages' => !empty($_POST['languages']) ? trim($_POST['languages']) : null,
            'analytics_tracking' => !empty($_POST['analytics_tracking']) ? $_POST['analytics_tracking'] : 'yes',
            'payment_integration' => !empty($_POST['payment_integrations']) ? 'yes' : 'no',
            'payment_integrations' => !empty($_POST['payment_integrations']) ? json_encode($_POST['payment_integrations']) : null,
            'integrations_needed' => !empty($_POST['integrations_needed']) ? trim($_POST['integrations_needed']) : null,
            
            // Hosting & Domain
            'domain_name' => !empty($_POST['domain_name']) ? trim($_POST['domain_name']) : null,
            'hosting_requirements' => !empty($_POST['hosting_requirements']) ? $_POST['hosting_requirements'] : null,
            
            // SEO & Marketing
            'seo_requirements' => !empty($_POST['seo_requirements']) ? trim($_POST['seo_requirements']) : null,
            'current_marketing_tools' => !empty($_POST['current_marketing_tools']) ? trim($_POST['current_marketing_tools']) : null,
            
            // Timeline
            'timeline_start' => !empty($_POST['timeline_start']) ? $_POST['timeline_start'] : null,
            'timeline_deadline' => !empty($_POST['timeline_deadline']) ? $_POST['timeline_deadline'] : null,
            
            // Support & Training
            'ongoing_support_needed' => !empty($_POST['ongoing_support_needed']) ? $_POST['ongoing_support_needed'] : 'no',
            'support_level' => !empty($_POST['support_level']) ? $_POST['support_level'] : null,
            'training_needed' => !empty($_POST['training_needed']) ? $_POST['training_needed'] : 'no',
            'training_details' => !empty($_POST['training_details']) ? trim($_POST['training_details']) : null,
            
            // Additional Notes
            'additional_notes' => !empty($_POST['additional_notes']) ? trim($_POST['additional_notes']) : null
        ];

        // Handle File Uploads
        $attachments = $this->handleFileUploads();
        $data['attachments'] = !empty($attachments) ? json_encode($attachments) : null;

        // Debug logging
        $logFile = APP_ROOT . '/writable/logs/contact_debug.log';
        if (!is_dir(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
        
        file_put_contents($logFile, "\n=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
        file_put_contents($logFile, "Data to insert: " . json_encode($data) . "\n", FILE_APPEND);
        
        try {
            $contactModel = new Contact();
            
            // Insert the data
            $result = $contactModel->insert($data);
            
            file_put_contents($logFile, "Insert result: " . ($result ? "SUCCESS" : "FAILED") . "\n", FILE_APPEND);
            
            if ($result) {
                $fileCount = count($attachments);
                $successMsg = 'Thank you! Your message has been sent successfully.';
                if ($fileCount > 0) {
                    $successMsg .= " ($fileCount file" . ($fileCount > 1 ? 's' : '') . " uploaded)";
                }
                
                file_put_contents($logFile, "Success message sent\n", FILE_APPEND);
                
                if ($this->isAjax()) {
                    return $this->jsonResponse(['success' => true, 'message' => $successMsg]);
                }
                
                return $this->view('contact', [
                    'title' => 'Contact Us',
                    'success' => $successMsg
                ]);
            } else {
                file_put_contents($logFile, "FAILED: Insert returned false\n", FILE_APPEND);
                
                if ($this->isAjax()) {
                    return $this->jsonResponse(['success' => false, 'message' => 'Failed to send message. Please try again.'], 500);
                }
                
                return $this->view('contact', [
                    'title' => 'Contact Us',
                    'error' => 'Failed to send message. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            file_put_contents($logFile, "EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents($logFile, "Stack trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
            
            if ($this->isAjax()) {
                return $this->jsonResponse(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
            
            return $this->view('contact', [
                'title' => 'Contact Us',
                'error' => 'An error occurred. Please try again.'
            ]);
        }
    }
    
    /**
     * Validate form data
     */
    private function validateForm() {
        $errors = [];
        
        // Validate name
        if (empty($_POST['name']) || strlen(trim($_POST['name'])) < 2) {
            $errors[] = 'Please enter your full name.';
        }
        
        // Validate email
        if (empty($_POST['email'])) {
            $errors[] = 'Please enter your email address.';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        // Validate description
        if (empty($_POST['description']) || strlen(trim($_POST['description'])) < 20) {
            $errors[] = 'Please provide a detailed description (minimum 20 characters).';
        }
        
        return $errors;
    }
    
    /**
     * Handle file uploads
     */
    private function handleFileUploads() {
        $attachments = [];
        
        // Check if files were uploaded
        if (!isset($_FILES['attachments']) || !isset($_FILES['attachments']['name'][0]) || empty($_FILES['attachments']['name'][0])) {
            return $attachments;
        }
        
        $files = $_FILES['attachments'];
        $count = count($files['name']);
        
        // Define upload directory
        $relUploadDir = 'uploads/contacts/';
        $uploadDir = APP_ROOT . '/public/' . $relUploadDir;
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Allowed file types
        $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
        $maxFileSize = 10 * 1024 * 1024; // 10MB
        
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            $tmpName = $files['tmp_name'][$i];
            $name = basename($files['name'][$i]);
            $fileSize = $files['size'][$i];
            $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            
            // Validate
            if (!in_array($fileExt, $allowedExtensions)) {
                continue;
            }
            
            if ($fileSize > $maxFileSize) {
                continue;
            }
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $name);
            $targetPath = $uploadDir . $filename;
            
            if (move_uploaded_file($tmpName, $targetPath)) {
                $attachments[] = $relUploadDir . $filename;
            }
        }
        
        return $attachments;
    }
    
    /**
     * Check if request is AJAX
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Return JSON response
     */
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function convertToProject() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        $contactId = $_POST['contact_id'] ?? null;
        if (!$contactId) {
            $_SESSION['error'] = 'Contact ID not provided.';
            return $this->redirect('/admin/dashboard');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/dashboard');
        }

        $contactModel = new Contact();
        $contact = $contactModel->find($contactId);

        if (!$contact) {
            $_SESSION['error'] = 'Contact not found.';
            return $this->redirect('/admin/dashboard');
        }

        // Check if already converted
        if ($contact['status'] === 'converted') {
            $_SESSION['error'] = 'This contact has already been converted to a project.';
            return $this->redirect('/admin/dashboard');
        }

        // Create or find user account
        $userModel = new \App\Models\User();
        $user = $userModel->findByEmail($contact['email']);

        if (!$user) {
            // Create new client account
            $userData = [
                'full_name' => $contact['name'],
                'email' => $contact['email'],
                'phone' => $contact['phone'],
                'company_name' => $contact['organization'],
                'role' => 'client',
                'password_hash' => password_hash('changeme123', PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $userId = $userModel->insert($userData);
            if (!$userId) {
                $_SESSION['error'] = 'Failed to create client account.';
                return $this->redirect('/admin/dashboard');
            }
            $user = $userModel->find($userId);
            
            // Send welcome email to new client
            try {
                $emailService = new EmailService();
                $emailService->sendWelcomeEmail($user, 'changeme123');
            } catch (\Exception $e) {
                error_log('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        // Validate user exists before creating project
        if (!$user || !isset($user['id'])) {
            $_SESSION['error'] = 'Failed to retrieve user account information.';
            return $this->redirect('/admin/dashboard');
        }

        // Create project
        $projectModel = new \App\Models\Project();
        $projectData = [
            'client_id' => $user['id'],
            'title' => !empty($contact['project_type']) ? ucfirst($contact['project_type']) . ' Project' : 'New Project',
            'description' => $contact['description'],
            'budget' => !empty($contact['budget']) ? (float)preg_replace('/[^0-9.]/', '', $contact['budget']) : 0.00,
            'status' => 'proposed',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $projectId = $projectModel->insert($projectData);
        if (!$projectId) {
            $_SESSION['error'] = 'Failed to create project.';
            return $this->redirect('/admin/dashboard');
        }

        // Update contact status
        $contactModel->update($contactId, ['status' => 'converted']);

        $_SESSION['success'] = 'Contact successfully converted to project! Project ID: #' . $projectId;
        return $this->redirect('/admin/project/manage?id=' . $projectId);
    }
}