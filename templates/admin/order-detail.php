<a href="/admin/commandes" style="color: var(--text-light); margin-bottom: 16px; display: inline-block;">&larr; Retour aux commandes</a>

<div class="admin-grid">
    <div class="admin-card">
        <h3>Client</h3>
        <p><strong><?= e($order['customer_firstname'] . ' ' . $order['customer_lastname']) ?></strong></p>
        <p><?= e($order['customer_email']) ?></p>
        <?php if ($order['customer_phone']): ?>
            <p><?= e($order['customer_phone']) ?></p>
        <?php endif; ?>
        <p style="margin-top: 12px;">
            <?= e($order['shipping_address']) ?><br>
            <?= e($order['shipping_postal'] . ' ' . $order['shipping_city']) ?><br>
            <?= e($order['shipping_country']) ?>
        </p>
    </div>

    <div class="admin-card">
        <h3>Commande</h3>
        <p><strong>N° :</strong> <?= e($order['order_number']) ?></p>
        <p><strong>Date :</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
        <p><strong>Mode de paiement :</strong> <?= e($order['payment_method']) ?></p>
        <?php if (!empty($order['shipping_method'])): ?>
            <p><strong>Livraison :</strong> <?= e(PaymentController::carrierLabel($order['shipping_method'])) ?></p>
            <p><strong>Frais de port :</strong> <?= formatPrice($order['shipping_cost'] ?? 0) ?></p>
        <?php endif; ?>
        <?php if (!empty($order['shipping_tracking'])): ?>
            <p><strong>N° suivi :</strong> <?= e($order['shipping_tracking']) ?></p>
        <?php endif; ?>
        <p><strong>Total :</strong> <?= formatPrice($order['total']) ?></p>

        <form method="POST" action="/admin/commandes/<?= $order['id'] ?>/statut" style="margin-top: 16px;">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="payment_status">Statut paiement</label>
                <select name="payment_status" id="payment_status">
                    <?php foreach (['pending', 'paid', 'failed', 'refunded'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['payment_status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Statut commande</label>
                <select name="status" id="status">
                    <?php foreach (['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="shipping_tracking">N° de suivi</label>
                <input type="text" name="shipping_tracking" id="shipping_tracking" value="<?= e($order['shipping_tracking'] ?? '') ?>" placeholder="Numéro de suivi">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>

<?php if (!empty($order['shipping_method']) && $order['shipping_method'] !== 'pickup'): ?>
<div class="admin-card" style="margin-top: 24px;">
    <h3>Expédition Packlink</h3>
    <?php if (!empty($packlinkConfig['packlink_api_key'])): ?>
        <p style="margin-bottom: 12px;">Créer une expédition via Packlink PRO avec les informations pré-remplies.</p>
        <div class="form-row" style="margin-bottom: 12px;">
            <div class="form-group">
                <label>Poids (kg)</label>
                <input type="number" step="0.1" min="0.1" id="parcel-weight" value="<?= e($packlinkConfig['default_parcel_weight'] ?? '2') ?>">
            </div>
            <div class="form-group">
                <label>Dimensions L x l x H (cm)</label>
                <input type="text" id="parcel-dimensions" value="<?= e($packlinkConfig['default_parcel_dimensions'] ?? '60x50x10') ?>" placeholder="60x50x10">
            </div>
        </div>
        <button type="button" class="btn btn-primary" onclick="sendWithPacklink(<?= $order['id'] ?>)">
            Envoyer via Packlink
        </button>
        <div id="packlink-result" style="margin-top: 12px;"></div>
    <?php else: ?>
        <p style="color: var(--text-light);">Configurez votre clé API Packlink PRO dans les <a href="/admin/parametres">paramètres</a> pour activer l'expédition.</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="admin-card" style="margin-top: 24px;">
    <h3>Articles</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th>Titre</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?php if ($item['image']): ?>
                            <img src="/uploads/thumbs/<?= e($item['image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        <?php endif; ?>
                    </td>
                    <td><?= e($item['title']) ?></td>
                    <td><?= formatPrice($item['price']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
