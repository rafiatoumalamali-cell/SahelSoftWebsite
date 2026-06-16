<?php

namespace App\Models;

use App\Core\Model;

class TimeEntry extends Model {
    protected $table = 'time_entries';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'project_id',
        'task_id',
        'user_id',
        'description',
        'hours',
        'date',
        'entry_type',
        'hourly_rate',
        'is_billable',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function createEntry($data) {
        $data['user_id'] = $_SESSION['user_id'] ?? null;
        $data['is_billable'] = $data['is_billable'] ?? true;
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->insert($data);
    }

    public function getEntryById($id) {
        $stmt = $this->pdo->prepare("
            SELECT te.*, 
                   p.title as project_title,
                   pt.title as task_title,
                   u.full_name as user_name
            FROM {$this->table} te
            LEFT JOIN projects p ON te.project_id = p.id
            LEFT JOIN project_tasks pt ON te.task_id = pt.id
            LEFT JOIN users u ON te.user_id = u.id
            WHERE te.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getProjectTimeEntries($projectId, $filters = []) {
        $sql = "
            SELECT te.*, u.full_name as user_name, pt.title as task_title
            FROM {$this->table} te
            LEFT JOIN users u ON te.user_id = u.id
            LEFT JOIN project_tasks pt ON te.task_id = pt.id
            WHERE te.project_id = :project_id
        ";
        
        $params = ['project_id' => $projectId];
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND te.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['task_id'])) {
            $sql .= " AND te.task_id = :task_id";
            $params['task_id'] = $filters['task_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND te.date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND te.date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        if (!empty($filters['is_billable'])) {
            $sql .= " AND te.is_billable = :is_billable";
            $params['is_billable'] = $filters['is_billable'];
        }
        
        $sql .= " ORDER BY te.date DESC, te.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserTimeEntries($userId, $filters = []) {
        $sql = "
            SELECT te.*, p.title as project_title, pt.title as task_title
            FROM {$this->table} te
            LEFT JOIN projects p ON te.project_id = p.id
            LEFT JOIN project_tasks pt ON te.task_id = pt.id
            WHERE te.user_id = :user_id
        ";
        
        $params = ['user_id' => $userId];
        
        if (!empty($filters['project_id'])) {
            $sql .= " AND te.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND te.date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND te.date <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        $sql .= " ORDER BY te.date DESC, te.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateEntry($id, $data) {
        return $this->update($id, $data);
    }

    public function deleteEntry($id) {
        return $this->delete($id);
    }

    public function getTimeStats($projectId = null, $userId = null, $dateFrom = null, $dateTo = null) {
        $sql = "
            SELECT 
                COUNT(*) as total_entries,
                SUM(hours) as total_hours,
                SUM(CASE WHEN is_billable = TRUE THEN hours ELSE 0 END) as billable_hours,
                SUM(CASE WHEN is_billable = FALSE THEN hours ELSE 0 END) as non_billable_hours,
                AVG(hours) as avg_hours_per_entry,
                SUM(hours * hourly_rate) as total_cost,
                SUM(CASE WHEN is_billable = TRUE THEN hours * hourly_rate ELSE 0 END) as billable_cost
            FROM {$this->table}
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($projectId) {
            $sql .= " AND project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        if ($dateFrom) {
            $sql .= " AND date >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND date <= :date_to";
            $params['date_to'] = $dateTo;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getDailyTimeTracking($projectId = null, $days = 30) {
        $sql = "
            SELECT 
                date,
                SUM(hours) as total_hours,
                COUNT(*) as entry_count,
                SUM(CASE WHEN is_billable = TRUE THEN hours ELSE 0 END) as billable_hours
            FROM {$this->table}
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
        ";
        
        $params = ['days' => $days];
        
        if ($projectId) {
            $sql .= " AND project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        $sql .= " GROUP BY date ORDER BY date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getWeeklyTimeTracking($projectId = null, $weeks = 12) {
        $sql = "
            SELECT 
                YEARWEEK(date) as week,
                MIN(date) as week_start,
                MAX(date) as week_end,
                SUM(hours) as total_hours,
                COUNT(*) as entry_count
            FROM {$this->table}
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL :weeks WEEK)
        ";
        
        $params = ['weeks' => $weeks];
        
        if ($projectId) {
            $sql .= " AND project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        $sql .= " GROUP BY YEARWEEK(date) ORDER BY week DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserProductivity($userId, $days = 30) {
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE(te.date) as date,
                SUM(te.hours) as daily_hours,
                COUNT(te.id) as entry_count,
                COUNT(DISTINCT te.project_id) as projects_worked
            FROM {$this->table} te
            WHERE te.user_id = :user_id
            AND te.date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(te.date)
            ORDER BY date DESC
        ");
        $stmt->execute(['user_id' => $userId, 'days' => $days]);
        return $stmt->fetchAll();
    }

    public function getProjectTimeBreakdown($projectId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                te.user_id,
                u.full_name,
                SUM(te.hours) as total_hours,
                SUM(te.hours * te.hourly_rate) as total_cost,
                COUNT(te.id) as entry_count,
                SUM(CASE WHEN te.is_billable = TRUE THEN te.hours ELSE 0 END) as billable_hours
            FROM {$this->table} te
            LEFT JOIN users u ON te.user_id = u.id
            WHERE te.project_id = :project_id
            GROUP BY te.user_id, u.full_name
            ORDER BY total_hours DESC
        ");
        $stmt->execute(['project_id' => $projectId]);
        return $stmt->fetchAll();
    }

    public function getTaskTimeBreakdown($taskId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                te.user_id,
                u.full_name,
                SUM(te.hours) as total_hours,
                SUM(te.hours * te.hourly_rate) as total_cost,
                COUNT(te.id) as entry_count
            FROM {$this->table} te
            LEFT JOIN users u ON te.user_id = u.id
            WHERE te.task_id = :task_id
            GROUP BY te.user_id, u.full_name
            ORDER BY total_hours DESC
        ");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll();
    }

    public function getUnbilledTime($projectId = null) {
        $sql = "
            SELECT te.*, p.title as project_title, u.full_name as user_name
            FROM {$this->table} te
            LEFT JOIN projects p ON te.project_id = p.id
            LEFT JOIN users u ON te.user_id = u.id
            WHERE te.is_billable = TRUE
            AND te.hourly_rate > 0
        ";
        
        $params = [];
        
        if ($projectId) {
            $sql .= " AND te.project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        $sql .= " ORDER BY te.date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getOvertimeEntries($projectId = null, $dateFrom = null, $dateTo = null) {
        $sql = "
            SELECT te.*, p.title as project_title, u.full_name as user_name
            FROM {$this->table} te
            LEFT JOIN projects p ON te.project_id = p.id
            LEFT JOIN users u ON te.user_id = u.id
            WHERE te.entry_type = 'overtime'
        ";
        
        $params = [];
        
        if ($projectId) {
            $sql .= " AND te.project_id = :project_id";
            $params['project_id'] = $projectId;
        }
        
        if ($dateFrom) {
            $sql .= " AND te.date >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND te.date <= :date_to";
            $params['date_to'] = $dateTo;
        }
        
        $sql .= " ORDER BY te.date DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function exportTimeEntries($filters = [], $format = 'csv') {
        $entries = [];
        
        if (!empty($filters['project_id'])) {
            $entries = $this->getProjectTimeEntries($filters['project_id'], $filters);
        } elseif (!empty($filters['user_id'])) {
            $entries = $this->getUserTimeEntries($filters['user_id'], $filters);
        } else {
            // Get all entries (with date limits for performance)
            $dateFrom = $filters['date_from'] ?? date('Y-m-01'); // First of current month
            $dateTo = $filters['date_to'] ?? date('Y-m-t'); // Last day of current month
            $entries = $this->getAllEntriesInRange($dateFrom, $dateTo);
        }
        
        switch ($format) {
            case 'csv':
                return $this->exportToCSV($entries);
            case 'json':
            default:
                return json_encode($entries, JSON_PRETTY_PRINT);
        }
    }

    private function getAllEntriesInRange($dateFrom, $dateTo) {
        $stmt = $this->pdo->prepare("
            SELECT te.*, 
                   p.title as project_title,
                   pt.title as task_title,
                   u.full_name as user_name
            FROM {$this->table} te
            LEFT JOIN projects p ON te.project_id = p.id
            LEFT JOIN project_tasks pt ON te.task_id = pt.id
            LEFT JOIN users u ON te.user_id = u.id
            WHERE te.date BETWEEN :date_from AND :date_to
            ORDER BY te.date DESC, te.created_at DESC
        ");
        $stmt->execute(['date_from' => $dateFrom, 'date_to' => $dateTo]);
        return $stmt->fetchAll();
    }

    private function exportToCSV($entries) {
        $csv = "Date,Project,Task,User,Description,Hours,Type,Rate,Billable,Created At\n";
        
        foreach ($entries as $entry) {
            $csv .= sprintf(
                "%s,\"%s\",\"%s\",\"%s\",\"%s\",%.2f,\"%s\",%.2f,\"%s\",%s\n",
                $entry['date'],
                str_replace('"', '""', $entry['project_title'] ?? ''),
                str_replace('"', '""', $entry['task_title'] ?? ''),
                str_replace('"', '""', $entry['user_name'] ?? ''),
                str_replace('"', '""', $entry['description'] ?? ''),
                $entry['hours'],
                $entry['entry_type'],
                $entry['hourly_rate'] ?? 0,
                $entry['is_billable'] ? 'Yes' : 'No',
                $entry['created_at']
            );
        }
        
        return $csv;
    }

    public function validateTimeEntry($data) {
        $errors = [];
        
        if (empty($data['project_id'])) {
            $errors[] = 'Project is required';
        }
        
        if (empty($data['hours']) || $data['hours'] <= 0) {
            $errors[] = 'Hours must be greater than 0';
        }
        
        if ($data['hours'] > 24) {
            $errors[] = 'Hours cannot exceed 24 for a single entry';
        }
        
        if (empty($data['date'])) {
            $errors[] = 'Date is required';
        }
        
        if (!strtotime($data['date'])) {
            $errors[] = 'Invalid date format';
        }
        
        if (!empty($data['date']) && strtotime($data['date']) > strtotime(date('Y-m-d'))) {
            $errors[] = 'Date cannot be in the future';
        }
        
        if (isset($data['hourly_rate']) && $data['hourly_rate'] < 0) {
            $errors[] = 'Hourly rate cannot be negative';
        }
        
        return $errors;
    }

    public function getRecentEntries($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT te.*, 
                   p.title as project_title,
                   pt.title as task_title,
                   u.full_name as user_name
            FROM {$this->table} te
            LEFT JOIN projects p ON te.project_id = p.id
            LEFT JOIN project_tasks pt ON te.task_id = pt.id
            LEFT JOIN users u ON te.user_id = u.id
            ORDER BY te.created_at DESC
            LIMIT :limit
        ");
        $stmt->execute(['limit' => $limit]);
        return $stmt->fetchAll();
    }

    public function searchEntries($query, $filters = []) {
        $sql = "
            SELECT te.*, 
                   p.title as project_title,
                   pt.title as task_title,
                   u.full_name as user_name
            FROM {$this->table} te
            LEFT JOIN projects p ON te.project_id = p.id
            LEFT JOIN project_tasks pt ON te.task_id = pt.id
            LEFT JOIN users u ON te.user_id = u.id
            WHERE (te.description LIKE :query OR p.title LIKE :query OR pt.title LIKE :query)
        ";
        
        $params = ['query' => '%' . $query . '%'];
        
        if (!empty($filters['project_id'])) {
            $sql .= " AND te.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }
        
        if (!empty($filters['user_id'])) {
            $sql .= " AND te.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        $sql .= " ORDER BY te.date DESC, te.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
