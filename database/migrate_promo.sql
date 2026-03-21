CREATE TABLE IF NOT EXISTS `promo_codes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `type` ENUM('percent', 'fixed') NOT NULL DEFAULT 'percent',
    `value` DECIMAL(10,2) NOT NULL,
    `min_order` DECIMAL(10,2) DEFAULT 0,
    `max_uses` INT UNSIGNED DEFAULT NULL,
    `used_count` INT UNSIGNED DEFAULT 0,
    `expires_at` DATE DEFAULT NULL,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE orders ADD COLUMN `promo_code` VARCHAR(50) DEFAULT NULL AFTER `shipping_cost`;
ALTER TABLE orders ADD COLUMN `discount` DECIMAL(10,2) DEFAULT 0 AFTER `promo_code`;
