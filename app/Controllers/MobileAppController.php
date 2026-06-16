<?php

namespace App\Controllers;

use App\Core\Controller;

class MobileAppController extends Controller {
    public function manifest() {
        // Get mobile app settings from database
        $settings = $this->getMobileAppSettings();
        
        $manifest = [
            'name' => $settings['app_name'] ?? 'SahelSoft',
            'short_name' => $settings['app_short_name'] ?? 'SahelSoft',
            'description' => $settings['app_description'] ?? 'Professional business management platform',
            'start_url' => $settings['start_url'] ?? '/',
            'scope' => $settings['scope'] ?? '/',
            'display' => $settings['display_mode'] ?? 'standalone',
            'orientation' => $settings['orientation'] ?? 'any',
            'theme_color' => $settings['theme_color'] ?? '#0f766e',
            'background_color' => $settings['background_color'] ?? '#ffffff',
            'icons' => [
                [
                    'src' => $settings['icon_192'] ?? '/images/icon?size=192',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ],
                [
                    'src' => $settings['icon_512'] ?? '/images/icon?size=512',
                    'sizes' => '512x512',
                    'type' => 'image/png'
                ]
            ],
            'splash_pages' => [
                [
                    'src' => $settings['splash_screen'] ?? '/public/images/splash.png',
                    'sizes' => '1280x720',
                    'type' => 'image/png'
                ]
            ],
            'shortcuts' => [
                [
                    'name' => 'Dashboard',
                    'short_name' => 'Dashboard',
                    'description' => 'View your dashboard',
                    'url' => '/dashboard',
                    'icons' => [
                        [
                            'src' => '/public/images/dashboard-icon.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Projects',
                    'short_name' => 'Projects',
                    'description' => 'Manage your projects',
                    'url' => '/admin/projects',
                    'icons' => [
                        [
                            'src' => '/public/images/projects-icon.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Messages',
                    'short_name' => 'Messages',
                    'description' => 'View messages',
                    'url' => '/client/messages',
                    'icons' => [
                        [
                            'src' => '/public/images/messages-icon.png',
                            'sizes' => '96x96'
                        ]
                    ]
                ]
            ],
            'categories' => ['business', 'productivity'],
            'lang' => 'en',
            'dir' => 'ltr',
            'prefer_related_applications' => false,
            'related_applications' => [],
            'screenshots' => [],
            'edge_side_panel' => [
                'preferred_width' => 400
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($manifest, JSON_PRETTY_PRINT);
        exit;
    }

    public function serviceWorker() {
        // Serve the service worker file
        header('Content-Type: application/javascript');
        readfile(APP_ROOT . '/public/sw.js');
        exit;
    }

    public function icon() {
        // Serve a dynamic PNG icon
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=3600');

        // Get size from parameter, default to 192
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 192;
        $size = in_array($size, [192, 512]) ? $size : 192;

        // Create PNG
        $img = imagecreatetruecolor($size, $size);

        // Define colors
        $bgColor = imagecolorallocate($img, 15, 118, 110); // #0f766e
        $textColor = imagecolorallocate($img, 255, 255, 255); // white

        // Fill background
        imagefill($img, 0, 0, $bgColor);

        // Add text
        $text = "SS";
        $font = 5; // Built-in font

        // Calculate text position to center it
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $x = ($size - $textWidth) / 2;
        $y = ($size - $textHeight) / 2;

        imagestring($img, $font, $x, $y, $text, $textColor);

        // Output the image
        imagepng($img);
        imagedestroy($img);
        exit;
    }

    public function offline() {
        return $this->view('mobile/offline', [
            'title' => 'Offline - SahelSoft'
        ]);
    }

    public function pushSubscription() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $subscriptionData = json_decode(file_get_contents('php://input'), true);
        
        if (!$subscriptionData) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid subscription data']);
            exit;
        }

        // Store subscription in database
        $result = $this->storePushSubscription($_SESSION['user_id'], $subscriptionData);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function trackEvent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $eventData = json_decode(file_get_contents('php://input'), true);
        
        if (!$eventData || !isset($eventData['event_type'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid event data']);
            exit;
        }

        // Store analytics event
        $result = $this->storeAnalyticsEvent($eventData);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function getSettings() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $settings = $this->getMobileAppSettings();
        $userPreferences = $this->getUserMobilePreferences($_SESSION['user_id']);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'settings' => $settings,
            'user_preferences' => $userPreferences
        ]);
        exit;
    }

    public function updatePreferences() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $preferences = json_decode(file_get_contents('php://input'), true);
        
        if (!$preferences) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid preferences data']);
            exit;
        }

        $result = $this->updateUserMobilePreferences($_SESSION['user_id'], $preferences);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function submitFeedback() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $feedbackData = json_decode(file_get_contents('php://input'), true);
        
        if (!$feedbackData || !isset($feedbackData['feedback_type'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid feedback data']);
            exit;
        }

        $result = $this->storeFeedback($_SESSION['user_id'], $feedbackData);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function syncOfflineData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $syncData = json_decode(file_get_contents('php://input'), true);
        
        // Process offline actions
        $result = $this->processOfflineActions($_SESSION['user_id'], $syncData);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'synced_actions' => $result]);
        exit;
    }

    public function getLatestData() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get latest data for offline caching
        $data = [
            'notifications' => $this->getLatestNotifications($_SESSION['user_id']),
            'projects' => $this->getLatestProjects($_SESSION['user_id']),
            'messages' => $this->getLatestMessages($_SESSION['user_id']),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // Helper methods
    private function getMobileAppSettings() {
        $pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        $stmt = $pdo->prepare("SELECT * FROM mobile_app_settings LIMIT 1");
        $stmt->execute();
        return $stmt->fetch() ?? [];
    }

    private function storePushSubscription($userId, $subscriptionData) {
        $pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        // Check if subscription already exists
        $stmt = $pdo->prepare("SELECT id FROM mobile_push_subscriptions WHERE user_id = :user_id AND endpoint = :endpoint");
        $stmt->execute(['user_id' => $userId, 'endpoint' => $subscriptionData['endpoint']]);
        
        if ($stmt->fetch()) {
            // Update existing subscription
            $stmt = $pdo->prepare("
                UPDATE mobile_push_subscriptions 
                SET p256dh_key = :p256dh_key, auth_key = :auth_key, device_info = :device_info, is_active = TRUE, last_used = NOW()
                WHERE user_id = :user_id AND endpoint = :endpoint
            ");
            return $stmt->execute([
                'user_id' => $userId,
                'endpoint' => $subscriptionData['endpoint'],
                'p256dh_key' => $subscriptionData['keys']['p256dh'],
                'auth_key' => $subscriptionData['keys']['auth'],
                'device_info' => json_encode($this->getDeviceInfo())
            ]);
        } else {
            // Insert new subscription
            $stmt = $pdo->prepare("
                INSERT INTO mobile_push_subscriptions 
                (user_id, endpoint, p256dh_key, auth_key, device_type, device_info, is_active)
                VALUES (:user_id, :endpoint, :p256dh_key, :auth_key, :device_type, :device_info, TRUE)
            ");
            return $stmt->execute([
                'user_id' => $userId,
                'endpoint' => $subscriptionData['endpoint'],
                'p256dh_key' => $subscriptionData['keys']['p256dh'],
                'auth_key' => $subscriptionData['keys']['auth'],
                'device_type' => $this->detectDeviceType(),
                'device_info' => json_encode($this->getDeviceInfo())
            ]);
        }
    }

    private function storeAnalyticsEvent($eventData) {
        $pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        $stmt = $pdo->prepare("
            INSERT INTO mobile_app_analytics 
            (user_id, session_id, event_type, event_data, device_info, app_version)
            VALUES (:user_id, :session_id, :event_type, :event_data, :device_info, :app_version)
        ");

        return $stmt->execute([
            'user_id' => $_SESSION['user_id'] ?? null,
            'session_id' => $eventData['session_id'] ?? session_id(),
            'event_type' => $eventData['event_type'],
            'event_data' => json_encode($eventData['data'] ?? []),
            'device_info' => json_encode($this->getDeviceInfo()),
            'app_version' => $eventData['app_version'] ?? '1.0.0'
        ]);
    }

    private function getUserMobilePreferences($userId) {
        $pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        $stmt = $pdo->prepare("SELECT * FROM mobile_app_preferences WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch() ?? [];
    }

    private function updateUserMobilePreferences($userId, $preferences) {
        $pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        $stmt = $pdo->prepare("
            INSERT INTO mobile_app_preferences 
            (user_id, push_notifications, email_notifications, theme, language, timezone, auto_sync, cache_size_mb, data_usage_wifi_only, biometric_enabled)
            VALUES (:user_id, :push_notifications, :email_notifications, :theme, :language, :timezone, :auto_sync, :cache_size_mb, :data_usage_wifi_only, :biometric_enabled)
            ON DUPLICATE KEY UPDATE
            push_notifications = VALUES(push_notifications),
            email_notifications = VALUES(email_notifications),
            theme = VALUES(theme),
            language = VALUES(language),
            timezone = VALUES(timezone),
            auto_sync = VALUES(auto_sync),
            cache_size_mb = VALUES(cache_size_mb),
            data_usage_wifi_only = VALUES(data_usage_wifi_only),
            biometric_enabled = VALUES(biometric_enabled),
            updated_at = NOW()
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'push_notifications' => $preferences['push_notifications'] ?? true,
            'email_notifications' => $preferences['email_notifications'] ?? true,
            'theme' => $preferences['theme'] ?? 'auto',
            'language' => $preferences['language'] ?? 'en',
            'timezone' => $preferences['timezone'] ?? 'UTC',
            'auto_sync' => $preferences['auto_sync'] ?? true,
            'cache_size_mb' => $preferences['cache_size_mb'] ?? 100,
            'data_usage_wifi_only' => $preferences['data_usage_wifi_only'] ?? true,
            'biometric_enabled' => $preferences['biometric_enabled'] ?? false
        ]);
    }

    private function storeFeedback($userId, $feedbackData) {
        $pdo = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );

        $stmt = $pdo->prepare("
            INSERT INTO mobile_app_feedback 
            (user_id, feedback_type, rating, title, description, app_version, device_info)
            VALUES (:user_id, :feedback_type, :rating, :title, :description, :app_version, :device_info)
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'feedback_type' => $feedbackData['feedback_type'],
            'rating' => $feedbackData['rating'] ?? null,
            'title' => $feedbackData['title'] ?? '',
            'description' => $feedbackData['description'] ?? '',
            'app_version' => $feedbackData['app_version'] ?? '1.0.0',
            'device_info' => json_encode($this->getDeviceInfo())
        ]);
    }

    private function processOfflineActions($userId, $syncData) {
        $processedActions = [];
        
        if (isset($syncData['actions']) && is_array($syncData['actions'])) {
            foreach ($syncData['actions'] as $action) {
                try {
                    // Process each offline action based on type
                    switch ($action['type']) {
                        case 'message':
                            $result = $this->processOfflineMessage($userId, $action);
                            break;
                        case 'project_update':
                            $result = $this->processOfflineProjectUpdate($userId, $action);
                            break;
                        case 'time_entry':
                            $result = $this->processOfflineTimeEntry($userId, $action);
                            break;
                        default:
                            $result = false;
                    }
                    
                    $processedActions[] = [
                        'id' => $action['id'],
                        'type' => $action['type'],
                        'success' => $result
                    ];
                } catch (\Exception $e) {
                    $processedActions[] = [
                        'id' => $action['id'],
                        'type' => $action['type'],
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }
        
        return $processedActions;
    }

    private function getLatestNotifications($userId) {
        // Get latest notifications for user
        $notificationModel = new \App\Models\Notification();
        return $notificationModel->getRecentNotifications($userId, 5);
    }

    private function getLatestProjects($userId) {
        // Get latest projects for user
        if ($_SESSION['role'] === 'admin') {
            $projectModel = new \App\Models\Project();
            return $projectModel->findAll();
        } else {
            $projectModel = new \App\Models\Project();
            return $projectModel->where('client_id', $userId)->findAll();
        }
    }

    private function getLatestMessages($userId) {
        // Get latest messages for user
        $messageModel = new \App\Models\Message();
        return $messageModel->getRecentMessages($userId, 5);
    }

    private function getDeviceInfo() {
        return [
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'platform' => $this->detectPlatform(),
            'screen_width' => $_POST['screen_width'] ?? null,
            'screen_height' => $_POST['screen_height'] ?? null
        ];
    }

    private function detectDeviceType() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
            return 'ios';
        } elseif (preg_match('/Android/i', $userAgent)) {
            return 'android';
        } elseif (preg_match('/Mobile/i', $userAgent)) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    private function detectPlatform() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/Windows/i', $userAgent)) return 'Windows';
        if (preg_match('/Mac/i', $userAgent)) return 'macOS';
        if (preg_match('/Linux/i', $userAgent)) return 'Linux';
        if (preg_match('/iPhone|iPad/i', $userAgent)) return 'iOS';
        if (preg_match('/Android/i', $userAgent)) return 'Android';
        
        return 'Unknown';
    }

    private function processOfflineMessage($userId, $action) {
        // Process offline message submission
        // Implementation would depend on your message system
        return true;
    }

    private function processOfflineProjectUpdate($userId, $action) {
        // Process offline project update
        // Implementation would depend on your project system
        return true;
    }

    private function processOfflineTimeEntry($userId, $action) {
        // Process offline time entry
        // Implementation would depend on your time tracking system
        return true;
    }
}
