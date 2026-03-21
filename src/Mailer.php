<?php

class Mailer
{
    private static function sendHtml(string $to, string $subject, string $body): bool
    {
        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Vogel Art Gallery <noreply@vogel-art.fr>',
            'Reply-To: ' . (Database::fetch("SELECT value FROM settings WHERE `key` = 'contact_email'")['value'] ?? 'noreply@vogel-art.fr'),
        ]);
        return mail($to, $subject, self::wrapLayout($subject, $body), $headers);
    }

    private static function wrapLayout(string $title, string $content): string
    {
        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
        <body style="margin:0;padding:0;background:#FAFAF8;font-family:Arial,Helvetica,sans-serif;color:#2D2D2D;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAFAF8;padding:32px 0;">
        <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.06);">
        <tr><td style="background:#2D2D2D;padding:24px 32px;text-align:center;">
            <span style="font-size:24px;font-weight:700;color:#FFFFFF;font-family:Georgia,serif;">Vogel <span style="color:#C9A96E;">Art</span> Gallery</span>
        </td></tr>
        <tr><td style="padding:32px;">' . $content . '</td></tr>
        <tr><td style="background:#F5F0EB;padding:20px 32px;text-align:center;font-size:12px;color:#6B6B6B;">
            &copy; ' . date('Y') . ' Vogel Art Gallery - <a href="' . SITE_URL . '" style="color:#C9A96E;text-decoration:none;">vogel-art.fr</a>
        </td></tr>
        </table>
        </td></tr></table></body></html>';
    }

    public static function orderConfirmationToCustomer(array $order, array $items): void
    {
        $itemsHtml = '';
        foreach ($items as $item) {
            $imgUrl = !empty($item['image']) ? SITE_URL . '/uploads/thumbs/' . $item['image'] : '';
            $imgHtml = $imgUrl ? '<img src="' . $imgUrl . '" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:4px;vertical-align:middle;margin-right:10px;">' : '';
            $itemsHtml .= '<tr>
                <td style="padding:12px;border-bottom:1px solid #E8E4DF;">' . $imgHtml . e($item['title']) . '</td>
                <td style="padding:12px;border-bottom:1px solid #E8E4DF;text-align:right;font-weight:600;">' . formatPrice($item['price']) . '</td>
            </tr>';
        }

        $shippingLabel = !empty($order['shipping_method']) ? statusLabel($order['shipping_method']) : '';
        $shippingHtml = '';
        if ($shippingLabel) {
            $shippingHtml = '<p style="margin:8px 0;"><strong>Livraison :</strong> ' . e($shippingLabel) . '</p>';
            if (($order['shipping_cost'] ?? 0) > 0) {
                $shippingHtml .= '<p style="margin:8px 0;"><strong>Frais de port :</strong> ' . formatPrice($order['shipping_cost']) . '</p>';
            }
        }
        if (!empty($order['notes']) && strpos($order['notes'], 'Point relais') !== false) {
            $shippingHtml .= '<p style="margin:8px 0;color:#C9A96E;"><strong>' . e($order['notes']) . '</strong></p>';
        }

        $paymentHtml = '';
        if ($order['payment_method'] === 'bank_transfer') {
            $bank = [];
            $bankSettings = Database::fetchAll("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'bank_%'");
            foreach ($bankSettings as $s) $bank[$s['key']] = $s['value'];
            $paymentHtml = '<div style="background:#F5F0EB;border-radius:8px;padding:16px;margin:16px 0;">
                <h3 style="margin:0 0 8px;font-size:16px;color:#2D2D2D;">Informations pour le virement</h3>
                <p style="margin:4px 0;"><strong>IBAN :</strong> ' . e($bank['bank_iban'] ?? '') . '</p>
                <p style="margin:4px 0;"><strong>BIC :</strong> ' . e($bank['bank_bic'] ?? '') . '</p>
                <p style="margin:4px 0;"><strong>Titulaire :</strong> ' . e($bank['bank_name'] ?? '') . '</p>
                <p style="margin:4px 0;"><strong>Référence :</strong> ' . e($order['order_number']) . '</p>
            </div>';
        } elseif ($order['payment_method'] === 'in_person') {
            $paymentHtml = '<p style="margin:12px 0;color:#6B6B6B;font-style:italic;">Nous vous contacterons pour organiser la remise en main propre.</p>';
        }

        $body = '
            <h1 style="font-family:Georgia,serif;font-size:24px;margin:0 0 8px;color:#2D2D2D;">Merci pour votre commande !</h1>
            <p style="color:#6B6B6B;margin:0 0 24px;">Commande n° <strong style="color:#2D2D2D;">' . e($order['order_number']) . '</strong></p>

            <table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;">
                <tr style="background:#F5F0EB;">
                    <td style="padding:10px 12px;font-weight:600;font-size:13px;">Article</td>
                    <td style="padding:10px 12px;font-weight:600;font-size:13px;text-align:right;">Prix</td>
                </tr>
                ' . $itemsHtml . '
                ' . (($order['shipping_cost'] ?? 0) > 0 ? '<tr><td style="padding:12px;border-bottom:1px solid #E8E4DF;color:#6B6B6B;">Livraison</td><td style="padding:12px;border-bottom:1px solid #E8E4DF;text-align:right;">' . formatPrice($order['shipping_cost']) . '</td></tr>' : '') . '
                <tr>
                    <td style="padding:16px 12px;font-size:18px;font-weight:700;">Total</td>
                    <td style="padding:16px 12px;font-size:18px;font-weight:700;text-align:right;color:#C9A96E;">' . formatPrice($order['total']) . '</td>
                </tr>
            </table>

            ' . $shippingHtml . '
            <p style="margin:8px 0;"><strong>Mode de paiement :</strong> ' . statusLabel($order['payment_method']) . '</p>
            ' . $paymentHtml . '

            <div style="background:#F5F0EB;border-radius:8px;padding:16px;margin:24px 0;">
                <h3 style="margin:0 0 8px;font-size:16px;">Adresse de livraison</h3>
                <p style="margin:4px 0;">' . e($order['customer_firstname'] . ' ' . $order['customer_lastname']) . '</p>
                <p style="margin:4px 0;">' . e($order['shipping_address']) . '</p>
                <p style="margin:4px 0;">' . e($order['shipping_postal'] . ' ' . $order['shipping_city']) . '</p>
            </div>

            <p style="text-align:center;margin:24px 0;">
                <a href="' . SITE_URL . '/commande/confirmation/' . $order['id'] . '" style="display:inline-block;background:#C9A96E;color:#FFFFFF;padding:12px 32px;border-radius:8px;text-decoration:none;font-weight:600;">Voir ma commande</a>
            </p>';

        self::sendHtml($order['customer_email'], 'Confirmation de commande ' . $order['order_number'] . ' - Vogel Art Gallery', $body);
    }

    public static function newOrderToMerchant(array $order, array $items): void
    {
        $merchantEmail = Database::fetch("SELECT value FROM settings WHERE `key` = 'contact_email'")['value'] ?? '';
        if (empty($merchantEmail)) return;

        $itemsHtml = '';
        foreach ($items as $item) {
            $imgUrl = !empty($item['image']) ? SITE_URL . '/uploads/thumbs/' . $item['image'] : '';
            $imgHtml = $imgUrl ? '<img src="' . $imgUrl . '" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:4px;vertical-align:middle;margin-right:10px;">' : '';
            $itemsHtml .= '<tr>
                <td style="padding:10px 12px;border-bottom:1px solid #E8E4DF;">' . $imgHtml . e($item['title']) . '</td>
                <td style="padding:10px 12px;border-bottom:1px solid #E8E4DF;text-align:right;font-weight:600;">' . formatPrice($item['price']) . '</td>
            </tr>';
        }

        $relayInfo = '';
        if (!empty($order['notes']) && strpos($order['notes'], 'Point relais') !== false) {
            $relayInfo = '<div style="background:#FFF3E0;border-left:4px solid #C9A96E;padding:12px 16px;margin:12px 0;">
                <strong>' . e($order['notes']) . '</strong>
            </div>';
        }

        $packlinkBtn = '';
        if (!empty($order['shipping_method']) && $order['shipping_method'] !== 'pickup') {
            $packlinkBtn = '<p style="text-align:center;margin:16px 0;">
                <a href="' . SITE_URL . '/admin/commandes/' . $order['id'] . '" style="display:inline-block;background:#2D2D2D;color:#FFFFFF;padding:12px 32px;border-radius:8px;text-decoration:none;font-weight:600;">Envoyer via Packlink</a>
            </p>';
        }

        $body = '
            <h1 style="font-family:Georgia,serif;font-size:24px;margin:0 0 8px;color:#2D2D2D;">&#127881; Nouvelle commande !</h1>
            <p style="color:#6B6B6B;margin:0 0 24px;">Commande n° <strong style="color:#C9A96E;">' . e($order['order_number']) . '</strong></p>

            <div style="background:#F5F0EB;border-radius:8px;padding:16px;margin:16px 0;">
                <h3 style="margin:0 0 8px;font-size:16px;">Client</h3>
                <p style="margin:4px 0;"><strong>' . e($order['customer_firstname'] . ' ' . $order['customer_lastname']) . '</strong></p>
                <p style="margin:4px 0;">' . e($order['customer_email']) . '</p>
                ' . (!empty($order['customer_phone']) ? '<p style="margin:4px 0;">' . e($order['customer_phone']) . '</p>' : '') . '
            </div>

            <div style="background:#F5F0EB;border-radius:8px;padding:16px;margin:16px 0;">
                <h3 style="margin:0 0 8px;font-size:16px;">Adresse de livraison</h3>
                <p style="margin:4px 0;">' . e($order['shipping_address']) . '</p>
                <p style="margin:4px 0;">' . e($order['shipping_postal'] . ' ' . $order['shipping_city']) . '</p>
                ' . (!empty($order['shipping_method']) ? '<p style="margin:4px 0;"><strong>Mode :</strong> ' . statusLabel($order['shipping_method']) . '</p>' : '') . '
            </div>

            ' . $relayInfo . '

            <table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;">
                <tr style="background:#F5F0EB;">
                    <td style="padding:10px 12px;font-weight:600;font-size:13px;">Article</td>
                    <td style="padding:10px 12px;font-weight:600;font-size:13px;text-align:right;">Prix</td>
                </tr>
                ' . $itemsHtml . '
                <tr>
                    <td style="padding:16px 12px;font-size:18px;font-weight:700;">Total</td>
                    <td style="padding:16px 12px;font-size:18px;font-weight:700;text-align:right;color:#C9A96E;">' . formatPrice($order['total']) . '</td>
                </tr>
            </table>

            <p style="margin:8px 0;"><strong>Paiement :</strong> ' . statusLabel($order['payment_method']) . '</p>

            ' . $packlinkBtn;

        self::sendHtml($merchantEmail, "\xF0\x9F\x8E\x89 Bravo Olivier ! Tu as une nouvelle commande \xF0\x9F\x96\xBC\xEF\xB8\x8F - " . $order['order_number'], $body);
    }

    public static function shippingNotification(array $order, string $trackingNumber): void
    {
        $trackingUrl = 'https://parcelsapp.com/fr/tracking/' . urlencode($trackingNumber);
        $shippingLabel = !empty($order['shipping_method']) ? statusLabel($order['shipping_method']) : 'Transporteur';

        $body = '
            <h1 style="font-family:Georgia,serif;font-size:24px;margin:0 0 8px;color:#2D2D2D;">Votre colis est en route !</h1>
            <p style="color:#6B6B6B;margin:0 0 24px;">Commande n° <strong style="color:#2D2D2D;">' . e($order['order_number']) . '</strong></p>

            <p style="font-size:16px;margin:16px 0;">Votre commande a été expédiée via <strong>' . e($shippingLabel) . '</strong>.</p>

            <div style="background:#F5F0EB;border-radius:8px;padding:24px;margin:24px 0;text-align:center;">
                <p style="margin:0 0 8px;font-size:14px;color:#6B6B6B;">Votre numéro de suivi</p>
                <p style="margin:0;font-size:28px;font-weight:700;color:#C44536;letter-spacing:2px;">' . e($trackingNumber) . '</p>
            </div>

            <p style="text-align:center;margin:24px 0;">
                <a href="' . $trackingUrl . '" style="display:inline-block;background:#C9A96E;color:#FFFFFF;padding:14px 40px;border-radius:8px;text-decoration:none;font-weight:600;font-size:16px;">Suivre mon colis</a>
            </p>

            <p style="color:#6B6B6B;font-size:14px;text-align:center;margin:16px 0;">
                Adresse de livraison : ' . e($order['shipping_address'] . ', ' . $order['shipping_postal'] . ' ' . $order['shipping_city']) . '
            </p>';

        self::sendHtml($order['customer_email'], 'Votre colis est en route ! - ' . $order['order_number'], $body);
    }
}
