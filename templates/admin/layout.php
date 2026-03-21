<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - Vogel Art Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime(PUBLIC_PATH . '/css/style.css') ?>">
    <link rel="stylesheet" href="/css/admin.css?v=<?= filemtime(PUBLIC_PATH . '/css/admin.css') ?>">
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
        <div id="top-mobile-nav" style="display:flex;background:#2D2D2D;padding:8px 4px;margin:-24px -32px 16px;gap:0;align-items:center;">
            <a href="/admin" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'dashboard' ? '#C9A96E' : '#999' ?>;font-size:9px;text-decoration:none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Accueil</a>
            <a href="/admin/tableaux" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'paintings' ? '#C9A96E' : '#999' ?>;font-size:9px;text-decoration:none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Tableaux</a>
            <a href="/admin/tableaux/ajouter" style="flex:0 0 44px;display:flex;align-items:center;justify-content:center;background:#C9A96E;width:44px;height:44px;border-radius:50%;margin:0 6px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></a>
            <a href="/admin/commandes" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'orders' ? '#C9A96E' : '#999' ?>;font-size:9px;text-decoration:none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>Commandes</a>
            <a href="/admin/parametres" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'settings' ? '#C9A96E' : '#999' ?>;font-size:9px;text-decoration:none;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>Réglages</a>
        </div>
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

    <nav id="mobile-nav" style="display:flex;position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #E8E4DF;z-index:99999;padding:4px 0;padding-bottom:max(4px,env(safe-area-inset-bottom));box-shadow:0 -2px 10px rgba(0,0,0,0.06);align-items:flex-end;">
        <a href="/admin" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'dashboard' ? '#A8853E' : '#6B6B6B' ?>;font-size:10px;text-decoration:none;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Accueil
        </a>
        <a href="/admin/tableaux" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'paintings' ? '#A8853E' : '#6B6B6B' ?>;font-size:10px;text-decoration:none;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Tableaux
        </a>
        <a href="/admin/tableaux/ajouter" style="position:relative;top:-16px;background:#C9A96E;width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(201,169,110,0.45);flex:0 0 50px;margin:0 4px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </a>
        <a href="/admin/commandes" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'orders' ? '#A8853E' : '#6B6B6B' ?>;font-size:10px;text-decoration:none;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Commandes
        </a>
        <a href="/admin/parametres" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= ($page ?? '') === 'settings' ? '#A8853E' : '#6B6B6B' ?>;font-size:10px;text-decoration:none;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            Réglages
        </a>
    </nav>

    <script>
    (function(){
        var bottomNav = document.getElementById('mobile-nav');
        var topNav = document.getElementById('top-mobile-nav');
        var sidebar = document.querySelector('.admin-sidebar');
        var main = document.querySelector('.admin-main');
        if(window.innerWidth > 1024){
            if(bottomNav) bottomNav.style.display = 'none';
            if(topNav) topNav.style.display = 'none';
        } else {
            if(sidebar) sidebar.style.display = 'none';
            if(main) { main.style.marginLeft = '0'; main.style.padding = '12px 14px 90px'; }
        }
    })();
    </script>
    <script src="/js/admin.js?v=<?= filemtime(PUBLIC_PATH . '/js/admin.js') ?>"></script>
</body>
</html>
