<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.backup-codes-container {
    max-width: 600px;
    margin: 80px auto 40px;
    padding: 40px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-left: 5px solid var(--primary-color);
}

.backup-codes-header {
    text-align: center;
    margin-bottom: 30px;
}

.backup-codes-header h1 {
    color: var(--text-dark);
    margin-bottom: 10px;
}

.backup-codes-header p {
    color: var(--text-muted);
    font-size: 1.1rem;
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

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
}

.backup-codes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin: 30px 0;
}

.backup-code {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    font-family: monospace;
    font-size: 1.1rem;
    text-align: center;
    border: 2px solid var(--border-color);
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}

.backup-code:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.backup-code.copied {
    background: #d1fae5;
    border-color: #10b981;
}

.backup-code.copied::after {
    content: '✓ Copied!';
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    background: #10b981;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-family: sans-serif;
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

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.instructions {
    background: #f8fafc;
    padding: 25px;
    border-radius: 8px;
    margin: 30px 0;
    border-left: 4px solid var(--primary-color);
}

.instructions h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
}

.instructions ol {
    margin: 0;
    padding-left: 20px;
    color: var(--text-dark);
    line-height: 1.8;
}

.instructions li {
    margin-bottom: 10px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.stat-card {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid var(--border-color);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-top: 5px;
}

@media (max-width: 768px) {
    .backup-codes-container {
        margin: 20px 10px;
        padding: 20px;
    }
    
    .backup-codes-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stats {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="backup-codes-container">
    <div class="backup-codes-header">
        <h1>🔑 2FA Backup Codes</h1>
        <p>Save these codes for emergency access to your account</p>
    </div>

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

    <div class="alert alert-warning">
        <strong>⚠️ Important Security Notice:</strong>
        <ul style="margin: 10px 0 0 20px;">
            <li>Each backup code can only be used once</li>
            <li>Store these codes in a secure location (password manager, safe, etc.)</li>
            <li>Don't store them on your computer or phone</li>
            <li>Print them out and keep them in a safe place</li>
            <li>Regenerate codes if you suspect they've been compromised</li>
        </ul>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?= count($backupCodes) ?></div>
            <div class="stat-label">Total Backup Codes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">1</div>
            <div class="stat-label">Use Per Code</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">∞</div>
            <div class="stat-label">Regenerations</div>
        </div>
    </div>

    <div class="backup-codes-grid">
        <?php foreach ($backupCodes as $index => $code): ?>
            <div class="backup-code" onclick="copyCode(this, '<?= htmlspecialchars($code) ?>')" title="Click to copy">
                <?= htmlspecialchars($code) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="instructions">
        <h3>How to Use Backup Codes</h3>
        <ol>
            <li>When prompted for 2FA verification, check "Use backup code instead"</li>
            <li>Enter any unused backup code from the list above</li>
            <li>The code will be marked as used and cannot be reused</li>
            <li>Regenerate new codes when you have fewer than 3 codes remaining</li>
            <li>Keep these codes accessible but secure</li>
        </ol>
    </div>

    <form method="POST" action="<?= APP_URL ?>/2fa/regenerate-backup-codes" id="regenerate-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="form-group">
            <label for="password">Confirm Password</label>
            <input type="password" name="password" id="password" class="form-control" 
                   placeholder="Enter your password to regenerate codes" required>
            <small style="color: var(--text-muted);">Enter your password to generate new backup codes</small>
        </div>

        <div style="text-align: center;">
            <button type="submit" class="btn btn-warning">🔄 Regenerate Codes</button>
            <a href="<?= APP_URL ?>/profile" class="btn btn-secondary">Back to Profile</a>
        </div>
    </form>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            <strong>Pro Tip:</strong> Take a screenshot of these codes and save them in an encrypted folder or password manager.
        </p>
    </div>
</div>

<script>
function copyCode(element, code) {
    navigator.clipboard.writeText(code).then(() => {
        // Visual feedback
        element.classList.add('copied');
        
        // Remove feedback after 2 seconds
        setTimeout(() => {
            element.classList.remove('copied');
        }, 2000);
        
        // Show success message
        showNotification('Code copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy code:', err);
        showNotification('Failed to copy code', 'error');
    });
}

function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification alert alert-${type}`;
    notification.textContent = message;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 1000; min-width: 300px;';
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Print functionality
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printBackupCodes();
    }
});

function printBackupCodes() {
    const codes = Array.from(document.querySelectorAll('.backup-code')).map(el => el.textContent.trim());
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>SahelSoft 2FA Backup Codes</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h1 { color: #0f766e; }
                .codes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 20px 0; }
                .code { background: #f8fafc; padding: 10px; border: 1px solid #e5e7eb; text-align: center; font-family: monospace; }
                .warning { background: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <h1>SahelSoft 2FA Backup Codes</h1>
            <p>Generated on: ${new Date().toLocaleString()}</p>
            <div class="warning">
                <strong>Important:</strong> Keep these codes in a secure location. Each code can only be used once.
            </div>
            <div class="codes">
                ${codes.map(code => `<div class="code">${code}</div>`).join('')}
            </div>
            <p><small>Store these codes in a safe place like a password manager or physical safe.</small></p>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Add print button hint
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.backup-codes-container');
    const hint = document.createElement('div');
    hint.style.cssText = 'text-align: center; margin-top: 20px; color: var(--text-muted); font-size: 0.9rem;';
    hint.innerHTML = '<strong>Tip:</strong> Press <kbd>Ctrl+P</kbd> to print these codes';
    container.appendChild(hint);
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
