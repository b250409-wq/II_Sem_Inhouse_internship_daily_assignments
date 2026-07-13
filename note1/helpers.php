<?php
/**
 * helpers.php
 * ------------
 * Shared helpers used by every endpoint under /api.
 * Include this AFTER db.php:
 *   require_once __DIR__ . '/../db.php';
 *   require_once __DIR__ . '/helpers.php';
 */

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Sends a JSON response and stops execution. */
function respond($data, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/** Sends a JSON success response. */
function respondSuccess(array $data = [], int $statusCode = 200): void {
    respond(array_merge(['success' => true], $data), $statusCode);
}

/** Sends a JSON error response. */
function respondError(string $message, int $statusCode = 400): void {
    respond(['success' => false, 'error' => $message], $statusCode);
}

/** Reads and JSON-decodes the raw request body into an associative array. */
function getJsonInput(): array {
    $raw = file_get_contents('php://input');
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

/** Returns the logged-in user's id, or null if no one is logged in. */
function currentUserId(): ?int {
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

/** Halts the request with 401 unless a user is logged in. Returns the user id. */
function requireLogin(): int {
    $userId = currentUserId();
    if ($userId === null) {
        respondError('You must be logged in to do that.', 401);
    }
    return $userId;
}

/** Basic trimmed-string validator with a max length. */
function cleanString($value, int $maxLength = 255): string {
    $value = is_string($value) ? trim($value) : '';
    if (function_exists('mb_substr')) {
        $value = mb_substr($value, 0, $maxLength);
    } else {
        $value = substr($value, 0, $maxLength);
    }
    return $value;
}

function isValidEmailFormat(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
