<?php
/**
 * api/notes_update.php
 * Updates a note's title/body. A user may only edit their own notes.
 * POST JSON: { id, title, body }
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed.', 405);
}

$userId = requireLogin();
$input  = getJsonInput();

$id    = isset($input['id']) ? (int) $input['id'] : 0;
$title = cleanString($input['title'] ?? '', 60);
$body  = cleanString($input['body'] ?? '', 300);

if ($id <= 0) {
    respondError('Invalid note.');
}
if ($title === '') {
    respondError('Please enter a title.');
}
if ($body === '') {
    respondError('Please enter note content.');
}

// Ensure the note exists AND belongs to this user.
$stmt = $pdo->prepare('SELECT id FROM notes WHERE id = :id AND user_id = :user_id LIMIT 1');
$stmt->execute(['id' => $id, 'user_id' => $userId]);
if (!$stmt->fetch()) {
    respondError('That note could not be found.', 404);
}

$stmt = $pdo->prepare(
    'UPDATE notes SET title = :title, body = :body, updated_at = NOW()
     WHERE id = :id AND user_id = :user_id'
);
$stmt->execute(['title' => $title, 'body' => $body, 'id' => $id, 'user_id' => $userId]);

$stmt = $pdo->prepare('SELECT id, title, body, created_at, updated_at FROM notes WHERE id = :id');
$stmt->execute(['id' => $id]);
$note = $stmt->fetch();

respondSuccess(['message' => 'Note updated successfully.', 'note' => $note]);
