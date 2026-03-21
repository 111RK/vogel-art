INSERT INTO settings (`key`, `value`) VALUES
('gallery_name', 'Vogel Art Gallery'),
('owner_firstname', ''),
('owner_lastname', ''),
('contact_address', ''),
('contact_city', ''),
('contact_postal', ''),
('contact_photo', '')
ON DUPLICATE KEY UPDATE `key`=`key`;
