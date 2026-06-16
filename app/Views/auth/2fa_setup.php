<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.2fa-container {
    max-width: 600px;
    margin: 80px auto 40px;
    padding: 40px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-left: 5px solid var(--primary-color);
}

.2fa-header {
    text-align: center;
    margin-bottom: 40px;
}

.2fa-header h1 {
    color: var(--text-dark);
    margin-bottom: 10px;
}

.2fa-header p {
    color: var(--text-muted);
    font-size: 1.1rem;
}

.2fa-steps {
    display: grid;
    gap: 30px;
    margin-bottom: 40px;
}

.step {
    padding: 25px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.step h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.step-number {
    background: var(--primary-color);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

.qr-code {
    text-align: center;
    margin: 20px 0;
}

.qr-code img {
    border: 2px solid var(--border-color);
    border-radius: 8px;
    padding: 10px;
    background: white;
}

.secret-key {
    background: #f3f4f6;
    padding: 15px;
    border-radius: 8px;
    font-family: monospace;
    font-size: 1.1rem;
    text-align: center;
    margin: 15px 0;
    border: 2px dashed var(--border-color);
}

.backup-codes {
    background: #fef3c7;
    padding: 20px;
    border-radius: 8px;
    border: 2px solid #f59e0b;
}

.backup-codes h4 {
    color: #d97706;
    margin-bottom: 15px;
}

.backup-codes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
}

.backup-code {
    background: white;
    padding: 10px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.9rem;
    text-align: center;
    border: 1px solid #f59e0b;
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

.app-links {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 20px 0;
}

.app-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-dark);
    transition: all 0.3s;
}

.app-link:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
}

.app-link img {
    width: 24px;
    height: 24px;
}

@media (max-width: 768px) {
    .2fa-container {
        margin: 20px 10px;
        padding: 20px;
    }
    
    .backup-codes-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .app-links {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<div class="2fa-container">
    <div class="2fa-header">
        <h1>🔐 Enable Two-Factor Authentication</h1>
        <p>Add an extra layer of security to your account</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="2fa-steps">
        <div class="step">
            <h3>
                <span class="step-number">1</span>
                Install an Authenticator App
            </h3>
            <p>Download and install one of these authenticator apps on your smartphone:</p>
            <div class="app-links">
                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="app-link">
                    <span>📱</span>
                    Google Authenticator
                </a>
                <a href="https://authy.com/" target="_blank" class="app-link">
                    <span>🔑</span>
                    Authy
                </a>
                <a href="https://microsoft.com/en-us/account/authenticator/" target="_blank" class="app-link">
                    <span>🪟</span>
                    Microsoft Authenticator
                </a>
            </div>
        </div>

        <div class="step">
            <h3>
                <span class="step-number">2</span>
                Scan the QR Code
            </h3>
            <p>Open your authenticator app and scan the QR code below:</p>
            <div class="qr-code">
                <?php if ($qrCodeUrl): ?>
                    <img src="<?= htmlspecialchars($qrCodeUrl) ?>" alt="QR Code for 2FA Setup" loading="lazy">
                <?php else: ?>
                    <p style="color: #ef4444;">QR code generation failed. Please try again.</p>
                <?php endif; ?>
            </div>
            <div class="secret-key">
                <strong>Manual Entry Key:</strong><br>
                <?= htmlspecialchars($secretKey) ?>
            </div>
        </div>

        <div class="step">
            <h3>
                <span class="step-number">3</span>
                Save Your Backup Codes
            </h3>
            <div class="alert alert-warning">
                <strong>⚠️ Important:</strong> Save these backup codes in a secure location. You can use them if you lose access to your authenticator app.
            </div>
            <div class="backup-codes-grid">
                <?php foreach ($backupCodes as $code): ?>
                    <div class="backup-code"><?= htmlspecialchars($code) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= APP_URL ?>/2fa/enable">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="form-group">
            <label for="code">Enter Verification Code</label>
            <input type="text" name="code" id="code" class="form-control" 
                   placeholder="Enter 6-digit code from your authenticator app"
                   required maxlength="6" pattern="[0-9]{6}">
            <small style="color: var(--text-muted);">Enter the 6-digit code shown in your authenticator app</small>
        </div>

        <div style="text-align: center;">
            <button type="submit" class="btn btn-primary">Enable 2FA</button>
            <a href="<?= APP_URL ?>/profile" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('code').addEventListener('input', function(e) {
    // Auto-move to next field or submit when 6 digits entered
    if (e.target.value.length === 6) {
        e.target.blur();
    }
});

// Copy secret key to clipboard
document.querySelector('.secret-key').addEventListener('click', function() {
    const text = this.textContent.replace('Manual Entry Key:', '').trim();
    navigator.clipboard.writeText(text).then(() => {
        const original = this.innerHTML;
        this.innerHTML = '<strong>✅ Copied to clipboard!</strong>';
        setTimeout(() => {
            this.innerHTML = original;
        }, 2000);
    });
});
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
