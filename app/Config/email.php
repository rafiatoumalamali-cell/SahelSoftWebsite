<?php

// Email Configuration
return [
    // SMTP Settings
    'smtp' => [
        'host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
        'port' => $_ENV['SMTP_PORT'] ?? 587,
        'username' => $_ENV['SMTP_USERNAME'] ?? 'your-email@gmail.com',
        'password' => $_ENV['SMTP_PASSWORD'] ?? 'your-app-password',
        'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
        'from_email' => $_ENV['FROM_EMAIL'] ?? 'sahelsoft38@gmail.com',
        'from_name' => $_ENV['FROM_NAME'] ?? 'SahelSoft',
        'reply_to' => $_ENV['REPLY_TO_EMAIL'] ?? 'sahelsoft38@gmail.com',
    ],
    
    // Email Templates
    'templates' => [
        'proposal_sent' => 'emails/proposal_sent',
        'proposal_accepted' => 'emails/proposal_accepted', 
        'proposal_rejected' => 'emails/proposal_rejected',
        'welcome' => 'emails/welcome',
        'payment_reminder' => 'emails/payment_reminder',
        'project_update' => 'emails/project_update'
    ],
    
    // Settings
    'settings' => [
        'charset' => 'UTF-8',
        'is_html' => true,
        'word_wrap' => 50,
        'logging' => true,
        'debug' => $_ENV['EMAIL_DEBUG'] ?? false
    ]
];
