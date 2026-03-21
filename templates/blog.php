<section class="blog-hero">
    <div class="container">
        <h1>Le Journal de l'Atelier</h1>
        <p>Technique, inspiration et coulisses de la peinture au couteau</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="blog-categories-tabs">
            <a href="/blog" class="blog-tab <?= empty($currentCategory) ? 'active' : '' ?>">Tous</a>
            <?php foreach ($categories as $cat): ?>
                <a href="/blog/categorie/<?= e($cat['slug']) ?>" class="blog-tab <?= ($currentCategory['id'] ?? 0) === $cat['id'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <h2>Aucun article pour le moment</h2>
                <p>Revenez bientôt pour découvrir nos prochains articles.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($posts as $post): ?>
                    <article class="blog-card">
                        <a href="/blog/<?= e($post['slug']) ?>" class="blog-card-image">
                            <?php if ($post['image']): ?>
                                <img src="/uploads/<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="blog-card-placeholder"></div>
                            <?php endif; ?>
                            <?php if (!empty($post['category_name'])): ?>
                                <span class="blog-category-badge"><?= e($post['category_name']) ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="blog-card-body">
                            <time class="blog-card-date" datetime="<?= date('Y-m-d', strtotime($post['created_at'])) ?>"><?= date('d F Y', strtotime($post['created_at'])) ?></time>
                            <h2 class="blog-card-title"><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h2>
                            <p class="blog-card-excerpt"><?= e($post['excerpt']) ?></p>
                            <a href="/blog/<?= e($post['slug']) ?>" class="blog-read-more">Lire l'article</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Blog",
    "name": "Le Journal de l'Atelier - Vogel Art",
    "description": "Blog sur la peinture au couteau : techniques, inspirations, conseils déco et guides d'achat pour les amateurs d'art.",
    "url": "<?= SITE_URL ?>/blog",
    "publisher": {
        "@type": "Organization",
        "name": "Vogel Art",
        "url": "<?= SITE_URL ?>"
    },
    "blogPost": [
        <?php foreach ($posts as $i => $post): ?>
        {
            "@type": "BlogPosting",
            "headline": <?= json_encode($post['title']) ?>,
            "url": "<?= SITE_URL ?>/blog/<?= $post['slug'] ?>",
            "datePublished": "<?= date('c', strtotime($post['created_at'])) ?>",
            "dateModified": "<?= date('c', strtotime($post['updated_at'])) ?>"
            <?php if ($post['image']): ?>,"image": "<?= SITE_URL ?>/uploads/<?= $post['image'] ?>"<?php endif; ?>
        }<?= $i < count($posts) - 1 ? ',' : '' ?>
        <?php endforeach; ?>
    ]
}
</script>
