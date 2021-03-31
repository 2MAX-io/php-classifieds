INSERT INTO `listing` (`id`, `category_id`, `user_id`, `valid_until_date`, `admin_activated`, `admin_rejected`, `admin_removed`, `user_deactivated`, `user_removed`, `featured`, `featured_until_date`, `featured_weight`, `order_by_date`, `first_created_date`, `last_edit_date`, `admin_last_activation_date`, `title`, `description`, `price`, `price_for`, `price_negotiable`, `phone`, `email`, `email_show`, `location`, `location_latitude`, `location_longitude`, `main_image`, `slug`, `search_text`, `custom_fields_inline_json`, `rejection_reason`) VALUES (1,30,1,'2021-03-29 06:49:36',0,0,0,0,0,0,NULL,0,'2021-03-22 06:49:00','2021-03-22 06:49:00','2021-03-22 06:49:00',NULL,'Test listing title','Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.',158654.1,NULL,0,'12345555555','user@example.com',0,'Lake Charley',NULL,NULL,'static/system/1920x1080.png','test-listing-title','At sit aliquam reprehenderit Tenetur provident et ut officiis voluptates. Quibusdam nihil quaerat qui fugit exercitationem in ad. Explicabo non maxime temporibus necessitatibus distinctio rerum officiis. Culpa omnis asperiores quis voluptas omnis dolor in.','[{\"name\":\"Material\",\"value\":\"Tinware\",\"type\":\"SELECT_SINGLE\",\"unit\":null}]',NULL);
INSERT INTO user (id, username, email, roles, enabled, display_username, notification_by_email_new_message, messages_enabled, password, registration_date, last_login) VALUES (2, 'user-demo2@2max.io', 'user-demo@2max.io', '["ROLE_USER"]', 1, 'Demo User', 1, 1, '$argon2i$v=19$m=65536,t=6,p=1$5AHOeTkPkMLHtjEvDRyzNg$Famkk9IWW8ELDgQLuCEb2qSv0OrN6P22ZSVACJu284g', '2010-01-01 00:00:00', '2021-03-31 12:33:17');
INSERT INTO user_message_thread (id, created_by_user_id, listing_id, created_datetime, latest_message_datetime) VALUES (1, 2, 1, '2021-03-31 14:42:14', '2021-03-31 14:42:14');
INSERT INTO user_message (id, sender_user_id, recipient_user_id, user_message_thread_id, datetime, message, spam_checked, spam, recipient_notified, recipient_read, recipient_read_datetime) VALUES (1, 2, 1, 1, '2021-03-31 14:42:14', 'test message', 0, 0, 0, 0, null);
