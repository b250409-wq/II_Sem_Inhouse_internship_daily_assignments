<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/User.php';
require_login();

$user = new User(); $uid = current_user_id();
$action = $_GET['action'] ?? '';
if ($_SERVER['REQUEST_METHOD']==='POST' && !csrf_verify()) { flash('error','CSRF'); redirect('/views/dashboard/profile.php'); }

if ($action === 'update' && $_SERVER['REQUEST_METHOD']==='POST') {
    $data = ['username'=>trim($_POST['username']), 'email'=>trim($_POST['email']), 'theme'=>$_POST['theme'] ?? 'system'];
    if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['size'] <= MAX_UPLOAD_SIZE) {
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
            $name = 'avatar_' . $uid . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['avatar']['tmp_name'], UPLOAD_DIR . $name);
            $data['avatar'] = $name;
        }
    }
    $user->update($uid, $data);
    $_SESSION['username'] = $data['username'];
    flash('success','Profile updated');
    redirect('/views/dashboard/profile.php');
}
if ($action === 'password' && $_SERVER['REQUEST_METHOD']==='POST') {
    $u = $user->findById($uid);
    if (!password_verify($_POST['current'] ?? '', $u['password'])) {
        flash('error','Current password wrong'); redirect('/views/dashboard/profile.php');
    }
    if (strlen($_POST['new'] ?? '') < 6 || $_POST['new'] !== $_POST['confirm']) {
        flash('error','Passwords do not match or too short'); redirect('/views/dashboard/profile.php');
    }
    $user->updatePassword($uid, $_POST['new']);
    flash('success','Password changed');
    redirect('/views/dashboard/profile.php');
}
