<?php

$slug = $slug ?? $_GET['slug'] ?? '';
$product = Product::findBySlug($slug);

if (!$product || $product['status'] !== 'active') {
    header('HTTP/1.0 404 Not Found');
    $pageTitle = 'Product Not Found';
    require_once BASE_PATH . 'includes/header.php';
    echo '<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto text-center"><h1 class="font-headline-md text-headline-md text-deep-royal mb-4">Product Not Found</h1><a href="/products" class="text-vibrant-amber underline font-label-caps">&larr; Back to Products</a></section>';
    require_once BASE_PATH . 'includes/footer.php';
    exit;
}

$pageTitle = Security::h($product['name']) . ' | White Lotus Trading';
$metaDescription = Security::h($product['meta_description'] ?? $product['description'] ?? '');
$ogTitle = $pageTitle;
$ogImage = $product['image_url'] ? uploadUrl($product['image_url']) : null;
$specs = $product['specs'] ? json_decode($product['specs'], true) : null;

require_once BASE_PATH . 'includes/header.php';
?>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <a href="/products" class="inline-flex items-center gap-2 font-label-caps text-label-caps text-on-surface-variant hover:text-deep-royal transition-colors mb-8">
        <span class="material-symbols-outlined">arrow_back</span> Back to Products
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <div class="aspect-square w-full rounded-2xl overflow-hidden bg-surface-container-low flex items-center justify-center border border-divider-gray">
            <?php if ($product['image_url']): ?>
                <img src="<?= uploadUrl($product['image_url']) ?>" class="w-full h-full object-contain p-8" alt="<?= Security::h($product['name']) ?>">
            <?php else: ?>
                <span class="material-symbols-outlined text-[120px] text-on-surface-variant/20"><?= $product['division'] === 'HVAC' ? 'ac_unit' : 'spa' ?></span>
            <?php endif; ?>
        </div>

        <div class="space-y-8">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold" style="background: <?= $product['division'] === 'HVAC' ? '#00236610' : '#FFBF0033' ?>; color: <?= $product['division'] === 'HVAC' ? '#002366' : '#795900' ?>;">
                    <?= Security::h($product['division']) ?>
                </span>
                <h1 class="font-display-lg text-display-lg-mobile md:text-display-lg text-deep-royal mt-4"><?= Security::h($product['name']) ?></h1>
                <p class="font-label-caps text-label-caps text-on-surface-variant mt-2">SKU: <?= Security::h($product['sku'] ?: 'N/A') ?></p>
            </div>

            <?php if ($product['description']): ?>
                <div class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
                    <p><?= Security::h($product['description']) ?></p>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-6 py-6 border-t border-b border-divider-gray">
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Price</p>
                    <p class="font-headline-sm text-headline-sm text-deep-royal"><?= $product['price'] > 0 ? '$' . number_format($product['price'], 2) : 'Contact for Price' ?></p>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Stock</p>
                    <p class="font-headline-sm text-headline-sm text-deep-royal"><?= (int)$product['stock'] ?> <?= Security::h($product['unit']) ?></p>
                </div>
            </div>

            <?php if ($specs && is_array($specs)): ?>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-deep-royal mb-4">Technical Specifications</h3>
                    <div class="space-y-3">
                        <?php foreach ($specs as $key => $value): ?>
                            <div class="flex justify-between py-2 border-b border-divider-gray">
                                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase"><?= Security::h($key) ?></span>
                                <span class="font-body-md text-body-md text-deep-royal font-medium"><?= Security::h($value) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="/inquiry?subject=<?= urlencode('Inquiry: ' . $product['name']) ?>" class="inline-block bg-vibrant-amber text-charcoal-text px-8 py-4 rounded-xl font-headline-sm text-headline-sm hover:shadow-lg transition-all">
                Request Quote
                <span class="material-symbols-outlined ml-2" style="vertical-align: middle;">send</span>
            </a>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
