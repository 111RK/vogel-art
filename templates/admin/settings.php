<form method="POST" action="/admin/parametres" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-card">
        <h3>Informations de contact</h3>
        <div class="form-group">
            <label for="contact_email">Email de contact</label>
            <input type="email" id="contact_email" name="contact_email" value="<?= e($config['contact_email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="contact_phone">Téléphone</label>
            <input type="text" id="contact_phone" name="contact_phone" value="<?= e($config['contact_phone'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-card">
        <h3>Textes du site</h3>
        <div class="form-group">
            <label for="about_text">Texte "À propos"</label>
            <textarea id="about_text" name="about_text" rows="4"><?= e($config['about_text'] ?? '') ?></textarea>
            <button type="button" class="btn btn-sm btn-outline" style="margin-top: 8px;" onclick="improveText('about_text')">Améliorer avec l'IA</button>
        </div>
        <div class="form-group">
            <label for="artist_bio">Parcours de l'artiste (affiché sur la Home)</label>
            <textarea id="artist_bio" name="artist_bio" rows="6"><?= e($config['artist_bio'] ?? '') ?></textarea>
            <button type="button" class="btn btn-sm btn-outline" style="margin-top: 8px;" onclick="improveText('artist_bio')">Améliorer avec l'IA</button>
        </div>
        <div class="form-group">
            <label for="timeline_data">Timeline (une étape par ligne : année | titre | description)</label>
            <textarea id="timeline_data" name="timeline_data" rows="6" placeholder="2020 | Début de la peinture | Découverte de l'art à travers l'acrylique&#10;2022 | Première exposition | ..."><?= e($config['timeline_data'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="shipping_info">Informations de livraison</label>
            <textarea id="shipping_info" name="shipping_info" rows="3"><?= e($config['shipping_info'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="admin-card">
        <h3>Stripe (Carte bancaire)</h3>
        <div class="form-group">
            <label for="stripe_public_key">Clé publique</label>
            <input type="text" id="stripe_public_key" name="stripe_public_key" value="<?= e($config['stripe_public_key'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="stripe_secret_key">Clé secrète</label>
            <input type="password" id="stripe_secret_key" name="stripe_secret_key" value="<?= e($config['stripe_secret_key'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-card">
        <h3>PayPal</h3>
        <div class="form-group">
            <label for="paypal_client_id">Client ID</label>
            <input type="text" id="paypal_client_id" name="paypal_client_id" value="<?= e($config['paypal_client_id'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="paypal_secret">Secret</label>
            <input type="password" id="paypal_secret" name="paypal_secret" value="<?= e($config['paypal_secret'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="paypal_mode">Mode</label>
            <select id="paypal_mode" name="paypal_mode">
                <option value="sandbox" <?= ($config['paypal_mode'] ?? '') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option>
                <option value="live" <?= ($config['paypal_mode'] ?? '') === 'live' ? 'selected' : '' ?>>Live</option>
            </select>
        </div>
    </div>

    <div class="admin-card">
        <h3>Virement bancaire</h3>
        <div class="form-group">
            <label for="bank_name">Titulaire du compte</label>
            <input type="text" id="bank_name" name="bank_name" value="<?= e($config['bank_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="bank_iban">IBAN</label>
            <input type="text" id="bank_iban" name="bank_iban" value="<?= e($config['bank_iban'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="bank_bic">BIC</label>
            <input type="text" id="bank_bic" name="bank_bic" value="<?= e($config['bank_bic'] ?? '') ?>">
        </div>
    </div>

    <div class="admin-card">
        <h3>Livraison (Packlink)</h3>
        <div class="form-group">
            <label for="packlink_api_key">Clé API Packlink PRO</label>
            <input type="password" id="packlink_api_key" name="packlink_api_key" value="<?= e($config['packlink_api_key'] ?? '') ?>" placeholder="Optionnel - pour la génération d'étiquettes">
        </div>

        <div style="margin-top: 20px;">
            <h4 style="margin-bottom: 12px;">Transporteurs</h4>

            <div class="shipping-admin-row">
                <label>
                    <input type="checkbox" name="shipping_mondial_relay_enabled" value="1" <?= ($config['shipping_mondial_relay_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                    Mondial Relay
                </label>
                <div class="form-group" style="display: inline-block; width: 120px; margin-left: 12px;">
                    <input type="number" step="0.01" min="0" name="shipping_mondial_relay_price" value="<?= e($config['shipping_mondial_relay_price'] ?? '6.90') ?>" placeholder="Prix">
                </div>
                <span style="color: var(--text-light); font-size: 0.85rem;">EUR</span>
            </div>

            <div class="shipping-admin-row">
                <label>
                    <input type="checkbox" name="shipping_shop2shop_enabled" value="1" <?= ($config['shipping_shop2shop_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                    Shop2Shop (Relais Colis)
                </label>
                <div class="form-group" style="display: inline-block; width: 120px; margin-left: 12px;">
                    <input type="number" step="0.01" min="0" name="shipping_shop2shop_price" value="<?= e($config['shipping_shop2shop_price'] ?? '5.90') ?>" placeholder="Prix">
                </div>
                <span style="color: var(--text-light); font-size: 0.85rem;">EUR</span>
            </div>

            <div class="shipping-admin-row">
                <label>
                    <input type="checkbox" name="shipping_ups_enabled" value="1" <?= ($config['shipping_ups_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                    UPS Standard
                </label>
                <div class="form-group" style="display: inline-block; width: 120px; margin-left: 12px;">
                    <input type="number" step="0.01" min="0" name="shipping_ups_price" value="<?= e($config['shipping_ups_price'] ?? '12.90') ?>" placeholder="Prix">
                </div>
                <span style="color: var(--text-light); font-size: 0.85rem;">EUR</span>
            </div>

            <div class="shipping-admin-row">
                <label>
                    <input type="checkbox" name="shipping_pickup_enabled" value="1" <?= ($config['shipping_pickup_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                    Retrait à domicile / en main propre (gratuit)
                </label>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer les paramètres</button>
</form>
