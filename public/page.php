<?php

$slug = $params['slug'] ?? '';
$page = Page::findBySlug($slug);

if (!$page || $page['status'] !== 'published') {
    http_response_code(404);
    $pageTitle = 'Page Not Found | White Lotus Trading';
    require_once BASE_PATH . 'includes/header.php';
    echo '<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto text-center"><h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal mb-4">404</h1><p class="font-body-lg text-on-surface-variant mb-8">The page you are looking for does not exist.</p><a href="/" class="bg-deep-royal text-pure-white px-8 py-4 rounded-xl font-label-caps inline-block hover:bg-vibrant-amber hover:text-deep-royal transition-all">Back to Home</a></section>';
    require_once BASE_PATH . 'includes/footer.php';
    exit;
}

$pageTitle = Security::h($page['title']) . ' | White Lotus Trading';
$metaDescription = Security::h($page['meta_description'] ?? '');

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="mb-12">
        <span class="font-label-caps text-label-caps text-vibrant-amber uppercase tracking-widest block mb-2"><?= Security::h($page['title']) ?></span>
        <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal"><?= Security::h($page['title']) ?></h1>
    </div>
    <div class="prose max-w-3xl text-body-lg text-on-surface-variant space-y-6">
        <?= $page['content'] ?>
    </div>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
