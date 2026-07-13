<?php
/**
 * api/notes_delete.php
 * Deletes a single note owned by the logged-in user.
 * POST JSON: { id }
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed.', 405);
}

$userId = requireLogin();
$input  = getJsonInput();

$id = isset($input['id']) ? (int) $input['id'] : 0;
if ($id <= 0) {
    respondError('Invalid note.');
}

$stmt = $pdo->prepare('DELETE FROM notes WHERE id = :id AND user_id = :user_id');
$stmt->execute(['id' => $id, 'user_id' => $userId]);

if ($stmt->rowCount() === 0) {
    respondError('That note could not be found.', 404);
}

respondSuccess(['message' => 'Note deleted.']);
