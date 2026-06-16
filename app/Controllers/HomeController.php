<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        $projectModel = new \App\Models\Project();
        $projects = $projectModel->getPublicProjects();
        $userModel = new \App\Models\User();
        
        $settingModel = new \App\Models\Setting();

        // Get detailed project statistics by status
        $allProjects = $projectModel->getAllProjects();
        $projectStats = [
            'completed' => 0,
            'in_progress' => 0,
            'planning' => 0,
            'on_hold' => 0,
            'total' => 0
        ];
        
        foreach ($allProjects as $project) {
            $status = strtolower($project['status'] ?? 'planning');
            if (isset($projectStats[$status])) {
                $projectStats[$status]++;
            }
            $projectStats['total']++;
        }

        $stats = [
            'projects' => $projectStats,
            'clients' => $userModel->where('role', 'client')->count(),
            'team' => $userModel->where('role', 'admin')->count() + $userModel->where('role', 'staff')->count(),
            'satisfaction' => $settingModel->get('client_satisfaction', '100')
        ];

        return $this->view('home', [
            'title' => 'Home',
            'projects' => array_slice($projects, 0, 3), // Featured projects
            'stats' => $stats
        ]);
    }

    public function services() {
        return $this->view('services', ['title' => 'Services']);
    }

    public function portfolio() {
        $projectModel = new \App\Models\Project();
        $projects = $projectModel->getPublicProjects();
        return $this->view('portfolio', [
            'title' => 'Portfolio',
            'projects' => $projects
        ]);
    }

    public function about() {
        $projectModel = new \App\Models\Project();
        $userModel = new \App\Models\User();
        $db = \App\Core\Database::getInstance();
        $conn = $db->getConnection();

        // Get project statistics
        $allProjects = $projectModel->getAllProjects();
        $totalProjects = count($allProjects);

        // Count unique clients with projects
        $result = $conn->query("SELECT COUNT(DISTINCT client_id) as count FROM projects WHERE client_id IS NOT NULL");
        $uniqueClients = $result->fetch()['count'] ?? 0;

        // Get all clients
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'client'");
        $totalClients = $result->fetch()['count'] ?? 0;

        // Get team members (admin, staff, and any users marked as team)
        $result = $conn->query("SELECT id, full_name, email, phone, company_name, bio FROM users WHERE role IN ('admin', 'staff') ORDER BY full_name ASC");
        $teamMembers = $result->fetchAll() ?? [];

        // If no team members, try to get first user as founder
        if (empty($teamMembers)) {
            $result = $conn->query("SELECT id, full_name, email, phone, company_name, bio FROM users LIMIT 1");
            $firstUser = $result->fetch();
            if ($firstUser) {
                $teamMembers = [$firstUser];
            }
        }

        // Client satisfaction score
        $settingModel = new \App\Models\Setting();
        $satisfaction = $settingModel->get('client_satisfaction', '95') ?? '95';

        // Count technologies used (estimated if no tech table)
        $technologies = 8;

        $stats = [
            'projects' => $totalProjects . '+',
            'clients' => $uniqueClients,
            'team' => count($teamMembers),
            'satisfaction' => $satisfaction . '%'
        ];

        return $this->view('about', [
            'title' => 'About Us',
            'stats' => $stats,
            'teamMembers' => $teamMembers
        ]);
    }

    public function contact() {
        return $this->view('contact', ['title' => 'Contact Us']);
    }

    public function websiteInquiry() {
        return $this->view('website_inquiry', ['title' => 'Website Project Inquiry']);
    }

    public function terms() {
        return $this->view('terms', ['title' => 'Terms of Service']);
    }

    public function privacy() {
        return $this->view('privacy', ['title' => 'Privacy Policy']);
    }

    public function help() {
        return $this->view('help', ['title' => 'Help Center & FAQ']);
    }
}
