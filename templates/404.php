<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="/" class="logo">Vogel <span>Art</span></a>
            <nav>
                <a href="/">Accueil</a>
                <a href="/boutique">Boutique</a>
                <a href="/contact">Contact</a>
            </nav>
        </div>
    </header>
    <main>
        <div class="empty-state" style="padding: 100px 0;">
            <h1 style="font-size: 4rem; color: var(--gold);">404</h1>
            <h2>Page non trouvée</h2>
            <p>La page que vous cherchez n'existe pas ou a été déplacée.</p>
            <a href="/" class="btn btn-primary" style="margin-top: 20px;">Retour à l'accueil</a>
        </div>
    </main>
</body>
</html>
