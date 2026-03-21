<?php
require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/src/Database.php';

header('Content-Type: application/xml; charset=UTF-8');

$pages = [
    ['url' => '', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['url' => '/boutique', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['url' => '/a-propos', 'priority' => '0.6', 'changefreq' => 'monthly'],
    ['url' => '/contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
    ['url' => '/faq', 'priority' => '0.5', 'changefreq' => 'monthly'],
    ['url' => '/livraison', 'priority' => '0.4', 'changefreq' => 'monthly'],
    ['url' => '/cgv', 'priority' => '0.3', 'changefreq' => 'yearly'],
];

$paintings = Database::fetchAll("SELECT slug, updated_at FROM paintings WHERE status IN ('available', 'sold') ORDER BY created_at DESC");
$posts = Database::fetchAll("SELECT slug, updated_at FROM blog_posts WHERE published = 1 ORDER BY created_at DESC");
$categories = Database::fetchAll("SELECT slug FROM blog_categories");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
    <url>
        <loc><?= SITE_URL . $p['url'] ?></loc>
        <changefreq><?= $p['changefreq'] ?></changefreq>
        <priority><?= $p['priority'] ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($paintings as $p): ?>
    <url>
        <loc><?= SITE_URL ?>/tableau/<?= htmlspecialchars($p['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($p['updated_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>
<?php foreach ($posts as $p): ?>
    <url>
        <loc><?= SITE_URL ?>/blog/<?= htmlspecialchars($p['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($p['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
<?php endforeach; ?>
<?php foreach ($categories as $cat): ?>
    <url>
        <loc><?= SITE_URL ?>/blog/categorie/<?= htmlspecialchars($cat['slug']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
<?php endforeach; ?>
</urlset>
