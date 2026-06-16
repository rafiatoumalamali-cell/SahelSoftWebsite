<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.notifications-page {
    padding: 100px 0;
    background: #f8fafc;
    min-height: calc(100vh - 300px);
}

.notifications-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.06);
    overflow: hidden;
    max-width: 900px;
    margin: 0 auto;
}

.card-header {
    padding: 25px 35px;
    background: white;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    margin: 0;
    color: var(--text-dark);
    font-size: 1.5rem;
}

.notification-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.notification-item {
    padding: 20px 35px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 20px;
    transition: all 0.3s ease;
    text-decoration: none;
    position: relative;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background: #f0f9ff;
}

.notification-item.unread::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary-color);
}

.notification-item:hover {
    background: #f8fafc;
}

.notification-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.icon-info { background: #e0f2fe; color: #0284c7; }
.icon-success { background: #dcfce7; color: #16a34a; }
.icon-warning { background: #fef3c7; color: #d97706; }
.icon-error { background: #fee2e2; color: #dc2626; }

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 5px;
    display: block;
}

.notification-message {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 8px;
}

.notification-time {
    font-size: 0.8rem;
    color: #94a3b8;
}

.btn-mark-all {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    background: #f1f5f9;
    color: #64748b;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-mark-all:hover {
    background: #e2e8f0;
    color: var(--text-dark);
}

.empty-state {
    padding: 80px 40px;
    text-align: center;
    color: #94a3b8;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    display: block;
    opacity: 0.3;
}
</style>

<div class="notifications-page">
    <div class="container">
        <div class="notifications-card animate-up">
            <div class="card-header">
                <h2><?= __('my_notifications') ?></h2>
                <div class="header-actions">
                    <?php if (!empty($notifications)): ?>
                        <button class="btn-mark-all" onclick="markAllRead()" style="margin-right: 10px;">
                            <i class="fas fa-check-double"></i> <?= __('mark_all_read') ?>
                        </button>
                        <button class="btn-mark-all" onclick="deleteRead()" title="<?= __('clear_read_desc') ?>">
                            <i class="fas fa-trash-alt"></i> <?= __('clear_read') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="notification-list" id="notificationsList">
                <?php if (empty($notifications)): ?>
                    <div class="empty-state">
                        <i class="far fa-bell-slash"></i>
                        <p><?= __('no_notifications') ?></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $n): ?>
                        <a href="<?= $n['link'] ? htmlspecialchars($n['link']) : '#' ?>" 
                           class="notification-item <?= !$n['is_read'] ? 'unread' : '' ?>"
                           onclick="markRead(<?= $n['id'] ?>, event, '<?= $n['link'] ?>')">
                            
                            <div class="notification-icon icon-<?= $n['type'] ?>">
                                <?php 
                                    $icon = 'info-circle';
                                    if($n['type'] == 'success') $icon = 'check-circle';
                                    if($n['type'] == 'warning') $icon = 'exclamation-triangle';
                                    if($n['type'] == 'error') $icon = 'times-circle';
                                ?>
                                <i class="fas fa-<?= $icon ?>"></i>
                            </div>

                            <div class="notification-content">
                                <span class="notification-title"><?= htmlspecialchars($n['title']) ?></span>
                                <div class="notification-message"><?= htmlspecialchars($n['message']) ?></div>
                                <span class="notification-time">
                                    <i class="far fa-clock"></i> <?= time_elapsed_string($n['created_at']) ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function markRead(id, event, link) {
    // If it's already read, just follow the link
    const item = event.currentTarget;
    if (!item.classList.contains('unread')) return;

    event.preventDefault();
    
    fetch('<?= APP_URL ?>/admin/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            item.classList.remove('unread');
            updateUnreadCount();
            if (link && link !== '#') {
                window.location.href = link;
            }
        }
    });
}

function markAllRead() {
    fetch('<?= APP_URL ?>/admin/notifications/mark-all-read', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('unread');
            });
            updateUnreadCount();
        }
    });
}

function deleteRead() {
    if (!confirm('<?= __('confirm_clear_read') ?>')) return;

    fetch('<?= APP_URL ?>/admin/notifications/delete-read', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}

function updateUnreadCount() {
    // Update the counter in the header if it exists
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        fetch('<?= APP_URL ?>/admin/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            });
    }
}
</script>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
