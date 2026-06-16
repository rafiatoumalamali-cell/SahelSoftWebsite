<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.manage-project { margin-top: 80px; padding: 40px 0; background: var(--bg-light); min-height: 100vh; }
.manage-card { background: white; padding: 30px; border-radius: 12px; box-shadow: var(--shadow-md); margin-bottom: 30px; }
.manage-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-color); text-decoration: none; font-weight: 500; margin-bottom: 20px; transition: color 0.3s; }
.btn-back:hover { color: var(--primary-color); }
.status-pill { padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
.status-completed { background: #d1fae5; color: #065f46; }
.status-in_progress { background: #dbeafe; color: #1e40af; }
.status-pending { background: #fef3c7; color: #92400e; }
.manage-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
@media (max-width: 992px) { .manage-grid { grid-template-columns: 1fr; } }
.table-container { margin-top: 20px; }
.action-group { display: flex; gap: 10px; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal.active { display: flex; }
.modal-content { background: white; padding: 30px; border-radius: 12px; width: 100%; max-width: 500px; position: relative; }
</style>

<div class="manage-project">
    <div class="container">
        <a href="<?= APP_URL ?>/admin/dashboard#projects" class="btn-back">← Back to Projects</a>
        
        <div class="manage-header">
            <div>
                <h1>Manage Project: <?= htmlspecialchars($project['title']) ?></h1>
                <p style="color: var(--text-light);">Directly control tasks, payments, and progress before the client sees it.</p>
            </div>
            <div style="text-align: right; display: flex; align-items: center; gap: 20px;">
                <form method="POST" action="<?= APP_URL ?>/admin/project/update-progress" style="display: flex; align-items: center; gap: 10px; background: white; padding: 10px 15px; border-radius: 8px; box-shadow: var(--shadow-sm);">
                    <input type="hidden" name="id" value="<?= $project['id'] ?>">
                    <div style="font-size: 0.85rem; font-weight: 600;">Progress:</div>
                    <input type="number" name="progress" value="<?= $project['progress'] ?? 0 ?>" min="0" max="100" style="width: 70px; padding: 5px; border: 1px solid var(--border-color); border-radius: 4px;">
                    <button type="submit" class="btn-primary" style="padding: 5px 10px; font-size: 0.8rem;">Update</button>
                </form>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color);"><?= $project['progress'] ?? 0 ?>%</div>
                    <div style="font-size: 0.85rem; color: var(--text-light);">Current Status</div>
                </div>
            </div>
        </div>

        <div class="manage-grid">
            <!-- Tasks Management -->
            <div class="manage-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>📋 Project Tasks</h3>
                    <button class="btn-primary" onclick="openTaskModal()" style="padding: 8px 16px; font-size: 0.9rem;">+ Add Task</button>
                </div>
                
                <div class="table-container">
                    <table class="data-table" style="min-width: 100%;">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Assignee</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($tasks)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--text-light);">No tasks yet.</td></tr>
                            <?php else: ?>
                                <?php foreach($tasks as $task): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                    <td><span class="status-pill status-<?= $task['status'] ?>"><?= ucfirst(str_replace('_', ' ', $task['status'])) ?></span></td>
                                    <td><?= $task['assigned_to'] ? 'Staff #'.$task['assigned_to'] : 'Unassigned' ?></td>
                                    <td>
                                        <div class="action-group">
                                            <button class="action-btn edit" onclick='editTask(<?= json_encode($task, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>✏️</button>
                                            <form method="POST" action="<?= APP_URL ?>/admin/tasks/delete" onsubmit="return confirm('Delete task?');">
                                                <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                                <button type="submit" class="action-btn delete">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments Management -->
            <div class="manage-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>💰 Budget & Payments</h3>
                    <button class="btn-primary" onclick="openPaymentModal()" style="padding: 8px 16px; font-size: 0.9rem; background: #10b981;">+ Record Payment</button>
                </div>
                
                <div class="table-container">
                    <table class="data-table" style="min-width: 100%;">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Proof</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($payments)): ?>
                                <tr><td colspan="4" style="text-align: center; color: var(--text-light);">No payments recorded.</td></tr>
                            <?php else: ?>
                                <?php foreach($payments as $pmt): ?>
                                <tr>
                                    <td style="font-weight: bold;"><?= number_format($pmt['amount']) ?> CFA</td>
                                    <td><?= htmlspecialchars($pmt['description']) ?></td>
                                    <td><span class="status-pill status-<?= $pmt['status'] ?>"><?= ucfirst($pmt['status']) ?></span></td>
                                    <td>
                                        <?php if(!empty($pmt['proof_file'])): ?>
                                            <a href="<?= APP_URL ?>/<?= $pmt['proof_file'] ?>" target="_blank" title="View Receipt" style="text-decoration: none;">📎 View</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <button class="action-btn edit" onclick='editPayment(<?= json_encode($pmt, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' title="Edit/Verify">✏️</button>
                                            <form method="POST" action="<?= APP_URL ?>/admin/payments/delete" onsubmit="return confirm('Delete payment record?');">
                                                <input type="hidden" name="id" value="<?= $pmt['id'] ?>">
                                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                                <button type="submit" class="action-btn delete">🗑️</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div id="taskModal" class="modal">
    <div class="modal-content">
        <h3 id="taskModalTitle">Add Project Task</h3>
        <form id="taskForm" method="POST" action="<?= APP_URL ?>/admin/tasks/create">
            <input type="hidden" name="id" id="taskId">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            
            <div class="form-group" style="margin-top: 15px;">
                <label>Task Title</label>
                <input type="text" name="title" id="taskTitle" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="taskDescription" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="taskStatus" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="form-group">
                <label>Assign to Team Member</label>
                <select name="assigned_to" id="taskAssigned" class="form-control">
                    <option value="">Unassigned</option>
                    <?php foreach($team as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['full_name']) ?> (<?= ucfirst($m['role']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" id="taskDueDate" class="form-control">
            </div>
            
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn-primary" style="flex: 1;">Save Task</button>
                <button type="button" class="btn-secondary" onclick="closeModals()" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <h3 id="paymentModalTitle">Record Payment</h3>
        <form id="paymentForm" method="POST" action="<?= APP_URL ?>/admin/payments/create">
            <input type="hidden" name="id" id="paymentId">
            <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
            
            <div class="form-group" style="margin-top: 15px;">
                <label>Amount (CFA)</label>
                <input type="number" name="amount" id="paymentAmount" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" id="paymentDescription" class="form-control" placeholder="e.g. Initial Deposit, Milestone 1">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="paymentStatus" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn-primary" style="flex: 1; background: #10b981;">Save Payment</button>
                <button type="button" class="btn-secondary" onclick="closeModals()" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeModals() {
    document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
}

function openTaskModal() {
    document.getElementById('taskForm').reset();
    document.getElementById('taskForm').action = '<?= APP_URL ?>/admin/tasks/create';
    document.getElementById('taskModalTitle').innerText = 'Add Project Task';
    document.getElementById('taskModal').classList.add('active');
}

function editTask(task) {
    openTaskModal();
    document.getElementById('taskForm').action = '<?= APP_URL ?>/admin/tasks/update';
    document.getElementById('taskModalTitle').innerText = 'Edit Task';
    document.getElementById('taskId').value = task.id;
    document.getElementById('taskTitle').value = task.title;
    document.getElementById('taskDescription').value = task.description;
    document.getElementById('taskStatus').value = task.status;
    document.getElementById('taskAssigned').value = task.assigned_to || '';
    document.getElementById('taskDueDate').value = task.due_date || '';
}

function openPaymentModal() {
    document.getElementById('paymentForm').reset();
    document.getElementById('paymentForm').action = '<?= APP_URL ?>/admin/payments/create';
    document.getElementById('paymentModalTitle').innerText = 'Record Payment';
    document.getElementById('paymentModal').classList.add('active');
}

function editPayment(pmt) {
    openPaymentModal();
    document.getElementById('paymentForm').action = '<?= APP_URL ?>/admin/payments/update';
    document.getElementById('paymentModalTitle').innerText = 'Verify/Edit Payment';
    document.getElementById('paymentId').value = pmt.id;
    document.getElementById('paymentAmount').value = pmt.amount;
    document.getElementById('paymentDescription').value = pmt.description;
    document.getElementById('paymentStatus').value = pmt.status;
}
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
