INSERT INTO `listing` (`id`, `category_id`, `user_id`, `valid_until_date`, `admin_activated`, `admin_rejected`, `admin_removed`, `user_deactivated`, `user_removed`, `featured`, `featured_until_date`, `featured_weight`, `order_by_date`, `first_created_date`, `last_edit_date`, `admin_last_activation_date`, `title`, `description`, `price`, `price_for`, `price_negotiable`, `phone`, `email`, `email_show`, `location`, `location_latitude`, `location_longitude`, `main_image`, `slug`, `search_text`, `custom_fields_inline_json`, `rejection_reason`) VALUES (1,140,1,'2099-01-01 00:00:00',0,0,0,0,0,0,NULL,0,'2021-03-22 06:49:00','2021-03-22 06:49:00','2021-03-22 06:49:00',NULL,'Test listing title','Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.',158654.1,NULL,0,'+48536363636','user@example.com',0,'Lake Charley',51.416804797758,-0.09613476434808,'static/system/1920x1080.png','test-listing-title','At sit aliquam reprehenderit Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.','[{\"name\":\"Material\",\"value\":\"Tinware\",\"type\":\"SELECT_SINGLE\",\"unit\":null}]',NULL);
INSERT INTO `listing_file` (`id`, `listing_id`, `path`, `filename`, `user_original_filename`, `mime_type`, `size_bytes`, `file_hash`, `image_width`, `image_height`, `user_removed`, `file_deleted`, `upload_date`, `sort`) VALUES (1,1,'static/system/category/phones_accessories.jpg','76a50887d8f1c2e9301755428990ad81479ee21c25b43215cf524541e0503269','phones_accessories.jpg','image/jpeg',363636,'phones_accessories.jpg',1920,1080,0,0,'2021-03-22 06:49:00',2);

INSERT INTO user (id, username, email, roles, enabled, display_username, notification_by_email_new_message, messages_enabled, password, registration_date, last_login) VALUES (2, 'user-demo2@2max.io', 'user-demo2@2max.io', '["ROLE_USER"]', 1, 'Demo User', 1, 1, '$argon2i$v=19$m=65536,t=6,p=1$5AHOeTkPkMLHtjEvDRyzNg$Famkk9IWW8ELDgQLuCEb2qSv0OrN6P22ZSVACJu284g', '2010-01-01 00:00:00', '2021-03-31 12:33:17');
INSERT INTO `listing` (`id`, `category_id`, `user_id`, `valid_until_date`, `admin_activated`, `admin_rejected`, `admin_removed`, `user_deactivated`, `user_removed`, `featured`, `featured_until_date`, `featured_weight`, `order_by_date`, `first_created_date`, `last_edit_date`, `admin_last_activation_date`, `title`, `description`, `price`, `price_for`, `price_negotiable`, `phone`, `email`, `email_show`, `location`, `location_latitude`, `location_longitude`, `main_image`, `slug`, `search_text`, `custom_fields_inline_json`, `rejection_reason`) VALUES (2,30,1,'2099-01-01 00:00:00',0,0,0,0,0,0,NULL,0,'2021-03-22 06:49:00','2021-03-22 06:49:00','2021-03-22 06:49:00',NULL,'Test lising of user 2','Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.',158654.1,NULL,0,'12345555555','user@example.com',0,'Lake Charley',NULL,NULL,'static/system/1920x1080.png','test-listing-title','At sit aliquam reprehenderit Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.','[{\"name\":\"Material\",\"value\":\"Tinware\",\"type\":\"SELECT_SINGLE\",\"unit\":null}]',NULL);


INSERT INTO user_message_thread (id, created_by_user_id, listing_id, created_datetime, latest_message_datetime) VALUES (1, 2, 1, '2021-03-31 14:42:14', '2021-03-31 14:42:14');
INSERT INTO user_message (id, sender_user_id, recipient_user_id, user_message_thread_id, datetime, message, spam_checked, spam, recipient_notified, recipient_read, recipient_read_datetime) VALUES (1, 2, 1, 1, '2021-03-31 14:42:14', 'test message', 0, 0, 0, 0, null);

INSERT INTO featured_package (id, name, admin_name, default_package, price, days_featured_expire, days_listing_expire, removed, description) VALUES (1, 'feature package test', 'name for admin', 1, 100, 1, 1, 0, 'description');
INSERT INTO payment (id, user_id, type, description, amount, gateway_amount_paid, currency, datetime, gateway_status, paid, delivered, canceled, gateway_payment_id, app_payment_token, gateway_name, gateway_mode) VALUES (1, 1, 'FOR_FEATURED_PACKAGE_TYPE', 'Payment for featured listing: Rerum quae neque corporis ad [id: 1], featured package: feature package test (name for admin) [id:1], price: 1, featured days: 1', 100, 100, 'USD', '2021-03-30 14:23:16', 'approved', 1, 1, 0, 'PAYID-MBRTIVA5VW80140HX8976436', 'test_app_payment_token', 'paypal', 'sandbox');
INSERT INTO payment_for_featured_package (id, payment_id, featured_package_id, listing_id) VALUES (1, 1, 1, 1);
INSERT INTO invoice (id, user_id, payment_id, invoice_number, invoice_date, total_money, currency, total_tax_money, external_system_reference, external_system_reference_secondary, uuid, display_to_user, exported, sent_to_user, invoice_file_path, company_name, individual_client_name, client_tax_number, city, street, building_number, unit_number, zip_code, country, seller_company_name, seller_tax_number, seller_city, seller_street, seller_building_number, seller_unit_number, seller_zip_code, seller_country, seller_email, email_to_send_invoice, export_date, sent_to_user_date, created_date, updated_date) VALUES (1, 1, 1, '1', '2021-03-30 14:23:37', '1', 'USD', null, null, null, '070b1fae-834e-40af-8cd2-9b155f5ff38d', 1, 0, 0, null, null, 'user-demo@2max.io', null, null, null, null, null, null, null, '', '', '', '', '', '', '', '', '', 'user-demo@2max.io', null, null, '2021-03-30 14:23:37', '2021-03-30 14:23:37');
UPDATE setting SET value = '1' WHERE name = 'paymentAllowed';
UPDATE setting SET value = 'auto' WHERE name = 'invoiceGenerationStrategy';

CREATE TABLE `zzzz_messenger_messages` (
   `id` bigint(20) NOT NULL AUTO_INCREMENT,
   `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
   `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
   `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
   `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
   `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime)',
   `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime)',
   PRIMARY KEY (`id`),
   KEY `IDX_9BEF6223FB7336F0` (`queue_name`),
   KEY `IDX_9BEF6223E3BD61CE` (`available_at`),
   KEY `IDX_9BEF622316BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

