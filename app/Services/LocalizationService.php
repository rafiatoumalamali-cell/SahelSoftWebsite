<?php

namespace App\Services;

use App\Core\Model;
use PDO;

class LocalizationService {
    private $pdo;
    private $currentLanguage;
    private $translations = [];
    
    public function __construct() {
        $this->pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        
        $this->currentLanguage = $this->getCurrentLanguage();
        $this->loadTranslations();
    }
    
    public function translate($key, $params = [], $language = null) {
        $language = $language ?: $this->currentLanguage;
        $translation = $this->getTranslation($key, $language);
        
        if ($translation) {
            return $this->replaceParams($translation, $params);
        }
        
        // Fallback to English if translation not found
        if ($language !== 'en') {
            $englishTranslation = $this->getTranslation($key, 'en');
            if ($englishTranslation) {
                return $this->replaceParams($englishTranslation, $params);
            }
        }
        
        // Return the key as last resort
        return $this->replaceParams($key, $params);
    }
    
    public function getAvailableLanguages() {
        $stmt = $this->pdo->prepare("
            SELECT code, name, native_name, flag_emoji, is_rtl, is_default, sort_order
            FROM languages
            WHERE is_active = TRUE
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getUserLanguagePreference($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM user_language_preferences
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }
    
    public function updateUserLanguagePreference($userId, $preferences) {
        $stmt = $this->pdo->prepare("
            INSERT INTO user_language_preferences 
            (user_id, language_code, timezone, date_format, time_format, number_format, currency_format)
            VALUES (:user_id, :language_code, :timezone, :date_format, :time_format, :number_format, :currency_format)
            ON DUPLICATE KEY UPDATE
            language_code = VALUES(language_code),
            timezone = VALUES(timezone),
            date_format = VALUES(date_format),
            time_format = VALUES(time_format),
            number_format = VALUES(number_format),
            currency_format = VALUES(currency_format),
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'language_code' => $preferences['language_code'] ?? 'en',
            'timezone' => $preferences['timezone'] ?? 'Africa/Niamey',
            'date_format' => $preferences['date_format'] ?? 'Y-m-d',
            'time_format' => $preferences['time_format'] ?? '24h',
            'number_format' => $preferences['number_format'] ?? 'en_US',
            'currency_format' => $preferences['currency_format'] ?? 'XOF'
        ]);
    }
    
    public function getAccessibilitySettings($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM accessibility_settings
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }
    
    public function updateAccessibilitySettings($userId, $settings) {
        $stmt = $this->pdo->prepare("
            INSERT INTO accessibility_settings 
            (user_id, font_size, high_contrast, reduced_motion, screen_reader_optimized, keyboard_navigation, focus_visible, color_blind_friendly, text_to_speech, auto_alt_text)
            VALUES (:user_id, :font_size, :high_contrast, :reduced_motion, :screen_reader_optimized, :keyboard_navigation, :focus_visible, :color_blind_friendly, :text_to_speech, :auto_alt_text)
            ON DUPLICATE KEY UPDATE
            font_size = VALUES(font_size),
            high_contrast = VALUES(high_contrast),
            reduced_motion = VALUES(reduced_motion),
            screen_reader_optimized = VALUES(screen_reader_optimized),
            keyboard_navigation = VALUES(keyboard_navigation),
            focus_visible = VALUES(focus_visible),
            color_blind_friendly = VALUES(color_blind_friendly),
            text_to_speech = VALUES(text_to_speech),
            auto_alt_text = VALUES(auto_alt_text),
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'font_size' => $settings['font_size'] ?? 'medium',
            'high_contrast' => $settings['high_contrast'] ?? false,
            'reduced_motion' => $settings['reduced_motion'] ?? false,
            'screen_reader_optimized' => $settings['screen_reader_optimized'] ?? false,
            'keyboard_navigation' => $settings['keyboard_navigation'] ?? true,
            'focus_visible' => $settings['focus_visible'] ?? true,
            'color_blind_friendly' => $settings['color_blind_friendly'] ?? 'none',
            'text_to_speech' => $settings['text_to_speech'] ?? false,
            'auto_alt_text' => $settings['auto_alt_text'] ?? true
        ]);
    }
    
    public function addTranslation($languageCode, $key, $value, $context = null, $isVerified = false) {
        $stmt = $this->pdo->prepare("
            INSERT INTO translations 
            (language_code, translation_key, translation_value, context, is_verified)
            VALUES (:language_code, :translation_key, :translation_value, :context, :is_verified)
            ON DUPLICATE KEY UPDATE
            translation_value = VALUES(translation_value),
            context = VALUES(context),
            is_verified = VALUES(is_verified),
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'language_code' => $languageCode,
            'translation_key' => $key,
            'translation_value' => $value,
            'context' => $context,
            'is_verified' => $isVerified
        ]);
    }
    
    public function addAlternativeText($entityType, $entityId, $languageCode, $altText, $description = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO alternative_texts 
            (entity_type, entity_id, language_code, alt_text, description)
            VALUES (:entity_type, :entity_id, :language_code, :alt_text, :description)
            ON DUPLICATE KEY UPDATE
            alt_text = VALUES(alt_text),
            description = VALUES(description),
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'language_code' => $languageCode,
            'alt_text' => $altText,
            'description' => $description
        ]);
    }
    
    public function getAlternativeText($entityType, $entityId, $languageCode = null) {
        $languageCode = $languageCode ?: $this->currentLanguage;
        
        $stmt = $this->pdo->prepare("
            SELECT alt_text, description 
            FROM alternative_texts
            WHERE entity_type = :entity_type 
            AND entity_id = :entity_id 
            AND language_code = :language_code
            LIMIT 1
        ");
        
        $stmt->execute([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'language_code' => $languageCode
        ]);
        
        return $stmt->fetch();
    }
    
    public function formatDate($date, $format = null, $language = null) {
        $language = $language ?: $this->currentLanguage;
        $userPreference = $this->getUserLanguagePreference($_SESSION['user_id'] ?? null);
        
        $dateFormat = $format ?: $userPreference['date_format'] ?? 'Y-m-d';
        
        if ($language === 'ar') {
            // Arabic date formatting
            $months = [
                'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
            ];
            
            $dateObj = is_string($date) ? new DateTime($date) : $date;
            return $dateObj->format('d') . ' ' . $months[$dateObj->format('n') - 1] . ' ' . $dateObj->format('Y');
        }
        
        return date($dateFormat, is_string($date) ? strtotime($date) : $date->getTimestamp());
    }
    
    public function formatTime($time, $format = null, $language = null) {
        $language = $language ?: $this->currentLanguage;
        $userPreference = $this->getUserLanguagePreference($_SESSION['user_id'] ?? null);
        
        $timeFormat = $format ?: $userPreference['time_format'] ?? '24h';
        
        if ($timeFormat === '12h') {
            return date('h:i A', is_string($time) ? strtotime($time) : $time->getTimestamp());
        }
        
        return date('H:i', is_string($time) ? strtotime($time) : $time->getTimestamp());
    }
    
    public function formatCurrency($amount, $currency = null, $language = null) {
        $language = $language ?: $this->currentLanguage;
        $userPreference = $this->getUserLanguagePreference($_SESSION['user_id'] ?? null);
        
        $currencyCode = $currency ?: $userPreference['currency_format'] ?? 'XOF';
        
        // Set locale for proper formatting
        $locale = $this->getLocaleForLanguage($language);
        setlocale(LC_MONETARY, $locale);
        
        return money_format('%i', $amount, $locale) . ' ' . $currencyCode;
    }
    
    public function formatNumber($number, $language = null) {
        $language = $language ?: $this->currentLanguage;
        $userPreference = $this->getUserLanguagePreference($_SESSION['user_id'] ?? null);
        
        $numberFormat = $userPreference['number_format'] ?? 'en_US';
        
        $locale = $this->getLocaleForLanguage($language);
        
        switch ($numberFormat) {
            case 'fr_FR':
                return number_format($number, 2, ',', ' ');
            case 'de_DE':
                return number_format($number, 2, ',', '.');
            case 'ja_JP':
                return number_format($number, 2, '.', ',');
            default:
                return number_format($number, 2, '.', ',');
        }
    }
    
    public function getAccessibilityClasses($userId = null) {
        $userId = $userId ?: $_SESSION['user_id'];
        $settings = $this->getAccessibilitySettings($userId);
        
        if (!$settings) {
            return [];
        }
        
        $classes = [];
        
        // Font size classes
        switch ($settings['font_size']) {
            case 'small':
                $classes[] = 'text-sm';
                break;
            case 'large':
                $classes[] = 'text-lg';
                break;
            case 'extra_large':
                $classes[] = 'text-xl';
                break;
        }
        
        // High contrast
        if ($settings['high_contrast']) {
            $classes[] = 'high-contrast';
        }
        
        // Reduced motion
        if ($settings['reduced_motion']) {
            $classes[] = 'reduce-motion';
        }
        
        // Screen reader optimized
        if ($settings['screen_reader_optimized']) {
            $classes[] = 'sr-optimized';
        }
        
        // Focus visible
        if ($settings['focus_visible']) {
            $classes[] = 'focus-visible';
        }
        
        // Color blind friendly
        if ($settings['color_blind_friendly'] !== 'none') {
            $classes[] = 'colorblind-' . $settings['color_blind_friendly'];
        }
        
        return $classes;
    }
    
    public function generateAltText($entityType, $entityData, $language = null) {
        $language = $language ?: $this->currentLanguage;
        
        $templates = [
            'image' => [
                'en' => 'Image: {title}',
                'fr' => 'Image : {title}',
                'ha' => 'Hotuna: {title}',
                'ar' => 'صورة: {title}'
            ],
            'video' => [
                'en' => 'Video: {title} ({duration})',
                'fr' => 'Vidéo : {title} ({duration})',
                'ha' => 'Bidiyo: {title} ({duration})',
                'ar' => 'فيديو: {title} ({duration})'
            ],
            'chart' => [
                'en' => 'Chart showing {description}',
                'fr' => 'Graphique montrant {description}',
                'ha' => 'Matsa mai nuna {description}',
                'ar' => 'رسم بياني يوضح {description}'
            ]
        ];
        
        $template = $templates[$entityType][$language] ?? $templates[$entityType]['en'];
        
        // Replace placeholders
        $altText = $template;
        foreach ($entityData as $key => $value) {
            $altText = str_replace('{' . $key . '}', $value, $altText);
        }
        
        return $altText;
    }
    
    public function logLanguageUsage($languageCode, $pageView = true) {
        $stmt = $this->pdo->prepare("
            INSERT INTO language_analytics 
            (language_code, date, page_views)
            VALUES (:language_code, CURDATE(), :page_views)
            ON DUPLICATE KEY UPDATE
            page_views = page_views + VALUES(page_views),
            updated_at = NOW()
        ");
        
        return $stmt->execute([
            'language_code' => $languageCode,
            'page_views' => $pageView ? 1 : 0
        ]);
    }
    
    public function getTranslationStatistics($languageCode = null) {
        $sql = "
            SELECT 
                l.code as language_code,
                l.name as language_name,
                COUNT(t.id) as total_translations,
                COUNT(CASE WHEN t.is_verified = TRUE THEN 1 END) as verified_translations,
                COUNT(CASE WHEN t.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_translations
            FROM languages l
            LEFT JOIN translations t ON l.code = t.language_code
            WHERE l.is_active = TRUE
        ";
        
        $params = [];
        
        if ($languageCode) {
            $sql .= " AND l.code = :language_code";
            $params['language_code'] = $languageCode;
        }
        
        $sql .= " GROUP BY l.code, l.name ORDER BY l.name ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Private helper methods
    private function getCurrentLanguage() {
        // Check user preference first
        if (isset($_SESSION['user_id'])) {
            $userPreference = $this->getUserLanguagePreference($_SESSION['user_id']);
            if ($userPreference) {
                return $userPreference['language_code'];
            }
        }
        
        // Check session language
        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }
        
        // Check browser language
        $browserLang = $this->getBrowserLanguage();
        if ($browserLang && $this->isLanguageSupported($browserLang)) {
            return $browserLang;
        }
        
        // Default to English
        return 'en';
    }
    
    private function getBrowserLanguage() {
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if (empty($acceptLanguage)) {
            return null;
        }
        
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $lang = trim($lang);
            $parts = explode(';', $lang);
            $languages[] = trim($parts[0]);
        }
        
        return $languages[0] ?? null;
    }
    
    private function isLanguageSupported($languageCode) {
        $stmt = $this->pdo->prepare("SELECT code FROM languages WHERE code = :code AND is_active = TRUE");
        $stmt->execute(['code' => $languageCode]);
        return $stmt->fetch() !== false;
    }
    
    private function loadTranslations() {
        $stmt = $this->pdo->prepare("
            SELECT translation_key, translation_value 
            FROM translations 
            WHERE language_code = :language_code 
            AND plural_form = 'other'
        ");
        $stmt->execute(['language_code' => $this->currentLanguage]);
        
        $results = $stmt->fetchAll();
        foreach ($results as $result) {
            $this->translations[$result['translation_key']] = $result['translation_value'];
        }
    }
    
    private function getTranslation($key, $language) {
        if ($language === $this->currentLanguage && isset($this->translations[$key])) {
            return $this->translations[$key];
        }
        
        $stmt = $this->pdo->prepare("
            SELECT translation_value 
            FROM translations 
            WHERE language_code = :language_code 
            AND translation_key = :translation_key 
            AND plural_form = 'other'
            LIMIT 1
        ");
        
        $stmt->execute([
            'language_code' => $language,
            'translation_key' => $key
        ]);
        
        $result = $stmt->fetch();
        return $result ? $result['translation_value'] : null;
    }
    
    private function replaceParams($text, $params) {
        foreach ($params as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
    
    private function getLocaleForLanguage($language) {
        $locales = [
            'en' => 'en_US',
            'fr' => 'fr_FR',
            'ha' => 'ha_NE',
            'dz' => 'dz_NE',
            'ar' => 'ar_MA',
            'es' => 'es_ES',
            'zh' => 'zh_CN'
        ];
        
        return $locales[$language] ?? 'en_US';
    }
}
