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
        render('shop', compact('paintings', 'content', 'pageTitle'));
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

        $content = 'product';
        $pageTitle = $painting['title'];
        render('product', compact('painting', 'related', 'content', 'pageTitle'));
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
