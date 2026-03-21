<?php $tab = $tab ?? 'general'; ?>
<div class="settings-tabs">
    <a href="?tab=general" class="settings-tab <?= $tab === 'general' ? 'active' : '' ?>">Général</a>
    <a href="?tab=paiement" class="settings-tab <?= $tab === 'paiement' ? 'active' : '' ?>">Paiement</a>
    <a href="?tab=livraison" class="settings-tab <?= $tab === 'livraison' ? 'active' : '' ?>">Livraison</a>
    <a href="?tab=site" class="settings-tab <?= $tab === 'site' ? 'active' : '' ?>">Contenu du site</a>
</div>

<?php if ($tab === 'general'): ?>
<form method="POST" action="/admin/parametres" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="general">

    <div class="admin-card">
        <h3>Galerie</h3>
        <div class="form-group">
            <label for="gallery_name">Nom de la galerie</label>
            <input type="text" id="gallery_name" name="gallery_name" value="<?= e($config['gallery_name'] ?? 'Vogel Art Gallery') ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="owner_firstname">Prénom du propriétaire</label>
                <input type="text" id="owner_firstname" name="owner_firstname" value="<?= e($config['owner_firstname'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="owner_lastname">Nom</label>
                <input type="text" id="owner_lastname" name="owner_lastname" value="<?= e($config['owner_lastname'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h3>Coordonnées</h3>
        <div class="form-group">
            <label for="contact_address">Adresse</label>
            <input type="text" id="contact_address" name="contact_address" value="<?= e($config['contact_address'] ?? '') ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="contact_city">Ville</label>
                <input type="text" id="contact_city" name="contact_city" value="<?= e($config['contact_city'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="contact_postal">Code postal</label>
                <input type="text" id="contact_postal" name="contact_postal" value="<?= e($config['contact_postal'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="contact_phone">Téléphone</label>
                <input type="text" id="contact_phone" name="contact_phone" value="<?= e($config['contact_phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="contact_email">Email</label>
                <input type="email" id="contact_email" name="contact_email" value="<?= e($config['contact_email'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h3>Photo de contact</h3>
        <?php if (!empty($config['contact_photo'])): ?>
            <img src="/uploads/<?= e($config['contact_photo']) ?>" alt="" style="width: 200px; border-radius: 8px; margin-bottom: 12px;">
            <label><input type="checkbox" name="remove_contact_photo" value="1"> Supprimer la photo</label>
        <?php endif; ?>
        <div class="form-group" style="margin-top: 8px;">
            <input type="file" name="contact_photo" accept="image/jpeg,image/png,image/webp">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>

<?php elseif ($tab === 'paiement'): ?>
<form method="POST" action="/admin/parametres" class="admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="paiement">

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

    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>

<?php elseif ($tab === 'livraison'): ?>
<form method="POST" action="/admin/parametres" class="admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="livraison">

    <div class="admin-card">
        <h3>Packlink PRO</h3>
        <div class="form-group">
            <label for="packlink_api_key">Clé API</label>
            <input type="password" id="packlink_api_key" name="packlink_api_key" value="<?= e($config['packlink_api_key'] ?? '') ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="packlink_sender_name">Nom expéditeur</label>
                <input type="text" id="packlink_sender_name" name="packlink_sender_name" value="<?= e($config['packlink_sender_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="packlink_sender_address">Adresse</label>
                <input type="text" id="packlink_sender_address" name="packlink_sender_address" value="<?= e($config['packlink_sender_address'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="packlink_sender_city">Ville</label>
                <input type="text" id="packlink_sender_city" name="packlink_sender_city" value="<?= e($config['packlink_sender_city'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="packlink_sender_postal">Code postal</label>
                <input type="text" id="packlink_sender_postal" name="packlink_sender_postal" value="<?= e($config['packlink_sender_postal'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="default_parcel_weight">Poids par défaut (kg)</label>
                <input type="number" step="0.1" min="0.1" id="default_parcel_weight" name="default_parcel_weight" value="<?= e($config['default_parcel_weight'] ?? '2') ?>">
            </div>
            <div class="form-group">
                <label for="default_parcel_dimensions">Dimensions (LxlxH cm)</label>
                <input type="text" id="default_parcel_dimensions" name="default_parcel_dimensions" value="<?= e($config['default_parcel_dimensions'] ?? '60x50x10') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h3>Transporteurs</h3>
        <div class="shipping-admin-row">
            <label><input type="checkbox" name="shipping_pickup_enabled" value="1" <?= ($config['shipping_pickup_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> Retrait en main propre (gratuit)</label>
        </div>
        <div class="shipping-admin-row">
            <label><input type="checkbox" name="shipping_mondial_relay_enabled" value="1" <?= ($config['shipping_mondial_relay_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> Mondial Relay - Point Relais</label>
            <div class="form-group" style="display:inline-block;width:120px;margin-left:12px;"><input type="number" step="0.01" min="0" name="shipping_mondial_relay_price" value="<?= e($config['shipping_mondial_relay_price'] ?? '6.90') ?>"></div> <span style="color:var(--text-light);font-size:0.85rem;">EUR</span>
        </div>
        <div class="shipping-admin-row">
            <label><input type="checkbox" name="shipping_shop2shop_enabled" value="1" <?= ($config['shipping_shop2shop_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> Chronopost - Shop2Shop</label>
            <div class="form-group" style="display:inline-block;width:120px;margin-left:12px;"><input type="number" step="0.01" min="0" name="shipping_shop2shop_price" value="<?= e($config['shipping_shop2shop_price'] ?? '5.90') ?>"></div> <span style="color:var(--text-light);font-size:0.85rem;">EUR</span>
        </div>
        <div class="shipping-admin-row">
            <label><input type="checkbox" name="shipping_ups_enabled" value="1" <?= ($config['shipping_ups_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> UPS - Access Point</label>
            <div class="form-group" style="display:inline-block;width:120px;margin-left:12px;"><input type="number" step="0.01" min="0" name="shipping_ups_price" value="<?= e($config['shipping_ups_price'] ?? '12.90') ?>"></div> <span style="color:var(--text-light);font-size:0.85rem;">EUR</span>
        </div>
        <div class="shipping-admin-row">
            <label><input type="checkbox" name="shipping_mondial_relay_domicile_enabled" value="1" <?= ($config['shipping_mondial_relay_domicile_enabled'] ?? '0') === '1' ? 'checked' : '' ?>> Mondial Relay - Domicile</label>
            <div class="form-group" style="display:inline-block;width:120px;margin-left:12px;"><input type="number" step="0.01" min="0" name="shipping_mondial_relay_domicile_price" value="<?= e($config['shipping_mondial_relay_domicile_price'] ?? '8.90') ?>"></div> <span style="color:var(--text-light);font-size:0.85rem;">EUR</span>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>

<?php elseif ($tab === 'site'): ?>
<form method="POST" action="/admin/parametres" class="admin-form">
    <?= csrf_field() ?>
    <input type="hidden" name="tab" value="site">

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
            <textarea id="timeline_data" name="timeline_data" rows="6" placeholder="2020 | Début de la peinture | ..."><?= e($config['timeline_data'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="shipping_info">Informations de livraison</label>
            <textarea id="shipping_info" name="shipping_info" rows="3"><?= e($config['shipping_info'] ?? '') ?></textarea>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
<?php endif; ?>
