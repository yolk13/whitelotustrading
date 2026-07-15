<?php

$pageTitle = 'Search | White Lotus Trading';
$query = Security::sanitize($_GET['q'] ?? '');
$results = [];

if ($query && strlen($query) >= 2) {
    $like = "%$query%";

    $products = Database::fetchAll(
        "SELECT name AS title, slug, description AS excerpt, 'product' AS type, updated_at AS date FROM products WHERE status = 'active' AND (name LIKE ? OR sku LIKE ? OR description LIKE ?)",
        [$like, $like, $like]
    );

    $posts = Database::fetchAll(
        "SELECT title, slug, excerpt, 'post' AS type, published_at AS date FROM posts WHERE status = 'published' AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)",
        [$like, $like, $like]
    );

    $pages = Database::fetchAll(
        "SELECT title, slug, content AS excerpt, 'page' AS type, updated_at AS date FROM pages WHERE status = 'published' AND (title LIKE ? OR content LIKE ?)",
        [$like, $like]
    );

    $results = array_merge($products, $posts, $pages);
    usort($results, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
}

$total = count($results);

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="max-w-2xl mx-auto text-center mb-12">
        <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal">Search</h1>
        <form method="GET" action="/search" class="mt-8">
            <div class="flex gap-4">
                <input class="flex-1 border border-divider-gray rounded-xl px-6 py-4 text-lg focus:ring-2 focus:ring-deep-royal focus:outline-none bg-pure-white shadow-sm" type="text" name="q" placeholder="Search products, blog posts, pages..." value="<?= Security::h($query) ?>" minlength="2" autofocus>
                <button class="bg-deep-royal text-pure-white px-8 rounded-xl font-label-caps hover:brightness-110 transition-all shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined">search</span>
                </button>
            </div>
        </form>
    </div>

    <?php if ($query): ?>
        <p class="text-on-surface-variant font-body-md mb-6">
            <?= $total ?> result<?= $total !== 1 ? 's' : '' ?> for <strong>"<?= Security::h($query) ?>"</strong>
        </p>

        <?php if (empty($results)): ?>
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4">search_off</span>
                <p class="font-body-lg text-on-surface-variant">No results found. Try a different search term.</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($results as $item): ?>
                    <?php
                    $link = match ($item['type']) {
                        'product' => '/product/' . rawurlencode($item['slug']),
                        'post' => '/blog/' . rawurlencode($item['slug']),
                        'page' => $item['slug'] === 'home' ? '/' : '/page/' . rawurlencode($item['slug']),
                    };
                    $typeLabel = match ($item['type']) {
                        'product' => 'Product',
                        'post' => 'Blog Post',
                        'page' => 'Page',
                    };
                    ?>
                    <a href="<?= $link ?>" class="block glass-card rounded-xl p-6 hover:shadow-md transition-all border border-on-surface/5">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-deep-royal/40 mt-1">
                                <?= match ($item['type']) { 'product' => 'inventory_2', 'post' => 'article', 'page' => 'description' } ?>
                            </span>
                            <div class="flex-1">
                                <span class="text-xs font-bold text-vibrant-amber uppercase tracking-wider"><?= $typeLabel ?></span>
                                <h3 class="font-headline-sm text-headline-sm text-deep-royal mt-1"><?= Security::h($item['title']) ?></h3>
                                <?php if (!empty($item['excerpt'])): ?>
                                    <p class="font-body-md text-body-md text-on-surface-variant mt-1 line-clamp-2"><?= Security::h(excerpt(strip_tags($item['excerpt']), 200)) ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="material-symbols-outlined text-on-surface-variant/40">chevron_right</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
