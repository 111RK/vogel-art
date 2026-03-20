INSERT INTO settings (`key`, `value`) VALUES
('shipping_mondial_relay_domicile_price', '8.90'),
('shipping_mondial_relay_domicile_enabled', '1')
ON DUPLICATE KEY UPDATE `key`=`key`;
