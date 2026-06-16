<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ContentPage;

class ContentController extends Controller {
    public function __construct() {
        // Check if user is admin for all content management methods
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $this->redirect('/login');
        }
    }

    public function index() {
        $contentModel = new ContentPage();
        $pages = $contentModel->getAllPages();
        $recentChanges = $contentModel->getRecentChanges(5);
        
        // Ensure pages is an array
        if (!is_array($pages)) {
            $pages = [];
        }
        if (!is_array($recentChanges)) {
            $recentChanges = [];
        }

        return $this->view('admin/content/index', [
            'title' => 'Content Management',
            'pages' => $pages,
            'recentChanges' => $recentChanges
        ]);
    }

    public function edit() {
        $pageKey = $_GET['key'] ?? null;
        if (!$pageKey) {
            $_SESSION['error'] = 'Page key not provided.';
            return $this->redirect('/admin/content');
        }

        $contentModel = new ContentPage();
        $page = $contentModel->getPageByKey($pageKey);

        if (!$page) {
            $_SESSION['error'] = 'Page not found.';
            return $this->redirect('/admin/content');
        }

        return $this->view('admin/content/edit', [
            'title' => 'Edit Content - ' . $page['title'],
            'page' => $page
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/content');
        }

        $pageKey = $_POST['page_key'] ?? null;
        if (!$pageKey) {
            $_SESSION['error'] = 'Page key not provided.';
            return $this->redirect('/admin/content');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/content/edit?key=' . $pageKey);
        }

        $contentModel = new ContentPage();
        $page = $contentModel->getPageByKey($pageKey);

        if (!$page) {
            $_SESSION['error'] = 'Page not found.';
            return $this->redirect('/admin/content');
        }

        $data = [
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'status' => $_POST['status'] ?? 'draft'
        ];

        // Validate required fields
        if (empty($data['title'])) {
            $_SESSION['error'] = 'Title is required.';
            return $this->redirect('/admin/content/edit?key=' . $pageKey);
        }

        if (empty($data['content'])) {
            $_SESSION['error'] = 'Content is required.';
            return $this->redirect('/admin/content/edit?key=' . $pageKey);
        }

        if ($contentModel->updatePage($pageKey, $data, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Page updated successfully!';
            return $this->redirect('/admin/content');
        } else {
            $_SESSION['error'] = 'Failed to update page. Please try again.';
            return $this->redirect('/admin/content/edit?key=' . $pageKey);
        }
    }

    public function create() {
        $contentModel = new ContentPage();
        
        // Get existing page keys to avoid duplicates
        $existingPages = $contentModel->getAllPages();
        $existingKeys = array_column($existingPages, 'page_key');

        return $this->view('admin/content/create', [
            'title' => 'Create New Page',
            'existingKeys' => $existingKeys
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/content');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/content/create');
        }

        $data = [
            'page_key' => $_POST['page_key'] ?? '',
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'status' => $_POST['status'] ?? 'draft'
        ];

        // Validate required fields
        if (empty($data['page_key'])) {
            $_SESSION['error'] = 'Page key is required.';
            return $this->redirect('/admin/content/create');
        }

        if (empty($data['title'])) {
            $_SESSION['error'] = 'Title is required.';
            return $this->redirect('/admin/content/create');
        }

        if (empty($data['content'])) {
            $_SESSION['error'] = 'Content is required.';
            return $this->redirect('/admin/content/create');
        }

        // Validate page key format (alphanumeric and underscores only)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['page_key'])) {
            $_SESSION['error'] = 'Page key can only contain letters, numbers, and underscores.';
            return $this->redirect('/admin/content/create');
        }

        $contentModel = new ContentPage();
        
        // Check if page key already exists
        if ($contentModel->getPageByKey($data['page_key'])) {
            $_SESSION['error'] = 'Page key already exists. Please choose a different key.';
            return $this->redirect('/admin/content/create');
        }

        if ($contentModel->createPage($data, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Page created successfully!';
            return $this->redirect('/admin/content');
        } else {
            $_SESSION['error'] = 'Failed to create page. Please try again.';
            return $this->redirect('/admin/content/create');
        }
    }

    public function publish() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/content');
        }

        $pageKey = $_POST['page_key'] ?? null;
        if (!$pageKey) {
            $_SESSION['error'] = 'Page key not provided.';
            return $this->redirect('/admin/content');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/content');
        }

        $contentModel = new ContentPage();
        $page = $contentModel->getPageByKey($pageKey);

        if (!$page) {
            $_SESSION['error'] = 'Page not found.';
            return $this->redirect('/admin/content');
        }

        if ($contentModel->publishPage($pageKey, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Page published successfully!';
        } else {
            $_SESSION['error'] = 'Failed to publish page. Please try again.';
        }

        return $this->redirect('/admin/content');
    }

    public function unpublish() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/content');
        }

        $pageKey = $_POST['page_key'] ?? null;
        if (!$pageKey) {
            $_SESSION['error'] = 'Page key not provided.';
            return $this->redirect('/admin/content');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/content');
        }

        $contentModel = new ContentPage();
        $page = $contentModel->getPageByKey($pageKey);

        if (!$page) {
            $_SESSION['error'] = 'Page not found.';
            return $this->redirect('/admin/content');
        }

        if ($contentModel->unpublishPage($pageKey, $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Page unpublished successfully!';
        } else {
            $_SESSION['error'] = 'Failed to unpublish page. Please try again.';
        }

        return $this->redirect('/admin/content');
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/content');
        }

        $pageKey = $_POST['page_key'] ?? null;
        if (!$pageKey) {
            $_SESSION['error'] = 'Page key not provided.';
            return $this->redirect('/admin/content');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/content');
        }

        $contentModel = new ContentPage();
        $page = $contentModel->getPageByKey($pageKey);

        if (!$page) {
            $_SESSION['error'] = 'Page not found.';
            return $this->redirect('/admin/content');
        }

        // Prevent deletion of core pages
        $corePages = ['home', 'about', 'services'];
        if (in_array($pageKey, $corePages)) {
            $_SESSION['error'] = 'Cannot delete core pages.';
            return $this->redirect('/admin/content');
        }

        if ($contentModel->deletePage($pageKey)) {
            $_SESSION['success'] = 'Page deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete page. Please try again.';
        }

        return $this->redirect('/admin/content');
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        $results = [];

        if (!empty($query)) {
            $contentModel = new ContentPage();
            $results = $contentModel->searchPages($query);
        }

        return $this->view('admin/content/search', [
            'title' => 'Search Content',
            'query' => $query,
            'results' => $results
        ]);
    }
}
