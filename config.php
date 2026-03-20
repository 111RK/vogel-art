<?php

define('DEBUG', false);

define('SITE_URL', 'http://198.186.131.142');
define('SITE_NAME', 'Vogel Art');

define('DB_HOST', 'localhost');
define('DB_NAME', 'vogel_ar1_bdd');
define('DB_USER', 'vogel_ar1');
define('DB_PASS', 'kUo5zQjocxPn1bK6');

define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__ . '/public_html');
define('UPLOAD_PATH', __DIR__ . '/public_html/uploads');
define('TEMPLATE_PATH', __DIR__ . '/templates');

define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

define('CLAUDE_API_KEY', '');
define('CLAUDE_MODEL', 'claude-sonnet-4-20250514');

define('STRIPE_PUBLIC_KEY', '');
define('STRIPE_SECRET_KEY', '');

define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_SECRET', '');
define('PAYPAL_MODE', 'sandbox');

define('BANK_IBAN', '');
define('BANK_BIC', '');
define('BANK_NAME', '');

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
