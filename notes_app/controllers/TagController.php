<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Tag.php';
require_login();

$tag = new Tag(); $uid = current_user_id();
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    flash('error','Invalid CSRF token'); redirect('/views/dashboard/tags.php');
}
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); $color = $_POST['color'] ?? '#6366f1';
    if ($name) $tag->create($uid, $name, $color);
    redirect('/views/dashboard/tags.php');
}
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tag->update((int)$_POST['id'], $uid, trim($_POST['name']), $_POST['color']);
    redirect('/views/dashboard/tags.php');
}
if ($action === 'delete') {
    $tag->delete((int)$_GET['id'], $uid);
    redirect('/views/dashboard/tags.php');
}
