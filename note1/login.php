<?php
/**
 * api/login.php
 * Verifies credentials and starts a session.
 * POST JSON: { email, password }
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed.', 405);
}

$input = getJsonInput();

$email    = strtolower(cleanString($input['email'] ?? '', 150));
$password = (string) ($input['password'] ?? '');

if (!isValidEmailFormat($email)) {
    respondError('Please enter a valid email address.');
}
if ($password === '') {
    respondError('Please enter your password.');
}

$stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

// Deliberately generic error message — don't reveal whether the email exists.
if (!$user || !password_verify($password, $user['password_hash'])) {
    respondError('Incorrect email or password.', 401);
}

session_regenerate_id(true);
$_SESSION['user_id']   = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];

respondSuccess([
    'message' => 'Logged in successfully.',
    'user'    => ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email']],
]);
