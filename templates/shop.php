<section class="section">
    <div class="container">
        <h1 class="section-title">Boutique</h1>

        <?php if (empty($paintings)): ?>
            <div class="empty-state">
                <h2>Aucun tableau disponible</h2>
                <p>De nouvelles créations arrivent bientôt...</p>
            </div>
        <?php else: ?>
            <div class="paintings-grid">
                <?php foreach ($paintings as $painting): ?>
                    <a href="/tableau/<?= e($painting['slug']) ?>" class="painting-card">
                        <div class="image-wrapper">
                            <img src="/uploads/thumbs/<?= e($painting['image']) ?>" alt="<?= e($painting['title']) ?>" loading="lazy">
                        </div>
                        <div class="card-body">
                            <h3><?= e($painting['title']) ?></h3>
                            <?php if ($painting['technique']): ?>
                                <p class="technique"><?= e($painting['technique']) ?></p>
                            <?php endif; ?>
                            <p class="price"><?= formatPrice($painting['price']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
