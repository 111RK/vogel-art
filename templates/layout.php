<!DOCTYPE html>
<html lang="fr">
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KV8NJHE290"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-KV8NJHE290');</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Accueil') ?> - <?= SITE_NAME ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'Vogel Art - Tableaux peints à la main au couteau, pièces uniques. Art original fait avec passion.') ?>">
    <?php if (!empty($ogTags)): ?>
    <meta property="og:title" content="<?= e($ogTags['title']) ?>">
    <meta property="og:description" content="<?= e($ogTags['description']) ?>">
    <meta property="og:url" content="<?= e($ogTags['url']) ?>">
    <meta property="og:type" content="<?= e($ogTags['type'] ?? 'website') ?>">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <?php if (!empty($ogTags['image'])): ?><meta property="og:image" content="<?= e($ogTags['image']) ?>"><?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime(PUBLIC_PATH . '/css/style.css') ?>">
</head>
<body>
    <header>
        <div class="container">
            <a href="/" class="logo"><img src="/img/logo.svg" alt="Vogel Art Gallery" style="height:80px;"></a>
            <button class="hamburger" onclick="document.querySelector('nav').classList.toggle('active')">
                <span></span><span></span><span></span>
            </button>
            <nav>
                <a href="/">Accueil</a>
                <a href="/boutique">Boutique</a>
                <a href="/blog">Blog</a>
                <a href="/faq">FAQ</a>
                <a href="/contact">Contact</a>
                <a href="/panier" class="cart-link">
                    Panier
                    <?php if (getCartCount() > 0): ?>
                        <span class="cart-count"><?= getCartCount() ?></span>
                    <?php endif; ?>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <?php if ($msg = flash('success')): ?>
            <div class="container"><div class="flash flash-success"><?= e($msg) ?></div></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="container"><div class="flash flash-error"><?= e($msg) ?></div></div>
        <?php endif; ?>
        <?php if ($msg = flash('info')): ?>
            <div class="container"><div class="flash flash-info"><?= e($msg) ?></div></div>
        <?php endif; ?>

        <?php require TEMPLATE_PATH . '/' . $content . '.php'; ?>
    </main>

    <footer>
        <div class="container">
            <div>
                <a href="/"><img src="/img/logo.svg" alt="Vogel Art Gallery" style="height:60px;margin-bottom:12px;"></a>
                <p>Chaque toile est une pièce unique, peinte à la main avec passion.</p>
            </div>
            <div>
                <h4>Navigation</h4>
                <ul>
                    <li><a href="/boutique">Boutique</a></li>
                    <li><a href="/blog">Blog</a></li>
                    <li><a href="/a-propos">À propos</a></li>
                    <li><a href="/contact">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Informations</h4>
                <ul>
                    <li><a href="/livraison">Livraison</a></li>
                    <li><a href="/cgv">CGV</a></li>
                    <li><a href="/mentions-legales">Mentions légales</a></li>
                    <li><a href="/confidentialite">Confidentialité</a></li>
                    <li><a href="/faq">FAQ</a></li>
                    <li><a href="/suivi">Suivi de commande</a></li>
                    <li><a href="/contact">Nous contacter</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Vogel Art. Tous droits réservés.</p>
        </div>
    </footer>

    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"Organization","name":"Vogel Art Gallery","url":"<?= SITE_URL ?>","logo":"<?= SITE_URL ?>/img/logo.svg","description":"Tableaux originaux peints à la main au couteau. Pièces uniques, art authentique.","sameAs":[]}
    </script>
    <script src="/js/app.js?v=<?= filemtime(PUBLIC_PATH . '/js/app.js') ?>"></script>
</body>
</html>
