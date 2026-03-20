SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `paintings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `width_cm` INT UNSIGNED DEFAULT NULL,
    `height_cm` INT UNSIGNED DEFAULT NULL,
    `technique` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('available', 'sold', 'hidden') DEFAULT 'available',
    `featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(20) NOT NULL UNIQUE,
    `customer_firstname` VARCHAR(100) NOT NULL,
    `customer_lastname` VARCHAR(100) NOT NULL,
    `customer_email` VARCHAR(255) NOT NULL,
    `customer_phone` VARCHAR(20) DEFAULT NULL,
    `shipping_address` TEXT NOT NULL,
    `shipping_city` VARCHAR(100) NOT NULL,
    `shipping_postal` VARCHAR(10) NOT NULL,
    `shipping_country` VARCHAR(100) DEFAULT 'France',
    `total` DECIMAL(10,2) NOT NULL,
    `payment_method` ENUM('stripe', 'paypal', 'bank_transfer', 'in_person') NOT NULL,
    `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    `payment_id` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    `shipping_method` VARCHAR(50) DEFAULT NULL,
    `shipping_cost` DECIMAL(10,2) DEFAULT 0.00,
    `shipping_tracking` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `painting_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`painting_id`) REFERENCES `paintings`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `settings` (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`key`, `value`) VALUES
('stripe_public_key', ''),
('stripe_secret_key', ''),
('paypal_client_id', ''),
('paypal_secret', ''),
('paypal_mode', 'sandbox'),
('bank_iban', ''),
('bank_bic', ''),
('bank_name', ''),
('contact_email', ''),
('contact_phone', ''),
('about_text', 'Artiste peintre passionné, chaque toile est une pièce unique.'),
('shipping_info', 'Livraison en France métropolitaine. Contactez-nous pour les envois internationaux.'),
('artist_bio', ''),
('timeline_data', ''),
('packlink_api_key', ''),
('shipping_mondial_relay_price', '6.90'),
('shipping_mondial_relay_enabled', '1'),
('shipping_shop2shop_price', '5.90'),
('shipping_shop2shop_enabled', '1'),
('shipping_ups_price', '12.90'),
('shipping_ups_enabled', '1'),
('shipping_pickup_price', '0'),
('shipping_pickup_enabled', '1')
ON DUPLICATE KEY UPDATE `key`=`key`;

INSERT INTO `admin_users` (`name`, `email`, `password`) VALUES
('Admin', 'admin@vogel-art.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE `email`=`email`;

SET FOREIGN_KEY_CHECKS = 1;
