<?php

namespace App\Services;

use App\Core\Model;
use PDO;

class WorkflowService {
    private $pdo;
    
    public function __construct() {
        $this->pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
    
    public function getWorkflowTemplates($filters = []) {
        $sql = "
            SELECT wt.*, u.full_name as created_by_name
            FROM workflow_templates wt
            LEFT JOIN users u ON wt.created_by = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['category'])) {
            $sql .= " AND wt.category = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['trigger_type'])) {
            $sql .= " AND wt.trigger_type = :trigger_type";
            $params['trigger_type'] = $filters['trigger_type'];
        }
        
        if (isset($filters['is_active'])) {
            $sql .= " AND wt.is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        if (!empty($filters['created_by'])) {
            $sql .= " AND wt.created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }
        
        $sql .= " ORDER BY wt.name ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $templates = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($templates as &$template) {
            $template['trigger_config'] = json_decode($template['trigger_config'] ?? '{}', true) ?? [];
            $template['actions'] = json_decode($template['actions'] ?? '[]', true) ?? [];
            $template['conditions'] = json_decode($template['conditions'] ?? '{}', true) ?? [];
            $template['variables'] = json_decode($template['variables'] ?? '{}', true) ?? [];
        }
        
        return $templates;
    }
    
    public function createWorkflowTemplate($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO workflow_templates 
            (name, description, category, trigger_type, trigger_config, actions, conditions, variables, is_active, created_by)
            VALUES (:name, :description, :category, :trigger_type, :trigger_config, :actions, :conditions, :variables, :is_active, :created_by)
        ");
        
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'trigger_type' => $data['trigger_type'],
            'trigger_config' => json_encode($data['trigger_config']),
            'actions' => json_encode($data['actions']),
            'conditions' => json_encode($data['conditions'] ?? []),
            'variables' => json_encode($data['variables'] ?? []),
            'is_active' => $data['is_active'] ?? true,
            'created_by' => $_SESSION['user_id']
        ]);
    }
    
    public function updateWorkflowTemplate($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE workflow_templates 
            SET name = :name,
                description = :description,
                category = :category,
                trigger_type = :trigger_type,
                trigger_config = :trigger_config,
                actions = :actions,
                conditions = :conditions,
                variables = :variables,
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'trigger_type' => $data['trigger_type'],
            'trigger_config' => json_encode($data['trigger_config']),
            'actions' => json_encode($data['actions']),
            'conditions' => json_encode($data['conditions'] ?? []),
            'variables' => json_encode($data['variables'] ?? []),
            'is_active' => $data['is_active'] ?? true
        ]);
    }
    
    public function deleteWorkflowTemplate($id) {
        // Check if template has executions
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM workflow_executions WHERE workflow_template_id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            return false; // Cannot delete template with executions
        }
        
        return $this->deleteTemplate($id);
    }
    
    private function deleteTemplate($id) {
        $stmt = $this->pdo->prepare("DELETE FROM workflow_templates WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    public function executeWorkflow($templateId, $triggerData = [], $triggerType = 'manual') {
        $template = $this->getWorkflowTemplate($templateId);
        
        if (!$template) {
            return ['success' => false, 'message' => 'Workflow template not found'];
        }
        
        if (!$template['is_active']) {
            return ['success' => false, 'message' => 'Workflow template is not active'];
        }
        
        // Check conditions
        if (!$this->checkConditions($template['conditions'], $triggerData)) {
            return ['success' => false, 'message' => 'Workflow conditions not met'];
        }
        
        // Create workflow execution
        $executionId = $this->createWorkflowExecution($templateId, $triggerType, $triggerData);
        
        if (!$executionId) {
            return ['success' => false, 'message' => 'Failed to create workflow execution'];
        }
        
        // Execute workflow asynchronously
        $this->executeWorkflowAsync($executionId, $template, $triggerData);
        
        return ['success' => true, 'execution_id' => $executionId];
    }
    
    private function getWorkflowTemplate($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM workflow_templates WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $template = $stmt->fetch();
        
        if ($template) {
            $template['trigger_config'] = json_decode($template['trigger_config'] ?? '{}', true) ?? [];
            $template['actions'] = json_decode($template['actions'] ?? '[]', true) ?? [];
            $template['conditions'] = json_decode($template['conditions'] ?? '{}', true) ?? [];
            $template['variables'] = json_decode($template['variables'] ?? '{}', true) ?? [];
        }
        
        return $template;
    }
    
    private function createWorkflowExecution($templateId, $triggerType, $triggerData) {
        $executionId = 'WF_' . date('Ymd_His') . '_' . rand(1000, 9999);
        
        $stmt = $this->pdo->prepare("
            INSERT INTO workflow_executions 
            (workflow_template_id, execution_id, trigger_type, trigger_data, status)
            VALUES (:workflow_template_id, :execution_id, :trigger_type, :trigger_data, 'pending')
        ");
        
        if ($stmt->execute([
            'workflow_template_id' => $templateId,
            'execution_id' => $executionId,
            'trigger_type' => $triggerType,
            'trigger_data' => json_encode($triggerData)
        ])) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
    }
    
    private function executeWorkflowAsync($executionId, $template, $triggerData) {
        // This would typically be handled by a queue system
        // For now, we'll execute synchronously
        
        $startTime = microtime(true);
        
        try {
            // Update execution status to running
            $this->updateExecutionStatus($executionId, 'running', $startTime);
            
            // Initialize workflow variables
            $variables = $this->initializeVariables($template, $triggerData);
            
            // Execute actions
            $results = $this->executeActions($executionId, $template['actions'], $variables);
            
            $duration = (microtime(true) - $startTime) * 1000;
            
            // Update execution status to completed
            $this->updateExecutionStatus($executionId, 'completed', $startTime, $duration, $results);
            
            // Update template usage count
            $this->incrementUsageCount($template['id']);
            
        } catch (Exception $e) {
            $duration = (microtime(true) - $startTime) * 1000;
            $this->updateExecutionStatus($executionId, 'failed', $startTime, $duration, [], $e->getMessage());
        }
    }
    
    private function executeActions($executionId, $actions, &$variables) {
        $results = [];
        
        foreach ($actions as $index => $action) {
            $actionId = 'action_' . $index;
            
            // Create workflow action record
            $this->createWorkflowAction($executionId, $actionId, $action['type'], $action['config']);
            
            // Check dependencies
            if (isset($action['depends_on']) && !$this->checkActionDependencies($executionId, $action['depends_on'])) {
                $this->updateActionStatus($executionId, $actionId, 'skipped', null, null, null, 'Dependencies not met');
                continue;
            }
            
            // Execute action
            $actionResult = $this->executeAction($executionId, $actionId, $action, $variables);
            
            if ($actionResult['success']) {
                // Update variables with action results
                if (isset($actionResult['variables'])) {
                    $variables = array_merge($variables, $actionResult['variables']);
                }
                
                $results[$actionId] = $actionResult['result'];
            } else {
                // Log action failure
                $this->logWorkflowError($executionId, $actionId, $actionResult['message']);
                
                // Decide whether to continue or stop workflow
                if ($action['stop_on_failure'] ?? false) {
                    throw new Exception("Action failed: " . $actionResult['message']);
                }
            }
        }
        
        return $results;
    }
    
    private function executeAction($executionId, $actionId, $action, &$variables) {
        $startTime = microtime(true);
        
        try {
            $this->updateActionStatus($executionId, $actionId, 'running', $startTime);
            
            $result = $this->processAction($action['type'], $action['config'], $variables);
            
            $duration = (microtime(true) - $startTime) * 1000;
            $this->updateActionStatus($executionId, $actionId, 'completed', $startTime, $duration, $result);
            
            return ['success' => true, 'result' => $result, 'variables' => $result['variables'] ?? []];
            
        } catch (Exception $e) {
            $duration = (microtime(true) - $startTime) * 1000;
            $this->updateActionStatus($executionId, $actionId, 'failed', $startTime, $duration, null, $e->getMessage());
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function processAction($actionType, $config, $variables) {
        switch ($actionType) {
            case 'send_email':
                return $this->actionSendEmail($config, $variables);
            case 'send_notification':
                return $this->actionSendNotification($config, $variables);
            case 'create_task':
                return $this->actionCreateTask($config, $variables);
            case 'update_project':
                return $this->actionUpdateProject($config, $variables);
            case 'delay':
                return $this->actionDelay($config, $variables);
            case 'conditional':
                return $this->actionConditional($config, $variables);
            case 'loop':
                return $this->actionLoop($config, $variables);
            case 'call_webhook':
                return $this->actionCallWebhook($config, $variables);
            case 'api_call':
                return $this->actionApiCall($config, $variables);
            default:
                throw new Exception("Unknown action type: {$actionType}");
        }
    }
    
    private function actionSendEmail($config, $variables) {
        $template = $this->replaceVariables($config['template'], $variables);
        $to = $this->replaceVariables($config['to'], $variables);
        $subject = $this->replaceVariables($config['subject'], $variables);
        
        // Send email using EmailService
        $emailService = new \App\Services\EmailService();
        $result = $emailService->sendTemplateEmail($to, $template, $variables);
        
        return [
            'success' => $result,
            'message' => $result ? 'Email sent successfully' : 'Failed to send email',
            'variables' => ['email_sent' => $result, 'email_to' => $to]
        ];
    }
    
    private function actionSendNotification($config, $variables) {
        $message = $this->replaceVariables($config['message'], $variables);
        $to = $this->replaceVariables($config['to'], $variables);
        
        $notificationService = new \App\Services\NotificationService();
        $result = $notificationService->sendNotification($to, 'Workflow Notification', $message);
        
        return [
            'success' => $result,
            'message' => $result ? 'Notification sent successfully' : 'Failed to send notification',
            'variables' => ['notification_sent' => $result]
        ];
    }
    
    private function actionCreateTask($config, $variables) {
        $title = $this->replaceVariables($config['title'], $variables);
        $description = $this->replaceVariables($config['description'] ?? '', $variables);
        $assignedTo = $this->replaceVariables($config['assigned_to'] ?? '', $variables);
        
        $taskModel = new \App\Models\ProjectTask();
        $taskId = $taskModel->createTask([
            'project_id' => $config['project_id'] ?? null,
            'title' => $title,
            'description' => $description,
            'assigned_to' => $assignedTo,
            'priority' => $config['priority'] ?? 'medium',
            'task_type' => $config['task_type'] ?? 'other'
        ]);
        
        return [
            'success' => $taskId > 0,
            'message' => $taskId > 0 ? 'Task created successfully' : 'Failed to create task',
            'variables' => ['task_id' => $taskId, 'task_title' => $title]
        ];
    }
    
    private function actionDelay($config, $variables) {
        $seconds = $this->replaceVariables($config['seconds'] ?? 0, $variables);
        $minutes = $this->replaceVariables($config['minutes'] ?? 0, $variables);
        $hours = $this->replaceVariables($config['hours'] ?? 0, $variables);
        
        $totalDelay = ($hours * 3600) + ($minutes * 60) + $seconds;
        
        if ($totalDelay > 0) {
            sleep($totalDelay);
        }
        
        return [
            'success' => true,
            'message' => "Delayed for {$totalDelay} seconds",
            'variables' => ['delay_seconds' => $totalDelay]
        ];
    }
    
    private function actionConditional($config, $variables) {
        $condition = $this->replaceVariables($config['condition'], $variables);
        $trueActions = $config['true_actions'] ?? [];
        $falseActions = $config['false_actions'] ?? [];
        
        $result = $this->evaluateCondition($condition, $variables);
        $actionsToExecute = $result ? $trueActions : $falseActions;
        
        $actionResults = [];
        foreach ($actionsToExecute as $action) {
            $actionResult = $this->processAction($action['type'], $action['config'], $variables);
            $actionResults[] = $actionResult;
            
            if (isset($actionResult['variables'])) {
                $variables = array_merge($variables, $actionResult['variables']);
            }
        }
        
        return [
            'success' => true,
            'message' => "Condition evaluated to: " . ($result ? 'true' : 'false'),
            'variables' => ['condition_result' => $result],
            'actions' => $actionResults
        ];
    }
    
    private function actionLoop($config, $variables) {
        $items = $this->replaceVariables($config['items'], $variables);
        $itemVariable = $config['item_variable'] ?? 'item';
        $actions = $config['actions'] ?? [];
        
        $results = [];
        
        if (is_array($items)) {
            foreach ($items as $index => $item) {
                $loopVariables = array_merge($variables, [$itemVariable => $item, 'index' => $index]);
                
                foreach ($actions as $action) {
                    $actionResult = $this->processAction($action['type'], $action['config'], $loopVariables);
                    $results[] = $actionResult;
                }
            }
        }
        
        return [
            'success' => true,
            'message' => "Loop executed on " . count($items) . " items",
            'variables' => ['loop_count' => count($items)],
            'actions' => $results
        ];
    }
    
    private function actionUpdateProject($config, $variables) {
        $projectId = $this->replaceVariables($config['project_id'], $variables);
        $updates = $config['updates'] ?? [];
        
        $projectModel = new \App\Models\Project();
        $result = $projectModel->update($projectId, $updates);
        
        return [
            'success' => $result,
            'message' => $result ? 'Project updated successfully' : 'Failed to update project',
            'variables' => ['project_updated' => $result]
        ];
    }
    
    private function actionCallWebhook($config, $variables) {
        $url = $this->replaceVariables($config['url'], $variables);
        $method = $config['method'] ?? 'POST';
        $headers = $config['headers'] ?? [];
        $payload = $config['payload'] ?? [];
        
        // Replace variables in payload
        if (is_array($payload)) {
            $payload = $this->replaceVariablesRecursive($payload, $variables);
        }
        
        // Make HTTP request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            ...array_map(fn($k, $v) => "$k: $v", array_keys($headers), $headers)
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $success = $httpCode >= 200 && $httpCode < 300;
        
        return [
            'success' => $success,
            'message' => $success ? 'Webhook called successfully' : 'Webhook call failed',
            'variables' => [
                'webhook_response' => $response,
                'webhook_status' => $httpCode
            ]
        ];
    }
    
    private function actionApiCall($config, $variables) {
        $service = $config['service'];
        $method = $config['method'];
        $parameters = $config['parameters'] ?? [];
        
        // Replace variables in parameters
        $parameters = $this->replaceVariablesRecursive($parameters, $variables);
        
        // This would integrate with specific service APIs
        switch ($service) {
            case 'salesforce':
                return $this->callSalesforceAPI($method, $parameters);
            case 'quickbooks':
                return $this->callQuickBooksAPI($method, $parameters);
            default:
                throw new Exception("Unknown API service: {$service}");
        }
    }
    
    private function callSalesforceAPI($method, $parameters) {
        // Simulate Salesforce API call
        return [
            'success' => true,
            'message' => 'Salesforce API call successful',
            'variables' => ['salesforce_result' => 'success']
        ];
    }
    
    private function callQuickBooksAPI($method, $parameters) {
        // Simulate QuickBooks API call
        return [
            'success' => true,
            'message' => 'QuickBooks API call successful',
            'variables' => ['quickbooks_result' => 'success']
        ];
    }
    
    private function replaceVariables($text, $variables) {
        foreach ($variables as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue; // Skip complex variables in simple replacement
            }
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        
        return $text;
    }
    
    private function replaceVariablesRecursive($data, $variables) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replaceVariablesRecursive($value, $variables);
            }
        } elseif (is_string($data)) {
            $data = $this->replaceVariables($data, $variables);
        }
        
        return $data;
    }
    
    private function evaluateCondition($condition, $variables) {
        // Simple condition evaluation
        // In a real implementation, you'd use a proper expression parser
        
        foreach ($variables as $key => $value) {
            $condition = str_replace('{{' . $key . '}}', var_export($value, true), $condition);
        }
        
        // Evaluate the condition (simplified)
        return eval("return ($condition);");
    }
    
    private function initializeVariables($template, $triggerData) {
        $variables = $template['variables'] ?? [];
        $variables['trigger_data'] = $triggerData;
        $variables['current_date'] = date('Y-m-d');
        $variables['current_time'] = date('H:i:s');
        $variables['current_datetime'] = date('Y-m-d H:i:s');
        $variables['current_user'] = $_SESSION['user_id'] ?? null;
        
        return $variables;
    }
    
    private function checkConditions($conditions, $triggerData) {
        if (empty($conditions)) {
            return true;
        }
        
        // Simple condition checking
        foreach ($conditions as $field => $expectedValue) {
            $actualValue = $this->getNestedValue($triggerData, $field);
            
            if ($actualValue != $expectedValue) {
                return false;
            }
        }
        
        return true;
    }
    
    private function getNestedValue($data, $path) {
        $keys = explode('.', $path);
        $value = $data;
        
        foreach ($keys as $key) {
            if (!is_array($value) || !isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }
        
        return $value;
    }
    
    private function updateExecutionStatus($executionId, $status, $startTime = null, $duration = null, $results = [], $error = null) {
        $stmt = $this->pdo->prepare("
            UPDATE workflow_executions 
            SET status = :status,
                started_at = COALESCE(started_at, :started_at),
                completed_at = CASE WHEN :status IN ('completed', 'failed') THEN NOW() ELSE completed_at END,
                duration_ms = COALESCE(:duration_ms, duration_ms),
                results = :results,
                error_message = :error_message,
                updated_at = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $executionId,
            'status' => $status,
            'started_at' => $startTime ? date('Y-m-d H:i:s', $startTime) : null,
            'duration_ms' => $duration,
            'results' => json_encode($results),
            'error_message' => $error
        ]);
    }
    
    private function createWorkflowAction($executionId, $actionId, $actionType, $actionConfig) {
        $stmt = $this->pdo->prepare("
            INSERT INTO workflow_actions 
            (workflow_execution_id, action_id, action_type, action_config, sort_order)
            VALUES (:workflow_execution_id, :action_id, :action_type, :action_config, :sort_order)
        ");
        
        return $stmt->execute([
            'workflow_execution_id' => $executionId,
            'action_id' => $actionId,
            'action_type' => $actionType,
            'action_config' => json_encode($actionConfig),
            'sort_order' => 0
        ]);
    }
    
    private function updateActionStatus($executionId, $actionId, $status, $startTime = null, $duration = null, $result = null, $error = null) {
        $stmt = $this->pdo->prepare("
            UPDATE workflow_actions 
            SET status = :status,
                started_at = COALESCE(started_at, :started_at),
                completed_at = CASE WHEN :status IN ('completed', 'failed', 'skipped') THEN NOW() ELSE completed_at END,
                duration_ms = COALESCE(:duration_ms, duration_ms),
                result = :result,
                error_message = :error_message,
                updated_at = NOW()
            WHERE workflow_execution_id = :workflow_execution_id AND action_id = :action_id
        ");
        
        return $stmt->execute([
            'workflow_execution_id' => $executionId,
            'action_id' => $actionId,
            'status' => $status,
            'started_at' => $startTime ? date('Y-m-d H:i:s', $startTime) : null,
            'duration_ms' => $duration,
            'result' => json_encode($result),
            'error_message' => $error
        ]);
    }
    
    private function checkActionDependencies($executionId, $dependencies) {
        foreach ($dependencies as $depActionId) {
            $stmt = $this->pdo->prepare("
                SELECT status FROM workflow_actions 
                WHERE workflow_execution_id = :execution_id AND action_id = :action_id
            ");
            $stmt->execute(['execution_id' => $executionId, 'action_id' => $depActionId]);
            $action = $stmt->fetch();
            
            if (!$action || $action['status'] !== 'completed') {
                return false;
            }
        }
        
        return true;
    }
    
    private function logWorkflowError($executionId, $actionId, $message) {
        $stmt = $this->pdo->prepare("
            INSERT INTO workflow_logs 
            (workflow_execution_id, action_id, log_level, message)
            VALUES (:workflow_execution_id, :action_id, 'error', :message)
        ");
        
        return $stmt->execute([
            'workflow_execution_id' => $executionId,
            'action_id' => $actionId,
            'message' => $message
        ]);
    }
    
    private function incrementUsageCount($templateId) {
        $stmt = $this->pdo->prepare("UPDATE workflow_templates SET usage_count = usage_count + 1 WHERE id = :id");
        return $stmt->execute(['id' => $templateId]);
    }
    
    public function getWorkflowExecutions($templateId = null, $limit = 50) {
        $sql = "
            SELECT we.*, wt.name as workflow_name, wt.category
            FROM workflow_executions we
            LEFT JOIN workflow_templates wt ON we.workflow_template_id = wt.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($templateId) {
            $sql .= " AND we.workflow_template_id = :template_id";
            $params['template_id'] = $templateId;
        }
        
        $sql .= " ORDER BY we.created_at DESC LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getWorkflowStatistics($templateId = null, $days = 30) {
        $sql = "
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as executions,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                AVG(duration_ms) as avg_duration
            FROM workflow_executions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        ";
        
        $params = ['days' => $days];
        
        if ($templateId) {
            $sql .= " AND workflow_template_id = :template_id";
            $params['template_id'] = $templateId;
        }
        
        $sql .= " GROUP BY DATE(created_at) ORDER BY date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
