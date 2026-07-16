<?php

$pageTitle = 'Inquiry | White Lotus Trading';
$sent = isset($_GET['sent']);
$prefillSubject = Security::sanitize($_GET['subject'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_subscribe'])) {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $GLOBALS['errors']['csrf'] = 'Invalid security token. Please try again.';
    } elseif (!empty($_POST['website'])) {
        $GLOBALS['errors']['spam'] = 'Spam detected.';
    } elseif (!Security::checkRateLimit('subscribe', SUBSCRIBE_RATE_LIMIT_MAX, SUBSCRIBE_RATE_LIMIT_WINDOW_MINUTES)) {
        http_response_code(429);
        $GLOBALS['errors']['rate'] = 'Too many subscribe attempts. Please try again later.';
    } else {
        $validator = new Validator();
        if ($validator->validate($_POST, ['email' => ['required', 'email']])) {
            Security::hitRateLimit('subscribe', SUBSCRIBE_RATE_LIMIT_WINDOW_MINUTES);
            $email = Security::sanitize($_POST['email']);
            $existing = Database::fetch("SELECT id, status FROM subscribers WHERE email = ?", [$email]);
            if ($existing) {
                if ($existing['status'] !== 'active') {
                    Database::update('subscribers', ['status' => 'active'], 'id = ?', [$existing['id']]);
                }
            } else {
                Database::insert('subscribers', ['email' => $email]);
            }
            flash('success', 'Thank you for subscribing!');
        }
    }
    redirectBack();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['quick_subscribe'])) {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $GLOBALS['errors']['csrf'] = 'Invalid security token. Please try again.';
    } elseif (!empty($_POST['website'])) {
        $GLOBALS['errors']['spam'] = 'Spam detected.';
    } elseif (!Security::checkRateLimit('inquiry', INQUIRY_RATE_LIMIT_MAX, INQUIRY_RATE_LIMIT_WINDOW_MINUTES)) {
        http_response_code(429);
        $GLOBALS['errors']['rate'] = 'You\'ve reached the submission limit. Please try again in a few minutes.';
    } else {
        Security::hitRateLimit('inquiry', INQUIRY_RATE_LIMIT_WINDOW_MINUTES);
        $validator = new Validator();
            $rules = [
                'name' => ['required', 'min:2', 'max:100'],
                'email' => ['required', 'email'],
                'phone' => ['phone'],
                'company' => ['max:200'],
                'division' => ['required', 'in:HVAC,Consumables,General'],
                'subject' => ['required', 'min:3', 'max:200'],
                'message' => ['required', 'min:10', 'max:5000'],
            ];

            if ($validator->validate($_POST, $rules)) {
                $inquiryData = [
                    'name' => Security::sanitize($_POST['name']),
                    'email' => Security::sanitize($_POST['email']),
                    'phone' => Security::sanitize($_POST['phone'] ?? ''),
                    'company' => Security::sanitize($_POST['company'] ?? ''),
                    'division' => $_POST['division'],
                    'subject' => Security::sanitize($_POST['subject']),
                    'message' => Security::sanitizeRich($_POST['message']),
                ];
                $email = Security::sanitize($_POST['email']);
                $existingCustomer = Database::fetch("SELECT id FROM customers WHERE email = ?", [$email]);
                if ($existingCustomer) {
                    Database::update('customers', [
                        'name' => Security::sanitize($_POST['name']),
                        'phone' => Security::sanitize($_POST['phone'] ?? ''),
                        'company' => Security::sanitize($_POST['company'] ?? ''),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ], 'id = ?', [$existingCustomer['id']]);
                    $inquiryData['customer_id'] = $existingCustomer['id'];
                } else {
                    $inquiryData['customer_id'] = Database::insert('customers', [
                        'email' => $email,
                        'name' => Security::sanitize($_POST['name']),
                        'phone' => Security::sanitize($_POST['phone'] ?? ''),
                        'company' => Security::sanitize($_POST['company'] ?? ''),
                    ]);
                }
                Inquiry::create($inquiryData);
                Mail::sendInquiryNotification($inquiryData);
                Mail::sendInquiryConfirmation($inquiryData);
                redirect('/inquiry?sent=1');
            } else {
                $GLOBALS['errors'] = $validator->errors();
            }
        }
    }

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="mb-12 text-center">
        <span class="font-label-caps text-label-caps text-vibrant-amber uppercase tracking-widest block mb-2">Get in Touch</span>
        <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal">Send Us an Inquiry</h1>
        <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mx-auto mt-4">Interested in our products or services? Fill out the form below and our team will get back to you within 24 hours.</p>
    </div>

    <?php if ($sent): ?>
        <div class="max-w-2xl mx-auto glass-card p-12 rounded-2xl text-center" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
            <span class="material-symbols-outlined text-6xl text-emerald-500 mb-4">check_circle</span>
            <h2 class="font-headline-md text-headline-md text-deep-royal mb-4">Inquiry Sent!</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant mb-8">Thank you for reaching out. Our team will review your inquiry and respond shortly.</p>
            <a href="/" class="inline-block bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:bg-vibrant-amber hover:text-deep-royal transition-all">Back to Home</a>
        </div>
    <?php else: ?>

    <?php if (!empty($GLOBALS['errors'])): ?>
        <div class="max-w-2xl mx-auto bg-error-container text-error px-6 py-4 rounded-lg font-body-md mb-8" style="background-color: #ffdad6; color: #ba1a1a;">
            <?php foreach ($GLOBALS['errors'] as $err): ?>
                <p><?= Security::h($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="max-w-2xl mx-auto glass-card p-8 md:p-12 rounded-2xl" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
        <form method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
            <div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Name <span class="text-error">*</span></label>
                    <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all <?= hasError('name') ? 'border-error' : '' ?>" name="name" value="<?= old('name') ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Email <span class="text-error">*</span></label>
                    <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all <?= hasError('email') ? 'border-error' : '' ?>" name="email" type="email" value="<?= old('email') ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Phone</label>
                    <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all" name="phone" value="<?= old('phone') ?>">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Company</label>
                    <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all" name="company" value="<?= old('company') ?>">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Division <span class="text-error">*</span></label>
                    <select class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all" name="division" required>
                        <option value="General" <?= selected(old('division'), 'General') ?>>General</option>
                        <option value="HVAC" <?= selected(old('division'), 'HVAC') ?>>HVAC</option>
                        <option value="Consumables" <?= selected(old('division'), 'Consumables') ?>>Consumables</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Subject <span class="text-error">*</span></label>
                    <input class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all <?= hasError('subject') ? 'border-error' : '' ?>" name="subject" value="<?= old('subject', $prefillSubject) ?>" required>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Message <span class="text-error">*</span></label>
                <textarea class="w-full border border-divider-gray rounded-lg px-4 py-3 focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all <?= hasError('message') ? 'border-error' : '' ?>" name="message" rows="6" required><?= old('message') ?></textarea>
            </div>
            <button type="submit" class="w-full bg-vibrant-amber text-charcoal-text px-8 py-4 rounded-xl font-headline-sm text-headline-sm hover:shadow-lg transition-all flex items-center justify-center gap-2">
                Submit Inquiry
                <span class="material-symbols-outlined">send</span>
            </button>
        </form>
    </div>
    <?php endif; ?>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
