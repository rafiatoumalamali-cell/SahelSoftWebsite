<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;

class AdminBlogController extends Controller {
    
    public function index() {
        $blogModel = new BlogPost();
        $posts = $blogModel->getAllPosts(50, 0);
        
        return $this->view('admin/dashboard', [
            'title' => 'Blog Management',
            'blogPosts' => $posts
        ]);
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin');
            exit;
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $author = trim($_POST['author'] ?? 'Admin');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        
        // Validate required fields
        if (empty($title) || empty($content)) {
            $_SESSION['error'] = 'Title and content are required';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        // Generate slug
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
        $slug = $slug . '-' . time();
        
        // Handle image upload
        $featuredImage = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = APP_ROOT . '/public/uploads/blog/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['featured_image']['name']));
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
                $featuredImage = 'uploads/blog/' . $fileName;
            }
        }
        
        // Insert into database - CORRECTED VERSION
        $db = \App\Core\Database::getInstance()->getConnection();
        $sql = "INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, author, category, status, created_at, updated_at) 
                VALUES (:title, :slug, :excerpt, :content, :featured_image, :author, :category, :status, NOW(), NOW())";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':excerpt' => $excerpt,
            ':content' => $content,
            ':featured_image' => $featuredImage,
            ':author' => $author,
            ':category' => $category,
            ':status' => $status
        ]);
        
        if ($result) {
            $_SESSION['success'] = 'Blog post created successfully';
        } else {
            $_SESSION['error'] = 'Failed to create blog post';
        }
        
        header('Location: ' . APP_URL . '/admin#blog');
        exit;
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin');
            exit;
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        $id = (int)$_POST['blog_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $author = trim($_POST['author'] ?? 'Admin');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = $_POST['status'] ?? 'draft';
        
        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid blog post ID';
            header('Location: ' . APP_URL . '/admin#blog');
            exit;
        }
        
        if (empty($title) || empty($content)) {
            $_SESSION['error'] = 'Title and content are required';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        // Generate slug
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
        $slug = $slug . '-' . time();
        
        // Handle image upload
        $featuredImage = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = APP_ROOT . '/public/uploads/blog/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['featured_image']['name']));
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
                $featuredImage = 'uploads/blog/' . $fileName;
            }
        }
        
        // Update database - CORRECTED VERSION
        $db = \App\Core\Database::getInstance()->getConnection();
        
        if ($featuredImage) {
            // Update with new image
            $sql = "UPDATE blog_posts 
                    SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, 
                        featured_image = :featured_image, author = :author, category = :category, 
                        status = :status, updated_at = NOW()
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':featured_image' => $featuredImage,
                ':author' => $author,
                ':category' => $category,
                ':status' => $status,
                ':id' => $id
            ]);
        } else {
            // Update without changing image
            $sql = "UPDATE blog_posts 
                    SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, 
                        author = :author, category = :category, status = :status, updated_at = NOW()
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':title' => $title,
                ':slug' => $slug,
                ':excerpt' => $excerpt,
                ':content' => $content,
                ':author' => $author,
                ':category' => $category,
                ':status' => $status,
                ':id' => $id
            ]);
        }
        
        if ($result) {
            $_SESSION['success'] = 'Blog post updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update blog post';
        }
        
        header('Location: ' . APP_URL . '/admin#blog');
        exit;
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/admin');
            exit;
        }
        
        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token';
            header('Location: ' . APP_URL . '/admin#blog');
            exit;
        }
        
        $id = (int)($_POST['blog_id'] ?? 0);
        
        if ($id > 0) {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = :id");
            $result = $stmt->execute([':id' => $id]);
            
            if ($result) {
                $_SESSION['success'] = 'Blog post deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete blog post';
            }
        } else {
            $_SESSION['error'] = 'Invalid blog post ID';
        }
        
        header('Location: ' . APP_URL . '/admin#blog');
        exit;
    }
}