<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Project;
use App\Models\User;

class TeamController extends Controller {
    public function __construct() {
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'project_manager', 'developer'])) {
            $this->redirect('/login');
        }
    }

    public function dashboard() {
        $projectModel = new Project();
        $projects = $projectModel->getAllProjects();
        
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT t.*, p.title as project_title 
                              FROM project_tasks t 
                              JOIN projects p ON t.project_id = p.id 
                              WHERE t.assigned_to = :user_id 
                              AND t.status != 'completed'
                              ORDER BY t.due_date ASC LIMIT 5");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $recentTasks = $stmt->fetchAll();
        
        return $this->view('team/dashboard', [
            'title' => 'Team Dashboard',
            'projects' => $projects,
            'recentTasks' => $recentTasks
        ]);
    }

    public function create() {
        // Only Admin/Manager can create projects
        if ($_SESSION['role'] === 'developer') return $this->redirect('/team/dashboard');

        // Need list of clients
        $userModel = new User(); // Need to add getClients method to User model or just raw query
        // For now, let's assume we can query users by role. 
        // I will add a method to User model later or do a direct query here if needed, 
        // but best practice is Model.
        // Let's hack it for now: fetch all users and filter in view or add method.
        // I'll add getClients method to User model in next step or now via MultiReplace? 
        // I'll just skip the client dropdown for now and mock it or enter ID manually to save steps?
        // No, I should do it right. I'll add getClients to User model.
        
        return $this->view('team/project_form', ['title' => 'Create Project']);
    }

    public function store() {
        if ($_SESSION['role'] === 'developer') return $this->redirect('/team/dashboard');

        $data = [
            'client_id' => $_POST['client_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'budget' => $_POST['budget'],
            'start_date' => $_POST['start_date'],
            'deadline' => $_POST['deadline']
        ];

        $projectModel = new Project();
        if ($projectModel->create($data)) {
            $this->redirect('/team/dashboard');
        } else {
            // Error handling
            echo "Error creating project";
        }
    }

    public function edit() {
        if ($_SESSION['role'] === 'developer') return $this->redirect('/team/dashboard');
        
        $id = $_GET['id'] ?? null;
        $projectModel = new Project();
        $project = $projectModel->getProjectById($id);

        return $this->view('team/project_form', [
            'title' => 'Edit Project',
            'project' => $project
        ]);
    }

    public function update() {
        if ($_SESSION['role'] === 'developer') return $this->redirect('/team/dashboard');

        $id = $_POST['id'];
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'budget' => $_POST['budget'],
            'start_date' => $_POST['start_date'],
            'deadline' => $_POST['deadline'],
            'status' => $_POST['status']
        ];

        $projectModel = new Project();
        if ($projectModel->update($id, $data)) {
            $this->redirect('/team/dashboard');
        } else {
             echo "Error updating project";
        }
    }

    public function viewProject() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->redirect('/team/dashboard');
        }
        
        $projectModel = new Project();
        $project = $projectModel->getProjectById($id);
        
        if (!$project) {
            return $this->redirect('/team/dashboard');
        }
        
        return $this->view('team/project_view', [
            'title' => 'View Project - ' . $project['title'],
            'project' => $project
        ]);
    }

    public function tasks() {
        $projectId = $_GET['project_id'] ?? null;
        
        // Fetch tasks for the user or project
        $pdo = \App\Core\Database::getInstance()->getConnection();
        
        if ($projectId) {
            $stmt = $pdo->prepare("SELECT * FROM project_tasks WHERE project_id = :project_id ORDER BY created_at DESC");
            $stmt->execute(['project_id' => $projectId]);
        } else {
            // Get tasks assigned to current user
            $stmt = $pdo->prepare("SELECT t.*, p.title as project_title FROM project_tasks t 
                                  JOIN projects p ON t.project_id = p.id 
                                  WHERE t.assigned_to = :user_id OR t.created_by = :user_id 
                                  ORDER BY t.due_date ASC");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
        }
        
        $tasks = $stmt->fetchAll();
        
        return $this->view('team/tasks', [
            'title' => 'My Tasks',
            'tasks' => $tasks,
            'projectId' => $projectId
        ]);
    }

    public function updateTaskStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/team/tasks');
        }
        
        $taskId = $_POST['task_id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if (!$taskId || !$status) {
            return $this->redirect('/team/tasks');
        }
        
        $pdo = \App\Core\Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE project_tasks SET status = :status, updated_at = NOW() WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $taskId]);
        
        return $this->redirect('/team/tasks');
    }

    public function reports() {
        // Only PM and Admin can view reports
        if ($_SESSION['role'] === 'developer') {
            return $this->redirect('/team/dashboard');
        }
        
        $projectModel = new Project();
        $userModel = new User();
        
        $projects = $projectModel->getAllProjects();
        $teamMembers = $userModel->getUsersByRole('developer');
        
        return $this->view('team/reports', [
            'title' => 'Team Reports',
            'projects' => $projects,
            'teamMembers' => $teamMembers
        ]);
    }

    public function messages() {
        return $this->view('team/messages', [
            'title' => 'Team Messages'
        ]);
    }
}
