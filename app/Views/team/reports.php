<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.reports-container {
    margin-top: 80px;
    padding: 30px 0;
    background: var(--bg-light);
    min-height: 100vh;
}

.reports-header {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 30px;
    border-left: 5px solid #3b82f6;
}

.report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.report-card {
    background: white;
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
}
</style>

<div class="reports-container">
    <div class="container">
        <div class="reports-header">
            <h1>Team Reports & Analytics</h1>
            <p>Monitor project performance and team productivity.</p>
        </div>

        <div class="report-grid">
            <div class="report-card">
                <h3>Project Status Distribution</h3>
                <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: var(--bg-light); border-radius: 10px; margin-top: 20px;">
                    <p style="color: var(--text-light);">Chart Placeholder</p>
                </div>
            </div>

            <div class="report-card">
                <h3>Team Workload</h3>
                <div style="height: 200px; display: flex; align-items: center; justify-content: center; background: var(--bg-light); border-radius: 10px; margin-top: 20px;">
                    <p style="color: var(--text-light);">Chart Placeholder</p>
                </div>
            </div>
        </div>

        <div class="view-card" style="margin-top: 30px; background: white; padding: 25px; border-radius: var(--border-radius);">
            <h3>Project Summary</h3>
            <table class="tasks-table" style="width: 100%; margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Progress</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= $p['progress'] ?? 0 ?>%</td>
                            <td><?= ucfirst($p['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
