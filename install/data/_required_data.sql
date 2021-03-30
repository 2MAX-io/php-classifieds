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
