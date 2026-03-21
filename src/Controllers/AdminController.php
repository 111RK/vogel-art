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

        $gallery = Database::fetchAll("SELECT * FROM painting_images WHERE painting_id = ? ORDER BY position", [(int)$id]);
        $pageTitle = 'Modifier : ' . $painting['title'];
        $page = 'paintings';
        renderAdmin('edit-painting', compact('painting', 'gallery', 'pageTitle', 'page'));
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

    public static function addPhotos(string $id): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/tableaux/modifier/' . $id);

        $painting = Database::fetch("SELECT * FROM paintings WHERE id = ?", [(int)$id]);
        if (!$painting) redirect('/admin/tableaux');

        $maxPos = Database::fetch("SELECT COALESCE(MAX(position), 0) as p FROM painting_images WHERE painting_id = ?", [(int)$id]);
        $pos = ($maxPos['p'] ?? 0) + 1;
        $count = 0;

        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['name'] as $i => $name) {
                $file = [
                    'name' => $_FILES['photos']['name'][$i],
                    'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                    'error' => $_FILES['photos']['error'][$i],
                    'size' => $_FILES['photos']['size'][$i],
                ];
                $image = uploadImage($file);
                if ($image) {
                    Database::query(
                        "INSERT INTO painting_images (painting_id, image, position) VALUES (?, ?, ?)",
                        [(int)$id, $image, $pos++]
                    );
                    $count++;
                }
            }
        }

        flash('success', $count . ' photo(s) ajoutée(s).');
        redirect('/admin/tableaux/modifier/' . $id);
    }

    public static function deletePhoto(string $id, string $photoId): void
    {
        Auth::requireAuth();

        $photo = Database::fetch("SELECT * FROM painting_images WHERE id = ? AND painting_id = ?", [(int)$photoId, (int)$id]);
        if ($photo) {
            if (file_exists(UPLOAD_PATH . '/' . $photo['image'])) unlink(UPLOAD_PATH . '/' . $photo['image']);
            $thumbPath = UPLOAD_PATH . '/thumbs/' . $photo['image'];
            if (file_exists($thumbPath)) unlink($thumbPath);
            Database::query("DELETE FROM painting_images WHERE id = ?", [(int)$photoId]);
            flash('success', 'Photo supprimée.');
        }

        redirect('/admin/tableaux/modifier/' . $id);
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

        $oldOrder = Database::fetch("SELECT status, payment_status FROM orders WHERE id = ?", [(int)$id]);

        if ($status) {
            Database::query("UPDATE orders SET status = ? WHERE id = ?", [$status, (int)$id]);

            if ($status === 'cancelled') {
                $items = Database::fetchAll("SELECT painting_id FROM order_items WHERE order_id = ?", [(int)$id]);
                foreach ($items as $item) {
                    Database::query("UPDATE paintings SET status = 'available' WHERE id = ? AND status = 'sold'", [$item['painting_id']]);
                }
            }

            if ($oldOrder && $status !== $oldOrder['status']) {
                try {
                    $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [(int)$id]);
                    Mailer::orderStatusNotification($order, $status);
                } catch (\Throwable $e) {}
            }
        }
        if ($paymentStatus) {
            if ($paymentStatus === 'refunded' && $oldOrder && $oldOrder['payment_status'] !== 'refunded') {
                $orderForRefund = Database::fetch("SELECT * FROM orders WHERE id = ?", [(int)$id]);
                if ($orderForRefund && $orderForRefund['payment_method'] === 'paypal') {
                    $refundOk = PaymentController::refundPaypal((int)$id);
                    if (!$refundOk) {
                        flash('error', 'Le remboursement PayPal a échoué. Vérifiez manuellement sur PayPal.');
                    } else {
                        flash('success', 'Remboursement PayPal effectué avec succès.');
                    }
                }
            }

            Database::query("UPDATE orders SET payment_status = ? WHERE id = ?", [$paymentStatus, (int)$id]);

            if ($oldOrder && $paymentStatus !== $oldOrder['payment_status'] && in_array($paymentStatus, ['refunded', 'failed'])) {
                try {
                    $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [(int)$id]);
                    Mailer::orderStatusNotification($order, $paymentStatus);
                } catch (\Throwable $e) {}
            }
        }

        $tracking = trim($_POST['shipping_tracking'] ?? '');
        $oldOrder = Database::fetch("SELECT shipping_tracking FROM orders WHERE id = ?", [(int)$id]);
        $oldTracking = $oldOrder['shipping_tracking'] ?? '';
        Database::query("UPDATE orders SET shipping_tracking = ? WHERE id = ?", [$tracking ?: null, (int)$id]);

        if ($tracking && $tracking !== $oldTracking) {
            $order = Database::fetch("SELECT * FROM orders WHERE id = ?", [(int)$id]);
            Mailer::shippingNotification($order, $tracking);
        }

        flash('success', 'Statut mis à jour.');
        redirect('/admin/commandes/' . $id);
    }

    public static function purgeCancelledOrders(): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/commandes');

        $cancelled = Database::fetchAll("SELECT id FROM orders WHERE status = 'cancelled'");
        foreach ($cancelled as $o) {
            Database::query("DELETE FROM order_items WHERE order_id = ?", [$o['id']]);
            Database::query("DELETE FROM orders WHERE id = ?", [$o['id']]);
        }

        flash('success', count($cancelled) . ' commande(s) annulée(s) supprimée(s).');
        redirect('/admin/commandes');
    }

    public static function users(): void
    {
        Auth::requireAuth();
        $users = Database::fetchAll("SELECT id, name, email, created_at FROM admin_users ORDER BY id");
        $pageTitle = 'Utilisateurs';
        $page = 'users';
        renderAdmin('users', compact('users', 'pageTitle', 'page'));
    }

    public static function addUser(): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/utilisateurs');

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            flash('error', 'Tous les champs sont obligatoires.');
            redirect('/admin/utilisateurs');
        }

        if (strlen($password) < 6) {
            flash('error', 'Le mot de passe doit faire au moins 6 caractères.');
            redirect('/admin/utilisateurs');
        }

        $existing = Database::fetch("SELECT id FROM admin_users WHERE email = ?", [$email]);
        if ($existing) {
            flash('error', 'Un utilisateur avec cet email existe déjà.');
            redirect('/admin/utilisateurs');
        }

        Database::query(
            "INSERT INTO admin_users (name, email, password) VALUES (?, ?, ?)",
            [$name, $email, password_hash($password, PASSWORD_DEFAULT)]
        );

        flash('success', 'Utilisateur ajouté.');
        redirect('/admin/utilisateurs');
    }

    public static function deleteUser(string $id): void
    {
        Auth::requireAuth();

        if ((int)$id === ($_SESSION['admin_id'] ?? 0)) {
            flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            redirect('/admin/utilisateurs');
        }

        Database::query("DELETE FROM admin_users WHERE id = ?", [(int)$id]);
        flash('success', 'Utilisateur supprimé.');
        redirect('/admin/utilisateurs');
    }

    public static function changePassword(string $id): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/utilisateurs');

        $password = $_POST['password'] ?? '';
        if (strlen($password) < 6) {
            flash('error', 'Le mot de passe doit faire au moins 6 caractères.');
            redirect('/admin/utilisateurs');
        }

        Database::query(
            "UPDATE admin_users SET password = ? WHERE id = ?",
            [password_hash($password, PASSWORD_DEFAULT), (int)$id]
        );

        flash('success', 'Mot de passe modifié.');
        redirect('/admin/utilisateurs');
    }

    public static function faqList(): void
    {
        Auth::requireAuth();
        $faqs = Database::fetchAll("SELECT * FROM faq ORDER BY position ASC");
        $pageTitle = 'FAQ';
        $page = 'faq';
        renderAdmin('faq', compact('faqs', 'pageTitle', 'page'));
    }

    public static function addFaq(): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/faq');

        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($question) || empty($answer)) {
            flash('error', 'La question et la réponse sont obligatoires.');
            redirect('/admin/faq');
        }

        $maxPos = Database::fetch("SELECT COALESCE(MAX(position), 0) as max_pos FROM faq");
        $position = ($maxPos['max_pos'] ?? 0) + 1;

        Database::query(
            "INSERT INTO faq (question, answer, position, active) VALUES (?, ?, ?, ?)",
            [$question, $answer, $position, $active]
        );

        flash('success', 'Question ajoutée.');
        redirect('/admin/faq');
    }

    public static function editFaq(string $id): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/faq');

        $faq = Database::fetch("SELECT * FROM faq WHERE id = ?", [(int)$id]);
        if (!$faq) redirect('/admin/faq');

        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;

        if (empty($question) || empty($answer)) {
            flash('error', 'La question et la réponse sont obligatoires.');
            redirect('/admin/faq');
        }

        Database::query(
            "UPDATE faq SET question = ?, answer = ?, active = ? WHERE id = ?",
            [$question, $answer, $active, (int)$id]
        );

        flash('success', 'Question mise à jour.');
        redirect('/admin/faq');
    }

    public static function deleteFaq(string $id): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/faq');

        Database::query("DELETE FROM faq WHERE id = ?", [(int)$id]);
        flash('success', 'Question supprimée.');
        redirect('/admin/faq');
    }

    public static function reorderFaq(): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/faq');

        $ids = array_filter(array_map('intval', explode(',', $_POST['ids'] ?? '')));
        foreach ($ids as $position => $id) {
            Database::query("UPDATE faq SET position = ? WHERE id = ?", [$position + 1, $id]);
        }

        flash('success', 'Ordre mis à jour.');
        redirect('/admin/faq');
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

        $ch = curl_init('https://api.packlink.com/v1/shipments');
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
            $updatedOrder = Database::fetch("SELECT * FROM orders WHERE id = ?", [$orderId]);
            Mailer::shippingNotification($updatedOrder, $response['reference']);
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
