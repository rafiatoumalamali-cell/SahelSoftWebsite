<?php

session_start();

require_once __DIR__ . '/../app/Config/config.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load Helpers
require_once __DIR__ . '/../app/Helpers/functions.php';

// Language Switcher Logic
if (isset($_GET['lang'])) {
    if (in_array($_GET['lang'], ['en', 'fr', 'ha'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    // Redirect back to remove query param or just reload
    // For simplicity, we just set it. 
    // Ideally redirect to same URL without param, but simple is fine.
    $redirect = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: $redirect");
    exit;
}

// Re-load language if session changed (though helper loaded at top, 
// we might need to reload if we moved the helper include down, 
// but since we redirect above, next request picks it up correctly.)

use App\Core\Router;

$router = new Router();

// Define Routes
// Public
$router->get('/', 'HomeController@index');
$router->get('/services', 'ServicesController@index');
$router->get('/portfolio', 'PortfolioController@index');
$router->get('/about', 'HomeController@about');
$router->get('/contact', 'HomeController@contact');
$router->get('/website-inquiry', 'HomeController@websiteInquiry');
$router->post('/contact/submit', 'ContactController@submit');
$router->get('/terms', 'HomeController@terms');
$router->get('/privacy', 'HomeController@privacy');
$router->get('/help', 'HomeController@help');
$router->get('/blog', 'BlogController@index');
$router->get('/blog/{slug}', 'BlogController@show');

// Admin Routes
$router->get('/admin', 'AdminController@dashboard');

// Admin Blog Routes
$router->post('/admin/blog/create', 'AdminBlogController@create');
$router->post('/admin/blog/update', 'AdminBlogController@update');
$router->post('/admin/blog/delete', 'AdminBlogController@delete');

// Auth
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@loginPost');
$router->get('/logout', 'AuthController@logout');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@registerPost');
$router->get('/forgot-password', 'AuthController@forgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPasswordPost');
$router->get('/reset-password', 'AuthController@resetPassword');
$router->post('/reset-password', 'AuthController@resetPasswordPost');
$router->get('/dashboard', 'AuthController@dashboard'); // Dispatcher

// Two-Factor Authentication
$router->get('/2fa/setup', 'TwoFactorController@setup');
$router->post('/2fa/enable', 'TwoFactorController@enable');
$router->post('/2fa/verify', 'TwoFactorController@verify');
$router->post('/2fa/disable', 'TwoFactorController@disable');
$router->get('/2fa/backup-codes', 'TwoFactorController@showBackupCodes');
$router->post('/2fa/regenerate-backup-codes', 'TwoFactorController@regenerateBackupCodes');

// Client Portal
$router->get('/client/dashboard', 'ClientController@dashboard');
$router->get('/profile', 'AuthController@profile');
$router->post('/profile/update', 'AuthController@profileUpdate');
$router->get('/messages', 'ClientController@allMessages');
$router->get('/client/project', 'ClientController@projectDetails'); // Expects ?id=X
$router->get('/client/project/messages', 'ClientController@messages');
$router->post('/client/project/messages', 'ClientController@sendMessage');
$router->get('/client/project/payments', 'ClientController@payments');
$router->post('/client/project/submit-payment', 'ClientController@submitPayment');
$router->get('/client/project/upload', 'ClientController@upload');
$router->post('/client/project/upload', 'ClientController@processUpload');

// Team Portal
$router->get('/team/dashboard', 'TeamController@dashboard');
$router->get('/team/project/create', 'TeamController@create');
$router->post('/team/project/create', 'TeamController@store');
$router->get('/team/project/view', 'TeamController@viewProject'); // Expects ?id=X
$router->get('/team/project/edit', 'TeamController@edit'); // Expects ?id=X
$router->post('/team/project/edit', 'TeamController@update');
$router->get('/team/tasks', 'TeamController@tasks'); // Team task list
$router->post('/team/tasks/update-status', 'TeamController@updateTaskStatus');
$router->get('/team/reports', 'TeamController@reports');
$router->get('/team/messages', 'MessageController@index');
$router->get('/api/messages/chat', 'MessageController@getChat');
$router->post('/api/messages/send', 'MessageController@send');

// Admin Portal
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->post('/admin/users/create', 'AdminController@createUser');
$router->post('/admin/users/update', 'AdminController@updateUser');
$router->post('/admin/users/delete', 'AdminController@deleteUser');
$router->post('/admin/projects/create', 'AdminController@createProject');
$router->post('/admin/projects/update', 'AdminController@updateProject');
$router->post('/admin/projects/delete', 'AdminController@deleteProject');
$router->post('/admin/settings/update', 'AdminController@updateSettings');
$router->post('/admin/requests/accept', 'AdminController@acceptRequest');
$router->post('/admin/requests/reject', 'AdminController@rejectRequest');

// Advanced Project Management (Tasks, Progress, Payments)
$router->get('/admin/project/manage', 'AdminController@manageProject'); // ?id=X
$router->post('/admin/project/update-progress', 'AdminController@updateProgress');
$router->post('/admin/tasks/create', 'AdminController@createTask');
$router->post('/admin/tasks/update', 'AdminController@updateTask');
$router->post('/admin/tasks/delete', 'AdminController@deleteTask');
$router->post('/admin/payments/create', 'AdminController@createPayment');
$router->post('/admin/payments/update', 'AdminController@updatePayment');
$router->post('/admin/payments/delete', 'AdminController@deletePayment');

// Simple test route
$router->get('/test-route', function() {
    echo "✅ Router is working! Test route accessed successfully.";
});

// Proposal Management
$router->get('/admin/proposals', 'ProposalController@index');
$router->get('/admin/proposals/create', 'ProposalController@create');
$router->post('/admin/proposals/create', 'ProposalController@store');
$router->get('/admin/proposals/view', 'ProposalController@viewProposal');
$router->get('/admin/proposals/edit', 'ProposalController@edit');
$router->post('/admin/proposals/update', 'ProposalController@update');
$router->post('/admin/proposals/send', 'ProposalController@send');

// Client Proposal Routes
$router->get('/client/proposals', 'ProposalController@clientIndex');
$router->get('/client/proposals/view', 'ProposalController@clientView');
$router->post('/client/proposals/accept', 'ProposalController@accept');
$router->post('/client/proposals/reject', 'ProposalController@reject');

// Contact to Project Conversion
$router->post('/admin/contacts/convert-to-project', 'ContactController@convertToProject');

// Content Management Routes
$router->get('/admin/content', 'ContentController@index');
$router->get('/admin/content/create', 'ContentController@create');
$router->post('/admin/content/create', 'ContentController@store');
$router->get('/admin/content/edit', 'ContentController@edit');
$router->post('/admin/content/update', 'ContentController@update');
$router->post('/admin/content/publish', 'ContentController@publish');
$router->post('/admin/content/unpublish', 'ContentController@unpublish');
$router->post('/admin/content/delete', 'ContentController@delete');
$router->get('/admin/content/search', 'ContentController@search');

// Analytics Routes
$router->get('/admin/analytics/dashboard', 'AnalyticsController@dashboard');
$router->get('/admin/analytics/revenue', 'AnalyticsController@revenue');
$router->get('/admin/analytics/projects', 'AnalyticsController@projects');
$router->get('/admin/analytics/clients', 'AnalyticsController@clients');
$router->get('/admin/analytics/proposals', 'AnalyticsController@proposals');
$router->get('/admin/analytics/financial', 'AnalyticsController@financial');
$router->get('/admin/analytics/reports', 'AnalyticsController@reports');
$router->get('/admin/analytics/kpi', 'AnalyticsController@kpi');
$router->get('/admin/analytics/realtime', 'AnalyticsController@realtime');
$router->get('/admin/analytics/compare', 'AnalyticsController@compare');
$router->get('/admin/analytics/forecast', 'AnalyticsController@forecast');
$router->get('/admin/analytics/exportData', 'AnalyticsController@exportData');

// File Management Routes
$router->get('/admin/files', 'FileController@index');
$router->get('/admin/files/upload', 'FileController@upload');
$router->post('/admin/files/upload', 'FileController@upload');
$router->get('/admin/files/view', 'FileController@view');
$router->get('/admin/files/download', 'FileController@download');
$router->get('/admin/files/edit', 'FileController@edit');
$router->post('/admin/files/update', 'FileController@update');
$router->post('/admin/files/delete', 'FileController@delete');
$router->post('/admin/files/share', 'FileController@share');
$router->get('/admin/files/shared', 'FileController@shared');
$router->get('/admin/files/search', 'FileController@search');
$router->post('/admin/files/create-version', 'FileController@createVersion');
$router->get('/admin/files/stats', 'FileController@stats');

// Notification System Routes
$router->get('/admin/notifications', 'NotificationController@index');
$router->get('/admin/notifications/recent', 'NotificationController@recent');
$router->get('/admin/notifications/unread-count', 'NotificationController@unreadCount');
$router->post('/admin/notifications/mark-read', 'NotificationController@markAsRead');
$router->post('/admin/notifications/mark-all-read', 'NotificationController@markAllAsRead');
$router->post('/admin/notifications/delete', 'NotificationController@delete');
$router->post('/admin/notifications/delete-read', 'NotificationController@deleteRead');
$router->get('/admin/notifications/cleanup', 'NotificationController@cleanup');
$router->get('/admin/notifications/preferences', 'NotificationController@preferences');
$router->post('/admin/notifications/preferences', 'NotificationController@preferences');
$router->get('/admin/notifications/search', 'NotificationController@search');
$router->get('/admin/notifications/test', 'NotificationController@test');
$router->post('/admin/notifications/test', 'NotificationController@test');
$router->post('/admin/notifications/cleanup', 'NotificationController@cleanup');
$router->get('/admin/notifications/stats', 'NotificationController@stats');
$router->get('/admin/notifications/realtime', 'NotificationController@realtime');

// Proposal Template Routes
$router->get('/admin/proposals/templates', 'ProposalTemplateController@index');
$router->get('/admin/proposals/templates/create', 'ProposalTemplateController@create');
$router->post('/admin/proposals/templates/create', 'ProposalTemplateController@create');
$router->get('/admin/proposals/templates/view', 'ProposalTemplateController@view');
$router->get('/admin/proposals/templates/edit', 'ProposalTemplateController@edit');
$router->post('/admin/proposals/templates/update', 'ProposalTemplateController@edit');
$router->post('/admin/proposals/templates/delete', 'ProposalTemplateController@delete');
$router->post('/admin/proposals/templates/duplicate', 'ProposalTemplateController@duplicate');
$router->post('/admin/proposals/templates/set-default', 'ProposalTemplateController@setDefault');
$router->post('/admin/proposals/templates/create-proposal', 'ProposalTemplateController@createProposal');
$router->get('/admin/proposals/templates/preview', 'ProposalTemplateController@preview');
$router->get('/admin/proposals/templates/analytics', 'ProposalTemplateController@analytics');
$router->get('/admin/proposals/templates/search', 'ProposalTemplateController@search');

// Project Management Enhancement Routes
$router->get('/admin/projects/tasks', 'ProjectManagementController@tasks');
$router->get('/admin/projects/tasks/create', 'ProjectManagementController@createTask');
$router->post('/admin/projects/tasks/create', 'ProjectManagementController@createTask');
$router->get('/admin/projects/tasks/view', 'ProjectManagementController@viewTask');
$router->get('/admin/projects/tasks/edit', 'ProjectManagementController@editTask');
$router->post('/admin/projects/tasks/update', 'ProjectManagementController@editTask');
$router->post('/admin/projects/tasks/delete', 'ProjectManagementController@deleteTask');
$router->get('/admin/projects/time', 'ProjectManagementController@timeTracking');
$router->get('/admin/projects/time/log', 'ProjectManagementController@logTime');
$router->post('/admin/projects/time/log', 'ProjectManagementController@logTime');
$router->get('/admin/projects/time/edit', 'ProjectManagementController@editTimeEntry');
$router->post('/admin/projects/time/update', 'ProjectManagementController@editTimeEntry');
$router->post('/admin/projects/time/delete', 'ProjectManagementController@deleteTimeEntry');
$router->get('/admin/projects/gantt', 'ProjectManagementController@gantt');

// Invoice Management Routes
$router->get('/admin/invoices', 'InvoiceController@adminIndex');
$router->get('/admin/invoices/create', 'InvoiceController@create');
$router->post('/admin/invoices/create', 'InvoiceController@store');
$router->get('/admin/invoices/view', 'InvoiceController@viewInvoice');
$router->post('/admin/invoices/send', 'InvoiceController@send');
$router->post('/admin/invoices/approve-payment', 'InvoiceController@approvePayment');

// Client Invoice Routes
$router->get('/client/invoices', 'InvoiceController@clientIndex');
$router->get('/client/invoices/view', 'InvoiceController@viewInvoice');
$router->post('/client/invoices/submit-payment', 'InvoiceController@submitPayment');
$router->get('/admin/projects/analytics', 'ProjectManagementController@analytics');
$router->post('/admin/projects/tasks/update-progress', 'ProjectManagementController@updateTaskProgress');
$router->get('/admin/projects/tasks/data', 'ProjectManagementController@getTaskData');

// Mobile App Routes
$router->get('/manifest.json', 'MobileAppController@manifest');
$router->get('/sw.js', 'MobileAppController@serviceWorker');
$router->get('/offline', 'MobileAppController@offline');
$router->get('/images/icon', 'MobileAppController@icon');
$router->post('/api/push-subscription', 'MobileAppController@pushSubscription');
$router->post('/api/track-event', 'MobileAppController@trackEvent');
$router->get('/api/mobile/settings', 'MobileAppController@getSettings');
$router->post('/api/mobile/preferences', 'MobileAppController@updatePreferences');
$router->post('/api/mobile/feedback', 'MobileAppController@submitFeedback');
$router->post('/api/sync-offline-actions', 'MobileAppController@syncOfflineData');
$router->get('/api/latest-data', 'MobileAppController@getLatestData');

// Client Portal Routes
$router->get('/client', 'ClientPortalController@dashboard');
$router->get('/client/dashboard', 'ClientPortalController@dashboard');
$router->get('/client/projects', 'ClientPortalController@projects');
$router->get('/client/projects/view', 'ClientPortalController@projectView');
$router->get('/client/messages', 'ClientPortalController@messages');
$router->get('/client/messages/thread', 'ClientPortalController@messageThread');
$router->post('/client/messages/send', 'ClientPortalController@sendMessage');
$router->get('/client/proposals', 'ClientPortalController@proposals');
$router->get('/client/proposals/view', 'ClientPortalController@proposalView');
$router->post('/client/proposals/accept', 'ClientPortalController@acceptProposal');
$router->post('/client/proposals/reject', 'ClientPortalController@rejectProposal');
$router->get('/client/payments', 'ClientPortalController@payments');
$router->get('/client/profile', 'ClientPortalController@profile');
$router->post('/client/profile/update', 'ClientPortalController@updateProfile');
$router->post('/client/settings/update', 'ClientPortalController@updateSettings');
$router->get('/client/support', 'ClientPortalController@support');
$router->get('/client/support/create', 'ClientPortalController@createSupportTicket');
$router->post('/client/support/create', 'ClientPortalController@createSupportTicket');
$router->get('/client/knowledge-base', 'ClientPortalController@knowledgeBase');
$router->get('/client/kb', 'ClientPortalController@knowledgeBase');
$router->get('/client/kb/article', 'ClientPortalController@kbArticle');

// Advanced Search Routes
$router->get('/search', 'SearchController@index');
$router->get('/admin/search', 'SearchController@index');
$router->get('/api/search/suggestions', 'SearchController@suggestions');
$router->get('/api/search/quick', 'SearchController@quickSearch');
$router->post('/api/search/save', 'SearchController@saveSearch');
$router->get('/api/search/load', 'SearchController@loadSavedSearch');
$router->get('/admin/search/analytics', 'SearchController@analytics');
$router->post('/admin/search/reindex', 'SearchController@reindex');

// Third-Party Integration Routes
$router->get('/admin/integrations', 'IntegrationController@index');
$router->get('/admin/integrations/connect', 'IntegrationController@connect');
$router->post('/admin/integrations/connect', 'IntegrationController@connect');
$router->get('/admin/integrations/configure', 'IntegrationController@configure');
$router->post('/admin/integrations/disconnect', 'IntegrationController@disconnect');
$router->post('/admin/integrations/sync', 'IntegrationController@sync');
$router->post('/admin/integrations/mapping', 'IntegrationController@updateMapping');
$router->get('/admin/integrations/analytics', 'IntegrationController@analytics');
$router->get('/admin/integrations/overview', 'IntegrationController@overview');
$router->post('/admin/integrations/test', 'IntegrationController@testConnection');
$router->post('/webhook/{provider}', 'IntegrationController@webhook');

// Workflow Automation Routes
$router->get('/admin/workflows', 'WorkflowController@index');
$router->get('/admin/workflows/create', 'WorkflowController@create');
$router->post('/admin/workflows/create', 'WorkflowController@create');
$router->get('/admin/workflows/edit', 'WorkflowController@edit');
$router->post('/admin/workflows/update', 'WorkflowController@edit');
$router->post('/admin/workflows/delete', 'WorkflowController@delete');
$router->post('/admin/workflows/execute', 'WorkflowController@execute');
$router->get('/admin/workflows/executions', 'WorkflowController@executions');
$router->get('/admin/workflows/analytics', 'WorkflowController@analytics');
$router->post('/admin/workflows/duplicate', 'WorkflowController@duplicate');
$router->post('/admin/workflows/toggle-status', 'WorkflowController@toggleStatus');

// Localization & Accessibility Routes
$router->get('/admin/localization', 'LocalizationController@index');
$router->get('/admin/localization/translations', 'LocalizationController@translations');
$router->post('/admin/localization/translations/add', 'LocalizationController@addTranslation');
$router->post('/admin/localization/translations/update', 'LocalizationController@updateTranslation');
$router->post('/admin/localization/translations/delete', 'LocalizationController@deleteTranslation');
$router->get('/admin/localization/preferences', 'LocalizationController@userPreferences');
$router->post('/admin/localization/preferences', 'LocalizationController@userPreferences');
$router->post('/api/localization/set-language', 'LocalizationController@setLanguage');
$router->get('/api/localization/translate', 'LocalizationController@getTranslation');
$router->get('/admin/localization/accessibility', 'LocalizationController@accessibility');
$router->get('/admin/localization/alternative-texts', 'LocalizationController@alternativeTexts');
$router->post('/admin/localization/alternative-texts', 'LocalizationController@alternativeTexts');
$router->post('/api/localization/generate-alt-text', 'LocalizationController@generateAltText');
$router->get('/admin/localization/analytics', 'LocalizationController@analytics');
$router->get('/admin/localization/export', 'LocalizationController@export');
$router->get('/admin/localization/import', 'LocalizationController@import');
$router->post('/admin/localization/import', 'LocalizationController@import');

// Performance & Scaling Routes
$router->get('/admin/performance', 'PerformanceController@index');
$router->get('/admin/performance/metrics', 'PerformanceController@metrics');
$router->get('/admin/performance/alerts', 'PerformanceController@alerts');
$router->post('/admin/performance/alerts/resolve', 'PerformanceController@resolveAlert');
$router->get('/admin/performance/cache', 'PerformanceController@cache');
$router->post('/admin/performance/cache/clear', 'PerformanceController@clearCache');
$router->get('/admin/performance/jobs', 'PerformanceController@backgroundJobs');
$router->post('/admin/performance/jobs/queue', 'PerformanceController@queueJob');
$router->post('/admin/performance/jobs/process', 'PerformanceController@processJobs');
$router->get('/admin/performance/scaling', 'PerformanceController@scaling');
$router->get('/admin/performance/scaling/add', 'PerformanceController@addScalingRule');
$router->post('/admin/performance/scaling/add', 'PerformanceController@addScalingRule');
$router->get('/admin/performance/database', 'PerformanceController@database');
$router->post('/admin/performance/database/optimize', 'PerformanceController@optimizeDatabase');
$router->get('/admin/performance/reports', 'PerformanceController@reports');
$router->post('/admin/performance/reports/generate', 'PerformanceController@generateReport');
$router->get('/admin/performance/cleanup', 'PerformanceController@cleanup');
$router->post('/admin/performance/cleanup', 'PerformanceController@cleanup');
$router->get('/admin/performance/server-health', 'PerformanceController@serverHealth');
$router->get('/admin/performance/settings', 'PerformanceController@settings');
$router->post('/admin/performance/settings', 'PerformanceController@settings');

// Run the application
$router->resolve();
