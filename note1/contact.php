<?php
/**
 * api/contact.php
 * Stores a contact form submission in the database.
 * POST JSON: { name, email, message }
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Method not allowed.', 405);
}

$input = getJsonInput();

$name    = cleanString($input['name'] ?? '', 100);
$email   = cleanString($input['email'] ?? '', 150);
$message = cleanString($input['message'] ?? '', 2000);

if ($name === '') {
    respondError('Please enter your name.');
}
if (!isValidEmailFormat($email)) {
    respondError('Please enter a valid email address.');
}
if (strlen($message) < 10) {
    respondError('Please enter a message (at least 10 characters).');
}

$stmt = $pdo->prepare(
    'INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)'
);
$stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);

respondSuccess(['message' => "Thanks, $name! Your message has been received."], 201);
