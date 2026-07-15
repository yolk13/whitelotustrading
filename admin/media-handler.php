<?php

$pageTitle = 'Media Library';

$files = [];
$uploadDir = UPLOAD_PATH;
if (is_dir($uploadDir)) {
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'];
    $iterator = new DirectoryIterator($uploadDir);
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), $allowedExt)) {
            $files[] = [
                'name' => $file->getFilename(),
                'path' => uploadUrl($file->getFilename()),
                'size' => $file->getSize(),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                'ext' => strtolower($file->getExtension()),
            ];
        }
    }
    usort($files, fn($a, $b) => strcmp($b['modified'], $a['modified']));
}

require_once BASE_PATH . 'includes/admin-header.php';
?>

<div class="flex justify-between items-end mb-6">
    <div>
        <nav class="flex gap-2 items-center text-on-surface-variant text-sm">
            <span>Admin</span>
            <span class="material-symbols-outlined text-xs">chevron_right</span>
            <span class="text-deep-royal font-medium">Media</span>
        </nav>
    </div>
    <p class="text-sm text-on-surface-variant"><?= count($files) ?> file<?= count($files) !== 1 ? 's' : '' ?></p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    <?php if (empty($files)): ?>
        <div class="col-span-full glass-card rounded-xl p-12 text-center border border-on-surface/5">
            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4">photo_library</span>
            <p class="font-body-md text-on-surface-variant">No media files found. Upload images through the page or blog editors.</p>
        </div>
    <?php else: ?>
        <?php foreach ($files as $file): ?>
        <div class="glass-card rounded-xl overflow-hidden border border-on-surface/5 group hover:shadow-md transition-all">
            <div class="aspect-square bg-surface-container-low flex items-center justify-center p-4">
                <?php if (in_array($file['ext'], ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                    <img src="<?= Security::h($file['path']) ?>" class="max-w-full max-h-full object-contain" alt="<?= Security::h($file['name']) ?>">
                <?php else: ?>
                    <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">insert_drive_file</span>
                <?php endif; ?>
            </div>
            <div class="p-3 border-t border-on-surface/5">
                <p class="text-xs truncate font-medium text-deep-royal" title="<?= Security::h($file['name']) ?>"><?= Security::h($file['name']) ?></p>
                <p class="text-[10px] text-on-surface-variant mt-1"><?= number_format($file['size'] / 1024, 1) ?> KB</p>
                <button class="mt-2 w-full text-xs bg-surface-container hover:bg-surface-container-high rounded-lg py-1.5 font-medium text-on-surface transition-colors" onclick="copyUrl('<?= Security::h($file['path']) ?>')">Copy URL</button>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function copyUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        var btn = event.target;
        var text = btn.textContent;
        btn.textContent = 'Copied!';
        setTimeout(function() { btn.textContent = text; }, 2000);
    });
}
</script>

<?php require_once BASE_PATH . 'includes/admin-footer.php'; ?>
