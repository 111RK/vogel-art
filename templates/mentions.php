<section class="section">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <h1 class="section-title">Mentions légales</h1>

        <div class="order-summary" style="padding: 32px; line-height: 1.8;">
            <h3>Éditeur du site</h3>
            <p>
                <?= e($contactInfo['gallery_name'] ?? 'Vogel Art Gallery') ?><br>
                <?= e(trim(($contactInfo['owner_firstname'] ?? '') . ' ' . ($contactInfo['owner_lastname'] ?? ''))) ?><br>
                <?php if (!empty($contactInfo['contact_address'])): ?>
                    <?= e($contactInfo['contact_address']) ?><br>
                    <?= e(trim(($contactInfo['contact_postal'] ?? '') . ' ' . ($contactInfo['contact_city'] ?? ''))) ?><br>
                <?php endif; ?>
                <?php if (!empty($contactInfo['contact_phone'])): ?>
                    Téléphone : <?= e($contactInfo['contact_phone']) ?><br>
                <?php endif; ?>
                <?php if (!empty($contactInfo['contact_email'])): ?>
                    Email : <?= e($contactInfo['contact_email']) ?>
                <?php endif; ?>
            </p>
            <p>Entrepreneur individuel - TVA non applicable, article 293B du CGI.</p>

            <h3 style="margin-top: 24px;">Hébergeur</h3>
            <p>
                WAI31<br>
                31210 Gourdan-Polignan<br>
                <a href="https://www.wai31.fr" target="_blank" style="color: var(--gold);">www.wai31.fr</a>
            </p>

            <h3 style="margin-top: 24px;">Propriété intellectuelle</h3>
            <p>L'ensemble du contenu du site vogel-art.fr (textes, images, photographies, œuvres d'art, logo, mise en page) est protégé par le droit d'auteur et le Code de la propriété intellectuelle. Toute reproduction, représentation, modification ou adaptation, totale ou partielle, est strictement interdite sans autorisation préalable écrite de l'éditeur.</p>

            <h3 style="margin-top: 24px;">Photographies des œuvres</h3>
            <p>Les photographies des tableaux présentés sur le site sont la propriété exclusive de l'artiste. Elles ne peuvent être utilisées, copiées ou diffusées sans autorisation préalable, même après l'achat de l'œuvre physique.</p>

            <h3 style="margin-top: 24px;">Responsabilité</h3>
            <p>Vogel Art Gallery s'efforce de fournir des informations aussi exactes que possible. Toutefois, il ne pourra être tenu responsable des omissions, inexactitudes ou carences dans la mise à jour. Les photographies des tableaux sont non contractuelles.</p>

            <h3 style="margin-top: 24px;">Cookies et traceurs</h3>
            <p>Le site utilise des cookies de session pour le fonctionnement du panier d'achat et de l'espace d'administration. Google Analytics est utilisé pour analyser la fréquentation du site. Pour en savoir plus, consultez notre <a href="/confidentialite" style="color: var(--gold);">politique de confidentialité</a>.</p>

            <h3 style="margin-top: 24px;">Droit applicable</h3>
            <p>Le présent site est soumis au droit français. Tout litige relatif à l'utilisation du site sera soumis à la compétence exclusive des tribunaux français.</p>

            <p style="margin-top: 24px; color: var(--text-light); font-size: 0.85rem;">Dernière mise à jour : mars 2026</p>
        </div>
    </div>
</section>
