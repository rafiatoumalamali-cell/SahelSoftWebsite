<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;

class BlogController extends Controller {
    public function index() {
        $blogModel = new BlogPost();
        $page = $_GET['page'] ?? 1;
        $limit = 6;
        $offset = ($page - 1) * $limit;
        
        $posts = $blogModel->getAllPublished($limit, $offset);
        $categories = $blogModel->getAllCategories();
        $recentPosts = $blogModel->getRecent(3);
        
        return $this->view('blog/index', [
            'title' => __('blog_title'),
            'posts' => $posts,
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'currentPage' => $page,
            'totalPosts' => count($blogModel->getAllPublished())
        ]);
    }
    
    public function show($slug = null) {
        if (!$slug) {
            return $this->redirect('/blog');
        }
        
        $blogModel = new BlogPost();
        $post = $blogModel->getBySlug($slug);
        
        if (!$post) {
            return $this->view('errors/404', ['title' => __('post_not_found')]);
        }
        
        $relatedPosts = $blogModel->getByCategory($post['category'], 3);
        
        return $this->view('blog/view', [
            'title' => $post['title'],
            'post' => $post,
            'relatedPosts' => $relatedPosts
        ]);
    }
}
