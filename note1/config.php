<?php
/**
 * config.php
 * -----------
 * Central configuration for the Notes App backend.
 * Edit the constants below to match your local / server MySQL setup.
 */

// ---- Database credentials -------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'notes_app');
define('DB_USER', 'root');
define('DB_PASS', '');        // set your MySQL password here
define('DB_CHARSET', 'utf8mb4');

// ---- App settings -----------------------------------------------------------
define('APP_ENV', 'development'); // 'development' shows detailed errors, 'production' hides them

// ---- Session / cookie hardening --------------------------------------------
// Must run before session_start() is called anywhere.
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
