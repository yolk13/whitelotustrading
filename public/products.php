<?php

$pageTitle = 'Products | White Lotus Trading';
$division = Security::sanitize($_GET['division'] ?? '');
$search = Security::sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$products = Product::paginateActive($page, 12, $division, $search);

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="mb-12">
        <span class="font-label-caps text-label-caps text-vibrant-amber uppercase tracking-widest block mb-2">Product Catalog</span>
        <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal">Our Products</h1>
    </div>

    <form class="mb-8 flex flex-wrap items-center gap-4" method="GET">
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input class="w-full pl-10 pr-4 py-3 bg-pure-white border border-divider-gray rounded-lg focus:ring-2 focus:ring-deep-royal focus:outline-none transition-all" name="search" placeholder="Search products..." value="<?= Security::h($search) ?>">
        </div>
        <div class="flex items-center gap-2 bg-pure-white px-4 py-2 border border-divider-gray rounded-lg">
            <select class="bg-transparent border-none focus:ring-0 text-sm font-medium text-deep-royal cursor-pointer" name="division" onchange="this.form.submit()">
                <option value="">All Divisions</option>
                <option value="HVAC" <?= selected($division, 'HVAC') ?>>HVAC</option>
                <option value="Consumables" <?= selected($division, 'Consumables') ?>>Consumables</option>
            </select>
        </div>
        <?php if ($search || $division): ?>
            <a href="/products" class="text-sm text-on-surface-variant hover:text-deep-royal underline transition-all">Clear filters</a>
        <?php endif; ?>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-gutter">
        <?php if (empty($products['items'])): ?>
            <div class="col-span-full text-center py-20">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4">search_off</span>
                <p class="font-headline-sm text-headline-sm text-on-surface-variant mb-2">No products found</p>
                <p class="font-body-md text-on-surface-variant">Try adjusting your search or filter criteria.</p>
                <a href="/products" class="inline-block mt-6 bg-deep-royal text-pure-white px-6 py-3 rounded-lg font-label-caps hover:bg-vibrant-amber hover:text-deep-royal transition-all">View All Products</a>
            </div>
        <?php else: ?>
            <?php foreach ($products['items'] as $product): ?>
            <div class="group bg-pure-white p-4 rounded-2xl border border-divider-gray hover:shadow-xl transition-all">
                <div class="aspect-square w-full rounded-xl overflow-hidden mb-4 bg-surface-container-low flex items-center justify-center relative">
                    <?php if ($product['image_url']): ?>
                        <img class="object-contain w-3/4 group-hover:scale-110 transition-transform duration-500" src="<?= uploadUrl($product['image_url']) ?>" alt="<?= Security::h($product['name']) ?>">
                    <?php else: ?>
                        <span class="material-symbols-outlined text-6xl text-on-surface-variant/30"><?= $product['division'] === 'HVAC' ? 'ac_unit' : 'spa' ?></span>
                    <?php endif; ?>
                    <span class="absolute top-3 left-3 px-2 py-1 rounded text-[10px] font-bold" style="background: <?= $product['division'] === 'HVAC' ? '#002366' : '#FFBF00' ?>; color: <?= $product['division'] === 'HVAC' ? '#ffffff' : '#1A1A1A' ?>;">
                        <?= Security::h($product['division'] === 'Consumables' ? 'WELLNESS' : $product['division']) ?>
                    </span>
                </div>
                <h4 class="font-headline-sm text-body-lg text-deep-royal mb-1"><?= Security::h($product['name']) ?></h4>
                <p class="font-label-caps text-[10px] text-on-surface-variant mb-3 tracking-wider uppercase">SKU: <?= Security::h($product['sku'] ?: 'N/A') ?></p>
                <p class="font-body-md text-sm text-on-surface-variant mb-4 line-clamp-2"><?= Security::h($product['description']) ?></p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-deep-royal"><?= $product['price'] > 0 ? '$' . number_format($product['price'], 2) : 'Contact for Price' ?></span>
                    <a href="/product/<?= Security::h($product['slug']) ?>" class="px-4 py-2 border border-deep-royal/20 text-deep-royal font-label-caps text-[11px] rounded-lg hover:bg-deep-royal hover:text-pure-white transition-colors">View Specs</a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($products['totalPages'] > 1): ?>
    <div class="mt-12 flex justify-center gap-2">
        <?php if ($products['hasPrev']): ?>
            <a href="?page=<?= $products['prevPage'] ?>&division=<?= urlencode($division) ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 border border-divider-gray rounded-lg hover:bg-surface-container transition-colors font-label-caps">Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $products['totalPages']; $i++): ?>
            <a href="?page=<?= $i ?>&division=<?= urlencode($division) ?>&search=<?= urlencode($search) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-label-caps transition-colors <?= $i === $products['page'] ? 'bg-deep-royal text-pure-white' : 'border border-divider-gray hover:bg-surface-container' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($products['hasNext']): ?>
            <a href="?page=<?= $products['nextPage'] ?>&division=<?= urlencode($division) ?>&search=<?= urlencode($search) ?>" class="px-4 py-2 border border-divider-gray rounded-lg hover:bg-surface-container transition-colors font-label-caps">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
