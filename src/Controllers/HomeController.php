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
        $artistBio = Database::fetch("SELECT value FROM settings WHERE `key` = 'artist_bio'");
        $contactInfo = [];
        foreach (['gallery_name', 'owner_firstname', 'owner_lastname', 'contact_photo'] as $k) {
            $r = Database::fetch("SELECT value FROM settings WHERE `key` = ?", [$k]);
            $contactInfo[$k] = $r['value'] ?? '';
        }
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
        $content = 'about';
        $pageTitle = 'À propos';
        render('about', compact('aboutText', 'artistBio', 'contactInfo', 'timeline', 'content', 'pageTitle'));
    }

    public static function contact(): void
    {
        $keys = ['gallery_name', 'owner_firstname', 'owner_lastname', 'contact_address', 'contact_city', 'contact_postal', 'contact_phone', 'contact_email', 'contact_photo'];
        $contactInfo = [];
        foreach ($keys as $k) {
            $row = Database::fetch("SELECT value FROM settings WHERE `key` = ?", [$k]);
            $contactInfo[$k] = $row['value'] ?? '';
        }
        $content = 'contact';
        $pageTitle = 'Contact';
        render('contact', compact('contactInfo', 'content', 'pageTitle'));
    }

    public static function cgv(): void
    {
        $content = 'cgv';
        $pageTitle = 'Conditions Générales de Vente';
        render('cgv', compact('content', 'pageTitle'));
    }

    public static function mentions(): void
    {
        $contactInfo = [];
        foreach (['gallery_name', 'owner_firstname', 'owner_lastname', 'contact_address', 'contact_city', 'contact_postal', 'contact_phone', 'contact_email'] as $k) {
            $r = Database::fetch("SELECT value FROM settings WHERE `key` = ?", [$k]);
            $contactInfo[$k] = $r['value'] ?? '';
        }
        $content = 'mentions';
        $pageTitle = 'Mentions légales';
        render('mentions', compact('contactInfo', 'content', 'pageTitle'));
    }

    public static function confidentialite(): void
    {
        $contactInfo = [];
        foreach (['gallery_name', 'owner_firstname', 'owner_lastname', 'contact_email'] as $k) {
            $r = Database::fetch("SELECT value FROM settings WHERE `key` = ?", [$k]);
            $contactInfo[$k] = $r['value'] ?? '';
        }
        $content = 'confidentialite';
        $pageTitle = 'Politique de confidentialité';
        render('confidentialite', compact('contactInfo', 'content', 'pageTitle'));
    }

    public static function faq(): void
    {
        $faqs = Database::fetchAll("SELECT * FROM faq WHERE active = 1 ORDER BY position ASC");
        $content = 'faq';
        $pageTitle = 'Questions fréquentes';
        render('faq', compact('faqs', 'content', 'pageTitle'));
    }

    public static function blog(): void
    {
        $categories = Database::fetchAll("SELECT * FROM blog_categories ORDER BY name");
        $posts = Database::fetchAll(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id WHERE bp.published = 1 ORDER BY bp.created_at DESC"
        );
        $currentCategory = null;
        $content = 'blog';
        $pageTitle = 'Blog - Le Journal de l\'Atelier';
        $metaDescription = 'Blog Vogel Art : articles sur la peinture au couteau, techniques artistiques, conseils décoration et guides d\'achat pour amateurs d\'art.';
        render('blog', compact('categories', 'posts', 'currentCategory', 'content', 'pageTitle', 'metaDescription'));
    }

    public static function blogCategory(string $slug): void
    {
        $currentCategory = Database::fetch("SELECT * FROM blog_categories WHERE slug = ?", [$slug]);
        if (!$currentCategory) {
            http_response_code(404);
            require TEMPLATE_PATH . '/404.php';
            return;
        }
        $categories = Database::fetchAll("SELECT * FROM blog_categories ORDER BY name");
        $posts = Database::fetchAll(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id WHERE bp.published = 1 AND bp.category_id = ? ORDER BY bp.created_at DESC",
            [$currentCategory['id']]
        );
        $content = 'blog';
        $pageTitle = $currentCategory['name'] . ' - Blog Vogel Art';
        $metaDescription = 'Articles ' . $currentCategory['name'] . ' : découvrez nos articles sur la peinture au couteau, l\'art et la décoration.';
        render('blog', compact('categories', 'posts', 'currentCategory', 'content', 'pageTitle', 'metaDescription'));
    }

    public static function blogPost(string $slug): void
    {
        $post = Database::fetch(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id WHERE bp.slug = ? AND bp.published = 1",
            [$slug]
        );
        if (!$post) {
            http_response_code(404);
            require TEMPLATE_PATH . '/404.php';
            return;
        }
        $related = Database::fetchAll(
            "SELECT bp.*, bc.name as category_name FROM blog_posts bp LEFT JOIN blog_categories bc ON bp.category_id = bc.id WHERE bp.published = 1 AND bp.id != ? ORDER BY (bp.category_id = ?) DESC, bp.created_at DESC LIMIT 3",
            [$post['id'], $post['category_id']]
        );
        $content = 'blog-post';
        $pageTitle = $post['title'];
        $metaDescription = $post['meta_description'] ?: $post['excerpt'];
        $ogTags = [
            'title' => $post['title'],
            'description' => $post['meta_description'] ?: $post['excerpt'],
            'url' => SITE_URL . '/blog/' . $post['slug'],
            'type' => 'article',
            'image' => $post['image'] ? SITE_URL . '/uploads/' . $post['image'] : null,
        ];
        render('blog-post', compact('post', 'related', 'content', 'pageTitle', 'metaDescription', 'ogTags'));
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
