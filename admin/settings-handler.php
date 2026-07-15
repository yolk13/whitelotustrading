<?php

$pageTitle = 'Home Page Settings';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token.';
    } else {
        $fields = ['hero_label', 'hero_title', 'hero_subtitle', 'hero_btn_1_text', 'hero_btn_1_link', 'hero_btn_2_text', 'hero_btn_2_link', 'hero_image_placeholder'];
        foreach ($fields as $key) {
            $value = $_POST[$key] ?? '';
            if ($key === 'hero_title' || $key === 'hero_subtitle') {
                $value = Security::sanitizeRich($value);
            } else {
                $value = Security::sanitize($value);
            }
            $existing = Database::fetch("SELECT key FROM settings WHERE key = ?", [$key]);
            if ($existing) {
                Database::update('settings', ['value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 'key = ?', [$key]);
            } else {
                Database::insert('settings', ['key' => $key, 'value' => $value]);
            }
        }
        $message = 'Settings saved successfully.';
    }
}

$settings = [];
$rows = Database::fetchAll("SELECT key, value FROM settings");
foreach ($rows as $row) {
    $settings[$row['key']] = $row['value'];
}

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<div class="glass-card rounded-xl border border-on-surface/5 p-8 max-w-3xl">
    <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-2">Hero Section</h3>
    <p class="text-sm text-on-surface-variant mb-6">Edit the main hero banner on the home page.</p>
    <form method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Label</label>
                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_label" value="<?= Security::h($settings['hero_label'] ?? 'Established Excellence') ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Image Placeholder Text</label>
                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_image_placeholder" value="<?= Security::h($settings['hero_image_placeholder'] ?? 'Hero Image') ?>">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Title (HTML allowed)</label>
            <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_title" value="<?= Security::h($settings['hero_title'] ?? 'Your Trusted Partner in HVAC & Wellness') ?>">
        </div>
        <div>
            <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Subtitle (HTML allowed)</label>
            <textarea class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_subtitle" rows="3"><?= Security::h($settings['hero_subtitle'] ?? '') ?></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4 border-t border-divider-gray pt-6">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Button 1 Text</label>
                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_btn_1_text" value="<?= Security::h($settings['hero_btn_1_text'] ?? 'Explore Industrial') ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Button 1 Link</label>
                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_btn_1_link" value="<?= Security::h($settings['hero_btn_1_link'] ?? '/products') ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Button 2 Text</label>
                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_btn_2_text" value="<?= Security::h($settings['hero_btn_2_text'] ?? 'Wellness Shop') ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Button 2 Link</label>
                <input class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_btn_2_link" value="<?= Security::h($settings['hero_btn_2_link'] ?? '/products?division=Consumables') ?>">
            </div>
        </div>
        <div class="flex gap-4 pt-4">
            <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md">Save Settings</button>
            <a href="/" class="px-6 py-3 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all" target="_blank">Preview Homepage</a>
        </div>
    </form>
</div>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
