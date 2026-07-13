<?php
/**
 * db.php
 * -------
 * Creates and returns a single shared PDO connection using the
 * credentials in config.php. Include this file wherever you need
 * database access: `require_once __DIR__ . '/db.php';` then use $pdo.
 */

require_once __DIR__ . '/config.php';

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => APP_ENV === 'development'
            ? 'Database connection failed: ' . $e->getMessage()
            : 'Database connection failed. Please try again later.',
    ]);
    exit;
}
