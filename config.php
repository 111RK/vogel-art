<?php

define('DEBUG', false);

define('SITE_URL', 'https://vogel-art.fr');
define('SITE_NAME', 'Vogel Art');

define('DB_HOST', 'localhost');
define('DB_NAME', 'vogel_ar1_bdd');
define('DB_USER', 'vogel_ar1');
define('DB_PASS', 'kUo5zQjocxPn1bK6');

define('ROOT_PATH', __DIR__);
define('PUBLIC_PATH', __DIR__ . '/public_html');
define('UPLOAD_PATH', __DIR__ . '/public_html/uploads');
define('TEMPLATE_PATH', __DIR__ . '/templates');

define('MAX_UPLOAD_SIZE', 25 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_VIDEO_EXTENSIONS', ['mp4', 'mov', 'webm']);
define('MAX_VIDEO_SIZE', 100 * 1024 * 1024);


define('ILOVEIMG_PUBLIC_KEY', 'project_public_8e085d98e76012d6becda70c52049008_e2jCo4f5061531081115f9abfb5f4d11c4cff');
define('ILOVEIMG_SECRET_KEY', 'secret_key_6a2e3025b7da1e43cf34587def57c7ca_YwwPkf56c2338c4448822caa7e90bc8706a27');

define('STRIPE_PUBLIC_KEY', '');
define('STRIPE_SECRET_KEY', '');

define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_SECRET', '');
define('PAYPAL_MODE', 'sandbox');

define('BANK_IBAN', '');
define('BANK_BIC', '');
define('BANK_NAME', '');

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
