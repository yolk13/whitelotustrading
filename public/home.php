<?php

$pageTitle = 'White Lotus Trading - F.Z.E. | HVAC & Wellness';
$activeProducts = Product::active();
$hvacProducts = array_filter($activeProducts, fn($p) => $p['division'] === 'HVAC');
$wellnessProducts = array_filter($activeProducts, fn($p) => $p['division'] === 'Consumables');
$homePage = Page::findBySlug('home');

require_once BASE_PATH . 'includes/header.php';
?>

<?= $homePage ? $homePage['content'] : '' ?>

<section class="py-section-gap bg-surface-container-low/50">
    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin-desktop">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div>
                <span class="font-label-caps text-label-caps text-deep-royal/60 mb-2 block uppercase">Product Catalog</span>
                <h2 class="font-headline-md text-headline-md text-deep-royal">Select Category</h2>
            </div>
            <div class="flex bg-pure-white p-1 rounded-xl shadow-sm">
                <button class="px-8 py-3 rounded-lg font-label-caps text-label-caps transition-all tab-active" id="btn-hvac" onclick="switchTab('hvac')">HVAC PRODUCTS</button>
                <button class="px-8 py-3 rounded-lg font-label-caps text-label-caps transition-all text-on-surface-variant hover:bg-surface-container" id="btn-wellness" onclick="switchTab('wellness')">CONSUMABLES</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter" id="grid-hvac">
            <?php if (empty($hvacProducts)): ?>
                <div class="col-span-full text-center py-12 text-on-surface-variant font-body-md">No HVAC products available yet.</div>
            <?php else: ?>
                <?php foreach (array_slice($hvacProducts, 0, 4) as $product): ?>
                <div class="group bg-pure-white p-4 rounded-2xl border border-divider-gray hover:shadow-xl transition-all">
                    <div class="aspect-square w-full rounded-xl overflow-hidden mb-6 bg-surface-container-low flex items-center justify-center relative">
                        <?php if ($product['image_url']): ?>
                            <img class="object-contain w-3/4 group-hover:scale-110 transition-transform duration-500" src="<?= uploadUrl($product['image_url']) ?>" alt="<?= Security::h($product['name']) ?>">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">inventory_2</span>
                        <?php endif; ?>
                        <span class="absolute top-4 left-4 bg-deep-royal text-pure-white text-[10px] font-bold px-2 py-1 rounded">INDUSTRIAL</span>
                    </div>
                    <h4 class="font-headline-sm text-body-lg text-deep-royal mb-1"><?= Security::h($product['name']) ?></h4>
                    <p class="font-label-caps text-[10px] text-on-surface-variant mb-4 tracking-wider uppercase"><?= Security::h($product['description'] ?: $product['sku']) ?></p>
                    <a href="/product/<?= Security::h($product['slug']) ?>" class="block w-full py-3 border border-deep-royal/20 text-deep-royal font-label-caps text-label-caps rounded-lg text-center hover:bg-deep-royal hover:text-pure-white transition-colors">View Specs</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter" id="grid-wellness">
            <?php if (empty($wellnessProducts)): ?>
                <div class="col-span-full text-center py-12 text-on-surface-variant font-body-md">No wellness products available yet.</div>
            <?php else: ?>
                <?php foreach (array_slice($wellnessProducts, 0, 4) as $product): ?>
                <div class="group bg-pure-white p-4 rounded-2xl border border-divider-gray hover:shadow-xl transition-all">
                    <div class="aspect-square w-full rounded-xl overflow-hidden mb-6 bg-surface-container-low flex items-center justify-center relative">
                        <?php if ($product['image_url']): ?>
                            <img class="object-contain w-3/4 group-hover:scale-110 transition-transform duration-500" src="<?= uploadUrl($product['image_url']) ?>" alt="<?= Security::h($product['name']) ?>">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-6xl text-on-surface-variant/30">spa</span>
                        <?php endif; ?>
                        <span class="absolute top-4 left-4 bg-vibrant-amber text-charcoal-text text-[10px] font-bold px-2 py-1 rounded">BEST SELLER</span>
                    </div>
                    <h4 class="font-headline-sm text-body-lg text-deep-royal mb-1"><?= Security::h($product['name']) ?></h4>
                    <p class="font-label-caps text-[10px] text-on-surface-variant mb-4 tracking-wider uppercase"><?= Security::h($product['description'] ?: $product['sku']) ?></p>
                    <a href="/product/<?= Security::h($product['slug']) ?>" class="block w-full py-3 border border-vibrant-amber/40 text-deep-royal font-label-caps text-label-caps rounded-lg text-center hover:bg-vibrant-amber hover:text-charcoal-text transition-colors">Shop Now</a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-section-gap px-margin-mobile md:px-margin-desktop max-w-[1280px] mx-auto">
    <div class="flex flex-col lg:flex-row items-center gap-16">
        <div class="w-full lg:w-1/2 space-y-6">
            <h2 class="font-headline-md text-headline-md text-deep-royal">Global Reach, <span class="text-vibrant-amber">Local Expertise</span></h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant">White Lotus Trading - F.Z.E operates at the intersection of international trade routes, ensuring seamless delivery of industrial hardware and organic wellness products across the MENA region and beyond.</p>
            <div class="grid grid-cols-2 gap-gutter pt-4">
                <div class="border-l-4 border-vibrant-amber pl-6">
                    <div class="font-display-lg text-headline-md text-deep-royal">15+</div>
                    <div class="font-label-caps text-label-caps text-on-surface-variant">Global Markets</div>
                </div>
                <div class="border-l-4 border-vibrant-amber pl-6">
                    <div class="font-display-lg text-headline-md text-deep-royal">200+</div>
                    <div class="font-label-caps text-label-caps text-on-surface-variant">Product Lines</div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/2 h-[400px] rounded-2xl overflow-hidden shadow-sm border border-divider-gray bg-surface-container-low flex items-center justify-center">
            <div class="text-center text-on-surface-variant opacity-30">
                <span class="material-symbols-outlined text-[80px]">map</span>
                <p class="font-label-caps">Global Map</p>
            </div>
        </div>
    </div>
</section>

<script>
function switchTab(category) {
    const hvacGrid = document.getElementById('grid-hvac');
    const wellnessGrid = document.getElementById('grid-wellness');
    const btnHvac = document.getElementById('btn-hvac');
    const btnWellness = document.getElementById('btn-wellness');
    if (category === 'hvac') {
        hvacGrid.classList.remove('hidden'); wellnessGrid.classList.add('hidden');
        btnHvac.classList.add('tab-active'); btnHvac.classList.remove('text-on-surface-variant');
        btnWellness.classList.remove('tab-active'); btnWellness.classList.add('text-on-surface-variant');
    } else {
        hvacGrid.classList.add('hidden'); wellnessGrid.classList.remove('hidden');
        btnWellness.classList.add('tab-active'); btnWellness.classList.remove('text-on-surface-variant');
        btnHvac.classList.remove('tab-active'); btnHvac.classList.add('text-on-surface-variant');
    }
}
</script>

<?php require_once BASE_PATH . 'includes/footer.php'; ?>
