<?php

namespace App\Services;

use App\Core\Model;
use PDO;

class IntegrationService {
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
    
    public function getAvailableIntegrations() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM integrations 
            WHERE is_active = TRUE 
            ORDER BY display_name ASC
        ");
        $stmt->execute();
        
        $integrations = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($integrations as &$integration) {
            $integration['configuration_schema'] = json_decode($integration['configuration_schema'] ?? '{}', true) ?? [];
            $integration['webhook_endpoints'] = json_decode($integration['webhook_endpoints'] ?? '[]', true) ?? [];
            $integration['api_endpoints'] = json_decode($integration['api_endpoints'] ?? '[]', true) ?? [];
            $integration['rate_limits'] = json_decode($integration['rate_limits'] ?? '{}', true) ?? [];
        }
        
        return $integrations;
    }
    
    public function getUserIntegrations($userId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                ui.*,
                i.provider,
                i.display_name,
                i.icon,
                i.integration_type,
                i.is_premium
            FROM user_integrations ui
            JOIN integrations i ON ui.integration_id = i.id
            WHERE ui.user_id = :user_id
            AND i.is_active = TRUE
            ORDER BY i.display_name ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        
        $userIntegrations = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($userIntegrations as &$integration) {
            $integration['configuration'] = json_decode($integration['configuration'] ?? '{}', true) ?? [];
            $integration['credentials'] = json_decode($integration['credentials'] ?? '{}', true) ?? [];
        }
        
        return $userIntegrations;
    }
    
    public function connectIntegration($userId, $integrationId, $connectionName, $configuration, $credentials) {
        // Validate integration exists
        $integration = $this->getIntegration($integrationId);
        if (!$integration) {
            return ['success' => false, 'message' => 'Integration not found'];
        }
        
        // Test connection
        $testResult = $this->testConnection($integration, $credentials);
        if (!$testResult['success']) {
            return ['success' => false, 'message' => 'Connection test failed: ' . $testResult['message']];
        }
        
        // Encrypt credentials
        $encryptedCredentials = $this->encryptCredentials($credentials);
        
        // Create user integration
        $stmt = $this->pdo->prepare("
            INSERT INTO user_integrations 
            (user_id, integration_id, connection_name, configuration, credentials, webhook_secret, status)
            VALUES (:user_id, :integration_id, :connection_name, :configuration, :credentials, :webhook_secret, 'active')
            ON DUPLICATE KEY UPDATE
            connection_name = VALUES(connection_name),
            configuration = VALUES(configuration),
            credentials = VALUES(credentials),
            webhook_secret = VALUES(webhook_secret),
            status = 'active',
            updated_at = NOW()
        ");
        
        $webhookSecret = $this->generateWebhookSecret();
        
        $result = $stmt->execute([
            'user_id' => $userId,
            'integration_id' => $integrationId,
            'connection_name' => $connectionName,
            'configuration' => json_encode($configuration),
            'credentials' => $encryptedCredentials,
            'webhook_secret' => $webhookSecret
        ]);
        
        if ($result) {
            // Create default field mappings
            $userIntegrationId = $this->pdo->lastInsertId();
            $this->createDefaultFieldMappings($userIntegrationId, $integration);
            
            return ['success' => true, 'user_integration_id' => $userIntegrationId];
        } else {
            return ['success' => false, 'message' => 'Failed to save integration'];
        }
    }
    
    public function disconnectIntegration($userId, $userIntegrationId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM user_integrations 
            WHERE id = :user_integration_id 
            AND user_id = :user_id
        ");
        
        return $stmt->execute([
            'user_integration_id' => $userIntegrationId,
            'user_id' => $userId
        ]);
    }
    
    public function syncIntegration($userIntegrationId, $syncType = 'manual') {
        $userIntegration = $this->getUserIntegration($userIntegrationId);
        if (!$userIntegration) {
            return ['success' => false, 'message' => 'Integration not found'];
        }
        
        if ($userIntegration['status'] !== 'active') {
            return ['success' => false, 'message' => 'Integration is not active'];
        }
        
        $integration = $this->getIntegration($userIntegration['integration_id']);
        $credentials = $this->decryptCredentials($userIntegration['credentials']);
        
        // Create sync log
        $syncLogId = $this->createSyncLog($userIntegrationId, $syncType);
        
        $startTime = microtime(true);
        $result = $this->performSync($integration, $userIntegration, $credentials, $syncLogId);
        $syncDuration = (microtime(true) - $startTime) * 1000;
        
        // Update sync log
        $this->updateSyncLog($syncLogId, $result, $syncDuration);
        
        // Update last sync time
        $this->updateLastSyncTime($userIntegrationId, $result['success']);
        
        return $result;
    }
    
    public function handleWebhook($integrationProvider, $webhookData, $signature = null) {
        // Find user integration for this provider
        $stmt = $this->pdo->prepare("
            SELECT ui.* FROM user_integrations ui
            JOIN integrations i ON ui.integration_id = i.id
            WHERE i.provider = :provider
            AND ui.status = 'active'
        ");
        $stmt->execute(['provider' => $integrationProvider]);
        $userIntegrations = $stmt->fetchAll();
        
        foreach ($userIntegrations as $userIntegration) {
            // Verify webhook signature if provided
            if ($signature && !$this->verifyWebhookSignature($userIntegration, $webhookData, $signature)) {
                continue;
            }
            
            // Process webhook
            $this->processWebhook($userIntegration, $webhookData);
        }
        
        return ['success' => true];
    }
    
    public function getFieldMappings($userIntegrationId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM integration_field_mappings 
            WHERE user_integration_id = :user_integration_id
            ORDER BY entity_type, local_field
        ");
        $stmt->execute(['user_integration_id' => $userIntegrationId]);
        
        $mappings = $stmt->fetchAll();
        
        // Decode transformation rules
        foreach ($mappings as &$mapping) {
            $mapping['transformation_rules'] = json_decode($mapping['transformation_rules'] ?? '{}', true) ?? [];
        }
        
        return $mappings;
    }
    
    public function updateFieldMapping($userIntegrationId, $entityType, $localField, $remoteField, $fieldType, $isBidirectional, $isRequired, $transformationRules = []) {
        $stmt = $this->pdo->prepare("
            INSERT INTO integration_field_mappings 
            (user_integration_id, entity_type, local_field, remote_field, field_type, is_bidirectional, is_required, transformation_rules)
            VALUES (:user_integration_id, :entity_type, :local_field, :remote_field, :field_type, :is_bidirectional, :is_required, :transformation_rules)
            ON DUPLICATE KEY UPDATE
            remote_field = VALUES(remote_field),
            field_type = VALUES(field_type),
            is_bidirectional = VALUES(is_bidirectional),
            is_required = VALUES(is_required),
            transformation_rules = VALUES(transformation_rules),
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'user_integration_id' => $userIntegrationId,
            'entity_type' => $entityType,
            'local_field' => $localField,
            'remote_field' => $remoteField,
            'field_type' => $fieldType,
            'is_bidirectional' => $isBidirectional,
            'is_required' => $isRequired,
            'transformation_rules' => json_encode($transformationRules)
        ]);
    }
    
    public function getSyncLogs($userIntegrationId, $limit = 50) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM integration_sync_logs 
            WHERE user_integration_id = :user_integration_id
            ORDER BY started_at DESC
            LIMIT :limit
        ");
        $stmt->execute(['user_integration_id' => $userIntegrationId, 'limit' => $limit]);
        
        $logs = $stmt->fetchAll();
        
        // Decode errors JSON
        foreach ($logs as &$log) {
            $log['errors'] = json_decode($log['errors'] ?? '[]', true) ?? [];
        }
        
        return $logs;
    }
    
    public function getIntegrationAnalytics($userIntegrationId, $days = 30) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM integration_analytics 
            WHERE user_integration_id = :user_integration_id
            AND date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            ORDER BY date DESC
        ");
        $stmt->execute(['user_integration_id' => $userIntegrationId, 'days' => $days]);
        
        return $stmt->fetchAll();
    }
    
    // Private helper methods
    private function getIntegration($integrationId) {
        $stmt = $this->pdo->prepare("SELECT * FROM integrations WHERE id = :id AND is_active = TRUE");
        $stmt->execute(['id' => $integrationId]);
        $integration = $stmt->fetch();
        
        if ($integration) {
            $integration['configuration_schema'] = json_decode($integration['configuration_schema'] ?? '{}', true) ?? [];
            $integration['webhook_endpoints'] = json_decode($integration['webhook_endpoints'] ?? '[]', true) ?? [];
            $integration['api_endpoints'] = json_decode($integration['api_endpoints'] ?? '[]', true) ?? [];
        }
        
        return $integration;
    }
    
    private function getUserIntegration($userIntegrationId) {
        $stmt = $this->pdo->prepare("SELECT * FROM user_integrations WHERE id = :id");
        $stmt->execute(['id' => $userIntegrationId]);
        $userIntegration = $stmt->fetch();
        
        if ($userIntegration) {
            $userIntegration['configuration'] = json_decode($userIntegration['configuration'] ?? '{}', true) ?? [];
            $userIntegration['credentials'] = json_decode($userIntegration['credentials'] ?? '{}', true) ?? [];
        }
        
        return $userIntegration;
    }
    
    private function testConnection($integration, $credentials) {
        // This would contain actual API calls to test the connection
        // For now, we'll simulate successful connection
        
        switch ($integration['provider']) {
            case 'salesforce':
                return $this->testSalesforceConnection($credentials);
            case 'quickbooks':
                return $this->testQuickBooksConnection($credentials);
            case 'google_calendar':
                return $this->testGoogleCalendarConnection($credentials);
            case 'stripe':
                return $this->testStripeConnection($credentials);
            default:
                return ['success' => true, 'message' => 'Connection test successful'];
        }
    }
    
    private function testSalesforceConnection($credentials) {
        // Simulate Salesforce API test
        if (empty($credentials['api_key']) || empty($credentials['api_secret'])) {
            return ['success' => false, 'message' => 'Missing API credentials'];
        }
        
        return ['success' => true, 'message' => 'Salesforce connection successful'];
    }
    
    private function testQuickBooksConnection($credentials) {
        // Simulate QuickBooks API test
        if (empty($credentials['client_id']) || empty($credentials['client_secret'])) {
            return ['success' => false, 'message' => 'Missing OAuth credentials'];
        }
        
        return ['success' => true, 'message' => 'QuickBooks connection successful'];
    }
    
    private function testGoogleCalendarConnection($credentials) {
        // Simulate Google Calendar API test
        if (empty($credentials['client_id']) || empty($credentials['refresh_token'])) {
            return ['success' => false, 'message' => 'Missing OAuth credentials'];
        }
        
        return ['success' => true, 'message' => 'Google Calendar connection successful'];
    }
    
    private function testStripeConnection($credentials) {
        // Simulate Stripe API test
        if (empty($credentials['api_key'])) {
            return ['success' => false, 'message' => 'Missing API key'];
        }
        
        return ['success' => true, 'message' => 'Stripe connection successful'];
    }
    
    private function encryptCredentials($credentials) {
        // Simple encryption - in production, use proper encryption
        return base64_encode(json_encode($credentials));
    }
    
    private function decryptCredentials($encryptedCredentials) {
        // Simple decryption - in production, use proper decryption
        return json_decode(base64_decode($encryptedCredentials), true) ?? [];
    }
    
    private function generateWebhookSecret() {
        return bin2hex(random_bytes(32));
    }
    
    private function createDefaultFieldMappings($userIntegrationId, $integration) {
        $defaultMappings = $this->getDefaultMappings($integration['provider']);
        
        foreach ($defaultMappings as $mapping) {
            $this->updateFieldMapping(
                $userIntegrationId,
                $mapping['entity_type'],
                $mapping['local_field'],
                $mapping['remote_field'],
                $mapping['field_type'],
                $mapping['is_bidirectional'],
                $mapping['is_required'],
                $mapping['transformation_rules'] ?? []
            );
        }
    }
    
    private function getDefaultMappings($provider) {
        $mappings = [
            'salesforce' => [
                ['entity_type' => 'client', 'local_field' => 'full_name', 'remote_field' => 'Name', 'field_type' => 'text', 'is_bidirectional' => true, 'is_required' => true],
                ['entity_type' => 'client', 'local_field' => 'email', 'remote_field' => 'Email', 'field_type' => 'text', 'is_bidirectional' => true, 'is_required' => true],
                ['entity_type' => 'client', 'local_field' => 'phone', 'remote_field' => 'Phone', 'field_type' => 'text', 'is_bidirectional' => true, 'is_required' => false],
            ],
            'quickbooks' => [
                ['entity_type' => 'invoice', 'local_field' => 'invoice_number', 'remote_field' => 'DocNumber', 'field_type' => 'text', 'is_bidirectional' => true, 'is_required' => true],
                ['entity_type' => 'invoice', 'local_field' => 'total_amount', 'remote_field' => 'TotalAmt', 'field_type' => 'number', 'is_bidirectional' => true, 'is_required' => true],
                ['entity_type' => 'invoice', 'local_field' => 'due_date', 'remote_field' => 'DueDate', 'field_type' => 'date', 'is_bidirectional' => true, 'is_required' => false],
            ],
            'google_calendar' => [
                ['entity_type' => 'project', 'local_field' => 'title', 'remote_field' => 'summary', 'field_type' => 'text', 'is_bidirectional' => true, 'is_required' => true],
                ['entity_type' => 'project', 'local_field' => 'deadline', 'remote_field' => 'end', 'field_type' => 'date', 'is_bidirectional' => true, 'is_required' => false],
                ['entity_type' => 'project', 'local_field' => 'description', 'remote_field' => 'description', 'field_type' => 'text', 'is_bidirectional' => true, 'is_required' => false],
            ],
            'stripe' => [
                ['entity_type' => 'invoice', 'local_field' => 'total_amount', 'remote_field' => 'amount', 'field_type' => 'number', 'is_bidirectional' => false, 'is_required' => true],
                ['entity_type' => 'invoice', 'local_field' => 'status', 'remote_field' => 'status', 'field_type' => 'text', 'is_bidirectional' => false, 'is_required' => false],
            ]
        ];
        
        return $mappings[$provider] ?? [];
    }
    
    private function createSyncLog($userIntegrationId, $syncType) {
        $stmt = $this->pdo->prepare("
            INSERT INTO integration_sync_logs 
            (user_integration_id, sync_type, status)
            VALUES (:user_integration_id, :sync_type, 'started')
        ");
        
        $stmt->execute(['user_integration_id' => $userIntegrationId, 'sync_type' => $syncType]);
        
        return $this->pdo->lastInsertId();
    }
    
    private function updateSyncLog($syncLogId, $result, $syncDuration) {
        $stmt = $this->pdo->prepare("
            UPDATE integration_sync_logs 
            SET status = :status,
                records_processed = :records_processed,
                records_created = :records_created,
                records_updated = :records_updated,
                records_deleted = :records_deleted,
                errors = :errors,
                sync_duration_ms = :sync_duration_ms,
                completed_at = NOW()
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $syncLogId,
            'status' => $result['success'] ? 'completed' : 'failed',
            'records_processed' => $result['records_processed'] ?? 0,
            'records_created' => $result['records_created'] ?? 0,
            'records_updated' => $result['records_updated'] ?? 0,
            'records_deleted' => $result['records_deleted'] ?? 0,
            'errors' => json_encode($result['errors'] ?? []),
            'sync_duration_ms' => round($syncDuration)
        ]);
    }
    
    private function updateLastSyncTime($userIntegrationId, $success) {
        $status = $success ? 'active' : 'error';
        $syncError = $success ? null : 'Last sync failed';
        
        $stmt = $this->pdo->prepare("
            UPDATE user_integrations 
            SET last_sync_at = NOW(),
                status = :status,
                sync_error = :sync_error
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $userIntegrationId,
            'status' => $status,
            'sync_error' => $syncError
        ]);
    }
    
    private function performSync($integration, $userIntegration, $credentials, $syncLogId) {
        // This would contain the actual sync logic for each integration
        // For now, we'll simulate a successful sync
        
        $result = [
            'success' => true,
            'records_processed' => 10,
            'records_created' => 2,
            'records_updated' => 5,
            'records_deleted' => 0,
            'errors' => []
        ];
        
        // Update analytics
        $this->updateIntegrationAnalytics($userIntegration['id'], 10, 0, 100);
        
        return $result;
    }
    
    private function verifyWebhookSignature($userIntegration, $webhookData, $signature) {
        // Verify webhook signature using the stored secret
        $expectedSignature = hash_hmac('sha256', json_encode($webhookData), $userIntegration['webhook_secret']);
        return hash_equals($expectedSignature, $signature);
    }
    
    private function processWebhook($userIntegration, $webhookData) {
        // Store webhook for processing
        $stmt = $this->pdo->prepare("
            INSERT INTO integration_webhooks 
            (user_integration_id, webhook_id, event_type, local_action, payload)
            VALUES (:user_integration_id, :webhook_id, :event_type, :local_action, :payload)
        ");
        
        return $stmt->execute([
            'user_integration_id' => $userIntegration['id'],
            'webhook_id' => $webhookData['id'] ?? uniqid(),
            'event_type' => $webhookData['type'] ?? 'unknown',
            'local_action' => 'sync',
            'payload' => json_encode($webhookData)
        ]);
    }
    
    private function updateIntegrationAnalytics($userIntegrationId, $apiCalls, $errors, $avgResponseTime) {
        $stmt = $this->pdo->prepare("
            INSERT INTO integration_analytics 
            (user_integration_id, date, api_calls, errors, avg_response_time_ms)
            VALUES (:user_integration_id, CURDATE(), :api_calls, :errors, :avg_response_time_ms)
            ON DUPLICATE KEY UPDATE
            api_calls = api_calls + VALUES(api_calls),
            errors = errors + VALUES(errors),
            avg_response_time_ms = (avg_response_time_ms + VALUES(avg_response_time_ms)) / 2,
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'user_integration_id' => $userIntegrationId,
            'api_calls' => $apiCalls,
            'errors' => $errors,
            'avg_response_time_ms' => $avgResponseTime
        ]);
    }
}
