<?php
// =========================================
// Application Configuration
// =========================================
define('APP_NAME', 'Notes App with Tags');
define('APP_URL', 'http://localhost/notes_app');
define('APP_ROOT', dirname(__DIR__));

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'notes_app');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Security
define('SESSION_LIFETIME', 60 * 60 * 2); // 2 hours
define('CSRF_TOKEN_NAME', '_csrf');

// Uploads
define('UPLOAD_DIR', APP_ROOT . '/assets/uploads/');
define('UPLOAD_URL', APP_URL . '/assets/uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB

date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', 1);
