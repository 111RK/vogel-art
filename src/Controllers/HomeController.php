<?php
class HomeController
{
    public static function index(): void
    {
        $featured = Database::fetchAll(
            "SELECT * FROM paintings WHERE status IN ('available', 'sold') AND featured = 1 ORDER BY created_at DESC LIMIT 6"
        );
        $recent = Database::fetchAll(
            "SELECT * FROM paintings WHERE status IN ('available', 'sold') ORDER BY created_at DESC LIMIT 8"
        );
        $artistBioRow = Database::fetch("SELECT value FROM settings WHERE `key` = 'artist_bio'");
        $artistBio = $artistBioRow['value'] ?? '';

        $timelineRow = Database::fetch("SELECT value FROM settings WHERE `key` = 'timeline_data'");
        $timeline = [];
        if (!empty($timelineRow['value'])) {
            foreach (explode("\n", $timelineRow['value']) as $line) {
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) >= 3) {
                    $timeline[] = ['year' => $parts[0], 'title' => $parts[1], 'description' => $parts[2]];
                }
            }
        }

        $content = 'home';
        $pageTitle = 'Accueil';
        render('home', compact('featured', 'recent', 'artistBio', 'timeline', 'content', 'pageTitle'));
    }

    public static function about(): void
    {
        $aboutText = Database::fetch("SELECT value FROM settings WHERE `key` = 'about_text'");
        $content = 'about';
        $pageTitle = 'À propos';
        render('about', compact('aboutText', 'content', 'pageTitle'));
    }

    public static function contact(): void
    {
        $content = 'contact';
        $pageTitle = 'Contact';
        render('contact', compact('content', 'pageTitle'));
    }

    public static function contactSubmit(): void
    {
        if (!verify_csrf()) {
            flash('error', 'Erreur de sécurité, veuillez réessayer.');
            redirect('/contact');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            flash('error', 'Tous les champs sont obligatoires.');
            redirect('/contact');
        }

        $contactEmail = Database::fetch("SELECT value FROM settings WHERE `key` = 'contact_email'");
        $to = $contactEmail['value'] ?? 'admin@vogel-art.fr';

        $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
        $subject = "Contact Vogel Art - $name";
        mail($to, $subject, $message, $headers);

        flash('success', 'Votre message a bien été envoyé. Merci !');
        redirect('/contact');
    }

    public static function cgv(): void
    {
        $content = 'cgv';
        $pageTitle = 'Conditions Générales de Vente';
        render('cgv', compact('content', 'pageTitle'));
    }

    public static function faq(): void
    {
        $faqs = Database::fetchAll("SELECT * FROM faq WHERE active = 1 ORDER BY position ASC");
        $content = 'faq';
        $pageTitle = 'Questions fréquentes';
        render('faq', compact('faqs', 'content', 'pageTitle'));
    }

    public static function shipping(): void
    {
        $shippingInfo = Database::fetch("SELECT value FROM settings WHERE `key` = 'shipping_info'");
        $settings = Database::fetchAll("SELECT `key`, `value` FROM settings WHERE `key` LIKE 'shipping_%'");
        $config = [];
        foreach ($settings as $s) $config[$s['key']] = $s['value'];

        $carriers = [];
        $carrierList = [
            'pickup' => ['label' => 'Retrait en main propre', 'icon' => 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'],
            'mondial_relay' => ['label' => 'Mondial Relay - Point Relais', 'icon' => 'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z'],
            'shop2shop' => ['label' => 'Chronopost - Shop2Shop', 'icon' => 'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z'],
            'ups' => ['label' => 'UPS - Access Point', 'icon' => 'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z'],
            'mondial_relay_domicile' => ['label' => 'Mondial Relay - Domicile', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8'],
        ];
        foreach ($carrierList as $key => $info) {
            if (($config["shipping_{$key}_enabled"] ?? '0') === '1') {
                $carriers[] = [
                    'label' => $info['label'],
                    'price' => floatval($config["shipping_{$key}_price"] ?? 0),
                ];
            }
        }

        $content = 'shipping';
        $pageTitle = 'Livraison';
        render('shipping', compact('shippingInfo', 'carriers', 'content', 'pageTitle'));
    }
}
