<section class="hero">
    <div class="container">
        <h1>L'art à portée de main</h1>
        <p>Découvrez des tableaux uniques, peints à la main. Chaque toile est une pièce originale, créée avec passion.</p>
        <a href="/boutique" class="btn btn-primary">Découvrir la collection</a>
    </div>
</section>

<?php if (!empty($featured)): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">En vedette</h2>
        <div class="paintings-grid">
            <?php foreach ($featured as $painting): ?>
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
    </div>
</section>
<?php endif; ?>

<?php if (!empty($artistBio)): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">L'artiste</h2>
        <div class="artist-bio">
            <p><?= nl2br(e($artistBio)) ?></p>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($timeline)): ?>
<section class="section" style="background: var(--white);">
    <div class="container">
        <h2 class="section-title">Parcours</h2>
        <div class="timeline">
            <?php foreach ($timeline as $i => $step): ?>
                <div class="timeline-item <?= $i % 2 === 0 ? 'left' : 'right' ?>">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year"><?= e($step['year']) ?></span>
                        <h3><?= e($step['title']) ?></h3>
                        <p><?= e($step['description']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($recent)): ?>
<section class="section" style="background: var(--bg-warm);">
    <div class="container">
        <h2 class="section-title">Dernières créations</h2>
        <div class="paintings-grid">
            <?php foreach ($recent as $painting): ?>
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
        <div style="text-align: center; margin-top: 32px;">
            <a href="/boutique" class="btn btn-outline">Voir toute la collection</a>
        </div>
    </div>
</section>
<?php endif; ?>
