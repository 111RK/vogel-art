<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - Vogel Art Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <a href="/admin">Vogel <span>Art</span> Gallery</a>
        </div>
        <nav class="admin-nav">
            <a href="/admin" class="<?= ($page ?? '') === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="/admin/tableaux" class="<?= ($page ?? '') === 'paintings' ? 'active' : '' ?>">Tableaux</a>
            <a href="/admin/commandes" class="<?= ($page ?? '') === 'orders' ? 'active' : '' ?>">Commandes</a>
            <a href="/admin/parametres" class="<?= ($page ?? '') === 'settings' ? 'active' : '' ?>">Paramètres</a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="/" target="_blank">Voir le site</a>
            <a href="/admin/logout">Déconnexion</a>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1><?= e($pageTitle ?? 'Admin') ?></h1>
            <span>Bonjour, <?= e(Auth::currentUser()['name'] ?? 'Admin') ?></span>
        </div>

        <?php if ($msg = flash('success')): ?>
            <div class="flash flash-success"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="flash flash-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <?php require TEMPLATE_PATH . '/admin/' . $content . '.php'; ?>
    </main>

    <script src="/js/admin.js"></script>
</body>
</html>
