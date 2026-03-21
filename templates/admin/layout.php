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
    <aside class="admin-sidebar" id="desktop-sidebar">
        <div class="admin-logo">
            <a href="/admin">Vogel <span>Art</span> Gallery</a>
        </div>
        <nav class="admin-nav">
            <a href="/admin" class="<?= ($page ?? '') === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="/admin/tableaux" class="<?= ($page ?? '') === 'paintings' ? 'active' : '' ?>">Tableaux</a>
            <a href="/admin/commandes" class="<?= ($page ?? '') === 'orders' ? 'active' : '' ?>">Commandes</a>
            <a href="/admin/blog" class="<?= ($page ?? '') === 'blog' ? 'active' : '' ?>">Blog</a>
            <a href="/admin/faq" class="<?= ($page ?? '') === 'faq' ? 'active' : '' ?>">FAQ</a>
            <a href="/admin/promos" class="<?= ($page ?? '') === 'promos' ? 'active' : '' ?>">Codes promo</a>
            <a href="/admin/parametres" class="<?= ($page ?? '') === 'settings' ? 'active' : '' ?>">Paramètres</a>
            <a href="/admin/utilisateurs" class="<?= ($page ?? '') === 'users' ? 'active' : '' ?>">Utilisateurs</a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="/" target="_blank">Voir le site</a>
            <a href="/admin/logout">Déconnexion</a>
        </div>
    </aside>

    <main class="admin-main" id="admin-main">
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

        <div id="mob-footer" style="display:none;margin-top:32px;padding-top:16px;border-top:1px solid #E8E4DF;text-align:center;">
            <a href="/" target="_blank" style="color:#C9A96E;font-size:13px;text-decoration:none;margin-right:16px;">Voir le site</a>
            <a href="/admin/logout" style="color:#999;font-size:13px;text-decoration:none;">Déconnexion</a>
        </div>
    </main>

    <?php $currentPage = $page ?? ''; ?>

    <div id="mob-menu-panel" style="display:none;position:fixed;bottom:70px;left:8px;right:8px;background:#2D2D2D;border-radius:16px;padding:20px;z-index:99998;box-shadow:0 -4px 24px rgba(0,0,0,0.3);max-height:70vh;overflow-y:auto;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            <?php
            $allMenuItems = [
                ['url' => '/admin', 'key' => 'dashboard', 'label' => 'Accueil', 'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
                ['url' => '/admin/tableaux', 'key' => 'paintings', 'label' => 'Tableaux', 'icon' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>'],
                ['url' => '/admin/commandes', 'key' => 'orders', 'label' => 'Commandes', 'icon' => '<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>'],
                ['url' => '/admin/blog', 'key' => 'blog', 'label' => 'Blog', 'icon' => '<path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>'],
                ['url' => '/admin/faq', 'key' => 'faq', 'label' => 'FAQ', 'icon' => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
                ['url' => '/admin/parametres', 'key' => 'settings', 'label' => 'Réglages', 'icon' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>'],
                ['url' => '/admin/utilisateurs', 'key' => 'users', 'label' => 'Utilisateurs', 'icon' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>'],
                ['url' => '/admin/tableaux/ajouter', 'key' => '_add2', 'label' => 'Ajouter', 'icon' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>'],
                ['url' => '/', 'key' => '_site', 'label' => 'Voir le site', 'icon' => '<path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>'],
            ];
            foreach ($allMenuItems as $mi):
            ?>
                <a href="<?= $mi['url'] ?>" <?= $mi['key'] === '_site' ? 'target="_blank"' : '' ?> style="display:flex;flex-direction:column;align-items:center;gap:6px;padding:14px 8px;background:<?= $currentPage === $mi['key'] ? 'rgba(201,169,110,0.15)' : 'rgba(255,255,255,0.05)' ?>;border-radius:12px;color:<?= $currentPage === $mi['key'] ? '#C9A96E' : '#ccc' ?>;font-size:11px;text-decoration:none;font-family:Inter,Arial,sans-serif;text-align:center;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?= $mi['icon'] ?></svg>
                    <?= $mi['label'] ?>
                </a>
            <?php endforeach; ?>
        </div>
        <a href="/admin/logout" style="display:block;text-align:center;margin-top:16px;padding:10px;color:#999;font-size:12px;text-decoration:none;border-top:1px solid rgba(255,255,255,0.1);">Déconnexion</a>
    </div>

    <div id="mob-nav" style="background:#2D2D2D;padding:8px 0;padding-bottom:max(8px,env(safe-area-inset-bottom));display:flex;align-items:flex-end;box-shadow:0 -2px 10px rgba(0,0,0,0.15);">
        <a href="/admin" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= $currentPage === 'dashboard' ? '#C9A96E' : '#999' ?>;font-size:10px;text-decoration:none;font-family:Inter,Arial,sans-serif;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Accueil
        </a>
        <a href="/admin/tableaux" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= $currentPage === 'paintings' ? '#C9A96E' : '#999' ?>;font-size:10px;text-decoration:none;font-family:Inter,Arial,sans-serif;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Tableaux
        </a>
        <a href="/admin/tableaux/ajouter" style="flex:0 0 48px;display:flex;align-items:center;justify-content:center;background:#C9A96E;width:48px;height:48px;border-radius:50%;margin:0 6px;position:relative;top:-10px;box-shadow:0 4px 14px rgba(201,169,110,0.45);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </a>
        <a href="/admin/commandes" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:<?= $currentPage === 'orders' ? '#C9A96E' : '#999' ?>;font-size:10px;text-decoration:none;font-family:Inter,Arial,sans-serif;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Commandes
        </a>
        <a href="#" onclick="event.preventDefault();var p=document.getElementById('mob-menu-panel');p.style.display=p.style.display==='none'?'block':'none';" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px;padding:6px 0;color:#999;font-size:10px;text-decoration:none;font-family:Inter,Arial,sans-serif;" id="mob-menu-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
            Plus
        </a>
    </div>

    <script>
    if(window.innerWidth > 1024){
        document.getElementById('mob-nav').style.display='none';
    } else {
        document.getElementById('desktop-sidebar').style.display='none';
        document.body.style.flexDirection='column';
        var m=document.getElementById('admin-main');
        m.style.marginLeft='0';
        m.style.padding='12px 14px 20px';
        m.style.flex='1';
        document.getElementById('mob-nav').style.position='sticky';
        document.getElementById('mob-nav').style.bottom='0';
        document.getElementById('mob-footer').style.display='block';
    }
    </script>
    <script src="/js/admin.js?v=<?= filemtime(PUBLIC_PATH . '/js/admin.js') ?>"></script>
</body>
</html>
