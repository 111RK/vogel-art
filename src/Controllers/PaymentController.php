<?php
class PaymentController
{
    public static function process(): void
    {
        if (!verify_csrf()) {
            redirect('/commande');
        }

        $cart = getCart();
        if (empty($cart)) {
            redirect('/panier');
        }

        // Valider les champs
        $required = ['firstname', 'lastname', 'email', 'address', 'city', 'postal', 'payment_method'];
        foreach ($required as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                flash('error', 'Veuillez remplir tous les champs obligatoires.');
                redirect('/commande');
            }
        }

        // Récupérer les articles
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
            flash('error', 'Les articles ne sont plus disponibles.');
            redirect('/panier');
        }

        $paymentMethod = $_POST['payment_method'];
        if (!in_array($paymentMethod, ['stripe', 'paypal', 'bank_transfer', 'in_person'])) {
            flash('error', 'Mode de paiement invalide.');
            redirect('/commande');
        }

        // Créer la commande
        $orderNumber = 'VA-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        Database::query(
            "INSERT INTO orders (order_number, customer_firstname, customer_lastname, customer_email, customer_phone, shipping_address, shipping_city, shipping_postal, total, payment_method)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $orderNumber,
                trim($_POST['firstname']),
                trim($_POST['lastname']),
                trim($_POST['email']),
                trim($_POST['phone'] ?? ''),
                trim($_POST['address']),
                trim($_POST['city']),
                trim($_POST['postal']),
                $total,
                $paymentMethod,
            ]
        );

        $orderId = Database::lastInsertId();

        // Ajouter les articles
        foreach ($items as $painting) {
            Database::query(
                "INSERT INTO order_items (order_id, painting_id, title, price) VALUES (?, ?, ?, ?)",
                [$orderId, $painting['id'], $painting['title'], $painting['price']]
            );
            // Marquer le tableau comme vendu
            Database::query(
                "UPDATE paintings SET status = 'sold' WHERE id = ?",
                [$painting['id']]
            );
        }

        // Vider le panier
        $_SESSION['cart'] = [];

        // Selon le mode de paiement
        switch ($paymentMethod) {
            case 'stripe':
                self::handleStripe($orderId, $total);
                break;
            case 'paypal':
                self::handlePaypal($orderId, $total);
                break;
            case 'bank_transfer':
            case 'in_person':
                redirect('/commande/confirmation/' . $orderId);
                break;
        }
    }

    private static function handleStripe(int $orderId, float $total): void
    {
        $settings = Database::fetchAll("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'stripe_%'");
        $config = [];
        foreach ($settings as $s) $config[$s['key']] = $s['value'];

        if (empty($config['stripe_secret_key'])) {
            flash('error', 'Le paiement par carte n\'est pas encore configuré.');
            redirect('/commande/confirmation/' . $orderId);
            return;
        }

        // Créer une session Stripe via cURL
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $config['stripe_secret_key'],
            ],
            CURLOPT_POSTFIELDS => http_build_query([
                'payment_method_types[]' => 'card',
                'line_items[0][price_data][currency]' => 'eur',
                'line_items[0][price_data][product_data][name]' => 'Commande Vogel Art #' . $orderId,
                'line_items[0][price_data][unit_amount]' => (int)($total * 100),
                'line_items[0][quantity]' => 1,
                'mode' => 'payment',
                'success_url' => SITE_URL . '/commande/confirmation/' . $orderId . '?payment=success',
                'cancel_url' => SITE_URL . '/commande/confirmation/' . $orderId . '?payment=cancel',
                'metadata[order_id]' => $orderId,
            ]),
        ]);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($response['url'])) {
            Database::query(
                "UPDATE orders SET payment_id = ? WHERE id = ?",
                [$response['id'], $orderId]
            );
            redirect($response['url']);
        } else {
            redirect('/commande/confirmation/' . $orderId);
        }
    }

    private static function handlePaypal(int $orderId, float $total): void
    {
        // Rediriger vers la page de confirmation avec instructions PayPal
        redirect('/commande/confirmation/' . $orderId);
    }

    public static function confirmation(string $id): void
    {
        $order = Database::fetch(
            "SELECT * FROM orders WHERE id = ?",
            [(int)$id]
        );

        if (!$order) {
            http_response_code(404);
            require TEMPLATE_PATH . '/404.php';
            return;
        }

        $orderItems = Database::fetchAll(
            "SELECT oi.*, p.image FROM order_items oi LEFT JOIN paintings p ON oi.painting_id = p.id WHERE oi.order_id = ?",
            [(int)$id]
        );

        // Vérifier le retour Stripe
        if (isset($_GET['payment']) && $_GET['payment'] === 'success') {
            Database::query(
                "UPDATE orders SET payment_status = 'paid', status = 'confirmed' WHERE id = ?",
                [(int)$id]
            );
            $order['payment_status'] = 'paid';
            $order['status'] = 'confirmed';
        }

        $bankInfo = [];
        if ($order['payment_method'] === 'bank_transfer') {
            $bankSettings = Database::fetchAll("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'bank_%'");
            foreach ($bankSettings as $s) $bankInfo[$s['key']] = $s['value'];
        }

        $content = 'confirmation';
        $pageTitle = 'Confirmation de commande';
        render('confirmation', compact('order', 'orderItems', 'bankInfo', 'content', 'pageTitle'));
    }

    public static function stripeWebhook(): void
    {
        $payload = file_get_contents('php://input');
        $event = json_decode($payload, true);

        if ($event['type'] === 'checkout.session.completed') {
            $session = $event['data']['object'];
            $orderId = $session['metadata']['order_id'] ?? null;
            if ($orderId) {
                Database::query(
                    "UPDATE orders SET payment_status = 'paid', payment_id = ?, status = 'confirmed' WHERE id = ?",
                    [$session['payment_intent'], $orderId]
                );
            }
        }

        http_response_code(200);
        echo json_encode(['status' => 'ok']);
    }

    public static function paypalWebhook(): void
    {
        http_response_code(200);
        echo json_encode(['status' => 'ok']);
    }
}
