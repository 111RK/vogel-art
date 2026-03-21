<?php
class ShopController
{
    public static function index(): void
    {
        $paintings = Database::fetchAll(
            "SELECT * FROM paintings WHERE status IN ('available', 'sold') ORDER BY status ASC, created_at DESC"
        );
        $content = 'shop';
        $pageTitle = 'Boutique';
        $metaDescription = 'Découvrez nos tableaux originaux peints au couteau. Pièces uniques, art authentique fait main. Livraison en France.';
        $ogTags = ['title' => 'Boutique - Vogel Art Gallery', 'description' => $metaDescription, 'url' => SITE_URL . '/boutique', 'type' => 'website'];
        render('shop', compact('paintings', 'content', 'pageTitle', 'metaDescription', 'ogTags'));
    }

    public static function show(string $slug): void
    {
        $painting = Database::fetch(
            "SELECT * FROM paintings WHERE slug = ?",
            [$slug]
        );

        if (!$painting) {
            http_response_code(404);
            require TEMPLATE_PATH . '/404.php';
            return;
        }

        $related = Database::fetchAll(
            "SELECT * FROM paintings WHERE status IN ('available', 'sold') AND id != ? ORDER BY RAND() LIMIT 4",
            [$painting['id']]
        );

        $gallery = Database::fetchAll("SELECT * FROM painting_images WHERE painting_id = ? ORDER BY position", [$painting['id']]);

        $content = 'product';
        $pageTitle = $painting['title'];
        $metaDescription = $painting['description'] ? mb_substr(strip_tags($painting['description']), 0, 160) : 'Tableau original "' . $painting['title'] . '" peint au couteau. Pièce unique.';
        $ogTags = [
            'title' => $painting['title'] . ' - Vogel Art Gallery',
            'description' => $metaDescription,
            'url' => SITE_URL . '/tableau/' . $painting['slug'],
            'type' => 'product',
            'image' => SITE_URL . '/uploads/' . $painting['image'],
        ];
        render('product', compact('painting', 'gallery', 'related', 'content', 'pageTitle', 'metaDescription', 'ogTags'));
    }

    public static function trackingForm(): void
    {
        $content = 'tracking';
        $pageTitle = 'Suivi de commande';
        $order = null;
        $orderItems = [];
        render('tracking', compact('content', 'pageTitle', 'order', 'orderItems'));
    }

    public static function trackingResult(): void
    {
        $email = trim($_POST['email'] ?? '');
        $orderNumber = trim($_POST['order_number'] ?? '');

        if (empty($email) || empty($orderNumber)) {
            flash('error', 'Veuillez remplir tous les champs.');
            redirect('/suivi');
        }

        $order = Database::fetch(
            "SELECT * FROM orders WHERE order_number = ? AND customer_email = ?",
            [$orderNumber, $email]
        );

        if (!$order) {
            flash('error', 'Commande introuvable. Vérifiez votre email et numéro de commande.');
            redirect('/suivi');
        }

        $orderItems = Database::fetchAll(
            "SELECT oi.*, p.image FROM order_items oi LEFT JOIN paintings p ON oi.painting_id = p.id WHERE oi.order_id = ?",
            [$order['id']]
        );

        $content = 'tracking';
        $pageTitle = 'Suivi de commande';
        render('tracking', compact('content', 'pageTitle', 'order', 'orderItems'));
    }
}
