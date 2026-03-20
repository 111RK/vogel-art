<section class="section">
    <div class="confirmation-page">
        <div class="checkmark">&#10003;</div>
        <h1>Merci pour votre commande !</h1>
        <p>Commande n° <strong><?= e($order['order_number']) ?></strong></p>

        <?php if ($order['payment_method'] === 'bank_transfer'): ?>
            <div class="order-summary">
                <h3>Informations pour le virement</h3>
                <p><strong>IBAN :</strong> <?= e($bankInfo['bank_iban'] ?? '') ?></p>
                <p><strong>BIC :</strong> <?= e($bankInfo['bank_bic'] ?? '') ?></p>
                <p><strong>Titulaire :</strong> <?= e($bankInfo['bank_name'] ?? '') ?></p>
                <p><strong>Montant :</strong> <?= formatPrice($order['total']) ?></p>
                <p><strong>Référence :</strong> <?= e($order['order_number']) ?></p>
            </div>
        <?php elseif ($order['payment_method'] === 'in_person'): ?>
            <div class="order-summary">
                <p>Nous vous contacterons par email pour organiser la remise en main propre et le paiement.</p>
            </div>
        <?php elseif ($order['payment_status'] === 'paid'): ?>
            <div class="order-summary">
                <p>Votre paiement a été confirmé. Nous préparons votre commande.</p>
            </div>
        <?php endif; ?>

        <div class="order-summary">
            <h3>Détail de la commande</h3>
            <?php foreach ($orderItems as $item): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <?php if ($item['image']): ?>
                            <img src="/uploads/thumbs/<?= e($item['image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        <?php endif; ?>
                        <span><?= e($item['title']) ?></span>
                    </div>
                    <strong><?= formatPrice($item['price']) ?></strong>
                </div>
            <?php endforeach; ?>
            <div style="display: flex; justify-content: space-between; padding: 16px 0; font-size: 1.1rem;">
                <span>Total</span>
                <strong style="color: var(--gold-dark);"><?= formatPrice($order['total']) ?></strong>
            </div>
        </div>

        <p style="margin-top: 24px;">Un email de confirmation a été envoyé à <strong><?= e($order['customer_email']) ?></strong></p>

        <a href="/boutique" class="btn btn-outline" style="margin-top: 20px;">Retour à la boutique</a>
    </div>
</section>
