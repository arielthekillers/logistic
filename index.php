<?php

// Set Default Timezone
date_default_timezone_set('Asia/Jakarta');

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load Environment Variables from .env
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, "\"' ");
        putenv("{$name}={$value}");
        $_ENV[$name] = $value;
    }
}

// Set Environment Mode
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'production');
}

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// PSR-4 Style Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

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

// Include Base Helpers
require_once __DIR__ . '/helpers/utilities.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/helpers/layout.php';
require_once __DIR__ . '/helpers/sidebar_layout.php';

// Run App Framework Core
require_once __DIR__ . '/app/Core/App.php';
$app = new \App\Core\App();
$app->run();
