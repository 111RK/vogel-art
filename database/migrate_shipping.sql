INSERT INTO settings (`key`, `value`) VALUES
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
