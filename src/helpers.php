<?php

function render(string $template, array $data = []): void
{
    extract($data);
    require TEMPLATE_PATH . '/layout.php';
}

function renderAdmin(string $template, array $data = []): void
{
    extract($data);
    require TEMPLATE_PATH . '/admin/layout.php';
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string
{
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function formatPrice(float $price): string
{
    return number_format($price, 2, ',', ' ') . ' €';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
    } else {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}

function uploadImage(array $file): ?string
{
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > MAX_UPLOAD_SIZE) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) return null;

    $filename = uniqid('painting_') . '.' . $ext;
    $destination = UPLOAD_PATH . '/' . $filename;

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo[0] > 1920) {
        $resized = resizeImage($file['tmp_name'], $ext, 1920);
        if ($resized) {
            file_put_contents($destination, $resized);
            createThumbnail($destination, $ext);
            return $filename;
        }
    }

    move_uploaded_file($file['tmp_name'], $destination);
    createThumbnail($destination, $ext);

    return $filename;
}

function resizeImage(string $path, string $ext, int $maxWidth): ?string
{
    $src = match ($ext) {
        'jpg', 'jpeg' => imagecreatefromjpeg($path),
        'png' => imagecreatefrompng($path),
        'webp' => imagecreatefromwebp($path),
        default => null,
    };
    if (!$src) return null;

    $w = imagesx($src);
    $h = imagesy($src);
    $ratio = $maxWidth / $w;
    $newH = (int)($h * $ratio);

    $dst = imagecreatetruecolor($maxWidth, $newH);

    if ($ext === 'png') {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $newH, $w, $h);

    ob_start();
    match ($ext) {
        'jpg', 'jpeg' => imagejpeg($dst, null, 85),
        'png' => imagepng($dst),
        'webp' => imagewebp($dst, null, 85),
    };
    $data = ob_get_clean();

    imagedestroy($src);
    imagedestroy($dst);

    return $data;
}

function createThumbnail(string $path, string $ext, int $maxWidth = 400): void
{
    $thumbPath = str_replace('uploads/', 'uploads/thumbs/', $path);
    $thumbDir = dirname($thumbPath);
    if (!is_dir($thumbDir)) {
        mkdir($thumbDir, 0755, true);
    }

    $data = resizeImage($path, $ext, $maxWidth);
    if ($data) {
        file_put_contents($thumbPath, $data);
    }
}

function getCartCount(): int
{
    return count($_SESSION['cart'] ?? []);
}

function getCart(): array
{
    return $_SESSION['cart'] ?? [];
}
