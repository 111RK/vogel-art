<?php
class AdminController
{
    public static function loginForm(): void
    {
        if (Auth::check()) redirect('/admin');
        $pageTitle = 'Connexion';
        require TEMPLATE_PATH . '/admin/login.php';
    }

    public static function login(): void
    {
        if (!verify_csrf()) redirect('/admin/login');

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::login($email, $password)) {
            redirect('/admin');
        }

        flash('error', 'Identifiants incorrects.');
        redirect('/admin/login');
    }

    public static function logout(): void
    {
        Auth::logout();
    }

    public static function dashboard(): void
    {
        Auth::requireAuth();

        $stats = [
            'paintings' => Database::fetch("SELECT COUNT(*) as count FROM paintings WHERE status = 'available'")['count'],
            'sold' => Database::fetch("SELECT COUNT(*) as count FROM paintings WHERE status = 'sold'")['count'],
            'orders' => Database::fetch("SELECT COUNT(*) as count FROM orders")['count'],
            'revenue' => Database::fetch("SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE payment_status = 'paid'")['total'],
        ];

        $recentOrders = Database::fetchAll(
            "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5"
        );

        $pageTitle = 'Dashboard';
        $page = 'dashboard';
        renderAdmin('dashboard', compact('stats', 'recentOrders', 'pageTitle', 'page'));
    }

    public static function paintings(): void
    {
        Auth::requireAuth();

        $paintings = Database::fetchAll("SELECT * FROM paintings ORDER BY created_at DESC");
        $pageTitle = 'Tableaux';
        $page = 'paintings';
        renderAdmin('paintings', compact('paintings', 'pageTitle', 'page'));
    }

    public static function addPaintingForm(): void
    {
        Auth::requireAuth();
        $pageTitle = 'Ajouter un tableau';
        $page = 'paintings';
        renderAdmin('add-painting', compact('pageTitle', 'page'));
    }

    public static function addPainting(): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/tableaux/ajouter');

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $technique = trim($_POST['technique'] ?? '');
        $widthCm = intval($_POST['width_cm'] ?? 0) ?: null;
        $heightCm = intval($_POST['height_cm'] ?? 0) ?: null;
        $featured = isset($_POST['featured']) ? 1 : 0;

        if (empty($title) || $price <= 0) {
            flash('error', 'Le titre et le prix sont obligatoires.');
            redirect('/admin/tableaux/ajouter');
        }

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = uploadImage($_FILES['image']);
            if (!$image) {
                flash('error', 'Erreur lors de l\'upload de l\'image.');
                redirect('/admin/tableaux/ajouter');
            }
        }

        if (!$image) {
            flash('error', 'L\'image est obligatoire.');
            redirect('/admin/tableaux/ajouter');
        }

        $slug = slugify($title);
        $existingSlug = Database::fetch("SELECT id FROM paintings WHERE slug = ?", [$slug]);
        if ($existingSlug) {
            $slug .= '-' . uniqid();
        }

        Database::query(
            "INSERT INTO paintings (title, slug, description, price, image, width_cm, height_cm, technique, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$title, $slug, $description, $price, $image, $widthCm, $heightCm, $technique, $featured]
        );

        flash('success', 'Tableau ajouté avec succès.');
        redirect('/admin/tableaux');
    }

    public static function editPaintingForm(string $id): void
    {
        Auth::requireAuth();

        $painting = Database::fetch("SELECT * FROM paintings WHERE id = ?", [(int)$id]);
        if (!$painting) redirect('/admin/tableaux');

        $pageTitle = 'Modifier : ' . $painting['title'];
        $page = 'paintings';
        renderAdmin('edit-painting', compact('painting', 'pageTitle', 'page'));
    }

    public static function editPainting(string $id): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/tableaux');

        $painting = Database::fetch("SELECT * FROM paintings WHERE id = ?", [(int)$id]);
        if (!$painting) redirect('/admin/tableaux');

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $technique = trim($_POST['technique'] ?? '');
        $widthCm = intval($_POST['width_cm'] ?? 0) ?: null;
        $heightCm = intval($_POST['height_cm'] ?? 0) ?: null;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $status = $_POST['status'] ?? $painting['status'];

        if (empty($title) || $price <= 0) {
            flash('error', 'Le titre et le prix sont obligatoires.');
            redirect('/admin/tableaux/modifier/' . $id);
        }

        $image = $painting['image'];
        if (!empty($_FILES['image']['name'])) {
            $newImage = uploadImage($_FILES['image']);
            if ($newImage) {
                if ($painting['image'] && file_exists(UPLOAD_PATH . '/' . $painting['image'])) {
                    unlink(UPLOAD_PATH . '/' . $painting['image']);
                    $thumbPath = UPLOAD_PATH . '/thumbs/' . $painting['image'];
                    if (file_exists($thumbPath)) unlink($thumbPath);
                }
                $image = $newImage;
            }
        }

        Database::query(
            "UPDATE paintings SET title = ?, description = ?, price = ?, image = ?, width_cm = ?, height_cm = ?, technique = ?, featured = ?, status = ? WHERE id = ?",
            [$title, $description, $price, $image, $widthCm, $heightCm, $technique, $featured, $status, (int)$id]
        );

        flash('success', 'Tableau mis à jour.');
        redirect('/admin/tableaux');
    }

    public static function deletePainting(string $id): void
    {
        Auth::requireAuth();

        $painting = Database::fetch("SELECT * FROM paintings WHERE id = ?", [(int)$id]);
        if ($painting) {
            if ($painting['image'] && file_exists(UPLOAD_PATH . '/' . $painting['image'])) {
                unlink(UPLOAD_PATH . '/' . $painting['image']);
                $thumbPath = UPLOAD_PATH . '/thumbs/' . $painting['image'];
                if (file_exists($thumbPath)) unlink($thumbPath);
            }
            Database::query("DELETE FROM paintings WHERE id = ?", [(int)$id]);
            flash('success', 'Tableau supprimé.');
        }

        redirect('/admin/tableaux');
    }

    public static function upscaleImage(): void
    {
        Auth::requireAuth();
        header('Content-Type: application/json');

        $id = (int)($_POST['painting_id'] ?? 0);
        $painting = Database::fetch("SELECT * FROM paintings WHERE id = ?", [$id]);

        if (!$painting) {
            echo json_encode(['error' => 'Tableau introuvable.']);
            return;
        }

        $filePath = UPLOAD_PATH . '/' . $painting['image'];
        if (!file_exists($filePath)) {
            echo json_encode(['error' => 'Fichier image introuvable.']);
            return;
        }

        $upscaler = new ILoveImgUpscaler();
        $result = $upscaler->upscale($filePath, 2);

        if ($result) {
            $ext = strtolower(pathinfo($painting['image'], PATHINFO_EXTENSION));
            $imageInfo = getimagesize($filePath);
            if ($imageInfo && $imageInfo[0] > 1920) {
                $resized = resizeImage($filePath, $ext, 1920);
                if ($resized) {
                    file_put_contents($filePath, $resized);
                }
            }
            createThumbnail($filePath, $ext);
            echo json_encode(['success' => true, 'message' => 'Image améliorée avec succès.']);
        } else {
            echo json_encode(['error' => 'Erreur lors du traitement. Vérifiez les clés API iLoveIMG.']);
        }
    }

    public static function generateDescription(): void
    {
        Auth::requireAuth();
        header('Content-Type: application/json');

        $title = $_POST['title'] ?? '';
        $technique = $_POST['technique'] ?? '';
        $width = intval($_POST['width_cm'] ?? 0) ?: null;
        $height = intval($_POST['height_cm'] ?? 0) ?: null;

        if (empty($title)) {
            echo json_encode(['error' => 'Veuillez renseigner un titre.']);
            return;
        }

        $description = TextGenerator::generateDescription($title, $technique, $width, $height);
        echo json_encode(['description' => $description]);
    }

    public static function improveText(): void
    {
        Auth::requireAuth();
        header('Content-Type: application/json');

        $text = $_POST['text'] ?? '';

        if (empty($text)) {
            echo json_encode(['error' => 'Aucun texte fourni.']);
            return;
        }

        $improved = TextGenerator::improveText($text);
        echo json_encode(['text' => $improved]);
    }

    public static function orders(): void
    {
        Auth::requireAuth();

        $orders = Database::fetchAll("SELECT * FROM orders ORDER BY created_at DESC");
        $pageTitle = 'Commandes';
        $page = 'orders';
        renderAdmin('orders', compact('orders', 'pageTitle', 'page'));
    }

    public static function orderDetail(string $id): void
    {
        Auth::requireAuth();

        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [(int)$id]);
        if (!$order) redirect('/admin/commandes');

        $items = Database::fetchAll(
            "SELECT oi.*, p.image FROM order_items oi LEFT JOIN paintings p ON oi.painting_id = p.id WHERE oi.order_id = ?",
            [(int)$id]
        );

        $packlinkSettings = Database::fetchAll("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'packlink_%' OR `key` LIKE 'default_parcel_%'");
        $packlinkConfig = [];
        foreach ($packlinkSettings as $s) $packlinkConfig[$s['key']] = $s['value'];

        $pageTitle = 'Commande #' . $order['order_number'];
        $page = 'orders';
        renderAdmin('order-detail', compact('order', 'items', 'packlinkConfig', 'pageTitle', 'page'));
    }

    public static function updateOrderStatus(string $id): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/commandes');

        $status = $_POST['status'] ?? '';
        $paymentStatus = $_POST['payment_status'] ?? '';

        if ($status) {
            $oldOrder = Database::fetch("SELECT status FROM orders WHERE id = ?", [(int)$id]);
            Database::query("UPDATE orders SET status = ? WHERE id = ?", [$status, (int)$id]);

            if ($status === 'cancelled' && $oldOrder && $oldOrder['status'] !== 'cancelled') {
                $items = Database::fetchAll("SELECT painting_id FROM order_items WHERE order_id = ?", [(int)$id]);
                foreach ($items as $item) {
                    Database::query("UPDATE paintings SET status = 'available' WHERE id = ? AND status = 'sold'", [$item['painting_id']]);
                }
            }
        }
        if ($paymentStatus) {
            Database::query("UPDATE orders SET payment_status = ? WHERE id = ?", [$paymentStatus, (int)$id]);
        }

        $tracking = trim($_POST['shipping_tracking'] ?? '');
        Database::query("UPDATE orders SET shipping_tracking = ? WHERE id = ?", [$tracking ?: null, (int)$id]);

        flash('success', 'Statut mis à jour.');
        redirect('/admin/commandes/' . $id);
    }

    public static function sendPacklink(): void
    {
        Auth::requireAuth();
        header('Content-Type: application/json');

        $orderId = (int)($_POST['order_id'] ?? 0);
        $weight = floatval($_POST['weight'] ?? 2);
        $dimensions = trim($_POST['dimensions'] ?? '60x50x10');

        $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
        if (!$order) {
            echo json_encode(['error' => 'Commande introuvable.']);
            return;
        }

        $settings = Database::fetchAll("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'packlink_%' OR `key` LIKE 'default_parcel_%'");
        $config = [];
        foreach ($settings as $s) $config[$s['key']] = $s['value'];

        if (empty($config['packlink_api_key'])) {
            echo json_encode(['error' => 'Clé API Packlink non configurée.']);
            return;
        }

        $dims = explode('x', strtolower($dimensions));
        $length = intval($dims[0] ?? 60);
        $width = intval($dims[1] ?? 50);
        $height = intval($dims[2] ?? 10);

        $carrierMap = [
            'mondial_relay' => 'mondial_relay',
            'shop2shop' => 'shop2shop',
            'ups' => 'ups',
        ];

        $payload = [
            'from' => [
                'name' => $config['packlink_sender_name'] ?? 'Vogel Art Gallery',
                'street1' => $config['packlink_sender_address'] ?? '',
                'zip_code' => $config['packlink_sender_postal'] ?? '',
                'city' => $config['packlink_sender_city'] ?? '',
                'country' => 'FR',
                'email' => Database::fetch("SELECT value FROM settings WHERE `key` = 'contact_email'")['value'] ?? '',
            ],
            'to' => [
                'name' => $order['customer_firstname'] . ' ' . $order['customer_lastname'],
                'street1' => $order['shipping_address'],
                'zip_code' => $order['shipping_postal'],
                'city' => $order['shipping_city'],
                'country' => 'FR',
                'email' => $order['customer_email'],
                'phone' => $order['customer_phone'] ?? '',
            ],
            'packages' => [
                [
                    'weight' => $weight,
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                ],
            ],
            'content' => 'Tableau - Commande ' . $order['order_number'],
            'content_value' => floatval($order['total']),
            'source' => 'vogel-art',
        ];

        $ch = curl_init('https://apisandbox.packlink.com/v1/shipments');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: ' . $config['packlink_api_key'],
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = json_decode(curl_exec($ch), true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && isset($response['reference'])) {
            Database::query(
                "UPDATE orders SET shipping_tracking = ?, status = 'shipped' WHERE id = ?",
                [$response['reference'], $orderId]
            );
            echo json_encode([
                'success' => true,
                'reference' => $response['reference'],
                'message' => 'Expédition créée. Référence : ' . $response['reference'],
            ]);
        } else {
            $errorMsg = $response['message'] ?? $response['messages'][0]['message'] ?? 'Erreur Packlink (HTTP ' . $httpCode . ')';
            echo json_encode(['error' => $errorMsg, 'debug' => $response]);
        }
    }

    public static function settings(): void
    {
        Auth::requireAuth();

        $settings = Database::fetchAll("SELECT `key`, `value` FROM settings");
        $config = [];
        foreach ($settings as $s) $config[$s['key']] = $s['value'];

        $pageTitle = 'Paramètres';
        $page = 'settings';
        renderAdmin('settings', compact('config', 'pageTitle', 'page'));
    }

    public static function saveSettings(): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/parametres');

        $fields = [
            'stripe_public_key', 'stripe_secret_key',
            'paypal_client_id', 'paypal_secret', 'paypal_mode',
            'bank_iban', 'bank_bic', 'bank_name',
            'contact_email', 'contact_phone',
            'about_text', 'artist_bio', 'timeline_data', 'shipping_info',
            'packlink_api_key', 'packlink_sender_name', 'packlink_sender_address', 'packlink_sender_city', 'packlink_sender_postal',
            'default_parcel_weight', 'default_parcel_dimensions',
            'shipping_mondial_relay_price', 'shipping_shop2shop_price', 'shipping_ups_price', 'shipping_pickup_price', 'shipping_mondial_relay_domicile_price',
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                Database::query(
                    "INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?",
                    [$field, trim($_POST[$field]), trim($_POST[$field])]
                );
            }
        }

        $checkboxes = [
            'shipping_mondial_relay_enabled',
            'shipping_shop2shop_enabled',
            'shipping_ups_enabled',
            'shipping_pickup_enabled',
            'shipping_mondial_relay_domicile_enabled',
        ];
        foreach ($checkboxes as $cb) {
            $val = isset($_POST[$cb]) ? '1' : '0';
            Database::query(
                "INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?",
                [$cb, $val, $val]
            );
        }

        flash('success', 'Paramètres sauvegardés.');
        redirect('/admin/parametres');
    }
}
