<?php
require __DIR__ . '/config.php';
echo '<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1.0"></head><body>';
echo '<p>Screen test - Public path: ' . PUBLIC_PATH . '</p>';
echo '<p>admin.css exists: ' . (file_exists(PUBLIC_PATH . '/css/admin.css') ? 'YES' : 'NO') . '</p>';
echo '<p>filemtime: ' . filemtime(PUBLIC_PATH . '/css/admin.css') . '</p>';

echo '<nav style="position:fixed;bottom:0;left:0;right:0;background:#2D2D2D;display:flex;padding:12px 0;z-index:99999;">';
echo '<a href="/admin" style="flex:1;text-align:center;color:#C9A96E;font-size:14px;text-decoration:none;">TEST BARRE VISIBLE</a>';
echo '</nav>';

echo '<p style="margin-top:80px;">Si tu vois une barre noire en bas avec "TEST BARRE VISIBLE" en doré, le HTML fonctionne.</p>';
echo '</body></html>';
