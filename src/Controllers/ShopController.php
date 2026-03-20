<?php
class ShopController
{
    public static function index(): void
    {
        $paintings = Database::fetchAll(
            "SELECT * FROM paintings WHERE status = 'available' ORDER BY created_at DESC"
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
            "SELECT * FROM paintings WHERE status = 'available' AND id != ? ORDER BY RAND() LIMIT 4",
            [$painting['id']]
        );

        $content = 'product';
        $pageTitle = $painting['title'];
        render('product', compact('painting', 'related', 'content', 'pageTitle'));
    }
}
