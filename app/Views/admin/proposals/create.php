<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.proposal-form-container {
    margin-top: 80px;
    padding: 20px 0;
    min-height: 100vh;
    background: var(--bg-light);
}

.form-header {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    border-left: 5px solid var(--primary-color);
}

.proposal-form {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-dark);
}

.form-group .required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-help {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 5px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-block;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-secondary {
    background: #6b7280;
    color: white;
    margin-left: 10px;
}

.btn-secondary:hover {
    background: #4b5563;
}

.contact-info {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid var(--primary-color);
}

.contact-info h4 {
    margin: 0 0 10px 0;
    color: var(--text-dark);
}

.contact-info p {
    margin: 5px 0;
    color: var(--text-muted);
}
</style>

<div class="proposal-form-container">
    <div class="container">
        <div class="form-header">
            <h1>Create Proposal</h1>
            <p>Create a new project proposal for a client</p>
        </div>

        <div class="proposal-form">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if ($selectedContact): ?>
                <div class="contact-info">
                    <h4>Selected Contact</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($selectedContact['name']) ?></p>
                    <p><strong>Organization:</strong> <?= htmlspecialchars($selectedContact['organization'] ?? 'N/A') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($selectedContact['email']) ?></p>
                    <p><strong>Project Type:</strong> <?= htmlspecialchars($selectedContact['project_type'] ?? 'N/A') ?></p>
                    <p><strong>Budget:</strong> <?= htmlspecialchars($selectedContact['budget'] ?? 'N/A') ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/admin/proposals/create">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_id">Contact Inquiry <span class="required">*</span></label>
                        <select name="contact_id" id="contact_id" class="form-control" required>
                            <option value="">Select a contact inquiry</option>
                            <?php foreach ($contacts as $contact): ?>
                                <option value="<?= $contact['id'] ?>" <?= ($selectedContact && $contact['id'] == $selectedContact['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($contact['name']) ?> - <?= htmlspecialchars($contact['project_type'] ?? 'General') ?>
                                    <?php if (!empty($contact['budget'])): ?>
                                        (Budget: <?= htmlspecialchars($contact['budget']) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help">Select the contact inquiry this proposal is for</div>
                    </div>

                    <div class="form-group">
                        <label for="client_id">Client <span class="required">*</span></label>
                        <select name="client_id" id="client_id" class="form-control" required>
                            <option value="">Select a client</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client['id'] ?>">
                                    <?= htmlspecialchars($client['full_name']) ?>
                                    <?php if (!empty($client['company_name'])): ?>
                                        (<?= htmlspecialchars($client['company_name']) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help">Select the client to send this proposal to</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title">Proposal Title <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" required
                           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                           placeholder="e.g., E-commerce Website Development">
                    <div class="form-help">A clear, descriptive title for the proposal</div>
                </div>

                <div class="form-group">
                    <label for="description">Project Description</label>
                    <textarea name="description" id="description" class="form-control"
                              placeholder="Describe the project scope, deliverables, and approach..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <div class="form-help">Detailed description of what will be delivered</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="total_amount">Total Amount (₦) <span class="required">*</span></label>
                        <input type="number" name="total_amount" id="total_amount" class="form-control" required
                               value="<?= htmlspecialchars($_POST['total_amount'] ?? '') ?>"
                               placeholder="500000" step="0.01" min="0">
                        <div class="form-help">Total project cost in Nigerian Naira</div>
                    </div>

                    <div class="form-group">
                        <label for="deposit_amount">Deposit Amount (₦)</label>
                        <input type="number" name="deposit_amount" id="deposit_amount" class="form-control"
                               value="<?= htmlspecialchars($_POST['deposit_amount'] ?? '') ?>"
                               placeholder="100000" step="0.01" min="0">
                        <div class="form-help">Required deposit to start the project</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="timeline_weeks">Timeline (weeks) <span class="required">*</span></label>
                    <input type="number" name="timeline_weeks" id="timeline_weeks" class="form-control" required
                           value="<?= htmlspecialchars($_POST['timeline_weeks'] ?? '') ?>"
                           placeholder="8" min="1">
                    <div class="form-help">Estimated project duration in weeks</div>
                </div>

                <div class="form-group">
                    <label for="admin_notes">Internal Notes</label>
                    <textarea name="admin_notes" id="admin_notes" class="form-control"
                              placeholder="Internal notes for the team..."><?= htmlspecialchars($_POST['admin_notes'] ?? '') ?></textarea>
                    <div class="form-help">Notes visible only to the internal team</div>
                </div>

                <div style="margin-top: 30px;">
                    <a href="<?= APP_URL ?>/admin/proposals/create" class="btn btn-primary">Create Proposal</a>
                    <a href="<?= APP_URL ?>/admin/proposals" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-calculate deposit percentage
document.getElementById('total_amount').addEventListener('input', function() {
    const total = parseFloat(this.value) || 0;
    const deposit = parseFloat(document.getElementById('deposit_amount').value) || 0;
    
    if (total > 0) {
        const percentage = ((deposit / total) * 100).toFixed(1);
        console.log(`Deposit is ${percentage}% of total`);
    }
});

document.getElementById('deposit_amount').addEventListener('input', function() {
    const total = parseFloat(document.getElementById('total_amount').value) || 0;
    const deposit = parseFloat(this.value) || 0;
    
    if (total > 0) {
        const percentage = ((deposit / total) * 100).toFixed(1);
        console.log(`Deposit is ${percentage}% of total`);
    }
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
