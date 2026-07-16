<?php

$pageTitle = 'Home Page Settings';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateCsrf($_POST['csrf_token'] ?? null)) {
        $message = 'Invalid security token.';
    } else {
        $textFields = ['hero_label', 'hero_title', 'hero_subtitle', 'hero_btn_1_text', 'hero_btn_1_link', 'hero_btn_2_text', 'hero_btn_2_link', 'hero_image_placeholder'];
        foreach ($textFields as $key) {
            if (!isset($_POST[$key])) continue;
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

        $imageKeys = ['hero_image', 'global_map_image', 'site_logo', 'favicon'];
        foreach ($imageKeys as $key) {
            if (!empty($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $filename = Security::saveUpload($_FILES[$key]);
                if ($filename) {
                    Database::update('settings', ['value' => $filename, 'updated_at' => date('Y-m-d H:i:s')], 'key = ?', [$key]);
                }
            }
            if (isset($_POST[$key . '_remove']) && $_POST[$key . '_remove'] === '1') {
                Database::update('settings', ['value' => '', 'updated_at' => date('Y-m-d H:i:s')], 'key = ?', [$key]);
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

$csrfToken = Security::generateCsrfToken();

require_once BASE_PATH . 'includes/admin-header.php';
?>

<?php if ($message): ?>
    <div class="bg-primary-fixed text-deep-royal px-6 py-3 rounded-lg font-body-md" style="background-color: #dbe1ff;"><?= Security::h($message) ?></div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
    <div class="lg:col-span-7 space-y-gutter">
        <div class="glass-card rounded-xl border border-on-surface/5 p-8">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-2">Hero Section</h3>
            <p class="text-sm text-on-surface-variant mb-6">Edit the main hero banner on the home page.</p>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
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
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Title</label>
                    <textarea id="hero-title-editor" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_title" rows="4"><?= Security::h($settings['hero_title'] ?? 'Your Trusted Partner in <span class="text-vibrant-amber">HVAC & Wellness</span>') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-1">Subtitle</label>
                    <textarea id="hero-subtitle-editor" class="w-full border border-divider-gray rounded-lg p-3 focus:ring-2 focus:ring-deep-royal focus:outline-none" name="hero_subtitle" rows="4"><?= Security::h($settings['hero_subtitle'] ?? '') ?></textarea>
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
                <div class="border-t border-divider-gray pt-6">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Hero Background Image</label>
                    <?php if (!empty($settings['hero_image'])): ?>
                        <div class="flex items-center gap-4 mb-3">
                            <img src="<?= uploadUrl($settings['hero_image']) ?>" class="w-32 h-24 object-cover rounded-lg border border-divider-gray">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant hover:text-deep-royal transition-colors">
                                <input type="checkbox" name="hero_image_remove" value="1" class="rounded border-divider-gray text-deep-royal focus:ring-deep-royal">
                                Remove image
                            </label>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="hero_image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-deep-royal file:text-pure-white hover:file:brightness-110 transition-all">
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md">Save Settings</button>
                    <a href="/" class="px-6 py-3 rounded-lg font-label-caps border border-divider-gray hover:bg-surface-container transition-all" target="_blank">Preview Homepage</a>
                </div>
            </form>
        </div>

        <div class="glass-card rounded-xl border border-on-surface/5 p-8">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-2">Global Map Section</h3>
            <p class="text-sm text-on-surface-variant mb-6">Image shown in the "Global Reach, Local Expertise" section.</p>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Section Image</label>
                    <?php if (!empty($settings['global_map_image'])): ?>
                        <div class="flex items-center gap-4 mb-3">
                            <img src="<?= uploadUrl($settings['global_map_image']) ?>" class="w-32 h-24 object-cover rounded-lg border border-divider-gray">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant hover:text-deep-royal transition-colors">
                                <input type="checkbox" name="global_map_image_remove" value="1" class="rounded border-divider-gray text-deep-royal focus:ring-deep-royal">
                                Remove image
                            </label>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="global_map_image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-deep-royal file:text-pure-white hover:file:brightness-110 transition-all">
                </div>
                <div>
                    <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-5 space-y-gutter">
        <div class="glass-card rounded-xl border border-on-surface/5 p-8">
            <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-2">Site Identity</h3>
            <p class="text-sm text-on-surface-variant mb-6">Manage your site logo and favicon.</p>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Site Logo</label>
                    <?php if (!empty($settings['site_logo'])): ?>
                        <div class="flex items-center gap-4 mb-3">
                            <img src="<?= uploadUrl($settings['site_logo']) ?>" class="h-12 object-contain rounded-lg border border-divider-gray p-1">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant hover:text-deep-royal transition-colors">
                                <input type="checkbox" name="site_logo_remove" value="1" class="rounded border-divider-gray text-deep-royal focus:ring-deep-royal">
                                Remove image
                            </label>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="site_logo" accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml" class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-deep-royal file:text-pure-white hover:file:brightness-110 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Favicon</label>
                    <?php if (!empty($settings['favicon'])): ?>
                        <div class="flex items-center gap-4 mb-3">
                            <img src="<?= uploadUrl($settings['favicon']) ?>" class="w-8 h-8 object-contain rounded border border-divider-gray">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant hover:text-deep-royal transition-colors">
                                <input type="checkbox" name="favicon_remove" value="1" class="rounded border-divider-gray text-deep-royal focus:ring-deep-royal">
                                Remove image
                            </label>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="favicon" accept="image/jpeg,image/png,image/gif,image/webp,image/x-icon,image/svg+xml" class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-deep-royal file:text-pure-white hover:file:brightness-110 transition-all">
                </div>
                <div>
                    <button type="submit" class="bg-deep-royal text-pure-white px-8 py-3 rounded-lg font-label-caps hover:brightness-110 transition-all shadow-md">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editors = ['hero-title-editor', 'hero-subtitle-editor'];
    editors.forEach(function(id) {
        const el = document.getElementById(id);
        if (el) {
            tinymce.init({
                target: el,
                height: 200,
                menubar: false,
                plugins: 'link image code lists',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code | removeformat',
                forced_root_block: false,
                images_upload_url: '/admin/upload-image',
                images_upload_handler: function(blobInfo, progress) {
                    return new Promise(function(resolve, reject) {
                        var xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '/admin/upload-image');
                        var formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.onload = function() {
                            if (xhr.status !== 200) {
                                reject('Upload failed: ' + xhr.status);
                                return;
                            }
                            var json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location !== 'string') {
                                reject('Invalid upload response');
                                return;
                            }
                            resolve(json.location);
                        };
                        xhr.onerror = function() {
                            reject('Upload error');
                        };
                        xhr.send(formData);
                    });
                },
                content_style: 'body { font-family: system-ui, sans-serif; font-size: 16px; line-height: 1.6; color: #1a1a2e; }',
                valid_elements: '*[*]',
                extended_valid_elements: 'span[*],img[*]',
            });
        }
    });
});
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
