<?php

$slug = $slug ?? $_GET['slug'] ?? '';
$post = Post::findBySlug($slug);

if (!$post || $post['status'] !== 'published') {
    header('HTTP/1.0 404 Not Found');
    $pageTitle = 'Post Not Found';
    require_once BASE_PATH . 'includes/header.php';
    echo '<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto text-center"><h1 class="font-headline-md text-headline-md text-deep-royal mb-4">Post Not Found</h1><a href="/blog" class="text-vibrant-amber underline font-label-caps">&larr; Back to Blog</a></section>';
    require_once BASE_PATH . 'includes/footer.php';
    exit;
}

$pageTitle = Security::h($post['title']) . ' | White Lotus Trading';
$recentPosts = Post::recentPosts(5);

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <a href="/blog" class="inline-flex items-center gap-2 font-label-caps text-label-caps text-on-surface-variant hover:text-deep-royal transition-colors mb-8">
        <span class="material-symbols-outlined">arrow_back</span> Back to Blog
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
        <article class="lg:col-span-3">
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 rounded text-xs font-bold" style="background: <?= $post['category'] === 'Wellness' ? '#FFBF0033' : ($post['category'] === 'Industry News' ? '#00236610' : '#f0eded') ?>; color: <?= $post['category'] === 'Wellness' ? '#795900' : ($post['category'] === 'Industry News' ? '#002366' : '#444650') ?>;">
                        <?= Security::h($post['category']) ?>
                    </span>
                    <span class="text-sm text-on-surface-variant"><?= formatDate($post['published_at']) ?></span>
                </div>
                <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal leading-tight"><?= Security::h($post['title']) ?></h1>
            </div>

            <?php if ($post['featured_image']): ?>
                <div class="aspect-[21/9] w-full rounded-2xl overflow-hidden mb-10 bg-surface-container-low">
                    <img src="<?= uploadUrl($post['featured_image']) ?>" class="w-full h-full object-cover" alt="<?= Security::h($post['title']) ?>">
                </div>
            <?php endif; ?>

            <?php if ($post['excerpt']): ?>
                <p class="font-headline-sm text-headline-sm text-on-surface-variant mb-8 leading-relaxed italic"><?= Security::h($post['excerpt']) ?></p>
            <?php endif; ?>

            <div class="font-body-lg text-body-lg text-on-surface leading-loose space-y-4">
                <?= Security::h($post['content']) ?>
            </div>
        </article>

        <aside class="lg:col-span-1 space-y-8">
            <div class="glass-card p-6 rounded-xl" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
                <h4 class="font-headline-sm text-headline-sm text-deep-royal mb-4">Recent Posts</h4>
                <div class="space-y-4">
                    <?php foreach ($recentPosts as $rp): ?>
                        <a href="/blog/<?= Security::h($rp['slug']) ?>" class="block group">
                            <p class="font-body-md text-body-md text-deep-royal group-hover:text-vibrant-amber transition-colors font-medium"><?= Security::h($rp['title']) ?></p>
                            <p class="text-[11px] text-on-surface-variant mt-1"><?= formatDate($rp['published_at']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
