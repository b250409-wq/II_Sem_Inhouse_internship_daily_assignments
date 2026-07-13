<?php
/**
 * api/notes_clear.php
 * Deletes every note belonging to the logged-in user.
 * POST (no body needed)
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed.', 405);
}

$userId = requireLogin();

$stmt = $pdo->prepare('DELETE FROM notes WHERE user_id = :user_id');
$stmt->execute(['user_id' => $userId]);

respondSuccess(['message' => 'All notes cleared.']);
