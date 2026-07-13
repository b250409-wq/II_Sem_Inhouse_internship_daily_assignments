<?php
/**
 * api/register.php
 * Creates a new user account and logs them in immediately.
 * POST JSON: { name, email, password }
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed.', 405);
}

$input = getJsonInput();

$name     = cleanString($input['name'] ?? '', 100);
$email    = strtolower(cleanString($input['email'] ?? '', 150));
$password = (string) ($input['password'] ?? '');

if ($name === '') {
    respondError('Please enter your name.');
}
if (!isValidEmailFormat($email)) {
    respondError('Please enter a valid email address.');
}
if (strlen($password) < 6) {
    respondError('Password must be at least 6 characters.');
}

// Check for an existing account with this email.
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    respondError('An account with that email already exists.', 409);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
);
$stmt->execute([
    'name'          => $name,
    'email'         => $email,
    'password_hash' => $passwordHash,
]);

$userId = (int) $pdo->lastInsertId();

// Log the new user in right away.
session_regenerate_id(true);
$_SESSION['user_id']   = $userId;
$_SESSION['user_name'] = $name;

respondSuccess([
    'message' => 'Account created successfully.',
    'user'    => ['id' => $userId, 'name' => $name, 'email' => $email],
], 201);
