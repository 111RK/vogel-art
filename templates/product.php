<section class="section">
    <div class="container">
        <div class="product-page">
            <div class="product-image">
                <img src="/uploads/<?= e($painting['image']) ?>" alt="<?= e($painting['title']) ?>">
            </div>
            <div class="product-info">
                <h1><?= e($painting['title']) ?></h1>
                <p class="price"><?= formatPrice($painting['price']) ?></p>

                <?php if ($painting['status'] === 'available'): ?>
                    <span class="badge badge-available">Disponible</span>
                <?php else: ?>
                    <span class="badge badge-sold">Vendu</span>
                <?php endif; ?>

                <?php if ($painting['description']): ?>
                    <div class="description">
                        <p><?= nl2br(e($painting['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="details">
                    <?php if ($painting['technique']): ?>
                        <p><strong>Technique :</strong> <?= e($painting['technique']) ?></p>
                    <?php endif; ?>
                    <?php if ($painting['width_cm'] && $painting['height_cm']): ?>
                        <p><strong>Dimensions :</strong> <?= $painting['width_cm'] ?> x <?= $painting['height_cm'] ?> cm</p>
                    <?php endif; ?>
                    <p><strong>Pièce unique</strong></p>
                </div>

                <?php if ($painting['status'] === 'available'): ?>
                    <form method="POST" action="/panier/ajouter">
                        <?= csrf_field() ?>
                        <input type="hidden" name="painting_id" value="<?= $painting['id'] ?>">
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($related)): ?>
            <div class="section">
                <h2 class="section-title">Vous aimerez aussi</h2>
                <div class="paintings-grid">
                    <?php foreach ($related as $item): ?>
                        <a href="/tableau/<?= e($item['slug']) ?>" class="painting-card">
                            <div class="image-wrapper">
                                <img src="/uploads/thumbs/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                            </div>
                            <div class="card-body">
                                <h3><?= e($item['title']) ?></h3>
                                <p class="price"><?= formatPrice($item['price']) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
