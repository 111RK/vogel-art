<?php if (!empty($post['image'])): ?>
<section class="blog-post-hero" style="background-image: url('/uploads/<?= e($post['image']) ?>')">
    <div class="blog-post-hero-overlay">
        <div class="container">
            <?php if (!empty($post['category_name'])): ?>
                <a href="/blog/categorie/<?= e($post['category_slug']) ?>" class="blog-category-badge"><?= e($post['category_name']) ?></a>
            <?php endif; ?>
            <h1><?= e($post['title']) ?></h1>
            <time datetime="<?= date('Y-m-d', strtotime($post['created_at'])) ?>"><?= dateFr($post['created_at']) ?></time>
        </div>
    </div>
</section>
<?php else: ?>
<section class="blog-post-hero-simple">
    <div class="container">
        <?php if (!empty($post['category_name'])): ?>
            <a href="/blog/categorie/<?= e($post['category_slug']) ?>" class="blog-category-badge"><?= e($post['category_name']) ?></a>
        <?php endif; ?>
        <h1><?= e($post['title']) ?></h1>
        <time datetime="<?= date('Y-m-d', strtotime($post['created_at'])) ?>"><?= dateFr($post['created_at']) ?></time>
    </div>
</section>
<?php endif; ?>

<article class="section blog-post-content">
    <div class="container">
        <div class="blog-post-body">
            <div class="blog-article-text">
                <?= $post['content'] ?>
            </div>

            <aside class="blog-post-cta">
                <div class="blog-cta-box">
                    <h3>Envie de couleurs chez vous ?</h3>
                    <p>Chaque tableau est une pièce unique, peinte à la main au couteau. Trouvez l'oeuvre qui parlera à votre sensibilité.</p>
                    <a href="/boutique" class="btn btn-primary">Découvrez nos tableaux</a>
                </div>
            </aside>
        </div>
    </div>
</article>

<?php if (!empty($related)): ?>
<section class="section blog-related">
    <div class="container">
        <h2 class="section-title">Articles similaires</h2>
        <div class="blog-grid blog-grid-3">
            <?php foreach ($related as $rel): ?>
                <article class="blog-card">
                    <a href="/blog/<?= e($rel['slug']) ?>" class="blog-card-image">
                        <?php if ($rel['image']): ?>
                            <img src="/uploads/<?= e($rel['image']) ?>" alt="<?= e($rel['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="blog-card-placeholder"></div>
                        <?php endif; ?>
                    </a>
                    <div class="blog-card-body">
                        <h3 class="blog-card-title"><a href="/blog/<?= e($rel['slug']) ?>"><?= e($rel['title']) ?></a></h3>
                        <p class="blog-card-excerpt"><?= e($rel['excerpt']) ?></p>
                        <a href="/blog/<?= e($rel['slug']) ?>" class="blog-read-more">Lire l'article</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": <?= json_encode($post['title']) ?>,
    "description": <?= json_encode($post['meta_description'] ?: $post['excerpt']) ?>,
    "url": "<?= SITE_URL ?>/blog/<?= $post['slug'] ?>",
    "datePublished": "<?= date('c', strtotime($post['created_at'])) ?>",
    "dateModified": "<?= date('c', strtotime($post['updated_at'])) ?>",
    <?php if (!empty($post['image'])): ?>
    "image": "<?= SITE_URL ?>/uploads/<?= $post['image'] ?>",
    <?php endif; ?>
    "author": {
        "@type": "Person",
        "name": "Vogel Art",
        "url": "<?= SITE_URL ?>/a-propos"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Vogel Art",
        "url": "<?= SITE_URL ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?= SITE_URL ?>/img/logo.png"
        }
    },
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?= SITE_URL ?>/blog/<?= $post['slug'] ?>"
    }
}
</script>
