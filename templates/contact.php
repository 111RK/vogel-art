<section class="section">
    <div class="container" style="max-width: 900px; margin: 0 auto;">
        <h1 class="section-title">Contact</h1>

        <div class="contact-layout">
            <div class="contact-info">
                <?php if (!empty($contactInfo['gallery_name'])): ?>
                    <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.6rem; margin-bottom: 4px;"><?= e($contactInfo['gallery_name']) ?></h2>
                <?php endif; ?>

                <?php if (!empty($contactInfo['owner_firstname']) || !empty($contactInfo['owner_lastname'])): ?>
                    <p style="color: var(--text-light); font-size: 1.05rem; margin-bottom: 28px;"><?= e(trim($contactInfo['owner_firstname'] . ' ' . $contactInfo['owner_lastname'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($contactInfo['contact_address'])): ?>
                    <div class="contact-detail">
                        <strong>Adresse</strong>
                        <p><?= e($contactInfo['contact_address']) ?></p>
                        <?php if (!empty($contactInfo['contact_postal']) || !empty($contactInfo['contact_city'])): ?>
                            <p><?= e(trim($contactInfo['contact_postal'] . ' ' . $contactInfo['contact_city'])) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($contactInfo['contact_phone'])): ?>
                    <div class="contact-detail">
                        <strong>Téléphone</strong>
                        <a href="tel:<?= e(preg_replace('/\s/', '', $contactInfo['contact_phone'])) ?>"><?= e($contactInfo['contact_phone']) ?></a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($contactInfo['contact_email'])): ?>
                    <?php
                    $emailParts = explode('@', $contactInfo['contact_email']);
                    $emailUser = base64_encode($emailParts[0] ?? '');
                    $emailDomain = base64_encode($emailParts[1] ?? '');
                    ?>
                    <div class="contact-detail">
                        <strong>Email</strong>
                        <span id="email-link" data-u="<?= $emailUser ?>" data-d="<?= $emailDomain ?>">
                            <noscript>Activez JavaScript pour voir l'email</noscript>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($contactInfo['contact_photo'])): ?>
                <div class="contact-photo">
                    <img src="/uploads/<?= e($contactInfo['contact_photo']) ?>" alt="<?= e(trim(($contactInfo['owner_firstname'] ?? '') . ' ' . ($contactInfo['owner_lastname'] ?? ''))) ?>">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function(){
    var el = document.getElementById('email-link');
    if (!el) return;
    var u = atob(el.dataset.u);
    var d = atob(el.dataset.d);
    var addr = u + '@' + d;
    var a = document.createElement('a');
    a.href = 'mai' + 'lto:' + addr;
    a.textContent = addr;
    a.style.color = '#C9A96E';
    a.style.textDecoration = 'none';
    el.innerHTML = '';
    el.appendChild(a);
})();
</script>
