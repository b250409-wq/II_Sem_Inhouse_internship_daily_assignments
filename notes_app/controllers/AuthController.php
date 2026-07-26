<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/User.php';

$action = $_GET['action'] ?? '';
$user = new User();

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { flash('error','Invalid CSRF token'); redirect('/views/auth/register.php'); }
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    if (strlen($username) < 3 || !valid_email($email) || strlen($pass) < 6 || $pass !== $confirm) {
        flash('error','Please check your input (min 3-char username, valid email, 6+ char matching passwords).');
        redirect('/views/auth/register.php');
    }
    if ($user->findByEmail($email)) { flash('error','Email already registered.'); redirect('/views/auth/register.php'); }
    $id = $user->create($username, $email, $pass);
    $_SESSION['user_id'] = $id;
    $_SESSION['role'] = 'user';
    $_SESSION['username'] = $username;
    flash('success','Welcome, ' . $username . '!');
    redirect('/views/dashboard/index.php');
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { flash('error','Invalid CSRF token'); redirect('/views/auth/login.php'); }
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $u = $user->findByEmail($email);
    if (!$u || !password_verify($pass, $u['password'])) {
        flash('error','Invalid email or password.');
        redirect('/views/auth/login.php');
    }
    session_regenerate_id(true);
    $_SESSION['user_id']  = (int)$u['id'];
    $_SESSION['role']     = $u['role'];
    $_SESSION['username'] = $u['username'];
    if (!empty($_POST['remember'])) {
        setcookie('remember', '1', time()+60*60*24*30, '/');
    }
    flash('success','Welcome back!');
    if ($u['role'] === 'admin') redirect('/views/admin/index.php');
    redirect('/views/dashboard/index.php');
}

if ($action === 'logout') {
    session_unset(); session_destroy();
    redirect('/index.php');
}
