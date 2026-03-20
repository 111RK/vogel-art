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

        $pageTitle = 'Commande #' . $order['order_number'];
        $page = 'orders';
        renderAdmin('order-detail', compact('order', 'items', 'pageTitle', 'page'));
    }

    public static function updateOrderStatus(string $id): void
    {
        Auth::requireAuth();
        if (!verify_csrf()) redirect('/admin/commandes');

        $status = $_POST['status'] ?? '';
        $paymentStatus = $_POST['payment_status'] ?? '';

        if ($status) {
            Database::query("UPDATE orders SET status = ? WHERE id = ?", [$status, (int)$id]);
        }
        if ($paymentStatus) {
            Database::query("UPDATE orders SET payment_status = ? WHERE id = ?", [$paymentStatus, (int)$id]);
        }

        flash('success', 'Statut mis à jour.');
        redirect('/admin/commandes/' . $id);
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
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                Database::query(
                    "INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?",
                    [$field, trim($_POST[$field]), trim($_POST[$field])]
                );
            }
        }

        flash('success', 'Paramètres sauvegardés.');
        redirect('/admin/parametres');
    }
}
