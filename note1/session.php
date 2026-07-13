<?php
/**
 * api/session.php
 * Reports whether a user is currently logged in.
 * GET
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

$userId = currentUserId();

if ($userId === null) {
    respondSuccess(['loggedIn' => false]);
}

$stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    // Stale session pointing at a deleted user — clear it.
    $_SESSION = [];
    session_destroy();
    respondSuccess(['loggedIn' => false]);
}

respondSuccess([
    'loggedIn' => true,
    'user'     => ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email']],
]);
