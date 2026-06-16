<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Proposal;
use App\Models\Contact;
use App\Models\User;
use App\Services\EmailService;

class ProposalController extends Controller {
    public function __construct() {
        // Check if user is logged in for all methods except public ones
        $publicMethods = ['clientView', 'accept', 'reject'];
        
        // Get current method from URL path or default to index
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Extract method from path (e.g., /admin/proposals/view -> view)
        if (strpos($path, '/admin/proposals/') !== false) {
            $parts = explode('/', $path);
            $currentMethod = $parts[4] ?? 'index';
        } elseif (strpos($path, '/client/proposals/') !== false) {
            $parts = explode('/', $path);
            $currentMethod = $parts[4] ?? 'index';
        } else {
            $currentMethod = 'index';
        }
        
        if (!in_array($currentMethod, $publicMethods)) {
            if (!isset($_SESSION['user_id'])) {
                $this->redirect('/login');
            }
        }
    }

    public function index() {
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }

        $proposalModel = new Proposal();
        $proposals = $proposalModel->getAllProposals();
        $stats = $proposalModel->getStats();

        return $this->view('admin/proposals/index', [
            'title' => 'Proposals Management',
            'proposals' => $proposals,
            'stats' => $stats
        ]);
    }

    public function create() {
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }

        $contactModel = new Contact();
        $userModel = new User();
        
        // Get contacts that don't have proposals yet
        $contacts = $contactModel->orderBy('created_at', 'DESC')->findAll();
        $clients = $userModel->where('role', 'client')->findAll();

        // Get contact ID from URL if provided
        $contactId = $_GET['contact_id'] ?? null;
        $selectedContact = null;
        
        if ($contactId) {
            $selectedContact = $contactModel->find($contactId);
        }

        return $this->view('admin/proposals/create', [
            'title' => 'Create Proposal',
            'contacts' => $contacts,
            'clients' => $clients,
            'selectedContact' => $selectedContact
        ]);
    }

    public function store() {
        if ($_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/proposals/create');
        }

        $data = [
            'project_id' => $_POST['project_id'] ?? null,
            'client_id' => $_POST['client_id'] ?? null,
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'content' => json_encode(['text' => $_POST['content'] ?? $_POST['description'] ?? '']),
            'total_amount' => $_POST['total_amount'] ?? 0,
            'currency' => $_POST['currency'] ?? 'NGN',
            'status' => 'draft',
            'valid_until' => date('Y-m-d', strtotime('+30 days')),
            'created_by' => $_SESSION['user_id'] ?? null
        ];

        // Validate required fields
        if (empty($data['title']) || empty($data['total_amount'])) {
            $_SESSION['error'] = 'Please fill in all required fields (title and total amount).';
            return $this->redirect('/admin/proposals/create');
        }

        // Validate amounts
        if (!is_numeric($data['total_amount']) || $data['total_amount'] <= 0) {
            $_SESSION['error'] = 'Please enter a valid total amount.';
            return $this->redirect('/admin/proposals/create');
        }

        $proposalModel = new Proposal();
        
        if ($proposalModel->insert($data)) {
            $_SESSION['success'] = 'Proposal created successfully!';
            return $this->redirect('/admin/proposals');
        } else {
            $_SESSION['error'] = 'Failed to create proposal. Please try again.';
            return $this->redirect('/admin/proposals/create');
        }
    }

    public function viewProposal() {
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/admin/proposals');
        }

        $proposalModel = new Proposal();
        $proposal = $proposalModel->getProposalById($id);

        if (!$proposal) {
            $_SESSION['error'] = 'Proposal not found.';
            return $this->redirect('/admin/proposals');
        }

        return $this->view('admin/proposals/view', [
            'title' => 'View Proposal',
            'proposal' => $proposal
        ]);
    }

    public function edit() {
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('/dashboard');
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/admin/proposals');
        }

        $proposalModel = new Proposal();
        $proposal = $proposalModel->getProposalById($id);

        if (!$proposal) {
            $_SESSION['error'] = 'Proposal not found.';
            return $this->redirect('/admin/proposals');
        }

        // Don't allow editing if already sent
        if ($proposal['status'] !== 'draft') {
            $_SESSION['error'] = 'Cannot edit proposal that has been sent.';
            return $this->redirect('/admin/proposals/view?id=' . $id);
        }

        $contactModel = new Contact();
        $userModel = new User();
        
        // Debug: Check if Contact model works
        try {
            $contacts = $contactModel->orderBy('created_at', 'DESC')->findAll();
            error_log("DEBUG: Found " . count($contacts) . " contacts");
        } catch (Exception $e) {
            error_log("DEBUG: Contact model error: " . $e->getMessage());
            $contacts = [];
        }
        
        $clients = $userModel->where('role', 'client')->findAll();

        return $this->view('admin/proposals/edit', [
            'title' => 'Edit Proposal',
            'proposal' => $proposal,
            'contacts' => $contacts,
            'clients' => $clients
        ]);
    }

    public function update() {
        if ($_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/admin/proposals');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/proposals/edit?id=' . $id);
        }

        $proposalModel = new Proposal();
        $proposal = $proposalModel->getProposalById($id);

        if (!$proposal) {
            $_SESSION['error'] = 'Proposal not found.';
            return $this->redirect('/admin/proposals');
        }

        // Don't allow updating if already sent
        if ($proposal['status'] !== 'draft') {
            $_SESSION['error'] = 'Cannot update proposal that has been sent.';
            return $this->redirect('/admin/proposals/view?id=' . $id);
        }

        $data = [
            'contact_id' => $_POST['contact_id'] ?? null,
            'client_id' => $_POST['client_id'] ?? null,
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'total_amount' => $_POST['total_amount'] ?? 0,
            'deposit_amount' => $_POST['deposit_amount'] ?? 0,
            'timeline_weeks' => $_POST['timeline_weeks'] ?? 0,
            'admin_notes' => $_POST['admin_notes'] ?? ''
        ];

        // Validate required fields
        if (empty($data['title']) || empty($data['total_amount']) || empty($data['timeline_weeks'])) {
            $_SESSION['error'] = 'Please fill in all required fields.';
            return $this->redirect('/admin/proposals/edit?id=' . $id);
        }

        // Validate amounts
        if (!is_numeric($data['total_amount']) || $data['total_amount'] <= 0) {
            $_SESSION['error'] = 'Please enter a valid total amount.';
            return $this->redirect('/admin/proposals/edit?id=' . $id);
        }

        if (!is_numeric($data['deposit_amount']) || $data['deposit_amount'] < 0) {
            $_SESSION['error'] = 'Please enter a valid deposit amount.';
            return $this->redirect('/admin/proposals/edit?id=' . $id);
        }

        if (!is_numeric($data['timeline_weeks']) || $data['timeline_weeks'] <= 0) {
            $_SESSION['error'] = 'Please enter a valid timeline.';
            return $this->redirect('/admin/proposals/edit?id=' . $id);
        }

        if ($proposalModel->update($id, $data)) {
            $_SESSION['success'] = 'Proposal updated successfully!';
            return $this->redirect('/admin/proposals');
        } else {
            $_SESSION['error'] = 'Failed to update proposal. Please try again.';
            return $this->redirect('/admin/proposals/edit?id=' . $id);
        }
    }

    public function send() {
        if ($_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/admin/proposals');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/proposals');
        }

        $proposalModel = new Proposal();
        $proposal = $proposalModel->getProposalById($id);

        if (!$proposal) {
            $_SESSION['error'] = 'Proposal not found.';
            return $this->redirect('/admin/proposals');
        }

        if ($proposal['status'] !== 'draft') {
            $_SESSION['error'] = 'Only draft proposals can be sent.';
            return $this->redirect('/admin/proposals/view?id=' . $id);
        }

        if (!$proposal['client_id']) {
            $_SESSION['error'] = 'Proposal must have a client assigned before sending.';
            return $this->redirect('/admin/proposals/view?id=' . $id);
        }

        if ($proposalModel->sendProposal($id)) {
            $_SESSION['success'] = 'Proposal sent to client successfully!';
            
            // Send email notification to client
            try {
                $emailService = new EmailService();
                $emailService->sendProposalNotification($proposal, 'sent');
            } catch (\Exception $e) {
                // Log error but don't fail the operation
                error_log('Failed to send proposal email: ' . $e->getMessage());
            }
            
            return $this->redirect('/admin/proposals');
        } else {
            $_SESSION['error'] = 'Failed to send proposal. Please try again.';
            return $this->redirect('/admin/proposals/view?id=' . $id);
        }
    }

    public function clientIndex() {
        if ($_SESSION['role'] !== 'client') {
            $this->redirect('/dashboard');
        }

        $proposalModel = new Proposal();
        $proposals = $proposalModel->getProposalsByClient($_SESSION['user_id']);

        return $this->view('client/proposals/index', [
            'title' => 'My Proposals',
            'proposals' => $proposals
        ]);
    }

    public function clientView() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/client/proposals');
        }

        $proposalModel = new Proposal();
        $proposal = $proposalModel->getProposalById($id);

        if (!$proposal) {
            $_SESSION['error'] = 'Proposal not found.';
            return $this->redirect('/client/proposals');
        }

        // Check if proposal belongs to current user
        if (isset($_SESSION['user_id']) && $proposal['client_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/client/proposals');
        }

        return $this->view('client/proposals/view', [
            'title' => 'View Proposal',
            'proposal' => $proposal
        ]);
    }

    public function accept() {
        if ($_SESSION['role'] !== 'client' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/client/proposals');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/client/proposals/view?id=' . $id);
        }

        $proposalModel = new Proposal();
        $proposal = $proposalModel->getProposalById($id);

        if (!$proposal) {
            $_SESSION['error'] = 'Proposal not found.';
            return $this->redirect('/client/proposals');
        }

        if ($proposal['client_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/client/proposals');
        }

        if ($proposal['status'] !== 'sent') {
            $_SESSION['error'] = 'This proposal cannot be accepted.';
            return $this->redirect('/client/proposals/view?id=' . $id);
        }

        if ($proposalModel->acceptProposal($id)) {
            // Create project from accepted proposal
            $projectModel = new \App\Models\Project();
            $projectData = [
                'client_id' => $proposal['client_id'],
                'title' => $proposal['title'],
                'description' => $proposal['description'],
                'budget' => $proposal['total_amount'],
                'status' => 'active',
                'start_date' => date('Y-m-d'),
                'deadline' => date('Y-m-d', strtotime('+' . $proposal['timeline_weeks'] . ' weeks')),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $projectId = $projectModel->insert($projectData);
            
            if ($projectId) {
                // Create payment milestones
                $milestoneModel = new \App\Models\PaymentMilestone();
                $milestoneModel->createMilestonesFromProposal($id, $projectId);
                
                $_SESSION['success'] = 'Proposal accepted! Project created and payment milestones set. We will contact you soon to start the project.';
            } else {
                $_SESSION['success'] = 'Proposal accepted! We will contact you soon to start the project.';
            }
            
            // Send email notification to client
            try {
                $emailService = new EmailService();
                $emailService->sendProposalNotification($proposal, 'accepted');
            } catch (\Exception $e) {
                // Log error but don't fail the operation
                error_log('Failed to send proposal acceptance email: ' . $e->getMessage());
            }
            
            return $this->redirect('/client/proposals');
        } else {
            $_SESSION['error'] = 'Failed to accept proposal. Please try again.';
            return $this->redirect('/client/proposals/view?id=' . $id);
        }
    }

    public function reject() {
        if ($_SESSION['role'] !== 'client' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Proposal ID not provided.';
            return $this->redirect('/client/proposals');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/client/proposals/view?id=' . $id);
        }

        $proposalModel = new Proposal();
        $proposal = $proposalModel->getProposalById($id);

        if (!$proposal) {
            $_SESSION['error'] = 'Proposal not found.';
            return $this->redirect('/client/proposals');
        }

        if ($proposal['client_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/client/proposals');
        }

        if ($proposal['status'] !== 'sent') {
            $_SESSION['error'] = 'This proposal cannot be rejected.';
            return $this->redirect('/client/proposals/view?id=' . $id);
        }

        if ($proposalModel->rejectProposal($id)) {
            $_SESSION['success'] = 'Proposal rejected. Thank you for your feedback.';
            
            // Send email notification to client
            try {
                $emailService = new EmailService();
                $emailService->sendProposalNotification($proposal, 'rejected');
            } catch (\Exception $e) {
                // Log error but don't fail the operation
                error_log('Failed to send proposal rejection email: ' . $e->getMessage());
            }
            
            return $this->redirect('/client/proposals');
        } else {
            $_SESSION['error'] = 'Failed to reject proposal. Please try again.';
            return $this->redirect('/client/proposals/view?id=' . $id);
        }
    }
}
