<section class="section">
    <div class="confirmation-page">
        <?php if (($order['payment_status'] ?? '') === 'failed' || (isset($_GET['payment']) && $_GET['payment'] === 'failed')): ?>
            <div style="font-size: 3rem; margin-bottom: 16px; color: var(--error);">&#10007;</div>
            <h1>Erreur de paiement</h1>
            <p>Commande n° <strong><?= e($order['order_number']) ?></strong></p>
            <div class="order-summary">
                <p>Le paiement n'a pas pu être effectué. Votre commande est en attente.</p>
                <p>Vous pouvez réessayer ou choisir un autre mode de paiement en nous contactant.</p>
            </div>
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
                <a href="/contact" class="btn btn-primary">Nous contacter</a>
                <a href="/boutique" class="btn btn-outline">Retour à la boutique</a>
            </div>

        <?php elseif (isset($_GET['payment']) && $_GET['payment'] === 'cancel'): ?>
            <div style="font-size: 3rem; margin-bottom: 16px; color: var(--gold-dark);">&#8634;</div>
            <h1>Paiement annulé</h1>
            <p>Commande n° <strong><?= e($order['order_number']) ?></strong></p>
            <div class="order-summary">
                <p>Vous avez annulé le paiement. Votre commande est toujours enregistrée et en attente de paiement.</p>
            </div>
            <div style="display: flex; gap: 12px; justify-content: center; margin-top: 20px;">
                <a href="/contact" class="btn btn-primary">Nous contacter</a>
                <a href="/boutique" class="btn btn-outline">Retour à la boutique</a>
            </div>

        <?php else: ?>
            <div class="checkmark">&#10003;</div>
            <h1>Merci pour votre commande !</h1>
            <p>Commande n° <strong><?= e($order['order_number']) ?></strong></p>
            <?php if (!empty($order['shipping_method'])): ?>
                <p>Livraison : <strong><?= e(PaymentController::carrierLabel($order['shipping_method'])) ?></strong></p>
            <?php endif; ?>

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
                <?php if (($order['shipping_cost'] ?? 0) > 0): ?>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); color: var(--text-light);">
                        <span>Frais de livraison</span>
                        <span><?= formatPrice($order['shipping_cost']) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; padding: 16px 0; font-size: 1.1rem;">
                    <span>Total</span>
                    <strong style="color: var(--gold-dark);"><?= formatPrice($order['total']) ?></strong>
                </div>
            </div>

            <p style="margin-top: 24px;">Un email de confirmation a été envoyé à <strong><?= e($order['customer_email']) ?></strong></p>

            <a href="/boutique" class="btn btn-outline" style="margin-top: 20px;">Retour à la boutique</a>
        <?php endif; ?>
    </div>
</section>
