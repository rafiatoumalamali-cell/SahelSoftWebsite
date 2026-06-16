<?php

function getLang() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fr', 'ha'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    return $_SESSION['lang'] ?? 'en';
}

function loadLanguage($lang) {
    $file = APP_ROOT . "/app/Lang/$lang.php";
    if (file_exists($file)) {
        return require $file;
    }
    return [];
}

$GLOBALS['lang_data'] = loadLanguage(getLang());

function __($key) {
    global $lang_data;
    if (empty($lang_data)) {
        $lang_data = loadLanguage(getLang());
    }
    return $lang_data[$key] ?? $key;
}

if (!function_exists('base_url')) {
    function base_url($path = '') {
        $path = ltrim($path, '/');
        return rtrim(APP_URL, '/') . '/' . $path;
    }
}
if (!function_exists('getLangUrl')) {
    function getLangUrl($lang) {
        $params = $_GET;
        $params['lang'] = $lang;
        return '?' . http_build_query($params);
    }
}

// CSRF Protection Functions
if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('csrf_verify')) {
    function csrf_verify() {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}

// System Settings Helper
if (!function_exists('getSetting')) {
    function getSetting($key, $default = '') {
        static $settingsCache = null;
        if ($settingsCache === null) {
            $settingModel = new \App\Models\Setting();
            $settingsCache = $settingModel->getAllSettings();
        }
        return $settingsCache[$key] ?? $default;
    }
}

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $weeks = floor($diff->d / 7);
        $days = $diff->d - ($weeks * 7);

        $string = array(
            'y' => ['label' => 'year', 'value' => $diff->y],
            'm' => ['label' => 'month', 'value' => $diff->m],
            'w' => ['label' => 'week', 'value' => (int)$weeks],
            'd' => ['label' => 'day', 'value' => (int)$days],
            'h' => ['label' => 'hour', 'value' => $diff->h],
            'i' => ['label' => 'minute', 'value' => $diff->i],
            's' => ['label' => 'second', 'value' => $diff->s],
        );

        foreach ($string as $k => &$v) {
            if ($v['value']) {
                $v = $v['value'] . ' ' . __($v['label'] . ($v['value'] > 1 ? 's' : ''));
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ' . __('ago') : __('just_now');
    }
}
