<?php

$pageTitle = 'Blog | White Lotus Trading';
$category = Security::sanitize($_GET['category'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$posts = Post::paginatePublished($page, 6, $category);
$categories = Post::categories();
$recentPosts = Post::recentPosts(5);

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="mb-12">
        <span class="font-label-caps text-label-caps text-vibrant-amber uppercase tracking-widest block mb-2">Insights & Updates</span>
        <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal">Our Blog</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-gutter">
        <div class="lg:col-span-3">
            <?php if (empty($posts['items'])): ?>
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4">rss_feed</span>
                    <p class="font-headline-sm text-headline-sm text-on-surface-variant mb-2">No posts yet</p>
                    <p class="font-body-md text-on-surface-variant">Check back soon for updates.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                    <?php foreach ($posts['items'] as $post): ?>
                    <a href="/blog/<?= Security::h($post['slug']) ?>" class="group bg-pure-white rounded-2xl border border-divider-gray overflow-hidden hover:shadow-xl transition-all">
                        <div class="aspect-[16/9] w-full bg-surface-container-low flex items-center justify-center overflow-hidden">
                            <?php if ($post['featured_image']): ?>
                                <img src="<?= uploadUrl($post['featured_image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="<?= Security::h($post['title']) ?>">
                            <?php else: ?>
                                <span class="material-symbols-outlined text-6xl text-on-surface-variant/20">article</span>
                            <?php endif; ?>
                        </div>
                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 rounded text-[10px] font-bold" style="background: <?= $post['category'] === 'Wellness' ? '#FFBF0033' : ($post['category'] === 'Industry News' ? '#00236610' : '#f0eded') ?>; color: <?= $post['category'] === 'Wellness' ? '#795900' : ($post['category'] === 'Industry News' ? '#002366' : '#444650') ?>;">
                                    <?= Security::h($post['category']) ?>
                                </span>
                                <span class="text-[11px] text-on-surface-variant"><?= formatDate($post['published_at']) ?></span>
                            </div>
                            <h3 class="font-headline-sm text-headline-sm text-deep-royal group-hover:text-vibrant-amber transition-colors"><?= Security::h($post['title']) ?></h3>
                            <?php if ($post['excerpt']): ?>
                                <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2"><?= Security::h($post['excerpt']) ?></p>
                            <?php endif; ?>
                            <span class="inline-flex items-center gap-1 font-label-caps text-label-caps text-deep-royal group-hover:text-vibrant-amber transition-colors">
                                Read More <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($posts['totalPages'] > 1): ?>
                <div class="mt-12 flex justify-center gap-2">
                    <?php if ($posts['hasPrev']): ?>
                        <a href="?page=<?= $posts['prevPage'] ?>&category=<?= urlencode($category) ?>" class="px-4 py-2 border border-divider-gray rounded-lg hover:bg-surface-container transition-colors font-label-caps">Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $posts['totalPages']; $i++): ?>
                        <a href="?page=<?= $i ?>&category=<?= urlencode($category) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-label-caps transition-colors <?= $i === $posts['page'] ? 'bg-deep-royal text-pure-white' : 'border border-divider-gray hover:bg-surface-container' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($posts['hasNext']): ?>
                        <a href="?page=<?= $posts['nextPage'] ?>&category=<?= urlencode($category) ?>" class="px-4 py-2 border border-divider-gray rounded-lg hover:bg-surface-container transition-colors font-label-caps">Next</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <aside class="lg:col-span-1 space-y-8">
            <div class="glass-card p-6 rounded-xl" style="background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);">
                <h4 class="font-headline-sm text-headline-sm text-deep-royal mb-4">Categories</h4>
                <div class="space-y-2">
                    <a href="/blog" class="block font-body-md <?= !$category ? 'text-deep-royal font-bold' : 'text-on-surface-variant hover:text-deep-royal' ?> transition-colors">All Categories</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/blog?category=<?= urlencode($cat['category']) ?>" class="block font-body-md <?= $category === $cat['category'] ? 'text-deep-royal font-bold' : 'text-on-surface-variant hover:text-deep-royal' ?> transition-colors"><?= Security::h($cat['category']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

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
