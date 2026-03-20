<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Accueil') ?> - <?= SITE_NAME ?></title>
    <meta name="description" content="Vogel Art - Tableaux peints à la main, pièces uniques. Art original fait avec passion.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="/" class="logo">Vogel <span>Art</span> Gallery</a>
            <button class="hamburger" onclick="document.querySelector('nav').classList.toggle('active')">
                <span></span><span></span><span></span>
            </button>
            <nav>
                <a href="/">Accueil</a>
                <a href="/boutique">Boutique</a>
                <a href="/a-propos">À propos</a>
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
                <div class="footer-brand">Vogel <span>Art</span> Gallery</div>
                <p>Chaque toile est une pièce unique, peinte à la main avec passion.</p>
            </div>
            <div>
                <h4>Navigation</h4>
                <ul>
                    <li><a href="/boutique">Boutique</a></li>
                    <li><a href="/a-propos">À propos</a></li>
                    <li><a href="/contact">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Informations</h4>
                <ul>
                    <li><a href="/cgv">CGV</a></li>
                    <li><a href="/contact">Nous contacter</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Vogel Art. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="/js/app.js"></script>
</body>
</html>
