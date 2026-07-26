<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Note.php';
require_login();

$note = new Note();
$uid  = current_user_id();
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    flash('error','Invalid CSRF token'); redirect('/views/dashboard/notes.php');
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? ''); $content = $_POST['content'] ?? '';
    $tags  = $_POST['tags'] ?? [];
    if ($title === '') { flash('error','Title required'); redirect('/views/dashboard/note-form.php'); }
    $note->create($uid, $title, $content, $tags);
    flash('success','Note created');
    redirect('/views/dashboard/notes.php');
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $note->update($id, $uid, trim($_POST['title']), $_POST['content'] ?? '', $_POST['tags'] ?? []);
    flash('success','Note updated');
    redirect('/views/dashboard/notes.php');
}

if ($action === 'toggle') {
    $id = (int)($_GET['id'] ?? 0); $field = $_GET['field'] ?? '';
    $note->toggle($id, $uid, $field);
    redirect($_GET['back'] ?? '/views/dashboard/notes.php');
}
if ($action === 'delete') {
    $note->softDelete((int)$_GET['id'], $uid); flash('success','Note archived to trash');
    redirect($_GET['back'] ?? '/views/dashboard/notes.php');
}
if ($action === 'restore') {
    $note->restore((int)$_GET['id'], $uid);
    redirect('/views/dashboard/archive.php');
}
if ($action === 'export') {
    $notes = $note->forUser($uid, []);
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="notes.json"');
    echo json_encode($notes, JSON_PRETTY_PRINT); exit;
}
