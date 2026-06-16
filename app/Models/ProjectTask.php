<?php

namespace App\Models;

use App\Core\Model;

class ProjectTask extends Model {
    protected $table = 'project_tasks';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_id',
        'title',
        'description',
        'assigned_to',
        'status',
        'priority',
        'task_type',
        'estimated_hours',
        'actual_hours',
        'start_date',
        'due_date',
        'completed_at',
        'parent_task_id',
        'sort_order',
        'progress_percentage',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function createTask($data) {
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        $data['progress_percentage'] = $data['progress_percentage'] ?? 0;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        
        return $this->insert($data);
    }

    public function getTaskById($id) {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, 
                   p.title as project_title,
                   u.full_name as assigned_to_name,
                   creator.full_name as created_by_name,
                   parent.title as parent_task_title
            FROM {$this->table} pt
            LEFT JOIN projects p ON pt.project_id = p.id
            LEFT JOIN users u ON pt.assigned_to = u.id
            LEFT JOIN users creator ON pt.created_by = creator.id
            LEFT JOIN project_tasks parent ON pt.parent_task_id = parent.id
            WHERE pt.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getProjectTasks($projectId, $filters = []) {
        $sql = "
            SELECT pt.*, u.full_name as assigned_to_name
            FROM {$this->table} pt
            LEFT JOIN users u ON pt.assigned_to = u.id
            WHERE pt.project_id = :project_id
        ";
        
        $params = ['project_id' => $projectId];
        
        if (!empty($filters['status'])) {
            $sql .= " AND pt.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND pt.assigned_to = :assigned_to";
            $params['assigned_to'] = $filters['assigned_to'];
        }
        
        if (!empty($filters['priority'])) {
            $sql .= " AND pt.priority = :priority";
            $params['priority'] = $filters['priority'];
        }
        
        if (!empty($filters['task_type'])) {
            $sql .= " AND pt.task_type = :task_type";
            $params['task_type'] = $filters['task_type'];
        }
        
        if (isset($filters['parent_task_id'])) {
            $sql .= " AND pt.parent_task_id = :parent_task_id";
            $params['parent_task_id'] = $filters['parent_task_id'];
        }
        
        $sql .= " ORDER BY pt.sort_order ASC, pt.due_date ASC, pt.created_at ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTasksByUser($userId, $filters = []) {
        $sql = "
            SELECT pt.*, p.title as project_title, p.client_id
            FROM {$this->table} pt
            LEFT JOIN projects p ON pt.project_id = p.id
            WHERE pt.assigned_to = :user_id
            AND pt.status != 'completed'
        ";
        
        $params = ['user_id' => $userId];
        
        if (!empty($filters['status'])) {
            $sql .= " AND pt.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['priority'])) {
            $sql .= " AND pt.priority = :priority";
            $params['priority'] = $filters['priority'];
        }
        
        $sql .= " ORDER BY pt.priority DESC, pt.due_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getSubtasks($parentTaskId) {
        $stmt = $this->pdo->prepare("
            SELECT pt.*, u.full_name as assigned_to_name
            FROM {$this->table} pt
            LEFT JOIN users u ON pt.assigned_to = u.id
            WHERE pt.parent_task_id = :parent_task_id
            ORDER BY pt.sort_order ASC
        ");
        $stmt->execute(['parent_task_id' => $parentTaskId]);
        return $stmt->fetchAll();
    }

    public function updateTask($id, $data) {
        // Auto-update completed_at when status changes to completed
        if (isset($data['status']) && $data['status'] === 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
            $data['progress_percentage'] = 100;
        }
        
        // Auto-update progress based on status
        if (isset($data['status'])) {
            switch ($data['status']) {
                case 'not_started':
                    $data['progress_percentage'] = 0;
                    break;
                case 'in_progress':
                    $data['progress_percentage'] = max($data['progress_percentage'] ?? 0, 1);
                    break;
                case 'completed':
                    $data['progress_percentage'] = 100;
                    break;
            }
        }
        
        return $this->update($id, $data);
    }

    public function deleteTask($id) {
        // Check if task has subtasks
        $subtasks = $this->getSubtasks($id);
        if (!empty($subtasks)) {
            return false; // Cannot delete task with subtasks
        }
        
        return $this->delete($id);
    }

    public function updateProgress($id, $progress) {
        $progress = max(0, min(100, (int)$progress));
        
        $data = ['progress_percentage' => $progress];
        
        // Auto-update status based on progress
        if ($progress === 0) {
            $data['status'] = 'not_started';
        } elseif ($progress === 100) {
            $data['status'] = 'completed';
            $data['completed_at'] = date('Y-m-d H:i:s');
        } else {
            $data['status'] = 'in_progress';
        }
        
        return $this->update($id, $data);
    }

    public function logTime($taskId, $hours, $description = '') {
        $task = $this->getTaskById($taskId);
        if (!$task) {
            return false;
        }
        
        // Update actual hours
        $newActualHours = ($task['actual_hours'] ?? 0) + $hours;
        $this->update($taskId, ['actual_hours' => $newActualHours]);
        
        // Create time entry
        $timeEntryModel = new TimeEntry();
        return $timeEntryModel->createEntry([
            'project_id' => $task['project_id'],
            'task_id' => $taskId,
            'user_id' => $_SESSION['user_id'],
            'description' => $description,
            'hours' => $hours,
            'date' => date('Y-m-d')
        ]);
    }

    public function getTaskStatistics($projectId = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN status = 'not_started' THEN 1 ELSE 0 END) as not_started_tasks,
                SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) as blocked_tasks,
                SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_tasks,
                AVG(progress_percentage) as avg_progress,
                SUM(estimated_hours) as total_estimated_hours,
                SUM(actual_hours) as total_actual_hours
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($projectId) {
            $sql .= " AND project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getOverdueTasks($projectId = null) {
        $sql = "
            SELECT pt.*, p.title as project_title, u.full_name as assigned_to_name
            FROM {$this->table} pt
            LEFT JOIN projects p ON pt.project_id = p.id
            LEFT JOIN users u ON pt.assigned_to = u.id
            WHERE pt.due_date < CURDATE() 
            AND pt.status NOT IN ('completed', 'cancelled')
        ";
        
        $params = [];
        
        if ($projectId) {
            $sql .= " AND pt.project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        $sql .= " ORDER BY pt.due_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUpcomingTasks($projectId = null, $days = 7) {
        $sql = "
            SELECT pt.*, p.title as project_title, u.full_name as assigned_to_name
            FROM {$this->table} pt
            LEFT JOIN projects p ON pt.project_id = p.id
            LEFT JOIN users u ON pt.assigned_to = u.id
            WHERE pt.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
            AND pt.status NOT IN ('completed', 'cancelled')
        ";
        
        $params = ['days' => $days];
        
        if ($projectId) {
            $sql .= " AND pt.project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        $sql .= " ORDER BY pt.due_date ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTaskDependencies($taskId) {
        $stmt = $this->pdo->prepare("
            SELECT td.*, pt.title as depends_on_task_title
            FROM task_dependencies td
            LEFT JOIN project_tasks pt ON td.depends_on_task_id = pt.id
            WHERE td.task_id = :task_id
        ");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll();
    }

    public function getDependentTasks($taskId) {
        $stmt = $this->pdo->prepare("
            SELECT td.*, pt.title as dependent_task_title
            FROM task_dependencies td
            LEFT JOIN project_tasks pt ON td.task_id = pt.id
            WHERE td.depends_on_task_id = :task_id
        ");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll();
    }

    public function canStartTask($taskId) {
        $dependencies = $this->getTaskDependencies($taskId);
        
        foreach ($dependencies as $dep) {
            $depTask = $this->getTaskById($dep['depends_on_task_id']);
            if (!$depTask || $depTask['status'] !== 'completed') {
                return false;
            }
        }
        
        return true;
    }

    public function getCriticalPath($projectId) {
        // This is a simplified critical path calculation
        // In a real implementation, you'd use a proper critical path algorithm
        
        $stmt = $this->pdo->prepare("
            SELECT pt.*, u.full_name as assigned_to_name
            FROM {$this->table} pt
            LEFT JOIN users u ON pt.assigned_to = u.id
            WHERE pt.project_id = :project_id
            AND pt.is_critical = TRUE
            ORDER BY pt.due_date ASC
        ");
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll();
    }

    public function getWorkloadByUser($projectId = null, $startDate = null, $endDate = null) {
        $sql = "
            SELECT 
                u.id,
                u.full_name,
                COUNT(pt.id) as task_count,
                SUM(pt.estimated_hours) as total_estimated_hours,
                SUM(pt.actual_hours) as total_actual_hours,
                AVG(pt.progress_percentage) as avg_progress
            FROM users u
            LEFT JOIN project_tasks pt ON u.id = pt.assigned_to
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($projectId) {
            $sql .= " AND pt.project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        if ($startDate) {
            $sql .= " AND pt.start_date >= :start_date";
            $params['start_date'] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND pt.due_date <= :end_date";
            $params['end_date'] = $endDate;
        }
        
        $sql .= " GROUP BY u.id, u.full_name
                  HAVING task_count > 0
                  ORDER BY task_count DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function duplicateTask($taskId, $newProjectId = null) {
        $originalTask = $this->getTaskById($taskId);
        
        if (!$originalTask) {
            return false;
        }
        
        $newTaskData = [
            'project_id' => $newProjectId ?? $originalTask['project_id'],
            'title' => $originalTask['title'] . ' (Copy)',
            'description' => $originalTask['description'],
            'assigned_to' => $originalTask['assigned_to'],
            'status' => 'not_started',
            'priority' => $originalTask['priority'],
            'task_type' => $originalTask['task_type'],
            'estimated_hours' => $originalTask['estimated_hours'],
            'actual_hours' => 0,
            'start_date' => null,
            'due_date' => null,
            'parent_task_id' => null,
            'sort_order' => $originalTask['sort_order'],
            'progress_percentage' => 0
        ];
        
        return $this->createTask($newTaskData);
    }

    public function searchTasks($query, $filters = []) {
        $sql = "
            SELECT pt.*, p.title as project_title, u.full_name as assigned_to_name
            FROM {$this->table} pt
            LEFT JOIN projects p ON pt.project_id = p.id
            LEFT JOIN users u ON pt.assigned_to = u.id
            WHERE (pt.title LIKE :query OR pt.description LIKE :query)
        ";
        
        $params = ['query' => '%' . $query . '%'];
        
        if (!empty($filters['project_id'])) {
            $sql .= " AND pt.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }
        
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND pt.assigned_to = :assigned_to";
            $params['assigned_to'] = $filters['assigned_to'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND pt.status = :status";
            $params['status'] = $filters['status'];
        }
        
        $sql .= " ORDER BY pt.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function exportTasks($projectId = null, $format = 'json') {
        $filters = $projectId ? ['project_id' => $projectId] : [];
        $tasks = $this->getProjectTasks($projectId ?? 0, $filters);
        
        switch ($format) {
            case 'csv':
                return $this->exportToCSV($tasks);
            case 'json':
            default:
                return json_encode($tasks, JSON_PRETTY_PRINT);
        }
    }

    private function exportToCSV($tasks) {
        $csv = "ID,Title,Description,Assigned To,Status,Priority,Type,Estimated Hours,Actual Hours,Start Date,Due Date,Progress,Created At\n";
        
        foreach ($tasks as $task) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",%.2f,%.2f,%s,%s,%d,%s\n",
                $task['id'],
                str_replace('"', '""', $task['title']),
                str_replace('"', '""', $task['description'] ?? ''),
                $task['assigned_to_name'] ?? '',
                $task['status'],
                $task['priority'],
                $task['task_type'],
                $task['estimated_hours'] ?? 0,
                $task['actual_hours'] ?? 0,
                $task['start_date'] ?? '',
                $task['due_date'] ?? '',
                $task['progress_percentage'] ?? 0,
                $task['created_at']
            );
        }
        
        return $csv;
    }
}
