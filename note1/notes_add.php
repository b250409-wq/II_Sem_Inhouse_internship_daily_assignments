<?php
/**
 * api/notes_add.php
 * Creates a new note for the logged-in user.
 * POST JSON: { title, body }
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed.', 405);
}

$userId = requireLogin();
$input  = getJsonInput();

$title = cleanString($input['title'] ?? '', 60);
$body  = cleanString($input['body'] ?? '', 300);

if ($title === '') {
    respondError('Please enter a title.');
}
if ($body === '') {
    respondError('Please write some note content.');
}

$stmt = $pdo->prepare(
    'INSERT INTO notes (user_id, title, body) VALUES (:user_id, :title, :body)'
);
$stmt->execute(['user_id' => $userId, 'title' => $title, 'body' => $body]);

$noteId = (int) $pdo->lastInsertId();

$stmt = $pdo->prepare('SELECT id, title, body, created_at, updated_at FROM notes WHERE id = :id');
$stmt->execute(['id' => $noteId]);
$note = $stmt->fetch();

respondSuccess(['message' => 'Note added successfully.', 'note' => $note], 201);
