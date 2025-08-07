<?php
// config/bootstrap.php

// --- 1. Basic Setup & Error Reporting ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Tehran');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/app/logs/php_errors.log');


// --- 2. Define Core Directory Paths ---
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('ROUTES_PATH', ROOT_PATH . '/routes');


// --- 3. Autoloading Classes (CORRECTED VERSION) ---
spl_autoload_register(function ($className) {
    // The fully qualified class name is like "App\Managers\DatabaseManager"
    // We need to strip the "App\" prefix to get the relative path inside the app directory.
    $prefix = 'App\\';
    if (strncmp($prefix, $className, strlen($prefix)) !== 0) {
        // Not a class from our App namespace, skip it.
        return;
    }
    
    // Get the relative class name (e.g., "Managers\DatabaseManager")
    $relativeClass = substr($className, strlen($prefix));

    // Replace namespace separators with directory separators
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass);
    
    // Construct the full path to the file.
    $file = APP_PATH . DIRECTORY_SEPARATOR . $classPath . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});


// --- 4. Load Application Settings ---
function load_settings(string $filePath): array {
    if (!file_exists($filePath)) {
        error_log("FATAL ERROR: Settings file not found at: " . $filePath);
        return [];
    }
    $jsonContent = file_get_contents($filePath);
    return json_decode($jsonContent, true) ?: [];
}
define('SETTINGS', load_settings(CONFIG_PATH . '/settings_data.json'));


// --- 5. Helper Functions ---
function setting(string $key, $default = null) {
    $keys = explode('.', $key);
    $value = SETTINGS;
    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }
    return $value;
}

function formatBytes(int $bytes, int $precision = 2): string
{
    if ($bytes === 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function formatTimestamp(int $timestamp_ms): string
{
    if ($timestamp_ms <= 0) {
        return 'نامحدود';
    }
    $timestamp_s = $timestamp_ms / 1000;
    return date('Y-m-d', $timestamp_s);
}