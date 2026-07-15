<?php

require_once __DIR__ . '/../core/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/login');
}

if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
    Session::set('login_error', 'Invalid security token.');
    redirect('/admin/login');
}

Auth::logout();
redirect('/admin/login');
