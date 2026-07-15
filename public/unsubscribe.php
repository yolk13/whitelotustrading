<?php

$pageTitle = 'Unsubscribe | White Lotus Trading';
$message = '';
$unsubscribed = false;

$email = Security::sanitize($_GET['email'] ?? '');
$token = $_GET['token'] ?? '';

if ($email && $token) {
    $expected = hash_hmac('sha256', $email, 'whitelotus-unsubscribe-secret');
    if (hash_equals($expected, $token)) {
        $sub = Database::fetch("SELECT id FROM subscribers WHERE email = ?", [$email]);
        if ($sub) {
            Database::update('subscribers', ['status' => 'unsubscribed'], 'id = ?', [$sub['id']]);
            $unsubscribed = true;
            $message = "You have been unsubscribed successfully.";
        } else {
            $message = "Email not found in our subscribers list.";
        }
    } else {
        $message = "Invalid unsubscribe link.";
    }
}

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-2xl mx-auto text-center">
    <div class="glass-card rounded-2xl p-12">
        <span class="material-symbols-outlined text-6xl <?= $unsubscribed ? 'text-emerald-500' : 'text-on-surface-variant/30' ?> mb-4">
            <?= $unsubscribed ? 'check_circle' : 'mail_outline' ?>
        </span>
        <h1 class="font-headline-md text-headline-md text-deep-royal mb-4">
            <?= $unsubscribed ? 'Unsubscribed' : 'Unsubscribe' ?>
        </h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant mb-8"><?= Security::h($message) ?></p>
        <a href="/" class="inline-block bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all">Back to Home</a>
    </div>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
