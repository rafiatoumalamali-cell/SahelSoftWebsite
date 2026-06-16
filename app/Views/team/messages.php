<?php include VIEW_PATH . '/layouts/header.php'; ?>

<style>
.messages-container {
    margin-top: 80px;
    height: calc(100vh - 80px);
    display: flex;
    background: #f8fafc;
}

.messages-sidebar {
    width: 350px;
    background: white;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.sidebar-header h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

.search-contacts {
    margin-top: 15px;
    position: relative;
}

.search-contacts input {
    width: 100%;
    padding: 10px 15px 10px 35px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #f1f5f9;
}

.search-contacts i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.contacts-list {
    flex: 1;
    overflow-y: auto;
}

.contacts-list .contact-item {
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid #f1f5f9;
}

.contacts-list .contact-item:hover {
    background: #f8fafc;
}

.contacts-list .contact-item.active {
    background: #eff6ff;
    border-right: 3px solid var(--primary-color);
}

.contact-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    flex-shrink: 0;
}

.contacts-list .contact-info {
    flex: 1;
    min-width: 0;
}

.contact-info h4 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-info p {
    margin: 2px 0 0 0;
    font-size: 0.8rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.unread-badge {
    background: #ef4444;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
    margin-left: 10px;
}

.chat-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: white;
}

.chat-header {
    padding: 15px 25px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-messages {
    flex: 1;
    padding: 25px;
    overflow-y: auto;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message-bubble {
    max-width: 70%;
    padding: 12px 18px;
    border-radius: 18px;
    font-size: 0.95rem;
    line-height: 1.5;
    position: relative;
}

.message-sender {
    font-size: 0.75rem;
    font-weight: 700;
    margin-bottom: 4px;
    color: var(--primary-color);
}

.message-sent {
    align-self: flex-end;
    background: var(--primary-color);
    color: white;
    border-bottom-right-radius: 4px;
}

.message-received {
    align-self: flex-start;
    background: white;
    color: var(--text-dark);
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.message-time {
    font-size: 0.7rem;
    margin-top: 5px;
    opacity: 0.7;
    display: block;
    text-align: right;
}

.chat-input-area {
    padding: 20px 25px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 15px;
    align-items: center;
}

.chat-input-wrapper {
    flex: 1;
    position: relative;
}

.chat-input-wrapper input {
    width: 100%;
    padding: 12px 20px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.chat-input-wrapper input:focus {
    outline: none;
    border-color: var(--primary-color);
}

.btn-send {
    background: var(--gradient-accent);
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-send:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(249, 115, 22, 0.3);
}

.empty-chat {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    text-align: center;
}

.empty-chat i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}
</style>

<div class="messages-container">
    <!-- Sidebar -->
    <aside class="messages-sidebar">
        <div class="sidebar-header">
            <h2>Contacts</h2>
            <div class="search-contacts">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search team members..." id="contactSearch">
            </div>
        </div>
        <div class="contacts-list" id="contactsList">
            <?php foreach ($contacts as $contact): 
                if ($contact['id'] == $_SESSION['user_id']) continue; ?>
                <div class="contact-item" data-id="<?= $contact['id'] ?>" onclick="selectContact(this)">
                    <div class="contact-avatar">
                        <?= strtoupper(substr($contact['full_name'], 0, 1)) ?>
                    </div>
                    <div class="contact-info">
                        <h4><?= htmlspecialchars($contact['full_name']) ?></h4>
                        <p><?= ucfirst($contact['role']) ?></p>
                    </div>
                    <?php if (isset($unreadCounts[$contact['id']]) && $unreadCounts[$contact['id']] > 0): ?>
                        <div class="unread-badge"><?= $unreadCounts[$contact['id']] ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- Main Chat Area -->
    <main class="chat-area" id="chatArea">
        <div class="empty-chat">
            <i class="fas fa-comments"></i>
            <h3>Select a conversation</h3>
            <p>Select a team member to start chatting.</p>
        </div>
    </main>
</div>

<script>
let currentReceiverId = null;
let chatInterval = null;

function selectContact(element) {
    // UI Update
    document.querySelectorAll('.contacts-list .contact-item').forEach(item => item.classList.remove('active'));
    element.classList.add('active');
    
    // Hide unread badge if exists
    const badge = element.querySelector('.unread-badge');
    if (badge) badge.style.display = 'none';
    
    const receiverId = element.getAttribute('data-id');
    const name = element.querySelector('h4').textContent;
    currentReceiverId = receiverId;

    // Initialize Chat Area
    const chatArea = document.getElementById('chatArea');
    chatArea.innerHTML = `
        <div class="chat-header">
            <div class="chat-user-info">
                <div class="contact-avatar" style="width: 40px; height: 40px; font-size: 0.9rem;">
                    ${name.charAt(0)}
                </div>
                <div>
                    <h3 style="margin:0; font-size: 1.1rem;">${name}</h3>
                    <span style="font-size: 0.75rem; color: #10b981;"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Online</span>
                </div>
            </div>
            <div class="chat-actions">
                <button class="action-btn" title="View Profile"><i class="fas fa-user"></i></button>
            </div>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="empty-chat"><p>Loading messages...</p></div>
        </div>
        <div class="chat-input-area">
            <div class="chat-input-wrapper">
                <input type="text" placeholder="Type a message..." id="messageInput">
            </div>
            <button class="btn-send" onclick="sendMessage()">
                <span>Send</span>
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    `;

    // Add Enter key listener
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    // Load messages
    loadMessages();
    
    // Auto-refresh
    if (chatInterval) clearInterval(chatInterval);
    chatInterval = setInterval(loadMessages, 3000);
}

function loadMessages() {
    if (!currentReceiverId) return;

    fetch(`<?= APP_URL ?>/api/messages/chat?receiver_id=${currentReceiverId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('chatMessages');
                const wasAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;
                
                if (data.messages.length === 0) {
                    container.innerHTML = '<div class="empty-chat"><p>No messages yet. Say hello!</p></div>';
                    return;
                }

                container.innerHTML = data.messages.map(m => `
                    <div class="message-bubble ${m.sender_id == <?= $_SESSION['user_id'] ?> ? 'message-sent' : 'message-received'}">
                        ${m.sender_id != <?= $_SESSION['user_id'] ?> ? `<div class="message-sender">${m.sender_name}</div>` : ''}
                        ${m.message}
                        <span class="message-time">${new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                    </div>
                `).join('');

                if (wasAtBottom) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        });
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !currentReceiverId) return;

    const formData = new FormData();
    formData.append('receiver_id', currentReceiverId);
    formData.append('message', message);

    input.value = '';

    fetch('<?= APP_URL ?>/api/messages/send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadMessages();
        }
    });
}

// Search functionality
document.getElementById('contactSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.contacts-list .contact-item').forEach(item => {
        const name = item.querySelector('h4').textContent.toLowerCase();
        item.style.display = name.includes(term) ? 'flex' : 'none';
    });
});

// Auto-select from URL
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('user_id');
    if (userId) {
        const contact = document.querySelector(`.contacts-list .contact-item[data-id="${userId}"]`);
        if (contact) selectContact(contact);
    }
});
</script>
