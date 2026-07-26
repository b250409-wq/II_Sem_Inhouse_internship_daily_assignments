<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/Contact.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) { flash('error','Invalid CSRF token'); redirect('/index.php#contact'); }
    $name = trim($_POST['name']); $email = trim($_POST['email']); $msg = trim($_POST['message']);
    if ($name && valid_email($email) && $msg) {
        (new Contact())->create($name, $email, $msg);
        flash('success','Message sent! We\'ll get back to you soon.');
    } else flash('error','Please fill all fields correctly.');
    redirect('/index.php#contact');
}
