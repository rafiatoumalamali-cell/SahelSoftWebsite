<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.tasks-container {
    margin-top: 80px;
    padding: 30px 0;
    background: var(--bg-light);
    min-height: 100vh;
}

.tasks-header {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 30px;
    border-left: 5px solid var(--accent-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.tasks-table-container {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.tasks-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.tasks-table th {
    background: var(--bg-light);
    padding: 15px 20px;
    text-align: left;
    font-weight: 700;
    color: var(--text-dark);
}

.tasks-table td {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    word-break: break-word;
}
</style>

<div class="tasks-container">
    <div class="container">
        <div class="tasks-header">
            <div>
                <h1>My Tasks</h1>
                <p>Manage your assigned tasks and update progress.</p>
            </div>
            <?php if (isset($projectId)): ?>
                <a href="<?= APP_URL ?>/team/project/view?id=<?= $projectId ?>" class="btn btn-primary">Back to Project</a>
            <?php endif; ?>
        </div>

        <div class="tasks-table-container">
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Project</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-light);">
                                No tasks found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($task['title']) ?></strong>
                                    <div style="font-size: 0.85rem; color: var(--text-light);"><?= htmlspecialchars(substr($task['description'], 0, 50)) ?>...</div>
                                </td>
                                <td><?= htmlspecialchars($task['project_title'] ?? 'N/A') ?></td>
                                <td><?= $task['due_date'] ? date('d M Y', strtotime($task['due_date'])) : 'No date' ?></td>
                                <td>
                                    <span class="status-badge status-<?= $task['status'] ?>">
                                        <?= ucfirst($task['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= APP_URL ?>/team/tasks/update-status" method="POST" style="display: flex; gap: 5px;">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <select name="status" onchange="this.form.submit()" class="filter-select" style="min-width: 120px; padding: 5px;">
                                            <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
