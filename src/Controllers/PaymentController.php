<?php
class PaymentController
{
    private static array $carrierLabels = [
        'mondial_relay' => 'Mondial Relay (Point Relais)',
        'shop2shop' => 'Shop2Shop (Relais Colis)',
        'ups' => 'UPS Standard (à domicile)',
        'pickup' => 'Retrait en main propre',
    ];

    private static array $relayCarriers = ['mondial_relay', 'shop2shop'];

    public static function relayPoints(): void
    {
        header('Content-Type: application/json');

        $postal = trim($_GET['postal'] ?? '');
        $carrier = trim($_GET['carrier'] ?? '');

        if (strlen($postal) < 5) {
            echo json_encode(['error' => 'Code postal invalide.']);
            return;
        }

        $apiKey = Database::fetch("SELECT value FROM settings WHERE `key` = 'packlink_api_key'");
        if (empty($apiKey['value'])) {
            echo json_encode(['error' => 'API Packlink non configurée.']);
            return;
        }

        $serviceMap = [
            'mondial_relay' => 'mondial_relay',
            'shop2shop' => 'shop2shop',
        ];

        $url = 'https://apisandbox.packlink.com/v1/dropoffs?'
            . http_build_query([
                'service' => $serviceMap[$carrier] ?? 'mondial_relay',
                'country' => 'FR',
                'zip' => $postal,
            ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $apiKey['value'],
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300 && is_array($data)) {
            $points = [];
            foreach (array_slice($data, 0, 15) as $point) {
                $points[] = [
                    'id' => $point['id'] ?? $point['code'] ?? uniqid(),
                    'name' => $point['name'] ?? $point['commerce_name'] ?? 'Point Relais',
                    'address' => trim(
                        ($point['address'] ?? $point['street'] ?? '') . ', '
                        . ($point['zip'] ?? $point['zip_code'] ?? $postal) . ' '
                        . ($point['city'] ?? '')
                    ),
                ];
            }
            echo json_encode(['points' => $points]);
        } else {
            echo json_encode(['error' => 'Impossible de récupérer les points relais. Vérifiez la clé API Packlink.', 'debug_code' => $httpCode]);
        }
    }

    public static function process(): void
    {
        if (!verify_csrf()) {
            redirect('/commande');
        }

        $cart = getCart();
        if (empty($cart)) {
            redirect('/panier');
        }

        $required = ['firstname', 'lastname', 'email', 'address', 'city', 'postal', 'payment_method', 'shipping_method'];
        foreach ($required as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                flash('error', 'Veuillez remplir tous les champs obligatoires.');
                redirect('/commande');
            }
        }

        $items = [];
        $subtotal = 0;
        foreach ($cart as $paintingId) {
            $painting = Database::fetch(
                "SELECT * FROM paintings WHERE id = ? AND status = 'available'",
                [$paintingId]
            );
            if ($painting) {
                $items[] = $painting;
                $subtotal += $painting['price'];
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

        $shippingMethod = $_POST['shipping_method'];
        if (!array_key_exists($shippingMethod, self::$carrierLabels)) {
            flash('error', 'Mode de livraison invalide.');
            redirect('/commande');
        }

        $settings = Database::fetchAll("SELECT `key`, `value` FROM settings");
        $config = [];
        foreach ($settings as $s) $config[$s['key']] = $s['value'];

        if (($config["shipping_{$shippingMethod}_enabled"] ?? '0') !== '1') {
            flash('error', 'Ce mode de livraison n\'est pas disponible.');
            redirect('/commande');
        }

        $shippingCost = floatval($config["shipping_{$shippingMethod}_price"] ?? 0);
        $total = $subtotal + $shippingCost;

        $relayPointName = trim($_POST['relay_point_name'] ?? '');
        $relayPointAddress = trim($_POST['relay_point_address'] ?? '');
        $relayPointId = trim($_POST['relay_point_id'] ?? '');

        $notes = '';
        if ($relayPointName) {
            $notes = 'Point relais : ' . $relayPointName . ' - ' . $relayPointAddress . ' (ID: ' . $relayPointId . ')';
        }

        $orderNumber = 'VA-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        Database::query(
            "INSERT INTO orders (order_number, customer_firstname, customer_lastname, customer_email, customer_phone, shipping_address, shipping_city, shipping_postal, total, payment_method, shipping_method, shipping_cost, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
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
                $shippingMethod,
                $shippingCost,
                $notes ?: null,
            ]
        );

        $orderId = Database::lastInsertId();

        foreach ($items as $painting) {
            Database::query(
                "INSERT INTO order_items (order_id, painting_id, title, price) VALUES (?, ?, ?, ?)",
                [$orderId, $painting['id'], $painting['title'], $painting['price']]
            );
            Database::query(
                "UPDATE paintings SET status = 'sold' WHERE id = ?",
                [$painting['id']]
            );
        }

        $_SESSION['cart'] = [];

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

    public static function carrierLabel(string $key): string
    {
        return self::$carrierLabels[$key] ?? $key;
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
