<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SearchService;

class SearchController extends Controller {
    private $searchService;

    public function __construct() {
        $this->searchService = new SearchService();
    }

    public function index() {
        $query = $_GET['q'] ?? '';
        $filters = $this->getSearchFilters();
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $results = [];
        $totalResults = 0;
        $searchTime = 0;

        if (!empty($query)) {
            $startTime = microtime(true);
            $results = $this->searchService->globalSearch($query, $filters, $limit, $offset);
            $searchTime = (microtime(true) - $startTime) * 1000;
            
            // Get total results count
            $allResults = $this->searchService->globalSearch($query, $filters, 1000, 0);
            $totalResults = count($allResults);
            
            // Increment suggestion usage if query matches a suggestion
            $suggestions = $this->searchService->getSearchSuggestions($query, 1);
            if (!empty($suggestions) && strtolower($suggestions[0]['suggestion']) === strtolower($query)) {
                $this->searchService->incrementSuggestionUsage($suggestions[0]['suggestion']);
            }
        }

        $availableFilters = $this->searchService->getAvailableFilters();
        $savedSearches = $this->searchService->getSavedSearches($_SESSION['user_id'] ?? null);
        $recentSearches = $this->searchService->getRecentSearches($_SESSION['user_id'] ?? null);

        return $this->view('admin/search/index', [
            'title' => 'Advanced Search',
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults,
            'searchTime' => round($searchTime, 2),
            'filters' => $filters,
            'availableFilters' => $availableFilters,
            'savedSearches' => $savedSearches,
            'recentSearches' => $recentSearches,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function suggestions() {
        $query = $_GET['q'] ?? '';
        $limit = intval($_GET['limit'] ?? 10);

        if (empty($query)) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        $suggestions = $this->searchService->getSearchSuggestions($query, $limit);

        header('Content-Type: application/json');
        echo json_encode($suggestions);
        exit;
    }

    public function saveSearch() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['name'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        $searchQuery = [
            'query' => $data['query'] ?? '',
            'filters' => $data['filters'] ?? []
        ];

        $result = $this->searchService->saveSearch(
            $data['name'],
            $data['description'] ?? '',
            $searchQuery,
            $data['filters'] ?? [],
            $data['is_public'] ?? false
        );

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function loadSavedSearch() {
        if (!isset($_GET['id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Search ID not provided']);
            exit;
        }

        $savedSearches = $this->searchService->getSavedSearches($_SESSION['user_id'] ?? null);
        $searchId = intval($_GET['id']);
        
        $targetSearch = null;
        foreach ($savedSearches as $search) {
            if ($search['id'] == $searchId) {
                $targetSearch = $search;
                break;
            }
        }

        if (!$targetSearch) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Saved search not found']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'search' => $targetSearch]);
        exit;
    }

    public function analytics() {
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/dashboard');
        }

        $dateFrom = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');
        
        $statistics = $this->searchService->getSearchStatistics($dateFrom, $dateTo);
        $popularSearches = $this->searchService->getPopularSearches(20);

        return $this->view('admin/search/analytics', [
            'title' => 'Search Analytics',
            'statistics' => $statistics,
            'popularSearches' => $popularSearches,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }

    public function reindex() {
        if ($_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $entityType = $_POST['entity_type'] ?? 'all';
        $reindexedCount = $this->reindexEntities($entityType);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => "Reindexed {$reindexedCount} entities",
            'count' => $reindexedCount
        ]);
        exit;
    }

    public function quickSearch() {
        $query = $_GET['q'] ?? '';
        $limit = intval($_GET['limit'] ?? 5);

        if (empty($query)) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        $results = $this->searchService->globalSearch($query, [], $limit, 0);

        // Format for quick search display
        $quickResults = [];
        foreach ($results as $result) {
            $quickResults[] = [
                'id' => $result['entity_id'],
                'type' => $result['entity_type'],
                'title' => $result['title'],
                'url' => $result['url'],
                'icon' => $result['icon'],
                'content' => substr($result['content'], 0, 100) . '...'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($quickResults);
        exit;
    }

    private function getSearchFilters() {
        $filters = [];
        
        // Entity types filter
        if (!empty($_GET['entity_types'])) {
            $filters['entity_types'] = is_array($_GET['entity_types']) ? $_GET['entity_types'] : [$_GET['entity_types']];
        }
        
        // Status filter
        if (!empty($_GET['status'])) {
            $filters['status'] = is_array($_GET['status']) ? $_GET['status'] : [$_GET['status']];
        }
        
        // Priority filter
        if (!empty($_GET['priority'])) {
            $filters['priority'] = is_array($_GET['priority']) ? $_GET['priority'] : [$_GET['priority']];
        }
        
        // Client filter
        if (!empty($_GET['client_id'])) {
            $filters['client_id'] = intval($_GET['client_id']);
        }
        
        // Message type filter
        if (!empty($_GET['message_type'])) {
            $filters['message_type'] = is_array($_GET['message_type']) ? $_GET['message_type'] : [$_GET['message_type']];
        }
        
        // Sender filter
        if (!empty($_GET['sender_id'])) {
            $filters['sender_id'] = intval($_GET['sender_id']);
        }
        
        // Amount range filter
        if (!empty($_GET['amount_min'])) {
            $filters['amount_min'] = floatval($_GET['amount_min']);
        }
        if (!empty($_GET['amount_max'])) {
            $filters['amount_max'] = floatval($_GET['amount_max']);
        }
        
        // File type filter
        if (!empty($_GET['file_type'])) {
            $filters['file_type'] = is_array($_GET['file_type']) ? $_GET['file_type'] : [$_GET['file_type']];
        }
        
        // Date range filter
        if (!empty($_GET['date_from'])) {
            $filters['date_from'] = $_GET['date_from'];
        }
        if (!empty($_GET['date_to'])) {
            $filters['date_to'] = $_GET['date_to'];
        }
        
        return $filters;
    }

    private function reindexEntities($entityType) {
        $count = 0;
        
        if ($entityType === 'all' || $entityType === 'project') {
            $projectModel = new \App\Models\Project();
            $projects = $projectModel->findAll();
            
            foreach ($projects as $project) {
                $this->searchService->updateSearchIndex(
                    'project',
                    $project['id'],
                    $project['title'],
                    $project['description'] . ' ' . $project['requirements'],
                    [
                        'status' => $project['status'],
                        'client_id' => $project['client_id'],
                        'priority' => $project['priority'],
                        'deadline' => $project['deadline']
                    ],
                    [],
                    1.0
                );
                $count++;
            }
        }
        
        if ($entityType === 'all' || $entityType === 'message') {
            $messageModel = new \App\Models\Message();
            $messages = $messageModel->findAll();
            
            foreach ($messages as $message) {
                $this->searchService->updateSearchIndex(
                    'message',
                    $message['id'],
                    $message['subject'],
                    $message['content'],
                    [
                        'sender_id' => $message['sender_id'],
                        'recipient_id' => $message['recipient_id'],
                        'message_type' => $message['message_type'],
                        'project_id' => $message['project_id']
                    ],
                    [],
                    0.8
                );
                $count++;
            }
        }
        
        if ($entityType === 'all' || $entityType === 'proposal') {
            $proposalModel = new \App\Models\Proposal();
            $proposals = $proposalModel->findAll();
            
            foreach ($proposals as $proposal) {
                $this->searchService->updateSearchIndex(
                    'proposal',
                    $proposal['id'],
                    $proposal['title'],
                    $proposal['description'],
                    [
                        'status' => $proposal['status'],
                        'client_id' => $proposal['client_id'],
                        'total_amount' => $proposal['total_amount']
                    ],
                    [],
                    0.9
                );
                $count++;
            }
        }
        
        if ($entityType === 'all' || $entityType === 'invoice') {
            $invoiceModel = new \App\Models\Invoice();
            $invoices = $invoiceModel->findAll();
            
            foreach ($invoices as $invoice) {
                $this->searchService->updateSearchIndex(
                    'invoice',
                    $invoice['id'],
                    $invoice['invoice_number'] . ' - ' . ($invoice['client_name'] ?? ''),
                    $invoice['description'] ?? '',
                    [
                        'status' => $invoice['status'],
                        'client_id' => $invoice['client_id'],
                        'amount' => $invoice['total_amount'],
                        'due_date' => $invoice['due_date']
                    ],
                    [],
                    0.9
                );
                $count++;
            }
        }
        
        if ($entityType === 'all' || $entityType === 'user') {
            $userModel = new \App\Models\User();
            $users = $userModel->findAll();
            
            foreach ($users as $user) {
                $this->searchService->updateSearchIndex(
                    'user',
                    $user['id'],
                    $user['full_name'],
                    $user['email'] . ' ' . ($user['company_name'] ?? '') . ' ' . ($user['phone'] ?? ''),
                    [
                        'role' => $user['role'],
                        'status' => $user['status'] ?? 'active'
                    ],
                    [],
                    0.7
                );
                $count++;
            }
        }
        
        return $count;
    }
}
