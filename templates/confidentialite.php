<section class="section">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <h1 class="section-title">Politique de confidentialité</h1>

        <div class="order-summary" style="padding: 32px; line-height: 1.8;">
            <h3>Introduction</h3>
            <p>Vogel Art Gallery s'engage à protéger la vie privée de ses utilisateurs. La présente politique de confidentialité décrit les données personnelles collectées, leurs finalités et vos droits conformément au Règlement Général sur la Protection des Données (RGPD).</p>

            <h3 style="margin-top: 24px;">Responsable du traitement</h3>
            <p>
                <?= e($contactInfo['gallery_name'] ?? 'Vogel Art Gallery') ?><br>
                <?= e(trim(($contactInfo['owner_firstname'] ?? '') . ' ' . ($contactInfo['owner_lastname'] ?? ''))) ?><br>
                Email : <?= e($contactInfo['contact_email'] ?? '') ?>
            </p>

            <h3 style="margin-top: 24px;">Données collectées</h3>
            <p>Nous collectons les données suivantes dans le cadre du traitement de vos commandes :</p>
            <ul style="margin: 8px 0; padding-left: 24px;">
                <li>Nom et prénom</li>
                <li>Adresse email</li>
                <li>Numéro de téléphone (optionnel)</li>
                <li>Adresse postale de livraison</li>
            </ul>

            <h3 style="margin-top: 24px;">Finalités du traitement</h3>
            <p>Vos données sont collectées pour :</p>
            <ul style="margin: 8px 0; padding-left: 24px;">
                <li>Le traitement et la livraison de vos commandes</li>
                <li>L'envoi des emails de confirmation et de suivi</li>
                <li>La gestion de la relation client (SAV, retours)</li>
                <li>L'établissement de factures</li>
            </ul>

            <h3 style="margin-top: 24px;">Base légale</h3>
            <p>Le traitement de vos données repose sur l'exécution du contrat de vente (article 6.1.b du RGPD) et, pour les cookies analytiques, sur votre consentement.</p>

            <h3 style="margin-top: 24px;">Durée de conservation</h3>
            <p>Les données liées aux commandes sont conservées pendant 5 ans à compter de la dernière commande, conformément aux obligations comptables et fiscales. Les données du panier (cookies de session) expirent à la fermeture du navigateur.</p>

            <h3 style="margin-top: 24px;">Destinataires des données</h3>
            <p>Vos données peuvent être transmises à :</p>
            <ul style="margin: 8px 0; padding-left: 24px;">
                <li><strong>PayPal / Stripe</strong> : pour le traitement des paiements</li>
                <li><strong>Packlink / transporteurs</strong> : pour la livraison (nom, adresse, téléphone)</li>
                <li><strong>Google Analytics</strong> : données de navigation anonymisées</li>
            </ul>
            <p>Aucune donnée n'est vendue ou cédée à des tiers à des fins commerciales.</p>

            <h3 style="margin-top: 24px;">Cookies</h3>
            <p>Le site utilise les cookies suivants :</p>
            <ul style="margin: 8px 0; padding-left: 24px;">
                <li><strong>PHPSESSID</strong> : cookie de session (panier, connexion admin) - Nécessaire</li>
                <li><strong>Google Analytics</strong> : mesure d'audience - Analytique</li>
            </ul>
            <p>Vous pouvez désactiver les cookies dans les paramètres de votre navigateur.</p>

            <h3 style="margin-top: 24px;">Vos droits</h3>
            <p>Conformément au RGPD, vous disposez des droits suivants :</p>
            <ul style="margin: 8px 0; padding-left: 24px;">
                <li><strong>Droit d'accès</strong> : obtenir une copie de vos données</li>
                <li><strong>Droit de rectification</strong> : corriger vos données</li>
                <li><strong>Droit de suppression</strong> : demander l'effacement de vos données</li>
                <li><strong>Droit de portabilité</strong> : recevoir vos données dans un format lisible</li>
                <li><strong>Droit d'opposition</strong> : vous opposer au traitement de vos données</li>
            </ul>
            <p>Pour exercer ces droits, contactez-nous à : <strong><?= e($contactInfo['contact_email'] ?? '') ?></strong></p>

            <h3 style="margin-top: 24px;">Sécurité</h3>
            <p>Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos données : chiffrement SSL/TLS, mots de passe hashés, accès restreint aux données. Les données de paiement sont traitées directement par PayPal/Stripe et ne transitent pas par nos serveurs.</p>

            <h3 style="margin-top: 24px;">Réclamation</h3>
            <p>Si vous estimez que le traitement de vos données ne respecte pas la réglementation, vous pouvez adresser une réclamation à la CNIL : <a href="https://www.cnil.fr" target="_blank" style="color: var(--gold);">www.cnil.fr</a></p>

            <p style="margin-top: 24px; color: var(--text-light); font-size: 0.85rem;">Dernière mise à jour : mars 2026</p>
        </div>
    </div>
</section>
