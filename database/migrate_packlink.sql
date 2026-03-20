INSERT INTO settings (`key`, `value`) VALUES
('packlink_sender_name', ''),
('packlink_sender_address', ''),
('packlink_sender_city', ''),
('packlink_sender_postal', ''),
('default_parcel_weight', '2'),
('default_parcel_dimensions', '60x50x10')
ON DUPLICATE KEY UPDATE `key`=`key`;
