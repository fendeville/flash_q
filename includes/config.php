<?php
// Application configuration
ini_set('display_errors', 0);
error_reporting(0);

define('APP_NAME', 'Flash.Q');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'The best queue management system');
define('APP_AUTHOR', 'WatchDog.E');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'flash_oversight');
define('DB_USER', 'Elonge_neville');
define('DB_PASS', '741074');

// Default timezone
date_default_timezone_set('Africa/Douala');

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseDir = dirname($_SERVER['PHP_SELF']);
$baseDir = ($baseDir == '/' || $baseDir == '\\') ? '' : $baseDir;
define('BASE_URL', $protocol . $host . $baseDir);
?>