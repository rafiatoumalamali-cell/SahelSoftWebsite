<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.2fa-container {
    max-width: 500px;
    margin: 80px auto 40px;
    padding: 40px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-left: 5px solid var(--primary-color);
}

.2fa-header {
    text-align: center;
    margin-bottom: 30px;
}

.2fa-header h1 {
    color: var(--text-dark);
    margin-bottom: 10px;
}

.2fa-header p {
    color: var(--text-muted);
    font-size: 1.1rem;
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
    text-align: center;
    font-family: monospace;
    font-size: 1.2rem;
    letter-spacing: 2px;
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

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.backup-option {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
    border-left: 4px solid var(--primary-color);
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 15px 0;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

.code-input-group {
    position: relative;
}

.code-input-group .form-control {
    padding-right: 40px;
}

.clear-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    font-size: 1.2rem;
}

.clear-btn:hover {
    color: var(--text-dark);
}

.timer {
    text-align: center;
    margin: 20px 0;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.timer.warning {
    color: #f59e0b;
}

.timer.danger {
    color: #ef4444;
}

@media (max-width: 768px) {
    .2fa-container {
        margin: 20px 10px;
        padding: 20px;
    }
}
</style>

<div class="2fa-container">
    <div class="2fa-header">
        <h1>🔐 Two-Factor Authentication</h1>
        <p>Enter your verification code to complete login</p>
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

    <form method="POST" action="<?= APP_URL ?>/2fa/verify" id="2fa-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="form-group">
            <label for="code">Verification Code</label>
            <div class="code-input-group">
                <input type="text" name="code" id="code" class="form-control" 
                       placeholder="000000"
                       required maxlength="6" pattern="[0-9]{6}"
                       autocomplete="one-time-code">
                <button type="button" class="clear-btn" onclick="clearCode()">×</button>
            </div>
            <small style="color: var(--text-muted);">Enter the 6-digit code from your authenticator app</small>
        </div>

        <div class="checkbox-group">
            <input type="checkbox" name="use_backup" id="use_backup">
            <label for="use_backup">Use backup code instead</label>
        </div>

        <div class="backup-option" id="backup-option" style="display: none;">
            <p><strong>Using Backup Code:</strong></p>
            <p>Enter one of your 8-digit backup codes if you can't access your authenticator app.</p>
        </div>

        <div class="timer" id="timer">
            Code refreshes in <span id="countdown">30</span> seconds
        </div>

        <div style="text-align: center;">
            <button type="submit" class="btn btn-primary">Verify</button>
            <a href="<?= APP_URL ?>/login" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center;">
        <p style="color: var(--text-muted); font-size: 0.9rem;">
            Having trouble? 
            <a href="<?= APP_URL ?>/contact" style="color: var(--primary-color);">Contact Support</a>
        </p>
    </div>
</div>

<script>
let countdown = 30;
const countdownElement = document.getElementById('countdown');
const timerElement = document.getElementById('timer');
const codeInput = document.getElementById('code');
const useBackupCheckbox = document.getElementById('use_backup');
const backupOption = document.getElementById('backup-option');

// Countdown timer
function updateTimer() {
    countdown--;
    countdownElement.textContent = countdown;
    
    if (countdown <= 10) {
        timerElement.classList.add('danger');
    } else if (countdown <= 20) {
        timerElement.classList.add('warning');
    }
    
    if (countdown <= 0) {
        countdown = 30;
        timerElement.classList.remove('warning', 'danger');
    }
}

setInterval(updateTimer, 1000);

// Handle backup code checkbox
useBackupCheckbox.addEventListener('change', function() {
    if (this.checked) {
        codeInput.placeholder = 'Enter 8-digit backup code';
        codeInput.maxLength = 8;
        codeInput.pattern = '[0-9]{8}';
        backupOption.style.display = 'block';
    } else {
        codeInput.placeholder = '000000';
        codeInput.maxLength = 6;
        codeInput.pattern = '[0-9]{6}';
        backupOption.style.display = 'none';
    }
});

// Auto-focus and move to next
codeInput.addEventListener('input', function(e) {
    const maxLength = useBackupCheckbox.checked ? 8 : 6;
    
    if (e.target.value.length === maxLength) {
        e.target.blur();
        // Auto-submit after a short delay
        setTimeout(() => {
            document.getElementById('2fa-form').submit();
        }, 100);
    }
});

// Clear code function
function clearCode() {
    codeInput.value = '';
    codeInput.focus();
}

// Auto-focus on load
window.addEventListener('load', function() {
    codeInput.focus();
});

// Prevent form submission on Enter if code is incomplete
codeInput.addEventListener('keypress', function(e) {
    const maxLength = useBackupCheckbox.checked ? 8 : 6;
    
    if (e.key === 'Enter' && e.target.value.length < maxLength) {
        e.preventDefault();
    }
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
