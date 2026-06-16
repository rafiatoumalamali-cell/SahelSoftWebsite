<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProjectTask;
use App\Models\TimeEntry;
use App\Models\Project;
use App\Services\NotificationService;

class ProjectManagementController extends Controller {
    private $taskModel;
    private $timeEntryModel;
    private $projectModel;
    private $notificationService;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
        
        $this->taskModel = new ProjectTask();
        $this->timeEntryModel = new TimeEntry();
        $this->projectModel = new Project();
        $this->notificationService = new NotificationService();
    }

    // Task Management
    public function tasks() {
        $projectId = $_GET['project_id'] ?? null;
        $filters = $this->getTaskFilters();
        
        if ($projectId) {
            $project = $this->projectModel->find($projectId);
            if (!$project) {
                $_SESSION['error'] = 'Project not found.';
                return $this->redirect('/admin/projects');
            }
            
            $tasks = $this->taskModel->getProjectTasks($projectId, $filters);
            $stats = $this->taskModel->getTaskStatistics($projectId);
            
            return $this->view('admin/projects/tasks/index', [
                'title' => 'Tasks - ' . $project['title'],
                'project' => $project,
                'tasks' => $tasks,
                'stats' => $stats,
                'filters' => $filters
            ]);
        } else {
            // Show all tasks across all projects
            $tasks = $this->getAllTasks($filters);
            
            return $this->view('admin/projects/tasks/all', [
                'title' => 'All Tasks',
                'tasks' => $tasks,
                'filters' => $filters
            ]);
        }
    }

    public function createTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $projectId = $_GET['project_id'] ?? null;
            $projects = $this->getAvailableProjects();
            $users = $this->getAvailableUsers();
            $parentTasks = [];
            
            if ($projectId) {
                $project = $this->projectModel->find($projectId);
                if (!$project) {
                    $_SESSION['error'] = 'Project not found.';
                    return $this->redirect('/admin/projects');
                }
                $parentTasks = $this->taskModel->getProjectTasks($projectId);
            }
            
            return $this->view('admin/projects/tasks/create', [
                'title' => 'Create Task',
                'project' => $project ?? null,
                'projects' => $projects,
                'users' => $users,
                'parentTasks' => $parentTasks
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/projects/tasks/create');
            }

            $taskData = [
                'project_id' => $_POST['project_id'],
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'assigned_to' => $_POST['assigned_to'] ?? null,
                'priority' => $_POST['priority'] ?? 'medium',
                'task_type' => $_POST['task_type'] ?? 'other',
                'estimated_hours' => floatval($_POST['estimated_hours'] ?? 0),
                'start_date' => $_POST['start_date'] ?? null,
                'due_date' => $_POST['due_date'] ?? null,
                'parent_task_id' => $_POST['parent_task_id'] ?? null
            ];

            $taskId = $this->taskModel->createTask($taskData);
            
            if ($taskId) {
                // Send notification to assigned user
                if ($taskData['assigned_to']) {
                    $project = $this->projectModel->find($taskData['project_id']);
                    $this->notificationService->sendNotification(
                        $taskData['assigned_to'],
                        'New Task Assigned',
                        "You have been assigned a new task: '{$taskData['title']}' in project '{$project['title']}'",
                        [
                            'category' => 'project',
                            'action_url' => APP_URL . "/admin/projects/tasks?project_id={$taskData['project_id']}",
                            'action_text' => 'View Task'
                        ]
                    );
                }
                
                $_SESSION['success'] = 'Task created successfully.';
                return $this->redirect('/admin/projects/tasks?project_id=' . $taskData['project_id']);
            } else {
                $_SESSION['error'] = 'Failed to create task.';
                return $this->redirect('/admin/projects/tasks/create');
            }
        }
    }

    public function viewTask() {
        $taskId = $_GET['id'] ?? null;
        if (!$taskId) {
            $_SESSION['error'] = 'Task ID not provided.';
            return $this->redirect('/admin/projects/tasks');
        }

        $task = $this->taskModel->getTaskById($taskId);
        if (!$task) {
            $_SESSION['error'] = 'Task not found.';
            return $this->redirect('/admin/projects/tasks');
        }

        $subtasks = $this->taskModel->getSubtasks($taskId);
        $dependencies = $this->taskModel->getTaskDependencies($taskId);
        $dependents = $this->taskModel->getDependentTasks($taskId);
        $timeEntries = $this->timeEntryModel->getProjectTimeEntries($task['project_id'], ['task_id' => $taskId]);

        return $this->view('admin/projects/tasks/view', [
            'title' => 'Task - ' . $task['title'],
            'task' => $task,
            'subtasks' => $subtasks,
            'dependencies' => $dependencies,
            'dependents' => $dependents,
            'timeEntries' => $timeEntries
        ]);
    }

    public function editTask() {
        $taskId = $_GET['id'] ?? null;
        if (!$taskId) {
            $_SESSION['error'] = 'Task ID not provided.';
            return $this->redirect('/admin/projects/tasks');
        }

        $task = $this->taskModel->getTaskById($taskId);
        if (!$task) {
            $_SESSION['error'] = 'Task not found.';
            return $this->redirect('/admin/projects/tasks');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $projects = $this->getAvailableProjects();
            $users = $this->getAvailableUsers();
            $parentTasks = $this->taskModel->getProjectTasks($task['project_id']);
            
            return $this->view('admin/projects/tasks/edit', [
                'title' => 'Edit Task - ' . $task['title'],
                'task' => $task,
                'projects' => $projects,
                'users' => $users,
                'parentTasks' => $parentTasks
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/projects/tasks/edit?id=' . $taskId);
            }

            $taskData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'assigned_to' => $_POST['assigned_to'] ?? null,
                'status' => $_POST['status'] ?? 'not_started',
                'priority' => $_POST['priority'] ?? 'medium',
                'task_type' => $_POST['task_type'] ?? 'other',
                'estimated_hours' => floatval($_POST['estimated_hours'] ?? 0),
                'start_date' => $_POST['start_date'] ?? null,
                'due_date' => $_POST['due_date'] ?? null,
                'progress_percentage' => intval($_POST['progress_percentage'] ?? 0)
            ];

            if ($this->taskModel->updateTask($taskId, $taskData)) {
                $_SESSION['success'] = 'Task updated successfully.';
                return $this->redirect('/admin/projects/tasks/view?id=' . $taskId);
            } else {
                $_SESSION['error'] = 'Failed to update task.';
                return $this->redirect('/admin/projects/tasks/edit?id=' . $taskId);
            }
        }
    }

    public function deleteTask() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/projects/tasks');
        }

        $taskId = $_POST['id'] ?? null;
        if (!$taskId) {
            $_SESSION['error'] = 'Task ID not provided.';
            return $this->redirect('/admin/projects/tasks');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/projects/tasks');
        }

        if ($this->taskModel->deleteTask($taskId)) {
            $_SESSION['success'] = 'Task deleted successfully.';
        } else {
            $_SESSION['error'] = 'Cannot delete task with subtasks.';
        }

        return $this->redirect('/admin/projects/tasks');
    }

    // Time Tracking
    public function timeTracking() {
        $projectId = $_GET['project_id'] ?? null;
        $filters = $this->getTimeFilters();
        
        if ($projectId) {
            $project = $this->projectModel->find($projectId);
            if (!$project) {
                $_SESSION['error'] = 'Project not found.';
                return $this->redirect('/admin/projects');
            }
            
            $timeEntries = $this->timeEntryModel->getProjectTimeEntries($projectId, $filters);
            $stats = $this->timeEntryModel->getTimeStats($projectId);
            
            return $this->view('admin/projects/time/index', [
                'title' => 'Time Tracking - ' . $project['title'],
                'project' => $project,
                'timeEntries' => $timeEntries,
                'stats' => $stats,
                'filters' => $filters
            ]);
        } else {
            // Show all time entries
            $timeEntries = $this->timeEntryModel->getRecentEntries(50);
            
            return $this->view('admin/projects/time/all', [
                'title' => 'Time Tracking',
                'timeEntries' => $timeEntries,
                'filters' => $filters
            ]);
        }
    }

    public function logTime() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $projectId = $_GET['project_id'] ?? null;
            $taskId = $_GET['task_id'] ?? null;
            $projects = $this->getAvailableProjects();
            $users = $this->getAvailableUsers();
            $tasks = [];
            
            if ($projectId) {
                $tasks = $this->taskModel->getProjectTasks($projectId);
            }
            
            return $this->view('admin/projects/time/log', [
                'title' => 'Log Time',
                'projects' => $projects,
                'users' => $users,
                'tasks' => $tasks,
                'projectId' => $projectId,
                'taskId' => $taskId
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/projects/time/log');
            }

            $timeData = [
                'project_id' => $_POST['project_id'],
                'task_id' => $_POST['task_id'] ?? null,
                'description' => $_POST['description'] ?? '',
                'hours' => floatval($_POST['hours'] ?? 0),
                'date' => $_POST['date'] ?? date('Y-m-d'),
                'entry_type' => $_POST['entry_type'] ?? 'regular',
                'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0),
                'is_billable' => isset($_POST['is_billable'])
            ];

            // Validate time entry
            $errors = $this->timeEntryModel->validateTimeEntry($timeData);
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                return $this->redirect('/admin/projects/time/log');
            }

            $entryId = $this->timeEntryModel->createEntry($timeData);
            
            if ($entryId) {
                $_SESSION['success'] = 'Time logged successfully.';
                return $this->redirect('/admin/projects/time?project_id=' . $timeData['project_id']);
            } else {
                $_SESSION['error'] = 'Failed to log time.';
                return $this->redirect('/admin/projects/time/log');
            }
        }
    }

    public function editTimeEntry() {
        $entryId = $_GET['id'] ?? null;
        if (!$entryId) {
            $_SESSION['error'] = 'Time entry ID not provided.';
            return $this->redirect('/admin/projects/time');
        }

        $entry = $this->timeEntryModel->getEntryById($entryId);
        if (!$entry) {
            $_SESSION['error'] = 'Time entry not found.';
            return $this->redirect('/admin/projects/time');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $projects = $this->getAvailableProjects();
            $tasks = $this->taskModel->getProjectTasks($entry['project_id']);
            
            return $this->view('admin/projects/time/edit', [
                'title' => 'Edit Time Entry',
                'entry' => $entry,
                'projects' => $projects,
                'tasks' => $tasks
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/projects/time/edit?id=' . $entryId);
            }

            $timeData = [
                'description' => $_POST['description'] ?? '',
                'hours' => floatval($_POST['hours'] ?? 0),
                'date' => $_POST['date'] ?? date('Y-m-d'),
                'entry_type' => $_POST['entry_type'] ?? 'regular',
                'hourly_rate' => floatval($_POST['hourly_rate'] ?? 0),
                'is_billable' => isset($_POST['is_billable'])
            ];

            // Validate time entry
            $errors = $this->timeEntryModel->validateTimeEntry($timeData);
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
                return $this->redirect('/admin/projects/time/edit?id=' . $entryId);
            }

            if ($this->timeEntryModel->updateEntry($entryId, $timeData)) {
                $_SESSION['success'] = 'Time entry updated successfully.';
                return $this->redirect('/admin/projects/time?project_id=' . $entry['project_id']);
            } else {
                $_SESSION['error'] = 'Failed to update time entry.';
                return $this->redirect('/admin/projects/time/edit?id=' . $entryId);
            }
        }
    }

    public function deleteTimeEntry() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->redirect('/admin/projects/time');
        }

        $entryId = $_POST['id'] ?? null;
        if (!$entryId) {
            $_SESSION['error'] = 'Time entry ID not provided.';
            return $this->redirect('/admin/projects/time');
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/projects/time');
        }

        $entry = $this->timeEntryModel->getEntryById($entryId);
        
        if ($this->timeEntryModel->deleteEntry($entryId)) {
            $_SESSION['success'] = 'Time entry deleted successfully.';
        } else {
            $_SESSION['error'] = 'Failed to delete time entry.';
        }

        return $this->redirect('/admin/projects/time?project_id=' . ($entry['project_id'] ?? ''));
    }

    // Gantt Chart and Analytics
    public function gantt() {
        $projectId = $_GET['project_id'] ?? null;
        if (!$projectId) {
            $_SESSION['error'] = 'Project ID not provided.';
            return $this->redirect('/admin/projects');
        }

        $project = $this->projectModel->find($projectId);
        if (!$project) {
            $_SESSION['error'] = 'Project not found.';
            return $this->redirect('/admin/projects');
        }

        $tasks = $this->taskModel->getProjectTasks($projectId);
        $milestones = $this->getProjectMilestones($projectId);

        return $this->view('admin/projects/gantt', [
            'title' => 'Gantt Chart - ' . $project['title'],
            'project' => $project,
            'tasks' => $tasks,
            'milestones' => $milestones
        ]);
    }

    public function analytics() {
        $projectId = $_GET['project_id'] ?? null;
        $period = $_GET['period'] ?? '30days';
        
        if ($projectId) {
            $project = $this->projectModel->find($projectId);
            if (!$project) {
                $_SESSION['error'] = 'Project not found.';
                return $this->redirect('/admin/projects');
            }
            
            $taskStats = $this->taskModel->getTaskStatistics($projectId);
            $timeStats = $this->timeEntryModel->getTimeStats($projectId);
            $timeBreakdown = $this->timeEntryModel->getProjectTimeBreakdown($projectId);
            $workload = $this->taskModel->getWorkloadByUser($projectId);
            
            return $this->view('admin/projects/analytics', [
                'title' => 'Project Analytics - ' . $project['title'],
                'project' => $project,
                'taskStats' => $taskStats,
                'timeStats' => $timeStats,
                'timeBreakdown' => $timeBreakdown,
                'workload' => $workload
            ]);
        } else {
            // Overall analytics
            $allTaskStats = $this->taskModel->getTaskStatistics();
            $allTimeStats = $this->timeEntryModel->getTimeStats();
            $overdueTasks = $this->taskModel->getOverdueTasks();
            $upcomingTasks = $this->taskModel->getUpcomingTasks();
            
            return $this->view('admin/projects/analytics', [
                'title' => 'Project Analytics',
                'taskStats' => $allTaskStats,
                'timeStats' => $allTimeStats,
                'overdueTasks' => $overdueTasks,
                'upcomingTasks' => $upcomingTasks
            ]);
        }
    }

    // Helper methods
    private function getAvailableProjects() {
        $userModel = new \App\Models\User();
        if ($_SESSION['role'] === 'admin') {
            return $this->projectModel->findAll();
        } else {
            // For clients, show only their projects
            return $this->projectModel->where('client_id', $_SESSION['user_id'])->findAll();
        }
    }

    private function getAvailableUsers() {
        $userModel = new \App\Models\User();
        return $userModel->where('role', 'admin')->findAll();
    }

    private function getAllTasks($filters) {
        // This would need to be implemented to get tasks across all projects
        // For now, return empty array
        return [];
    }

    private function getProjectMilestones($projectId) {
        // This would need to be implemented when we create the ProjectMilestone model
        return [];
    }

    private function getTaskFilters() {
        return [
            'status' => $_GET['status'] ?? null,
            'assigned_to' => $_GET['assigned_to'] ?? null,
            'priority' => $_GET['priority'] ?? null,
            'task_type' => $_GET['task_type'] ?? null
        ];
    }

    private function getTimeFilters() {
        return [
            'user_id' => $_GET['user_id'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'is_billable' => $_GET['is_billable'] ?? null
        ];
    }

    public function updateTaskProgress() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
        }

        $taskId = $_POST['task_id'] ?? null;
        $progress = intval($_POST['progress'] ?? 0);

        if (!$taskId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Task ID not provided']);
        }

        if ($this->taskModel->updateProgress($taskId, $progress)) {
            if ($progress === 100) {
                $task = $this->taskModel->getTaskById($taskId);
                $project = $this->projectModel->find($task['project_id']);
                $userModel = new \App\Models\User();
                $admins = $userModel->where('role', 'admin')->findAll();
                
                foreach ($admins as $admin) {
                    $this->notificationService->sendNotification(
                        $admin['id'],
                        'Task Completed',
                        ($_SESSION['full_name'] ?? 'A developer') . ' completed task "' . $task['title'] . '" in project "' . $project['title'] . '".',
                        [
                            'type' => 'success',
                            'category' => 'project',
                            'action_url' => APP_URL . "/admin/project/manage?id={$task['project_id']}"
                        ]
                    );
                }
            }
            return $this->jsonResponse(['success' => true, 'message' => 'Progress updated']);
        } else {
            return $this->jsonResponse(['success' => false, 'message' => 'Failed to update progress']);
        }
    }

    public function getTaskData() {
        $taskId = $_GET['task_id'] ?? null;
        if (!$taskId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Task ID not provided']);
        }

        $task = $this->taskModel->getTaskById($taskId);
        if ($task) {
            return $this->jsonResponse(['success' => true, 'task' => $task]);
        } else {
            return $this->jsonResponse(['success' => false, 'message' => 'Task not found']);
        }
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
