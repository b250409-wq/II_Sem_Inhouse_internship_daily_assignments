<?php
/**
 * api/notes_list.php
 * Returns all notes belonging to the logged-in user, newest first.
 * GET, optional ?q=searchTerm to filter by title/body server-side too.
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

$userId = requireLogin();

$q = isset($_GET['q']) ? cleanString($_GET['q'], 100) : '';

if ($q !== '') {
    $stmt = $pdo->prepare(
        'SELECT id, title, body, created_at, updated_at FROM notes
         WHERE user_id = :user_id AND (title LIKE :q OR body LIKE :q)
         ORDER BY created_at DESC'
    );
    $stmt->execute(['user_id' => $userId, 'q' => '%' . $q . '%']);
} else {
    $stmt = $pdo->prepare(
        'SELECT id, title, body, created_at, updated_at FROM notes
         WHERE user_id = :user_id
         ORDER BY created_at DESC'
    );
    $stmt->execute(['user_id' => $userId]);
}

$notes = $stmt->fetchAll();

respondSuccess(['notes' => $notes]);
