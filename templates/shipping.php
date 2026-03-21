<section class="section">
    <div class="container" style="max-width: 750px; margin: 0 auto;">
        <h1 class="section-title">Livraison</h1>

        <div class="order-summary" style="margin-bottom: 24px;">
            <p style="line-height: 1.8; color: var(--text);"><?= nl2br(e($shippingInfo['value'] ?? '')) ?></p>
        </div>

        <?php if (!empty($carriers)): ?>
        <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.4rem; margin-bottom: 20px;">Nos modes de livraison</h2>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($carriers as $carrier): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius);">
                    <span style="font-weight: 500;"><?= e($carrier['label']) ?></span>
                    <?php if ($carrier['price'] > 0): ?>
                        <strong style="color: var(--gold-dark);"><?= formatPrice($carrier['price']) ?></strong>
                    <?php else: ?>
                        <strong style="color: var(--success);">Gratuit</strong>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="order-summary" style="margin-top: 24px;">
            <h3 style="margin-bottom: 12px;">Emballage soigné</h3>
            <p style="color: var(--text-light); line-height: 1.8;">Chaque tableau est soigneusement emballé avec des protections adaptées pour garantir une livraison en parfait état. Vous recevez un numéro de suivi par email dès l'expédition.</p>
        </div>

        <div style="text-align: center; margin-top: 32px;">
            <a href="/boutique" class="btn btn-primary">Voir la boutique</a>
            <a href="/contact" class="btn btn-outline" style="margin-left: 12px;">Nous contacter</a>
        </div>
    </div>
</section>
