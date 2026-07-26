<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// CSRF
function csrf_token(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}
function csrf_field(): string {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
}
function csrf_verify(): bool {
    $t = $_POST[CSRF_TOKEN_NAME] ?? '';
    return !empty($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $t);
}

// XSS escape
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Redirect
function redirect(string $path): void {
    header('Location: ' . APP_URL . $path);
    exit;
}

// Auth
function is_logged_in(): bool { return !empty($_SESSION['user_id']); }
function current_user_id(): ?int { return $_SESSION['user_id'] ?? null; }
function current_user_role(): string { return $_SESSION['role'] ?? 'user'; }
function require_login(): void { if (!is_logged_in()) redirect('/views/auth/login.php'); }
function require_admin(): void { require_login(); if (current_user_role() !== 'admin') redirect('/views/dashboard/index.php'); }

// Flash messages
function flash(string $key, ?string $msg = null) {
    if ($msg === null) {
        $v = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $v;
    }
    $_SESSION['_flash'][$key] = $msg;
}

// Validation
function valid_email(string $e): bool { return (bool) filter_var($e, FILTER_VALIDATE_EMAIL); }
