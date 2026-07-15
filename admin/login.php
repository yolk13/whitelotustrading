<?php

$error = Session::get('login_error');
Session::remove('login_error');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Security::sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (Auth::attempt($username, $password)) {
        $redirect = Session::get('redirect_after_login', '/admin');
        Session::remove('redirect_after_login');
        redirect($redirect);
    } else {
        $error = Session::get('login_error');
        Session::remove('login_error');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | White Lotus Trading</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap" rel="stylesheet">
<style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
</style>
</head>
<body class="min-h-screen bg-surface font-body-md flex items-center justify-center p-4" style="background-color: #fcf9f8;">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <h1 class="font-display-lg text-headline-md text-deep-royal font-bold" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 30px; color: #002366;">WL Admin</h1>
        <p class="font-label-caps text-on-surface-variant mt-2" style="font-family: Inter; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase;">Control Center Login</p>
    </div>
    <div class="glass-card p-8 rounded-2xl" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px); border: 1px solid rgba(26,26,26,0.1); border-radius: 0.5rem;">
        <?php if ($error): ?>
            <div class="bg-error-container text-error px-4 py-3 rounded-lg mb-6 font-body-md" style="background-color: #ffdad6; color: #ba1a1a;"><?= Security::h($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2" style="font-family: Inter; letter-spacing: 0.05em;">Username</label>
                <input type="text" name="username" value="<?= Security::h($_POST['username'] ?? '') ?>" class="w-full border px-4 py-3 rounded-lg focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all" style="border-color: #F8F9FA; background: #ffffff;" placeholder="Enter username" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2" style="font-family: Inter; letter-spacing: 0.05em;">Password</label>
                <input type="password" name="password" class="w-full border px-4 py-3 rounded-lg focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all" style="border-color: #F8F9FA; background: #ffffff;" placeholder="Enter password" required>
            </div>
            <button type="submit" class="w-full bg-vibrant-amber text-charcoal-text py-3 rounded-lg font-bold font-label-caps hover:brightness-105 transition-all active:scale-95 shadow-sm" style="background-color: #FFBF00; color: #1A1A1A; font-family: Inter; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase;">
                Sign In
            </button>
        </form>
    </div>
    <p class="text-center mt-8 font-label-caps text-on-surface-variant" style="font-size: 11px;">
        <a href="/" class="hover:text-deep-royal transition-colors">&larr; Back to Website</a>
    </p>
</div>
</body>
</html>
