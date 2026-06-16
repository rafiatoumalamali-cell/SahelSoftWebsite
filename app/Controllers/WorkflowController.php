<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\WorkflowService;

class WorkflowController extends Controller {
    private $workflowService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        $this->workflowService = new WorkflowService();
    }

    public function index() {
        $filters = $this->getWorkflowFilters();
        $templates = $this->workflowService->getWorkflowTemplates($filters);
        
        return $this->view('admin/workflows/index', [
            'title' => 'Workflow Automation',
            'templates' => $templates,
            'filters' => $filters
        ]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->view('admin/workflows/create', [
                'title' => 'Create Workflow',
                'categories' => $this->getWorkflowCategories(),
                'triggerTypes' => $this->getTriggerTypes(),
                'actionTypes' => $this->getActionTypes()
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/workflows/create');
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'category' => $_POST['category'] ?? 'custom',
                'trigger_type' => $_POST['trigger_type'] ?? 'manual',
                'trigger_config' => $this->parseTriggerConfig($_POST),
                'actions' => $this->parseActions($_POST),
                'conditions' => $this->parseConditions($_POST),
                'variables' => $this->parseVariables($_POST),
                'is_active' => isset($_POST['is_active'])
            ];

            // Validate workflow data
            $errors = $this->validateWorkflowData($data);
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                return $this->redirect('/admin/workflows/create');
            }

            if ($this->workflowService->createWorkflowTemplate($data)) {
                $_SESSION['success'] = 'Workflow created successfully.';
                return $this->redirect('/admin/workflows');
            } else {
                $_SESSION['error'] = 'Failed to create workflow.';
                return $this->redirect('/admin/workflows/create');
            }
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Workflow ID not provided.';
            return $this->redirect('/admin/workflows');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $templates = $this->workflowService->getWorkflowTemplates();
            $template = null;
            
            foreach ($templates as $t) {
                if ($t['id'] == $id) {
                    $template = $t;
                    break;
                }
            }

            if (!$template) {
                $_SESSION['error'] = 'Workflow not found.';
                return $this->redirect('/admin/workflows');
            }

            return $this->view('admin/workflows/edit', [
                'title' => 'Edit Workflow - ' . $template['name'],
                'template' => $template,
                'categories' => $this->getWorkflowCategories(),
                'triggerTypes' => $this->getTriggerTypes(),
                'actionTypes' => $this->getActionTypes()
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/workflows/edit?id=' . $id);
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'category' => $_POST['category'] ?? 'custom',
                'trigger_type' => $_POST['trigger_type'] ?? 'manual',
                'trigger_config' => $this->parseTriggerConfig($_POST),
                'actions' => $this->parseActions($_POST),
                'conditions' => $this->parseConditions($_POST),
                'variables' => $this->parseVariables($_POST),
                'is_active' => isset($_POST['is_active'])
            ];

            // Validate workflow data
            $errors = $this->validateWorkflowData($data);
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                return $this->redirect('/admin/workflows/edit?id=' . $id);
            }

            if ($this->workflowService->updateWorkflowTemplate($id, $data)) {
                $_SESSION['success'] = 'Workflow updated successfully.';
                return $this->redirect('/admin/workflows');
            } else {
                $_SESSION['error'] = 'Failed to update workflow.';
                return $this->redirect('/admin/workflows/edit?id=' . $id);
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/workflows');
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Workflow ID not provided.';
            return $this->redirect('/admin/workflows');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/workflows');
        }

        if ($this->workflowService->deleteWorkflowTemplate($id)) {
            $_SESSION['success'] = 'Workflow deleted successfully.';
        } else {
            $_SESSION['error'] = 'Cannot delete workflow that has executions.';
        }

        return $this->redirect('/admin/workflows');
    }

    public function execute() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Workflow ID not provided']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $triggerData = $_POST['trigger_data'] ?? [];
        if (is_string($triggerData)) {
            $triggerData = json_decode($triggerData, true) ?? [];
        }

        $result = $this->workflowService->executeWorkflow($id, $triggerData, 'manual');

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function executions() {
        $templateId = $_GET['template_id'] ?? null;
        $executions = $this->workflowService->getWorkflowExecutions($templateId, 50);

        return $this->view('admin/workflows/executions', [
            'title' => 'Workflow Executions',
            'executions' => $executions,
            'templateId' => $templateId
        ]);
    }

    public function analytics() {
        $templateId = $_GET['template_id'] ?? null;
        $days = intval($_GET['days'] ?? 30);
        
        $statistics = $this->workflowService->getWorkflowStatistics($templateId, $days);

        return $this->view('admin/workflows/analytics', [
            'title' => 'Workflow Analytics',
            'statistics' => $statistics,
            'templateId' => $templateId,
            'days' => $days
        ]);
    }

    public function duplicate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/workflows');
        }

        $id = $_POST['id'] ?? null;
        $newName = $_POST['new_name'] ?? '';
        
        if (!$id || empty($newName)) {
            $_SESSION['error'] = 'Workflow ID and new name are required.';
            return $this->redirect('/admin/workflows');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/workflows');
        }

        $templates = $this->workflowService->getWorkflowTemplates();
        $template = null;
        
        foreach ($templates as $t) {
            if ($t['id'] == $id) {
                $template = $t;
                break;
            }
        }

        if (!$template) {
            $_SESSION['error'] = 'Workflow not found.';
            return $this->redirect('/admin/workflows');
        }

        $newTemplateData = [
            'name' => $newName,
            'description' => $template['description'] . ' (Copy)',
            'category' => $template['category'],
            'trigger_type' => $template['trigger_type'],
            'trigger_config' => $template['trigger_config'],
            'actions' => $template['actions'],
            'conditions' => $template['conditions'],
            'variables' => $template['variables'],
            'is_active' => false
        ];

        if ($this->workflowService->createWorkflowTemplate($newTemplateData)) {
            $_SESSION['success'] = 'Workflow duplicated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to duplicate workflow.';
        }

        return $this->redirect('/admin/workflows');
    }

    public function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        $isActive = $_POST['is_active'] ?? null;
        
        if (!$id || $isActive === null) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $templates = $this->workflowService->getWorkflowTemplates();
        $template = null;
        
        foreach ($templates as $t) {
            if ($t['id'] == $id) {
                $template = $t;
                break;
            }
        }

        if (!$template) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Workflow not found']);
            exit;
        }

        $data = [
            'name' => $template['name'],
            'description' => $template['description'],
            'category' => $template['category'],
            'trigger_type' => $template['trigger_type'],
            'trigger_config' => $template['trigger_config'],
            'actions' => $template['actions'],
            'conditions' => $template['conditions'],
            'variables' => $template['variables'],
            'is_active' => $isActive == 'true'
        ];

        $result = $this->workflowService->updateWorkflowTemplate($id, $data);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    // Helper methods
    private function getWorkflowFilters() {
        return [
            'category' => $_GET['category'] ?? null,
            'trigger_type' => $_GET['trigger_type'] ?? null,
            'is_active' => isset($_GET['is_active']) ? $_GET['is_active'] : null
        ];
    }

    private function getWorkflowCategories() {
        return [
            'project_management' => 'Project Management',
            'client_onboarding' => 'Client Onboarding',
            'billing' => 'Billing',
            'notifications' => 'Notifications',
            'compliance' => 'Compliance',
            'custom' => 'Custom'
        ];
    }

    private function getTriggerTypes() {
        return [
            'manual' => 'Manual Trigger',
            'scheduled' => 'Scheduled Trigger',
            'event_based' => 'Event-Based Trigger',
            'condition_based' => 'Condition-Based Trigger'
        ];
    }

    private function getActionTypes() {
        return [
            'send_email' => 'Send Email',
            'send_notification' => 'Send Notification',
            'create_task' => 'Create Task',
            'update_project' => 'Update Project',
            'delay' => 'Delay',
            'conditional' => 'Conditional Logic',
            'loop' => 'Loop',
            'call_webhook' => 'Call Webhook',
            'api_call' => 'API Call',
            'create_document' => 'Create Document'
        ];
    }

    private function parseTriggerConfig($postData) {
        $config = [];
        
        switch ($postData['trigger_type']) {
            case 'scheduled':
                $config['type'] = $postData['schedule_type'] ?? 'daily';
                $config['time'] = $postData['schedule_time'] ?? '09:00';
                
                if ($postData['schedule_type'] === 'weekly') {
                    $config['day'] = $postData['schedule_day'] ?? 'monday';
                } elseif ($postData['schedule_type'] === 'monthly') {
                    $config['day'] = $postData['schedule_day'] ?? 1;
                } elseif ($postData['schedule_type'] === 'cron') {
                    $config['cron'] = $postData['cron_expression'] ?? '';
                }
                break;
                
            case 'event_based':
                $config['event'] = $postData['event_type'] ?? '';
                $config['entity_type'] = $postData['entity_type'] ?? '';
                break;
                
            case 'condition_based':
                $config['conditions'] = $postData['condition_expression'] ?? '';
                $config['check_interval'] = $postData['check_interval'] ?? 60; // minutes
                break;
        }
        
        return $config;
    }

    private function parseActions($postData) {
        $actions = [];
        $actionNames = $postData['action_name'] ?? [];
        $actionTypes = $postData['action_type'] ?? [];
        $actionConfigs = $postData['action_config'] ?? [];
        
        foreach ($actionNames as $index => $name) {
            if (empty($name)) continue;
            
            $actionType = $actionTypes[$index] ?? '';
            $actionConfig = $this->parseActionConfig($actionType, $actionConfigs[$index] ?? []);
            
            $actions[] = [
                'name' => $name,
                'type' => $actionType,
                'config' => $actionConfig,
                'stop_on_failure' => isset($postData['action_stop_on_failure'][$index] ?? false)
            ];
        }
        
        return $actions;
    }

    private function parseActionConfig($actionType, $configData) {
        switch ($actionType) {
            case 'send_email':
                return [
                    'template' => $configData['template'] ?? '',
                    'to' => $configData['to'] ?? '',
                    'subject' => $configData['subject'] ?? ''
                ];
                
            case 'send_notification':
                return [
                    'message' => $configData['message'] ?? '',
                    'to' => $configData['to'] ?? ''
                ];
                
            case 'create_task':
                return [
                    'project_id' => $configData['project_id'] ?? '',
                    'title' => $configData['title'] ?? '',
                    'description' => $configData['description'] ?? '',
                    'assigned_to' => $configData['assigned_to'] ?? '',
                    'priority' => $configData['priority'] ?? 'medium'
                ];
                
            case 'delay':
                return [
                    'seconds' => intval($configData['seconds'] ?? 0),
                    'minutes' => intval($configData['minutes'] ?? 0),
                    'hours' => intval($configData['hours'] ?? 0)
                ];
                
            case 'conditional':
                return [
                    'condition' => $configData['condition'] ?? '',
                    'true_actions' => $this->parseSubActions($configData['true_actions'] ?? []),
                    'false_actions' => $this->parseSubActions($configData['false_actions'] ?? [])
                ];
                
            case 'loop':
                return [
                    'items' => $configData['items'] ?? '',
                    'item_variable' => $configData['item_variable'] ?? 'item',
                    'actions' => $this->parseSubActions($configData['actions'] ?? [])
                ];
                
            case 'call_webhook':
                return [
                    'url' => $configData['url'] ?? '',
                    'method' => $configData['method'] ?? 'POST',
                    'headers' => $configData['headers'] ?? [],
                    'payload' => $configData['payload'] ?? []
                ];
                
            case 'api_call':
                return [
                    'service' => $configData['service'] ?? '',
                    'method' => $configData['method'] ?? '',
                    'parameters' => $configData['parameters'] ?? []
                ];
                
            default:
                return [];
        }
    }

    private function parseSubActions($subActionData) {
        if (is_string($subActionData)) {
            $subActionData = json_decode($subActionData, true) ?? [];
        }
        
        $actions = [];
        foreach ($subActionData as $action) {
            $actions[] = [
                'type' => $action['type'] ?? '',
                'config' => $action['config'] ?? []
            ];
        }
        
        return $actions;
    }

    private function parseConditions($postData) {
        $conditions = [];
        $conditionFields = $postData['condition_field'] ?? [];
        $conditionOperators = $postData['condition_operator'] ?? [];
        $conditionValues = $postData['condition_value'] ?? [];
        
        foreach ($conditionFields as $index => $field) {
            if (empty($field)) continue;
            
            $conditions[$field] = [
                'operator' => $conditionOperators[$index] ?? 'equals',
                'value' => $conditionValues[$index] ?? ''
            ];
        }
        
        return $conditions;
    }

    private function parseVariables($postData) {
        $variables = [];
        $variableNames = $postData['variable_name'] ?? [];
        $variableTypes = $postData['variable_type'] ?? [];
        $variableValues = $postData['variable_value'] ?? [];
        
        foreach ($variableNames as $index => $name) {
            if (empty($name)) continue;
            
            $variables[$name] = [
                'type' => $variableTypes[$index] ?? 'string',
                'value' => $variableValues[$index] ?? ''
            ];
        }
        
        return $variables;
    }

    private function validateWorkflowData($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Workflow name is required';
        }
        
        if (empty($data['trigger_type'])) {
            $errors[] = 'Trigger type is required';
        }
        
        if (empty($data['actions'])) {
            $errors[] = 'At least one action is required';
        }
        
        // Validate trigger configuration
        if ($data['trigger_type'] === 'scheduled') {
            if (empty($data['trigger_config']['time'])) {
                $errors[] = 'Schedule time is required for scheduled triggers';
            }
        } elseif ($data['trigger_type'] === 'event_based') {
            if (empty($data['trigger_config']['event'])) {
                $errors[] = 'Event type is required for event-based triggers';
            }
        }
        
        // Validate actions
        foreach ($data['actions'] as $action) {
            if (empty($action['type'])) {
                $errors[] = 'Action type is required for all actions';
            }
            
            if ($action['type'] === 'send_email') {
                if (empty($action['config']['to'])) {
                    $errors[] = 'Email recipient is required for send_email action';
                }
            } elseif ($action['type'] === 'create_task') {
                if (empty($action['config']['title'])) {
                    $errors[] = 'Task title is required for create_task action';
                }
            }
        }
        
        return $errors;
    }
}
