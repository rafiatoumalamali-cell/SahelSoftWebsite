<?php

namespace App\Models;

use App\Core\Database;

class BlogPost {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllPublished($limit = 10, $offset = 0) {
        $sql = "
            SELECT id, title, slug, excerpt, featured_image, created_at
            FROM blog_posts 
            WHERE status = 'published'
            ORDER BY created_at DESC 
            LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBySlug($slug) {
        $stmt = $this->db->prepare("
            SELECT id, title, slug, excerpt, content, featured_image, author, category, created_at, updated_at
            FROM blog_posts 
            WHERE slug = :slug AND status = 'published'
        ");
        
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function getRecent($limit = 5) {
        $sql = "
            SELECT id, title, slug, excerpt, featured_image, created_at
            FROM blog_posts 
            WHERE status = 'published'
            ORDER BY created_at DESC 
            LIMIT " . (int)$limit . "
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByCategory($category, $limit = 10) {
        $sql = "
            SELECT id, title, slug, excerpt, featured_image, created_at
            FROM blog_posts 
            WHERE category = :category AND status = 'published'
            ORDER BY created_at DESC 
            LIMIT " . (int)$limit . "
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll();
    }

    public function getAllPosts($limit = 50, $offset = 0) {
        $sql = "
            SELECT id, title, slug, excerpt, featured_image, author, category, status, created_at, updated_at
            FROM blog_posts 
            ORDER BY created_at DESC 
            LIMIT " . (int)$limit . " OFFSET " . (int)$offset . "
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllCategories() {
        $stmt = $this->db->prepare("
            SELECT DISTINCT category 
            FROM blog_posts 
            WHERE status = 'published' AND category IS NOT NULL
            ORDER BY category
        ");
        
        $stmt->execute();
        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = $row['category'];
        }
        return array_filter($categories);
    }
}
