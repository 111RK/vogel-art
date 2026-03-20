<section class="section">
    <div class="container">
        <h1 class="section-title">Commander</h1>

        <form method="POST" action="/commande/valider" class="checkout-form">
            <?= csrf_field() ?>

            <div>
                <h3 style="margin-bottom: 20px;">Vos informations</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstname">Prénom *</label>
                        <input type="text" id="firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Nom *</label>
                        <input type="text" id="lastname" name="lastname" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="address">Adresse *</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">Ville *</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="postal">Code postal *</label>
                        <input type="text" id="postal" name="postal" required>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 20px;">Récapitulatif</h3>

                <div class="order-summary">
                    <?php foreach ($items as $item): ?>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border);">
                            <span><?= e($item['title']) ?></span>
                            <strong><?= formatPrice($item['price']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                    <div style="display: flex; justify-content: space-between; padding: 16px 0; font-size: 1.2rem;">
                        <span>Total</span>
                        <strong style="color: var(--gold-dark);"><?= formatPrice($total) ?></strong>
                    </div>
                </div>

                <h3 style="margin: 20px 0;">Mode de paiement</h3>

                <div class="payment-methods">
                    <?php if (!empty($config['stripe_public_key'])): ?>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="stripe">
                            <span>Carte bancaire (Stripe)</span>
                        </label>
                    <?php endif; ?>

                    <?php if (!empty($config['paypal_client_id'])): ?>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="paypal">
                            <span>PayPal</span>
                        </label>
                    <?php endif; ?>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="bank_transfer">
                        <span>Virement bancaire</span>
                    </label>

                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="in_person" checked>
                        <span>Paiement en main propre</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Confirmer la commande</button>
            </div>
        </form>
    </div>
</section>
