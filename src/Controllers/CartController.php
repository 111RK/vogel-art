<?php
class CartController
{
    public static function index(): void
    {
        $cart = getCart();
        $items = [];
        $total = 0;

        foreach ($cart as $paintingId) {
            $painting = Database::fetch(
                "SELECT * FROM paintings WHERE id = ? AND status = 'available'",
                [$paintingId]
            );
            if ($painting) {
                $items[] = $painting;
                $total += $painting['price'];
            }
        }

        $content = 'cart';
        $pageTitle = 'Panier';
        render('cart', compact('items', 'total', 'content', 'pageTitle'));
    }

    public static function add(): void
    {
        if (!verify_csrf()) {
            redirect('/panier');
        }

        $paintingId = (int)($_POST['painting_id'] ?? 0);

        // Vérifier que le tableau existe et est disponible
        $painting = Database::fetch(
            "SELECT id FROM paintings WHERE id = ? AND status = 'available'",
            [$paintingId]
        );

        if ($painting) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            // Article unique - on ne peut l'ajouter qu'une fois
            if (!in_array($paintingId, $_SESSION['cart'])) {
                $_SESSION['cart'][] = $paintingId;
                flash('success', 'Tableau ajouté au panier.');
            } else {
                flash('info', 'Ce tableau est déjà dans votre panier.');
            }
        }

        redirect('/panier');
    }

    public static function remove(): void
    {
        if (!verify_csrf()) {
            redirect('/panier');
        }

        $paintingId = (int)($_POST['painting_id'] ?? 0);
        $_SESSION['cart'] = array_values(array_diff($_SESSION['cart'] ?? [], [$paintingId]));

        flash('success', 'Tableau retiré du panier.');
        redirect('/panier');
    }

    public static function checkout(): void
    {
        $cart = getCart();
        if (empty($cart)) {
            redirect('/panier');
        }

        $items = [];
        $total = 0;
        foreach ($cart as $paintingId) {
            $painting = Database::fetch(
                "SELECT * FROM paintings WHERE id = ? AND status = 'available'",
                [$paintingId]
            );
            if ($painting) {
                $items[] = $painting;
                $total += $painting['price'];
            }
        }

        if (empty($items)) {
            flash('error', 'Les articles de votre panier ne sont plus disponibles.');
            redirect('/panier');
        }

        // Récupérer les paramètres de paiement
        $settings = Database::fetchAll("SELECT `key`, `value` FROM settings");
        $config = [];
        foreach ($settings as $s) {
            $config[$s['key']] = $s['value'];
        }

        $shippingOptions = [];
        $carriers = [
            'pickup' => ['label' => 'Retrait en main propre', 'relay' => false],
            'mondial_relay' => ['label' => 'Mondial Relay (Point Relais)', 'relay' => true],
            'shop2shop' => ['label' => 'Shop2Shop (Relais Colis)', 'relay' => true],
            'ups' => ['label' => 'UPS Standard (à domicile)', 'relay' => false],
        ];
        foreach ($carriers as $key => $info) {
            if (($config["shipping_{$key}_enabled"] ?? '0') === '1') {
                $shippingOptions[] = [
                    'key' => $key,
                    'label' => $info['label'],
                    'price' => floatval($config["shipping_{$key}_price"] ?? 0),
                    'relay' => $info['relay'],
                ];
            }
        }

        $content = 'checkout';
        $pageTitle = 'Commander';
        render('checkout', compact('items', 'total', 'shippingOptions', 'config', 'content', 'pageTitle'));
    }
}
