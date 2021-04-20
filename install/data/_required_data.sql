INSERT INTO category (id, parent_id, name, slug, sort, lft, rgt, lvl, picture) VALUES (1, null, 'Main Category', 'main-category', 0, 1, 9999, 0, '');

CREATE TABLE `zzzz_doctrine_migration_versions`
(
    `version`        VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
    `executed_at`    DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime)',
    `execution_time` INT(11)  DEFAULT NULL,
    PRIMARY KEY (`version`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
INSERT INTO zzzz_doctrine_migration_versions (version, executed_at, execution_time) VALUES ('App\\Migrations\\Version1', '2000-01-01 00:00:00', 36);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO package (id, name, admin_name, default_package, price, days_featured_expire, seconds_featured_expire, days_listing_expire, removed, description, listing_featured_priority, demo_package, show_price, show_expiration_days, show_featured_days) VALUES (1, '7 days', null, 1, null, 0, 0, 7, 0, null, 0, 0, 1, 1, 1);
