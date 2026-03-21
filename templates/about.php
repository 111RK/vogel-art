<section class="section" style="background: var(--bg-warm);">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        <h1 class="section-title">À propos</h1>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
            <div>
                <?php if (!empty($contactInfo['gallery_name'])): ?>
                    <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.8rem; margin-bottom: 8px;"><?= e($contactInfo['gallery_name']) ?></h2>
                <?php endif; ?>
                <?php if (!empty($contactInfo['owner_firstname'])): ?>
                    <p style="color: var(--gold-dark); font-size: 1.1rem; margin-bottom: 24px;"><?= e(trim($contactInfo['owner_firstname'] . ' ' . $contactInfo['owner_lastname'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($aboutText['value'])): ?>
                    <div style="color: var(--text); line-height: 1.9; text-align: justify;">
                        <p><?= nl2br(e($aboutText['value'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($contactInfo['contact_photo'])): ?>
                <div>
                    <img src="/uploads/<?= e($contactInfo['contact_photo']) ?>" alt="<?= e(trim(($contactInfo['owner_firstname'] ?? '') . ' ' . ($contactInfo['owner_lastname'] ?? ''))) ?>" style="width: 100%; border-radius: var(--radius); box-shadow: var(--shadow);">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($artistBio['value'])): ?>
<section class="section">
    <div class="container">
        <div class="artist-block">
            <div class="artist-accent"></div>
            <h2 class="section-title">Mon parcours</h2>
            <div class="artist-bio">
                <p><?= nl2br(e($artistBio['value'])) ?></p>
            </div>
            <div class="artist-accent artist-accent-bottom"></div>
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

<section class="section" style="background: var(--bg-warm); text-align: center;">
    <div class="container">
        <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.6rem; margin-bottom: 16px;">Envie de découvrir mes créations ?</h2>
        <p style="color: var(--text-light); margin-bottom: 24px;">Chaque tableau est une pièce unique, peinte à la main au couteau.</p>
        <a href="/boutique" class="btn btn-primary" style="margin-right: 12px;">Voir la boutique</a>
        <a href="/contact" class="btn btn-outline">Me contacter</a>
    </div>
</section>
