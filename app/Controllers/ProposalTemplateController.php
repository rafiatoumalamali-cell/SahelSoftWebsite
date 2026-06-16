<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProposalTemplate;
use App\Models\Proposal;
use App\Services\NotificationService;

class ProposalTemplateController extends Controller {
    private $templateModel;
    private $proposalModel;
    private $notificationService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        $this->templateModel = new ProposalTemplate();
        $this->proposalModel = new Proposal();
        $this->notificationService = new NotificationService();
    }

    public function index() {
        $filters = $this->getFiltersFromRequest();
        $templates = $this->templateModel->getAllTemplates($filters);
        $categories = $this->getTemplateCategories();
        
        return $this->view('admin/proposals/templates/index', [
            'title' => 'Proposal Templates',
            'templates' => $templates,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $categories = $this->getTemplateCategories();
            
            return $this->view('admin/proposals/templates/create', [
                'title' => 'Create Proposal Template',
                'categories' => $categories
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/proposals/templates/create');
            }

            $templateData = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'category' => $_POST['category'] ?? 'custom',
                'template_content' => $this->parseTemplateContent($_POST),
                'terms_conditions' => $_POST['terms_conditions'] ?? '',
                'is_active' => isset($_POST['is_active'])
            ];

            $pricingTiers = $this->parsePricingTiers($_POST);
            $sections = $this->parseSections($_POST);
            $variables = $this->parseVariables($_POST);

            $templateId = $this->templateModel->createTemplate($templateData, $pricingTiers, $sections, $variables);
            
            if ($templateId) {
                $_SESSION['success'] = 'Proposal template created successfully.';
                return $this->redirect('/admin/proposals/templates/view?id=' . $templateId);
            } else {
                $_SESSION['error'] = 'Failed to create proposal template.';
                return $this->redirect('/admin/proposals/templates/create');
            }
        }
    }

    public function view() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Template ID not provided.';
            return $this->redirect('/admin/proposals/templates');
        }

        $template = $this->templateModel->getTemplateById($id);
        
        if (!$template) {
            $_SESSION['error'] = 'Template not found.';
            return $this->redirect('/admin/proposals/templates');
        }

        $analytics = $this->templateModel->getTemplateAnalytics($id);
        $stats = $this->templateModel->getTemplateStats($id);

        return $this->view('admin/proposals/templates/view', [
            'title' => 'View Template - ' . $template['name'],
            'template' => $template,
            'analytics' => $analytics,
            'stats' => $stats
        ]);
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Template ID not provided.';
            return $this->redirect('/admin/proposals/templates');
        }

        $template = $this->templateModel->getTemplateById($id);
        
        if (!$template) {
            $_SESSION['error'] = 'Template not found.';
            return $this->redirect('/admin/proposals/templates');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $categories = $this->getTemplateCategories();
            
            return $this->view('admin/proposals/templates/edit', [
                'title' => 'Edit Template - ' . $template['name'],
                'template' => $template,
                'categories' => $categories
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/proposals/templates/edit?id=' . $id);
            }

            $templateData = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'category' => $_POST['category'] ?? 'custom',
                'template_content' => $this->parseTemplateContent($_POST),
                'terms_conditions' => $_POST['terms_conditions'] ?? '',
                'is_active' => isset($_POST['is_active'])
            ];

            $pricingTiers = $this->parsePricingTiers($_POST);
            $sections = $this->parseSections($_POST);
            $variables = $this->parseVariables($_POST);

            if ($this->templateModel->updateTemplate($id, $templateData, $pricingTiers, $sections, $variables)) {
                $_SESSION['success'] = 'Template updated successfully.';
                return $this->redirect('/admin/proposals/templates/view?id=' . $id);
            } else {
                $_SESSION['error'] = 'Failed to update template.';
                return $this->redirect('/admin/proposals/templates/edit?id=' . $id);
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/proposals/templates');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Template ID not provided.';
            return $this->redirect('/admin/proposals/templates');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/proposals/templates');
        }

        if ($this->templateModel->deleteTemplate($id)) {
            $_SESSION['success'] = 'Template deleted successfully.';
        } else {
            $_SESSION['error'] = 'Cannot delete template that is used in proposals.';
        }

        return $this->redirect('/admin/proposals/templates');
    }

    public function duplicate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/proposals/templates');
        }

        $id = $_POST['id'] ?? null;
        $newName = $_POST['new_name'] ?? '';
        
        if (!$id || empty($newName)) {
            $_SESSION['error'] = 'Template ID and new name are required.';
            return $this->redirect('/admin/proposals/templates');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/proposals/templates');
        }

        $newTemplateId = $this->templateModel->duplicateTemplate($id, $newName);
        
        if ($newTemplateId) {
            $_SESSION['success'] = 'Template duplicated successfully.';
            return $this->redirect('/admin/proposals/templates/edit?id=' . $newTemplateId);
        } else {
            $_SESSION['error'] = 'Failed to duplicate template.';
            return $this->redirect('/admin/proposals/templates');
        }
    }

    public function setDefault() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/proposals/templates');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Template ID not provided.';
            return $this->redirect('/admin/proposals/templates');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/proposals/templates');
        }

        if ($this->templateModel->setAsDefault($id)) {
            $_SESSION['success'] = 'Template set as default successfully.';
        } else {
            $_SESSION['error'] = 'Failed to set template as default.';
        }

        return $this->redirect('/admin/proposals/templates');
    }

    public function createProposal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/proposals/templates');
        }

        $templateId = $_POST['template_id'] ?? null;
        $clientId = $_POST['client_id'] ?? null;
        
        if (!$templateId || !$clientId) {
            $_SESSION['error'] = 'Template ID and Client ID are required.';
            return $this->redirect('/admin/proposals/templates');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/proposals/templates');
        }

        // Get client data
        $userModel = new \App\Models\User();
        $client = $userModel->find($clientId);
        
        if (!$client) {
            $_SESSION['error'] = 'Client not found.';
            return $this->redirect('/admin/proposals/templates');
        }

        $clientData = [
            'client_name' => $client['full_name'],
            'client_email' => $client['email'],
            'client_phone' => $client['phone'] ?? '',
            'company_name' => $client['company_name'] ?? ''
        ];

        // Parse customizations
        $customizations = [
            'selected_tier' => $_POST['selected_tier'] ?? null,
            'price_adjustment' => floatval($_POST['price_adjustment'] ?? 0),
            'custom_content' => $_POST['custom_content'] ?? []
        ];

        // Generate proposal from template
        $proposalData = $this->templateModel->generateProposalFromTemplate($templateId, $clientData, $customizations);
        
        if ($proposalData) {
            // Add additional proposal data
            $proposalData['client_id'] = $clientId;
            $proposalData['status'] = 'draft';
            $proposalData['admin_notes'] = $_POST['admin_notes'] ?? '';

            // Create proposal
            $proposalId = $this->proposalModel->create($proposalData);
            
            if ($proposalId) {
                // Log template usage
                $this->templateModel->logTemplateUsage($templateId, $proposalId, 'created', $customizations);
                
                // Create analytics record
                $this->createProposalAnalytics($proposalId, $templateId, $clientId);
                
                $_SESSION['success'] = 'Proposal created successfully from template.';
                return $this->redirect('/admin/proposals/view?id=' . $proposalId);
            }
        }

        $_SESSION['error'] = 'Failed to create proposal from template.';
        return $this->redirect('/admin/proposals/templates');
    }

    public function preview() {
        $templateId = $_GET['id'] ?? null;
        $clientId = $_GET['client_id'] ?? null;
        
        if (!$templateId) {
            $_SESSION['error'] = 'Template ID not provided.';
            return $this->redirect('/admin/proposals/templates');
        }

        $template = $this->templateModel->getTemplateById($templateId);
        
        if (!$template) {
            $_SESSION['error'] = 'Template not found.';
            return $this->redirect('/admin/proposals/templates');
        }

        $clientData = [];
        if ($clientId) {
            $userModel = new \App\Models\User();
            $client = $userModel->find($clientId);
            if ($client) {
                $clientData = [
                    'client_name' => $client['full_name'],
                    'client_email' => $client['email'],
                    'client_phone' => $client['phone'] ?? '',
                    'company_name' => $client['company_name'] ?? ''
                ];
            }
        }

        // Generate preview data
        $previewData = $this->templateModel->generateProposalFromTemplate($templateId, $clientData);

        return $this->view('admin/proposals/templates/preview', [
            'title' => 'Preview Template - ' . $template['name'],
            'template' => $template,
            'preview' => $previewData,
            'client_data' => $clientData
        ]);
    }

    public function analytics() {
        $templateId = $_GET['template_id'] ?? null;
        $filters = $this->getAnalyticsFilters();
        
        $analytics = $this->templateModel->getTemplateAnalytics($templateId, $filters);
        $stats = $this->templateModel->getTemplateStats($templateId);
        $popularTemplates = $this->templateModel->getPopularTemplates(10);

        return $this->view('admin/proposals/templates/analytics', [
            'title' => 'Template Analytics',
            'analytics' => $analytics,
            'stats' => $stats,
            'popularTemplates' => $popularTemplates,
            'filters' => $filters
        ]);
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        $filters = $this->getFiltersFromRequest();
        
        if (empty($query)) {
            return $this->redirect('/admin/proposals/templates');
        }

        $templates = $this->templateModel->searchTemplates($query, $filters);
        
        return $this->view('admin/proposals/templates/search', [
            'title' => 'Search Templates',
            'templates' => $templates,
            'query' => $query,
            'filters' => $filters
        ]);
    }

    // Helper methods
    private function getTemplateCategories() {
        return [
            'web_development' => 'Web Development',
            'mobile_app' => 'Mobile App Development',
            'consulting' => 'Consulting Services',
            'marketing' => 'Digital Marketing',
            'custom' => 'Custom Solutions'
        ];
    }

    private function parseTemplateContent($postData) {
        $content = [
            'sections' => $postData['sections'] ?? [],
            'style' => $postData['style'] ?? 'modern',
            'includes' => []
        ];

        // Parse includes checkboxes
        $includes = ['cms', 'analytics', 'seo', 'testing', 'consulting'];
        foreach ($includes as $include) {
            if (isset($postData['include_' . $include])) {
                $content['includes'][$include] = true;
            }
        }

        return $content;
    }

    private function parsePricingTiers($postData) {
        $tiers = [];
        $tierNames = $postData['tier_name'] ?? [];
        $tierDescriptions = $postData['tier_description'] ?? [];
        $tierPrices = $postData['tier_price'] ?? [];
        $tierFeatures = $postData['tier_features'] ?? [];
        $tierTimelines = $postData['tier_timeline'] ?? [];
        $tierSupport = $postData['tier_support'] ?? [];
        $tierRevisions = $postData['tier_revisions'] ?? [];
        $tierPopular = $postData['tier_popular'] ?? [];

        foreach ($tierNames as $index => $name) {
            if (!empty($name)) {
                $tiers[] = [
                    'tier_name' => $name,
                    'description' => $tierDescriptions[$index] ?? '',
                    'base_price' => floatval($tierPrices[$index] ?? 0),
                    'features' => $this->parseFeatures($tierFeatures[$index] ?? ''),
                    'timeline_weeks' => intval($tierTimelines[$index] ?? 4),
                    'support_level' => $tierSupport[$index] ?? 'basic',
                    'revisions_included' => intval($tierRevisions[$index] ?? 2),
                    'is_popular' => isset($tierPopular[$index]),
                    'sort_order' => $index
                ];
            }
        }

        return $tiers;
    }

    private function parseFeatures($featuresString) {
        if (empty($featuresString)) {
            return [];
        }
        
        return array_map('trim', explode("\n", $featuresString));
    }

    private function parseSections($postData) {
        $sections = [];
        $sectionNames = $postData['section_name'] ?? [];
        $sectionTypes = $postData['section_type'] ?? [];
        $sectionContents = $postData['section_content'] ?? [];
        $sectionRequired = $postData['section_required'] ?? [];

        foreach ($sectionNames as $index => $name) {
            if (!empty($name)) {
                $sections[] = [
                    'section_name' => $name,
                    'section_type' => $sectionTypes[$index] ?? 'custom',
                    'content' => $sectionContents[$index] ?? '',
                    'is_required' => isset($sectionRequired[$index]),
                    'sort_order' => $index
                ];
            }
        }

        return $sections;
    }

    private function parseVariables($postData) {
        $variables = [];
        $variableNames = $postData['variable_name'] ?? [];
        $variableTypes = $postData['variable_type'] ?? [];
        $variableDefaults = $postData['variable_default'] ?? [];
        $variableDescriptions = $postData['variable_description'] ?? [];
        $variableRequired = $postData['variable_required'] ?? [];

        foreach ($variableNames as $index => $name) {
            if (!empty($name)) {
                $variables[] = [
                    'variable_name' => $name,
                    'variable_type' => $variableTypes[$index] ?? 'text',
                    'default_value' => $variableDefaults[$index] ?? '',
                    'description' => $variableDescriptions[$index] ?? '',
                    'is_required' => isset($variableRequired[$index]),
                    'sort_order' => $index
                ];
            }
        }

        return $variables;
    }

    private function getFiltersFromRequest() {
        return [
            'category' => $_GET['category'] ?? null,
            'is_active' => isset($_GET['is_active']) ? $_GET['is_active'] : null,
            'created_by' => $_GET['created_by'] ?? null
        ];
    }

    private function getAnalyticsFilters() {
        return [
            'conversion_status' => $_GET['conversion_status'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];
    }

    private function createProposalAnalytics($proposalId, $templateId, $clientId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO proposal_analytics 
            (proposal_id, template_id, client_id, view_count, time_spent, conversion_status)
            VALUES (:proposal_id, :template_id, :client_id, 0, 0, 'viewed')
        ");
        
        return $stmt->execute([
            'proposal_id' => $proposalId,
            'template_id' => $templateId,
            'client_id' => $clientId
        ]);
    }
}
