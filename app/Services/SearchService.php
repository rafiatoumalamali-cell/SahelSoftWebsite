<?php

namespace App\Services;

use App\Core\Model;
use PDO;

class SearchService {
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
    
    public function globalSearch($query, $filters = [], $limit = 20, $offset = 0) {
        $startTime = microtime(true);
        
        // Log search analytics
        $userId = $_SESSION['user_id'] ?? null;
        
        // Build search parameters
        $entityTypes = $filters['entity_types'] ?? ['project', 'message', 'proposal', 'invoice', 'user', 'contact', 'file', 'document', 'task', 'payment'];
        
        // Perform search
        $results = $this->performSearch($query, $entityTypes, $filters, $limit, $offset);
        
        // Calculate search time
        $timeTaken = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        
        // Log analytics
        $this->logSearchAnalytics($userId, $query, $filters, count($results), $timeTaken);
        
        return $results;
    }
    
    public function performSearch($query, $entityTypes, $filters, $limit, $offset) {
        $results = [];
        
        foreach ($entityTypes as $entityType) {
            $entityResults = $this->searchEntityType($entityType, $query, $filters, $limit, $offset);
            $results = array_merge($results, $entityResults);
        }
        
        // Sort by relevance
        usort($results, function($a, $b) {
            return $b['relevance_score'] <=> $a['relevance_score'];
        });
        
        // Apply limit and offset
        return array_slice($results, $offset, $limit);
    }
    
    private function searchEntityType($entityType, $query, $filters, $limit, $offset) {
        $sql = $this->buildSearchQuery($entityType, $filters);
        $params = $this->buildSearchParams($entityType, $query, $filters);
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            // Add entity type specific data
            foreach ($results as &$result) {
                $result['entity_type'] = $entityType;
                $result['url'] = $this->getEntityUrl($entityType, $result['entity_id']);
                $result['icon'] = $this->getEntityIcon($entityType);
                $result['metadata'] = json_decode($result['metadata'] ?? '{}', true) ?? [];
            }
            
            return $results;
        } catch (Exception $e) {
            error_log("Search error for entity type $entityType: " . $e->getMessage());
            return [];
        }
    }
    
    private function buildSearchQuery($entityType, $filters) {
        $baseQuery = "
            SELECT 
                si.entity_id,
                si.title,
                si.content,
                MATCH(si.title, si.content) AGAINST(:query IN NATURAL LANGUAGE MODE) * si.weight as relevance_score,
                si.metadata
            FROM search_index si
            WHERE si.entity_type = :entity_type
            AND MATCH(si.title, si.content) AGAINST(:query IN NATURAL LANGUAGE MODE)
        ";
        
        // Add entity-specific filters
        $baseQuery .= $this->addEntityFilters($entityType, $filters);
        
        $baseQuery .= " ORDER BY relevance_score DESC";
        
        return $baseQuery;
    }
    
    private function addEntityFilters($entityType, $filters) {
        $filterSql = '';
        
        switch ($entityType) {
            case 'project':
                if (!empty($filters['status'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.status') IN ('" . implode("','", $filters['status']) . "')";
                }
                if (!empty($filters['priority'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.priority') IN ('" . implode("','", $filters['priority']) . "')";
                }
                if (!empty($filters['client_id'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.client_id') = :client_id";
                }
                break;
                
            case 'message':
                if (!empty($filters['message_type'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.message_type') IN ('" . implode("','", $filters['message_type']) . "')";
                }
                if (!empty($filters['sender_id'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.sender_id') = :sender_id";
                }
                break;
                
            case 'invoice':
                if (!empty($filters['status'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.status') IN ('" . implode("','", $filters['status']) . "')";
                }
                if (!empty($filters['amount_min'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.amount') >= :amount_min";
                }
                if (!empty($filters['amount_max'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.amount') <= :amount_max";
                }
                break;
                
            case 'proposal':
                if (!empty($filters['status'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.status') IN ('" . implode("','", $filters['status']) . "')";
                }
                break;
                
            case 'file':
                if (!empty($filters['file_type'])) {
                    $filterSql .= " AND JSON_EXTRACT(si.metadata, '$.file_type') IN ('" . implode("','", $filters['file_type']) . "')";
                }
                break;
        }
        
        // Add date range filter
        if (!empty($filters['date_from'])) {
            $filterSql .= " AND si.created_at >= :date_from";
        }
        if (!empty($filters['date_to'])) {
            $filterSql .= " AND si.created_at <= :date_to";
        }
        
        return $filterSql;
    }
    
    private function buildSearchParams($entityType, $query, $filters) {
        $params = [
            'query' => $query,
            'entity_type' => $entityType
        ];
        
        // Add filter parameters
        if (!empty($filters['client_id'])) {
            $params['client_id'] = $filters['client_id'];
        }
        if (!empty($filters['sender_id'])) {
            $params['sender_id'] = $filters['sender_id'];
        }
        if (!empty($filters['amount_min'])) {
            $params['amount_min'] = $filters['amount_min'];
        }
        if (!empty($filters['amount_max'])) {
            $params['amount_max'] = $filters['amount_max'];
        }
        if (!empty($filters['date_from'])) {
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $params['date_to'] = $filters['date_to'];
        }
        
        return $params;
    }
    
    public function getSearchSuggestions($query, $limit = 10) {
        $sql = "
            SELECT suggestion, category, weight
            FROM search_suggestions
            WHERE is_active = TRUE
            AND suggestion LIKE :query
            ORDER BY weight DESC, usage_count DESC
            LIMIT :limit
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'query' => '%' . $query . '%',
            'limit' => $limit
        ]);
        
        return $stmt->fetchAll();
    }
    
    public function getAvailableFilters() {
        $stmt = $this->pdo->prepare("
            SELECT * FROM search_filters 
            WHERE is_active = TRUE 
            ORDER BY sort_order ASC
        ");
        $stmt->execute();
        
        $filters = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($filters as &$filter) {
            $filter['entity_types'] = json_decode($filter['entity_types'] ?? '[]', true) ?? [];
            $filter['filter_config'] = json_decode($filter['filter_config'] ?? '{}', true) ?? [];
        }
        
        return $filters;
    }
    
    public function saveSearch($name, $description, $searchQuery, $filters, $isPublic = false) {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            return false;
        }
        
        $stmt = $this->pdo->prepare("
            INSERT INTO saved_searches 
            (user_id, name, description, search_query, filters, is_public)
            VALUES (:user_id, :name, :description, :search_query, :filters, :is_public)
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'name' => $name,
            'description' => $description,
            'search_query' => json_encode($searchQuery),
            'filters' => json_encode($filters),
            'is_public' => $isPublic
        ]);
    }
    
    public function getSavedSearches($userId = null) {
        $sql = "
            SELECT ss.*, u.full_name as created_by_name
            FROM saved_searches ss
            LEFT JOIN users u ON ss.user_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($userId) {
            $sql .= " AND (ss.user_id = :user_id OR ss.is_public = TRUE)";
            $params['user_id'] = $userId;
        } else {
            $sql .= " AND ss.is_public = TRUE";
        }
        
        $sql .= " ORDER BY ss.usage_count DESC, ss.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $searches = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($searches as &$search) {
            $search['search_query'] = json_decode($search['search_query'] ?? '{}', true) ?? [];
            $search['filters'] = json_decode($search['filters'] ?? '{}', true) ?? [];
        }
        
        return $searches;
    }
    
    public function updateSearchIndex($entityType, $entityId, $title, $content, $metadata = [], $tags = [], $weight = 1.0) {
        $stmt = $this->pdo->prepare("
            INSERT INTO search_index 
            (entity_type, entity_id, title, content, metadata, tags, keywords, weight)
            VALUES (:entity_type, :entity_id, :title, :content, :metadata, :tags, :keywords, :weight)
            ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            content = VALUES(content),
            metadata = VALUES(metadata),
            tags = VALUES(tags),
            keywords = VALUES(keywords),
            weight = VALUES(weight),
            updated_at = NOW()
        ");
        
        // Extract keywords from content
        $keywords = $this->extractKeywords($content);
        
        return $stmt->execute([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'title' => $title,
            'content' => $content,
            'metadata' => json_encode($metadata),
            'tags' => json_encode($tags),
            'keywords' => json_encode($keywords),
            'weight' => $weight
        ]);
    }
    
    public function removeFromSearchIndex($entityType, $entityId) {
        $stmt = $this->pdo->prepare("
            DELETE FROM search_index 
            WHERE entity_type = :entity_type AND entity_id = :entity_id
        ");
        
        return $stmt->execute([
            'entity_type' => $entityType,
            'entity_id' => $entityId
        ]);
    }
    
    public function getSearchStatistics($dateFrom = null, $dateTo = null) {
        $sql = "
            SELECT 
                DATE(created_at) as search_date,
                COUNT(*) as total_searches,
                AVG(results_count) as avg_results,
                AVG(time_taken_ms) as avg_time_ms,
                COUNT(DISTINCT user_id) as unique_users
            FROM search_analytics
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($dateFrom) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params['date_to'] = $dateTo;
        }
        
        $sql .= " GROUP BY DATE(created_at) ORDER BY search_date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }
    
    public function getPopularSearches($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT 
                search_query,
                COUNT(*) as search_count,
                AVG(results_count) as avg_results,
                COUNT(DISTINCT user_id) as unique_users
            FROM search_analytics
            WHERE search_query IS NOT NULL AND search_query != ''
            GROUP BY search_query
            ORDER BY search_count DESC
            LIMIT :limit
        ");
        
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }
    
    private function getEntityUrl($entityType, $entityId) {
        $urls = [
            'project' => APP_URL . '/admin/projects/view?id=' . $entityId,
            'message' => APP_URL . '/admin/messages/view?id=' . $entityId,
            'proposal' => APP_URL . '/admin/proposals/view?id=' . $entityId,
            'invoice' => APP_URL . '/admin/invoices/view?id=' . $entityId,
            'user' => APP_URL . '/admin/users/view?id=' . $entityId,
            'contact' => APP_URL . '/admin/contacts/view?id=' . $entityId,
            'file' => APP_URL . '/admin/files/view?id=' . $entityId,
            'document' => APP_URL . '/admin/documents/view?id=' . $entityId,
            'task' => APP_URL . '/admin/projects/tasks/view?id=' . $entityId,
            'payment' => APP_URL . '/admin/payments/view?id=' . $entityId
        ];
        
        return $urls[$entityType] ?? '#';
    }
    
    private function getEntityIcon($entityType) {
        $icons = [
            'project' => '📁',
            'message' => '💬',
            'proposal' => '📋',
            'invoice' => '🧾',
            'user' => '👤',
            'contact' => '📞',
            'file' => '📄',
            'document' => '📑',
            'task' => '✅',
            'payment' => '💳'
        ];
        
        return $icons[$entityType] ?? '📌';
    }
    
    private function extractKeywords($content) {
        $keywords = [];
        $content = strtolower($content);
        
        // Define important keywords to look for
        $importantWords = [
            'urgent', 'important', 'critical', 'high priority', 'low priority',
            'payment', 'invoice', 'proposal', 'contract', 'agreement',
            'deadline', 'timeline', 'schedule', 'milestone',
            'client', 'customer', 'admin', 'team', 'project',
            'completed', 'pending', 'active', 'cancelled', 'rejected',
            'bug', 'issue', 'feature', 'enhancement', 'update'
        ];
        
        foreach ($importantWords as $word) {
            if (strpos($content, $word) !== false) {
                $keywords[] = $word;
            }
        }
        
        return array_unique($keywords);
    }
    
    private function logSearchAnalytics($userId, $query, $filters, $resultsCount, $timeTaken) {
        $stmt = $this->pdo->prepare("
            INSERT INTO search_analytics 
            (user_id, session_id, search_query, filters, results_count, time_taken_ms)
            VALUES (:user_id, :session_id, :search_query, :filters, :results_count, :time_taken_ms)
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'session_id' => session_id(),
            'search_query' => $query,
            'filters' => json_encode($filters),
            'results_count' => $resultsCount,
            'time_taken_ms' => round($timeTaken)
        ]);
    }
    
    public function incrementSuggestionUsage($suggestion) {
        $stmt = $this->pdo->prepare("
            UPDATE search_suggestions 
            SET usage_count = usage_count + 1 
            WHERE suggestion = :suggestion
        ");
        
        return $stmt->execute(['suggestion' => $suggestion]);
    }
    
    public function getRecentSearches($userId, $limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT search_query, created_at
            FROM search_analytics
            WHERE user_id = :user_id
            AND search_query IS NOT NULL AND search_query != ''
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        
        $stmt->execute(['user_id' => $userId, 'limit' => $limit]);
        return $stmt->fetchAll();
    }
}
