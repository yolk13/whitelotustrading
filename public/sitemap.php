<?php

header('Content-Type: application/xml; charset=UTF-8');

$staticUrls = [
    ['loc' => SITE_URL, 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => SITE_URL . '/products', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['loc' => SITE_URL . '/blog', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['loc' => SITE_URL . '/inquiry', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['loc' => SITE_URL . '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
];

$pages = Database::fetchAll("SELECT slug, updated_at FROM pages WHERE status = 'published'");
$posts = Database::fetchAll("SELECT slug, GREATEST(COALESCE(published_at, created_at), updated_at) AS lastmod FROM posts WHERE status = 'published'");
$products = Database::fetchAll("SELECT slug, updated_at FROM products WHERE status = 'active'");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticUrls as $url): ?>
    <url>
        <loc><?= Security::h($url['loc']) ?></loc>
        <priority><?= $url['priority'] ?></priority>
        <changefreq><?= $url['changefreq'] ?></changefreq>
    </url>
<?php endforeach; ?>
<?php foreach ($pages as $page): ?>
    <url>
        <loc><?= Security::h(SITE_URL . '/page/' . rawurlencode($page['slug'])) ?></loc>
        <lastmod><?= date('c', strtotime($page['updated_at'] ?? 'now')) ?></lastmod>
        <priority>0.7</priority>
        <changefreq>monthly</changefreq>
    </url>
<?php endforeach; ?>
<?php foreach ($posts as $post): ?>
    <url>
        <loc><?= Security::h(SITE_URL . '/blog/' . rawurlencode($post['slug'])) ?></loc>
        <lastmod><?= date('c', strtotime($post['lastmod'] ?? 'now')) ?></lastmod>
        <priority>0.8</priority>
        <changefreq>monthly</changefreq>
    </url>
<?php endforeach; ?>
<?php foreach ($products as $product): ?>
    <url>
        <loc><?= Security::h(SITE_URL . '/product/' . rawurlencode($product['slug'])) ?></loc>
        <lastmod><?= date('c', strtotime($product['updated_at'] ?? 'now')) ?></lastmod>
        <priority>0.6</priority>
        <changefreq>weekly</changefreq>
    </url>
<?php endforeach; ?>
</urlset>
