<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\LocalizationService;

class LocalizationController extends Controller {
    private $localizationService;

    public function __construct() {
        $this->localizationService = new LocalizationService();
    }

    public function index() {
        $languages = $this->localizationService->getAvailableLanguages();
        $statistics = $this->localizationService->getTranslationStatistics();

        return $this->view('admin/localization/index', [
            'title' => 'Localization & Accessibility',
            'languages' => $languages,
            'statistics' => $statistics
        ]);
    }

    public function translations() {
        $languageCode = $_GET['language'] ?? 'en';
        $context = $_GET['context'] ?? null;

        $stmt = $this->pdo->prepare("
            SELECT * FROM translations 
            WHERE language_code = :language_code
        ");
        
        $params = ['language_code' => $languageCode];
        
        if ($context) {
            $stmt = $this->pdo->prepare("
                SELECT * FROM translations 
                WHERE language_code = :language_code AND context = :context
                ORDER BY translation_key ASC
            ");
            $params['context'] = $context;
        } else {
            $stmt = $this->pdo->prepare("
                SELECT * FROM translations 
                WHERE language_code = :language_code
                ORDER BY translation_key ASC
            ");
        }
        
        $stmt->execute($params);
        $translations = $stmt->fetchAll();

        return $this->view('admin/localization/translations', [
            'title' => 'Translations',
            'translations' => $translations,
            'languageCode' => $languageCode,
            'context' => $context,
            'languages' => $this->localizationService->getAvailableLanguages()
        ]);
    }

    public function addTranslation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $languageCode = $_POST['language_code'] ?? '';
        $key = $_POST['translation_key'] ?? '';
        $value = $_POST['translation_value'] ?? '';
        $context = $_POST['context'] ?? null;

        if (empty($languageCode) || empty($key) || empty($value)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $result = $this->localizationService->addTranslation($languageCode, $key, $value, $context);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function updateTranslation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $id = $_POST['id'] ?? null;
        $value = $_POST['translation_value'] ?? '';
        $context = $_POST['context'] ?? null;

        if (!$id || empty($value)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        $stmt = $this->pdo->prepare("
            UPDATE translations 
            SET translation_value = :translation_value, context = :context, updated_at = NOW()
            WHERE id = :id
        ");

        $result = $stmt->execute([
            'id' => $id,
            'translation_value' => $value,
            'context' => $context
        ]);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function deleteTranslation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $id = $_POST['id'] ?? null;

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Translation ID not provided']);
            exit;
        }

        $stmt = $this->pdo->prepare("DELETE FROM translations WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);

        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }

    public function userPreferences() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $userId = $_SESSION['user_id'];
            $preferences = $this->localizationService->getUserLanguagePreference($userId);
            $accessibilitySettings = $this->localizationService->getAccessibilitySettings($userId);
            $languages = $this->localizationService->getAvailableLanguages();

            return $this->view('admin/localization/preferences', [
                'title' => 'Language & Accessibility Preferences',
                'preferences' => $preferences,
                'accessibilitySettings' => $accessibilitySettings,
                'languages' => $languages
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/localization/preferences');
            }

            $userId = $_SESSION['user_id'];
            
            // Update language preferences
            $languagePreferences = [
                'language_code' => $_POST['language_code'] ?? 'en',
                'timezone' => $_POST['timezone'] ?? 'Africa/Niamey',
                'date_format' => $_POST['date_format'] ?? 'Y-m-d',
                'time_format' => $_POST['time_format'] ?? '24h',
                'number_format' => $_POST['number_format'] ?? 'en_US',
                'currency_format' => $_POST['currency_format'] ?? 'XOF'
            ];

            $this->localizationService->updateUserLanguagePreference($userId, $languagePreferences);

            // Update accessibility settings
            $accessibilitySettings = [
                'font_size' => $_POST['font_size'] ?? 'medium',
                'high_contrast' => isset($_POST['high_contrast']),
                'reduced_motion' => isset($_POST['reduced_motion']),
                'screen_reader_optimized' => isset($_POST['screen_reader_optimized']),
                'keyboard_navigation' => isset($_POST['keyboard_navigation']),
                'focus_visible' => isset($_POST['focus_visible']),
                'color_blind_friendly' => $_POST['color_blind_friendly'] ?? 'none',
                'text_to_speech' => isset($_POST['text_to_speech']),
                'auto_alt_text' => isset($_POST['auto_alt_text'])
            ];

            $this->localizationService->updateAccessibilitySettings($userId, $accessibilitySettings);

            // Update session language
            $_SESSION['language'] = $languagePreferences['language_code'];

            $_SESSION['success'] = 'Preferences updated successfully.';
            return $this->redirect('/admin/localization/preferences');
        }
    }

    public function setLanguage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $languageCode = $_POST['language_code'] ?? 'en';

        // Update session language
        $_SESSION['language'] = $languageCode;

        // Update user preference if logged in
        if (isset($_SESSION['user_id'])) {
            $currentPreferences = $this->localizationService->getUserLanguagePreference($_SESSION['user_id']);
            if ($currentPreferences) {
                $this->localizationService->updateUserLanguagePreference($_SESSION['user_id'], [
                    'language_code' => $languageCode,
                    'timezone' => $currentPreferences['timezone'],
                    'date_format' => $currentPreferences['date_format'],
                    'time_format' => $currentPreferences['time_format'],
                    'number_format' => $currentPreferences['number_format'],
                    'currency_format' => $currentPreferences['currency_format']
                ]);
            }
        }

        // Log language usage
        $this->localizationService->logLanguageUsage($languageCode);

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    public function getTranslation() {
        $key = $_GET['key'] ?? '';
        $language = $_GET['language'] ?? null;
        $params = $_GET['params'] ?? [];

        if (empty($key)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Translation key not provided']);
            exit;
        }

        $translation = $this->localizationService->translate($key, $params, $language);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'translation' => $translation]);
        exit;
    }

    public function accessibility() {
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/dashboard');
        }

        $userId = $_GET['user_id'] ?? null;
        
        if ($userId) {
            $accessibilitySettings = $this->localizationService->getAccessibilitySettings($userId);
            $userModel = new \App\Models\User();
            $user = $userModel->find($userId);
            
            return $this->view('admin/localization/user-accessibility', [
                'title' => 'User Accessibility Settings',
                'accessibilitySettings' => $accessibilitySettings,
                'user' => $user
            ]);
        } else {
            // Get all users with accessibility settings
            $stmt = $this->pdo->prepare("
                SELECT as_.*, u.full_name, u.email
                FROM accessibility_settings as_
                JOIN users u ON as_.user_id = u.id
                ORDER BY u.full_name ASC
            ");
            $stmt->execute();
            $userSettings = $stmt->fetchAll();

            return $this->view('admin/localization/accessibility-overview', [
                'title' => 'Accessibility Overview',
                'userSettings' => $userSettings
            ]);
        }
    }

    public function alternativeTexts() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $entityType = $_GET['entity_type'] ?? null;
            $language = $_GET['language'] ?? null;

            $sql = "
                SELECT at.*, l.name as language_name
                FROM alternative_texts at
                LEFT JOIN languages l ON at.language_code = l.code
                WHERE 1=1
            ";

            $params = [];

            if ($entityType) {
                $sql .= " AND at.entity_type = :entity_type";
                $params['entity_type'] = $entityType;
            }

            if ($language) {
                $sql .= " AND at.language_code = :language";
                $params['language'] = $language;
            }

            $sql .= " ORDER BY at.entity_type, at.entity_id, at.language_code";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $altTexts = $stmt->fetchAll();

            return $this->view('admin/localization/alternative-texts', [
                'title' => 'Alternative Texts',
                'altTexts' => $altTexts,
                'entityType' => $entityType,
                'language' => $language,
                'languages' => $this->localizationService->getAvailableLanguages()
            ]);
        } else {
            // Verify CSRF token
            if (!csrf_verify()) {
                $_SESSION['error'] = 'Invalid security token. Please try again.';
                return $this->redirect('/admin/localization/alternative-texts');
            }

            $entityType = $_POST['entity_type'] ?? '';
            $entityId = $_POST['entity_id'] ?? '';
            $languageCode = $_POST['language_code'] ?? '';
            $altText = $_POST['alt_text'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($entityType) || empty($entityId) || empty($languageCode) || empty($altText)) {
                $_SESSION['error'] = 'Missing required fields.';
                return $this->redirect('/admin/localization/alternative-texts');
            }

            $result = $this->localizationService->addAlternativeText($entityType, $entityId, $languageCode, $altText, $description);

            if ($result) {
                $_SESSION['success'] = 'Alternative text added successfully.';
            } else {
                $_SESSION['error'] = 'Failed to add alternative text.';
            }

            return $this->redirect('/admin/localization/alternative-texts');
        }
    }

    public function generateAltText() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        $entityType = $_POST['entity_type'] ?? '';
        $languageCode = $_POST['language_code'] ?? 'en';
        
        // Get entity data based on type
        $entityData = [];
        
        switch ($entityType) {
            case 'image':
                $imageId = $_POST['entity_id'] ?? '';
                // Get image data from database
                $stmt = $this->pdo->prepare("SELECT filename, alt_text FROM uploaded_files WHERE id = :id");
                $stmt->execute(['id' => $imageId]);
                $image = $stmt->fetch();
                
                if ($image) {
                    $entityData = [
                        'title' => $image['alt_text'] ?? $image['filename'],
                        'description' => ''
                    ];
                }
                break;
                
            case 'project':
                $projectId = $_POST['entity_id'] ?? '';
                $projectModel = new \App\Models\Project();
                $project = $projectModel->find($projectId);
                
                if ($project) {
                    $entityData = [
                        'title' => $project['title'],
                        'description' => substr($project['description'], 0, 100)
                    ];
                }
                break;
        }

        if (empty($entityData)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Entity not found']);
            exit;
        }

        $altText = $this->localizationService->generateAltText($entityType, $entityData, $languageCode);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'alt_text' => $altText]);
        exit;
    }

    public function analytics() {
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/dashboard');
        }

        $languageCode = $_GET['language'] ?? null;
        $statistics = $this->localizationService->getTranslationStatistics($languageCode);

        return $this->view('admin/localization/analytics', [
            'title' => 'Localization Analytics',
            'statistics' => $statistics,
            'language' => $languageCode,
            'languages' => $this->localizationService->getAvailableLanguages()
        ]);
    }

    public function export() {
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied.';
            return $this->redirect('/dashboard');
        }

        $languageCode = $_GET['language'] ?? 'en';
        $format = $_GET['format'] ?? 'json';

        $stmt = $this->pdo->prepare("
            SELECT translation_key, translation_value, context
            FROM translations 
            WHERE language_code = :language_code
            ORDER BY translation_key ASC
        ");
        $stmt->execute(['language_code' => $languageCode]);
        $translations = $stmt->fetchAll();

        if ($format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="translations_' . $languageCode . '.json"');
            
            $exportData = [];
            foreach ($translations as $translation) {
                $exportData[$translation['translation_key']] = $translation['translation_value'];
            }
            
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } elseif ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="translations_' . $languageCode . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Key', 'Value', 'Context']);
            
            foreach ($translations as $translation) {
                fputcsv($output, [
                    $translation['translation_key'],
                    $translation['translation_value'],
                    $translation['context'] ?? ''
                ]);
            }
            
            fclose($output);
        }

        exit;
    }

    public function import() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->view('admin/localization/import', [
                'title' => 'Import Translations',
                'languages' => $this->localizationService->getAvailableLanguages()
            ]);
        }

        // Verify CSRF token
        if (!csrf_verify()) {
            $_SESSION['error'] = 'Invalid security token. Please try again.';
            return $this->redirect('/admin/localization/import');
        }

        $languageCode = $_POST['language_code'] ?? '';
        $format = $_POST['format'] ?? 'json';

        if (empty($languageCode)) {
            $_SESSION['error'] = 'Language code is required.';
            return $this->redirect('/admin/localization/import');
        }

        if (!isset($_FILES['import_file'])) {
            $_SESSION['error'] = 'Please select a file to import.';
            return $this->redirect('/admin/localization/import');
        }

        $file = $_FILES['import_file'];
        $fileContent = file_get_contents($file['tmp_name']);

        if ($format === 'json') {
            $translations = json_decode($fileContent, true);
            
            if (!$translations) {
                $_SESSION['error'] = 'Invalid JSON file.';
                return $this->redirect('/admin/localization/import');
            }

            $importedCount = 0;
            foreach ($translations as $key => $value) {
                if ($this->localizationService->addTranslation($languageCode, $key, $value)) {
                    $importedCount++;
                }
            }

            $_SESSION['success'] = "Imported {$importedCount} translations successfully.";
        } elseif ($format === 'csv') {
            $lines = explode("\n", $fileContent);
            $importedCount = 0;
            
            foreach ($lines as $line) {
                if (empty($line)) continue;
                
                $data = str_getcsv($line);
                if (count($data) >= 2) {
                    $key = $data[0];
                    $value = $data[1];
                    $context = $data[2] ?? null;
                    
                    if ($this->localizationService->addTranslation($languageCode, $key, $value, $context)) {
                        $importedCount++;
                    }
                }
            }

            $_SESSION['success'] = "Imported {$importedCount} translations successfully.";
        }

        return $this->redirect('/admin/localization/import');
    }

    private function getPDO() {
        return new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]
        );
    }
}
