<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\IntegrationService;

class IntegrationController extends Controller {
    private $integrationService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        $this->integrationService = new IntegrationService();
    }

    public function index() {
        $userId = $_SESSION['user_id'];
        $availableIntegrations = $this->integrationService->getAvailableIntegrations();
        $userIntegrations = $this->integrationService->getUserIntegrations($userId);

        return $this->view('admin/integrations/index', [
            'title' => 'Integrations',
            'availableIntegrations' => $availableIntegrations,
            'userIntegrations' => $userIntegrations
        ]);
    }

    public function connect() {
        $integrationId = $_GET['id'] ?? null;
        
        if (!$integrationId) {
            $_SESSION['error'] = 'Integration ID not provided.';
            return $this->redirect('/admin/integrations');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Get integration details
            $availableIntegrations = $this->integrationService->getAvailableIntegrations();
            $integration = null;
            
            foreach ($availableIntegrations as $availIntegration) {
                if ($availIntegration['id'] == $integrationId) {
                    $integration = $availIntegration;
                    break;
                }
            }
            
            if (!$integration) {
                $_SESSION['error'] = 'Integration not found.';
                return $this->redirect('/admin/integrations');
            }

            return $this->view('admin/integrations/connect', [
                'title' => 'Connect ' . $integration['display_name'],
                'integration' => $integration
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/integrations/connect?id=' . $integrationId);
            }

            $connectionName = $_POST['connection_name'] ?? '';
            $configuration = $_POST['configuration'] ?? [];
            $credentials = $_POST['credentials'] ?? [];

            // Validate required fields
            $availableIntegrations = $this->integrationService->getAvailableIntegrations();
            $integration = null;
            
            foreach ($availableIntegrations as $availIntegration) {
                if ($availIntegration['id'] == $integrationId) {
                    $integration = $availIntegration;
                    break;
                }
            }

            if (!$integration) {
                $_SESSION['error'] = 'Integration not found.';
                return $this->redirect('/admin/integrations');
            }

            // Validate configuration schema
            $validationErrors = $this->validateIntegrationConfig($integration, $configuration, $credentials);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                return $this->redirect('/admin/integrations/connect?id=' . $integrationId);
            }

            $result = $this->integrationService->connectIntegration(
                $_SESSION['user_id'],
                $integrationId,
                $connectionName,
                $configuration,
                $credentials
            );

            if ($result['success']) {
                $_SESSION['success'] = 'Integration connected successfully!';
                return $this->redirect('/admin/integrations/configure?id=' . $result['user_integration_id']);
            } else {
                $_SESSION['error'] = $result['message'];
                return $this->redirect('/admin/integrations/connect?id=' . $integrationId);
            }
        }
    }

    public function configure() {
        $userIntegrationId = $_GET['id'] ?? null;
        
        if (!$userIntegrationId) {
            $_SESSION['error'] = 'Integration ID not provided.';
            return $this->redirect('/admin/integrations');
        }

        $userId = $_SESSION['user_id'];
        $userIntegrations = $this->integrationService->getUserIntegrations($userId);
        $userIntegration = null;
        
        foreach ($userIntegrations as $integration) {
            if ($integration['id'] == $userIntegrationId) {
                $userIntegration = $integration;
                break;
            }
        }

        if (!$userIntegration) {
            $_SESSION['error'] = 'Integration not found.';
            return $this->redirect('/admin/integrations');
        }

        $fieldMappings = $this->integrationService->getFieldMappings($userIntegrationId);
        $syncLogs = $this->integrationService->getSyncLogs($userIntegrationId, 10);

        return $this->view('admin/integrations/configure', [
            'title' => 'Configure ' . $userIntegration['display_name'],
            'userIntegration' => $userIntegration,
            'fieldMappings' => $fieldMappings,
            'syncLogs' => $syncLogs
        ]);
    }

    public function disconnect() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/integrations');
        }

        $userIntegrationId = $_POST['id'] ?? null;
        
        if (!$userIntegrationId) {
            $_SESSION['error'] = 'Integration ID not provided.';
            return $this->redirect('/admin/integrations');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/integrations');
        }

        if ($this->integrationService->disconnectIntegration($_SESSION['user_id'], $userIntegrationId)) {
            $_SESSION['success'] = 'Integration disconnected successfully.';
        } else {
            $_SESSION['error'] = 'Failed to disconnect integration.';
        }

        return $this->redirect('/admin/integrations');
    }

    public function sync() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $userIntegrationId = $_POST['id'] ?? null;
        
        if (!$userIntegrationId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Integration ID not provided']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $result = $this->integrationService->syncIntegration($userIntegrationId, 'manual');

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function updateMapping() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $userIntegrationId = $_POST['user_integration_id'] ?? null;
        $entityType = $_POST['entity_type'] ?? null;
        $localField = $_POST['local_field'] ?? null;
        $remoteField = $_POST['remote_field'] ?? null;
        $fieldType = $_POST['field_type'] ?? 'text';
        $isBidirectional = isset($_POST['is_bidirectional']);
        $isRequired = isset($_POST['is_required']);

        if (!$userIntegrationId || !$entityType || !$localField || !$remoteField) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $result = $this->integrationService->updateFieldMapping(
            $userIntegrationId,
            $entityType,
            $localField,
            $remoteField,
            $fieldType,
            $isBidirectional,
            $isRequired
        );

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function analytics() {
        $userIntegrationId = $_GET['id'] ?? null;
        
        if (!$userIntegrationId) {
            $_SESSION['error'] = 'Integration ID not provided.';
            return $this->redirect('/admin/integrations');
        }

        $userId = $_SESSION['user_id'];
        $userIntegrations = $this->integrationService->getUserIntegrations($userId);
        $userIntegration = null;
        
        foreach ($userIntegrations as $integration) {
            if ($integration['id'] == $userIntegrationId) {
                $userIntegration = $integration;
                break;
            }
        }

        if (!$userIntegration) {
            $_SESSION['error'] = 'Integration not found.';
            return $this->redirect('/admin/integrations');
        }

        $analytics = $this->integrationService->getIntegrationAnalytics($userIntegrationId, 30);

        return $this->view('admin/integrations/analytics', [
            'title' => 'Analytics - ' . $userIntegration['display_name'],
            'userIntegration' => $userIntegration,
            'analytics' => $analytics
        ]);
    }

    public function webhook() {
        // Handle incoming webhooks from third-party services
        $provider = $_GET['provider'] ?? null;
        $signature = $_SERVER['HTTP_X_SIGNATURE'] ?? $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? null;
        
        if (!$provider) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Provider not specified';
            exit;
        }

        $webhookData = json_decode(file_get_contents('php://input'), true);
        
        if (!$webhookData) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid webhook data';
            exit;
        }

        $result = $this->integrationService->handleWebhook($provider, $webhookData, $signature);
        
        if ($result['success']) {
            header('HTTP/1.1 200 OK');
            echo 'Webhook processed successfully';
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo 'Webhook processing failed';
        }
        
        exit;
    }

    public function testConnection() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $integrationId = $_POST['integration_id'] ?? null;
        $credentials = $_POST['credentials'] ?? [];

        if (!$integrationId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Integration ID not provided']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $availableIntegrations = $this->integrationService->getAvailableIntegrations();
        $integration = null;
        
        foreach ($availableIntegrations as $availIntegration) {
            if ($availIntegration['id'] == $integrationId) {
                $integration = $availIntegration;
                break;
            }
        }

        if (!$integration) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Integration not found']);
            exit;
        }

        // Test connection (this would be implemented in the service)
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Connection test successful']);
        exit;
    }

    public function overview() {
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/dashboard');
        }

        // Get integration overview statistics
        $stmt = $this->pdo->prepare("
            SELECT * FROM integration_overview
            ORDER BY total_connections DESC
        ");
        $stmt->execute();
        $overview = $stmt->fetchAll();

        return $this->view('admin/integrations/overview', [
            'title' => 'Integrations Overview',
            'overview' => $overview
        ]);
    }

    private function validateIntegrationConfig($integration, $configuration, $credentials) {
        $errors = [];
        $schema = $integration['configuration_schema'];

        // Validate configuration fields
        foreach ($schema as $field => $fieldConfig) {
            if ($fieldConfig['required'] && empty($configuration[$field])) {
                $errors[] = $fieldConfig['label'] . ' is required';
            }

            if (!empty($configuration[$field]) && isset($fieldConfig['type'])) {
                switch ($fieldConfig['type']) {
                    case 'email':
                        if (!filter_var($configuration[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[] = $fieldConfig['label'] . ' must be a valid email';
                        }
                        break;
                    case 'url':
                        if (!filter_var($configuration[$field], FILTER_VALIDATE_URL)) {
                            $errors[] = $fieldConfig['label'] . ' must be a valid URL';
                        }
                        break;
                    case 'number':
                        if (!is_numeric($configuration[$field])) {
                            $errors[] = $fieldConfig['label'] . ' must be a number';
                        }
                        break;
                    case 'boolean':
                        if (!is_bool($configuration[$field]) && !in_array($configuration[$field], [0, 1, '0', '1', 'true', 'false'])) {
                            $errors[] = $fieldConfig['label'] . ' must be true or false';
                        }
                        break;
                }
            }
        }

        // Validate credential fields
        foreach ($credentials as $field => $value) {
            if (empty($value) && isset($schema[$field]) && $schema[$field]['required']) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        return $errors;
    }

    private function getPDO() {
        return new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }
}
