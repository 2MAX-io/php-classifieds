<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version1 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'first database structure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE admin (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email VARCHAR(70) NOT NULL, roles JSON NOT NULL, enabled TINYINT(1) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_id INT UNSIGNED DEFAULT NULL, name VARCHAR(50) NOT NULL, lvl SMALLINT UNSIGNED NOT NULL, lft SMALLINT UNSIGNED NOT NULL, rgt SMALLINT UNSIGNED NOT NULL, sort SMALLINT UNSIGNED NOT NULL, slug VARCHAR(100) NOT NULL, advertisement_zone_id VARCHAR(50) DEFAULT NULL, picture VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug), INDEX IDX_64C19C1727ACA70 (parent_id), INDEX lft_rgt (lft, rgt), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_field (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, name_for_admin VARCHAR(50) DEFAULT NULL, type VARCHAR(40) NOT NULL, required TINYINT(1) NOT NULL, searchable TINYINT(1) NOT NULL, inline_on_list TINYINT(1) NOT NULL, sort SMALLINT UNSIGNED NOT NULL, unit VARCHAR(25) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_field_for_category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, category_id INT UNSIGNED NOT NULL, custom_field_id INT UNSIGNED NOT NULL, sort SMALLINT UNSIGNED NOT NULL, INDEX IDX_C82630EE12469DE2 (category_id), INDEX IDX_C82630EEA1E5E0D4 (custom_field_id), UNIQUE INDEX unique_field_category_pair (custom_field_id, category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_field_option (id INT UNSIGNED AUTO_INCREMENT NOT NULL, custom_field_id INT UNSIGNED NOT NULL, name VARCHAR(50) NOT NULL, value VARCHAR(50) NOT NULL, sort SMALLINT UNSIGNED NOT NULL, INDEX IDX_B459BA9A1E5E0D4 (custom_field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE featured_package (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, admin_name VARCHAR(70) NOT NULL, default_package TINYINT(1) NOT NULL, price INT NOT NULL, days_featured_expire SMALLINT UNSIGNED NOT NULL, days_listing_expire SMALLINT UNSIGNED NOT NULL, removed TINYINT(1) NOT NULL, description VARCHAR(10000) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE featured_package_for_category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, category_id INT UNSIGNED NOT NULL, featured_package_id INT UNSIGNED NOT NULL, INDEX IDX_37B7E1D212469DE2 (category_id), INDEX IDX_37B7E1D22AAF55BA (featured_package_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, payment_id INT UNSIGNED DEFAULT NULL, invoice_number VARCHAR(100) DEFAULT NULL, invoice_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', total_money VARCHAR(255) NOT NULL, currency VARCHAR(50) NOT NULL, total_tax_money VARCHAR(255) DEFAULT NULL, external_system_reference VARCHAR(255) DEFAULT NULL, external_system_reference_secondary VARCHAR(255) DEFAULT NULL, uuid VARCHAR(255) NOT NULL, display_to_user TINYINT(1) NOT NULL, exported TINYINT(1) NOT NULL, sent_to_user TINYINT(1) NOT NULL, invoice_file_path VARCHAR(255) NOT NULL, company_name VARCHAR(255) DEFAULT NULL, individual_client_name VARCHAR(255) DEFAULT NULL, client_tax_number VARCHAR(50) DEFAULT NULL, city VARCHAR(100) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, building_number VARCHAR(50) DEFAULT NULL, unit_number VARCHAR(50) DEFAULT NULL, zip_code VARCHAR(50) DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, seller_company_name VARCHAR(255) NOT NULL, seller_tax_number VARCHAR(50) DEFAULT NULL, seller_city VARCHAR(100) NOT NULL, seller_street VARCHAR(255) DEFAULT NULL, seller_building_number VARCHAR(50) NOT NULL, seller_unit_number VARCHAR(50) DEFAULT NULL, seller_zip_code VARCHAR(50) NOT NULL, seller_country VARCHAR(50) DEFAULT NULL, seller_email VARCHAR(100) DEFAULT NULL, email_to_send_invoice VARCHAR(255) NOT NULL, export_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', sent_to_user_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', updated_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_906517442DA68207 (invoice_number), INDEX IDX_90651744A76ED395 (user_id), INDEX IDX_906517444C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing (id INT UNSIGNED AUTO_INCREMENT NOT NULL, category_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, valid_until_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', admin_activated TINYINT(1) NOT NULL, admin_rejected TINYINT(1) NOT NULL, admin_removed TINYINT(1) NOT NULL, user_deactivated TINYINT(1) NOT NULL, user_removed TINYINT(1) NOT NULL, featured TINYINT(1) NOT NULL, featured_until_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', featured_weight SMALLINT NOT NULL, order_by_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', first_created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', last_edit_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', admin_last_activation_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', title VARCHAR(70) NOT NULL, description VARCHAR(12000) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, price_for VARCHAR(40) DEFAULT NULL, price_negotiable TINYINT(1) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, email VARCHAR(70) DEFAULT NULL, email_show TINYINT(1) NOT NULL, location VARCHAR(30) DEFAULT NULL, location_latitude DOUBLE PRECISION DEFAULT NULL, location_longitude DOUBLE PRECISION DEFAULT NULL, main_image VARCHAR(255) DEFAULT NULL, slug VARCHAR(100) NOT NULL, search_text LONGTEXT NOT NULL, custom_fields_inline_json VARCHAR(1000) DEFAULT NULL, rejection_reason VARCHAR(150) DEFAULT NULL, INDEX IDX_CB0048D412469DE2 (category_id), INDEX IDX_CB0048D4A76ED395 (user_id), INDEX IDX_public_listings (user_deactivated, valid_until_date, user_removed, admin_activated, admin_rejected, admin_removed, featured, featured_weight, order_by_date, id), INDEX IDX_activated (admin_activated, admin_removed, user_removed, user_deactivated, admin_rejected), INDEX IDX_featured (featured, user_deactivated, valid_until_date, user_removed, admin_activated, admin_removed), INDEX IDX_public_listings_cat (category_id, user_deactivated, valid_until_date, user_removed, admin_activated, admin_removed, featured, featured_weight, order_by_date, id), INDEX IDX_public_filtered (category_id, user_deactivated, valid_until_date, user_removed, admin_activated, admin_removed, price, featured, featured_weight, order_by_date, id), INDEX IDX_latest_listings (user_deactivated, valid_until_date, user_removed, admin_activated, admin_removed, first_created_date), INDEX IDX_first_created_date (first_created_date), INDEX IDX_user_listings (user_id, user_removed, last_edit_date), FULLTEXT INDEX IDX_fulltext_search (search_text), FULLTEXT INDEX IDX_fulltext_search_admin (search_text, email, phone, rejection_reason), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing_custom_field_value (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED NOT NULL, custom_field_id INT UNSIGNED NOT NULL, custom_field_option_id INT UNSIGNED DEFAULT NULL, value VARCHAR(70) NOT NULL, INDEX IDX_89DD084D4619D1A (listing_id), INDEX IDX_89DD084A1E5E0D4 (custom_field_id), INDEX IDX_89DD08424EB451A (custom_field_option_id), INDEX IDX_filter (listing_id, custom_field_id, value), UNIQUE INDEX unique_custom_field_value_in_listing (listing_id, custom_field_id, value, custom_field_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing_file (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED NOT NULL, path VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, user_original_filename VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(100) NOT NULL, size_bytes INT NOT NULL, file_hash VARCHAR(64) NOT NULL, image_width SMALLINT UNSIGNED DEFAULT NULL, image_height SMALLINT UNSIGNED DEFAULT NULL, user_removed TINYINT(1) NOT NULL, file_deleted TINYINT(1) NOT NULL, upload_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', sort SMALLINT UNSIGNED NOT NULL, INDEX IDX_DB61E1C5D4619D1A (listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing_view (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED NOT NULL, view_count INT NOT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_A9037C5BD4619D1A (listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT UNSIGNED AUTO_INCREMENT NOT NULL, slug VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, amount INT NOT NULL, gateway_amount_paid INT DEFAULT NULL, currency VARCHAR(10) NOT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', gateway_status VARCHAR(255) NOT NULL, paid TINYINT(1) NOT NULL, delivered TINYINT(1) NOT NULL, canceled TINYINT(1) NOT NULL, gateway_payment_id VARCHAR(255) DEFAULT NULL, app_payment_token VARCHAR(70) NOT NULL, gateway_name VARCHAR(70) NOT NULL, gateway_mode VARCHAR(70) NOT NULL, INDEX IDX_6D28840DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_for_balance_top_up (id INT UNSIGNED AUTO_INCREMENT NOT NULL, payment_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_134B3E824C3A3BB (payment_id), INDEX IDX_134B3E82A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_for_featured_package (id INT UNSIGNED AUTO_INCREMENT NOT NULL, payment_id INT UNSIGNED NOT NULL, featured_package_id INT UNSIGNED NOT NULL, listing_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_549331944C3A3BB (payment_id), INDEX IDX_549331942AAF55BA (featured_package_id), INDEX IDX_54933194D4619D1A (listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, query VARCHAR(255) NOT NULL, results_count INT UNSIGNED NOT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(70) NOT NULL, value VARCHAR(5000) NOT NULL, last_update_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_9F74B8985E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, username VARCHAR(70) NOT NULL, email VARCHAR(70) NOT NULL, display_username VARCHAR(100) DEFAULT NULL, roles JSON NOT NULL, enabled TINYINT(1) NOT NULL, password VARCHAR(255) NOT NULL, registration_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_balance_change (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, payment_id INT UNSIGNED DEFAULT NULL, balance_change INT NOT NULL, balance_final INT NOT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', description VARCHAR(5000) NOT NULL, INDEX IDX_AE296710A76ED395 (user_id), INDEX IDX_AE2967104C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_invoice_details (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, company_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, tax_number VARCHAR(50) DEFAULT NULL, city VARCHAR(100) NOT NULL, street VARCHAR(255) DEFAULT NULL, building_number VARCHAR(50) NOT NULL, unit_number VARCHAR(50) DEFAULT NULL, zip_code VARCHAR(50) NOT NULL, country VARCHAR(50) DEFAULT NULL, email_to_send_invoice VARCHAR(255) NOT NULL, created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', updated_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_922351D6A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_message (id INT UNSIGNED AUTO_INCREMENT NOT NULL, sender_user_id INT UNSIGNED NOT NULL, recipient_user_id INT UNSIGNED NOT NULL, user_message_thread_id INT UNSIGNED NOT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', message VARCHAR(3600) NOT NULL, spam_checked TINYINT(1) NOT NULL, spam TINYINT(1) NOT NULL, recipient_notified TINYINT(1) NOT NULL, recipient_read TINYINT(1) NOT NULL, recipient_read_datetime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_EEB02E752A98155E (sender_user_id), INDEX IDX_EEB02E75B15EFB97 (recipient_user_id), INDEX IDX_EEB02E757036FD04 (user_message_thread_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_message_thread (id INT UNSIGNED AUTO_INCREMENT NOT NULL, created_by_user_id INT UNSIGNED NOT NULL, listing_id INT UNSIGNED DEFAULT NULL, created_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', latest_message_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_6D820ED57D182D95 (created_by_user_id), INDEX IDX_6D820ED5D4619D1A (listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_observed_listing (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, listing_id INT UNSIGNED NOT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_7BA20613A76ED395 (user_id), INDEX IDX_7BA20613D4619D1A (listing_id), UNIQUE INDEX unique_user_listing (user_id, listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_executed_upgrade (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, upgrade_id INT NOT NULL, executed_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_listing_internal_data (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED NOT NULL, last_listing_regeneration_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_6E584F9FD4619D1A (listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_listing_report (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED DEFAULT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', report_message VARCHAR(3000) NOT NULL, email VARCHAR(100) NOT NULL, removed TINYINT(1) NOT NULL, police_log VARCHAR(255) NOT NULL, INDEX IDX_520E32CAD4619D1A (listing_id), INDEX IDX_520E32CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_police_log_listing (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED DEFAULT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', source_ip VARCHAR(50) NOT NULL, source_port VARCHAR(10) NOT NULL, destination_ip VARCHAR(50) NOT NULL, destination_port VARCHAR(10) NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_police_log_user_message (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED DEFAULT NULL, sender_user_id INT UNSIGNED DEFAULT NULL, recipient_user_id INT UNSIGNED DEFAULT NULL, user_message_id INT UNSIGNED DEFAULT NULL, user_message_thread_id INT UNSIGNED DEFAULT NULL, datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', source_ip VARCHAR(50) NOT NULL, source_port VARCHAR(10) NOT NULL, destination_ip VARCHAR(50) NOT NULL, destination_port VARCHAR(10) NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_system_log (id INT UNSIGNED AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', type VARCHAR(70) NOT NULL, message VARCHAR(1000) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_token (id INT UNSIGNED AUTO_INCREMENT NOT NULL, token_string VARCHAR(100) NOT NULL, type VARCHAR(50) NOT NULL, created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', used TINYINT(1) NOT NULL, valid_until_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_7629DA559FC61DAB (token_string), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zzzz_token_field (id INT UNSIGNED AUTO_INCREMENT NOT NULL, token_id INT UNSIGNED NOT NULL, name VARCHAR(100) NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_C38DA32E41DEE7B9 (token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE custom_field_for_category ADD CONSTRAINT FK_C82630EE12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE custom_field_for_category ADD CONSTRAINT FK_C82630EEA1E5E0D4 FOREIGN KEY (custom_field_id) REFERENCES custom_field (id)');
        $this->addSql('ALTER TABLE custom_field_option ADD CONSTRAINT FK_B459BA9A1E5E0D4 FOREIGN KEY (custom_field_id) REFERENCES custom_field (id)');
        $this->addSql('ALTER TABLE featured_package_for_category ADD CONSTRAINT FK_37B7E1D212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE featured_package_for_category ADD CONSTRAINT FK_37B7E1D22AAF55BA FOREIGN KEY (featured_package_id) REFERENCES featured_package (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517444C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE listing ADD CONSTRAINT FK_CB0048D412469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE listing ADD CONSTRAINT FK_CB0048D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE listing_custom_field_value ADD CONSTRAINT FK_89DD084D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE listing_custom_field_value ADD CONSTRAINT FK_89DD084A1E5E0D4 FOREIGN KEY (custom_field_id) REFERENCES custom_field (id)');
        $this->addSql('ALTER TABLE listing_custom_field_value ADD CONSTRAINT FK_89DD08424EB451A FOREIGN KEY (custom_field_option_id) REFERENCES custom_field_option (id)');
        $this->addSql('ALTER TABLE listing_file ADD CONSTRAINT FK_DB61E1C5D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE listing_view ADD CONSTRAINT FK_A9037C5BD4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment_for_balance_top_up ADD CONSTRAINT FK_134B3E824C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE payment_for_balance_top_up ADD CONSTRAINT FK_134B3E82A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment_for_featured_package ADD CONSTRAINT FK_549331944C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE payment_for_featured_package ADD CONSTRAINT FK_549331942AAF55BA FOREIGN KEY (featured_package_id) REFERENCES featured_package (id)');
        $this->addSql('ALTER TABLE payment_for_featured_package ADD CONSTRAINT FK_54933194D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE user_balance_change ADD CONSTRAINT FK_AE296710A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_balance_change ADD CONSTRAINT FK_AE2967104C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE user_invoice_details ADD CONSTRAINT FK_922351D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E752A98155E FOREIGN KEY (sender_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E75B15EFB97 FOREIGN KEY (recipient_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_message ADD CONSTRAINT FK_EEB02E757036FD04 FOREIGN KEY (user_message_thread_id) REFERENCES user_message_thread (id)');
        $this->addSql('ALTER TABLE user_message_thread ADD CONSTRAINT FK_6D820ED57D182D95 FOREIGN KEY (created_by_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_message_thread ADD CONSTRAINT FK_6D820ED5D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE user_observed_listing ADD CONSTRAINT FK_7BA20613A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_observed_listing ADD CONSTRAINT FK_7BA20613D4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE zzzz_listing_internal_data ADD CONSTRAINT FK_6E584F9FD4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE zzzz_listing_report ADD CONSTRAINT FK_520E32CAD4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE zzzz_listing_report ADD CONSTRAINT FK_520E32CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE zzzz_token_field ADD CONSTRAINT FK_C38DA32E41DEE7B9 FOREIGN KEY (token_id) REFERENCES zzzz_token (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE custom_field_for_category DROP FOREIGN KEY FK_C82630EE12469DE2');
        $this->addSql('ALTER TABLE featured_package_for_category DROP FOREIGN KEY FK_37B7E1D212469DE2');
        $this->addSql('ALTER TABLE listing DROP FOREIGN KEY FK_CB0048D412469DE2');
        $this->addSql('ALTER TABLE custom_field_for_category DROP FOREIGN KEY FK_C82630EEA1E5E0D4');
        $this->addSql('ALTER TABLE custom_field_option DROP FOREIGN KEY FK_B459BA9A1E5E0D4');
        $this->addSql('ALTER TABLE listing_custom_field_value DROP FOREIGN KEY FK_89DD084A1E5E0D4');
        $this->addSql('ALTER TABLE listing_custom_field_value DROP FOREIGN KEY FK_89DD08424EB451A');
        $this->addSql('ALTER TABLE featured_package_for_category DROP FOREIGN KEY FK_37B7E1D22AAF55BA');
        $this->addSql('ALTER TABLE payment_for_featured_package DROP FOREIGN KEY FK_549331942AAF55BA');
        $this->addSql('ALTER TABLE listing_custom_field_value DROP FOREIGN KEY FK_89DD084D4619D1A');
        $this->addSql('ALTER TABLE listing_file DROP FOREIGN KEY FK_DB61E1C5D4619D1A');
        $this->addSql('ALTER TABLE listing_view DROP FOREIGN KEY FK_A9037C5BD4619D1A');
        $this->addSql('ALTER TABLE payment_for_featured_package DROP FOREIGN KEY FK_54933194D4619D1A');
        $this->addSql('ALTER TABLE user_message_thread DROP FOREIGN KEY FK_6D820ED5D4619D1A');
        $this->addSql('ALTER TABLE user_observed_listing DROP FOREIGN KEY FK_7BA20613D4619D1A');
        $this->addSql('ALTER TABLE zzzz_listing_internal_data DROP FOREIGN KEY FK_6E584F9FD4619D1A');
        $this->addSql('ALTER TABLE zzzz_listing_report DROP FOREIGN KEY FK_520E32CAD4619D1A');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517444C3A3BB');
        $this->addSql('ALTER TABLE payment_for_balance_top_up DROP FOREIGN KEY FK_134B3E824C3A3BB');
        $this->addSql('ALTER TABLE payment_for_featured_package DROP FOREIGN KEY FK_549331944C3A3BB');
        $this->addSql('ALTER TABLE user_balance_change DROP FOREIGN KEY FK_AE2967104C3A3BB');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A76ED395');
        $this->addSql('ALTER TABLE listing DROP FOREIGN KEY FK_CB0048D4A76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DA76ED395');
        $this->addSql('ALTER TABLE payment_for_balance_top_up DROP FOREIGN KEY FK_134B3E82A76ED395');
        $this->addSql('ALTER TABLE user_balance_change DROP FOREIGN KEY FK_AE296710A76ED395');
        $this->addSql('ALTER TABLE user_invoice_details DROP FOREIGN KEY FK_922351D6A76ED395');
        $this->addSql('ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E752A98155E');
        $this->addSql('ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E75B15EFB97');
        $this->addSql('ALTER TABLE user_message_thread DROP FOREIGN KEY FK_6D820ED57D182D95');
        $this->addSql('ALTER TABLE user_observed_listing DROP FOREIGN KEY FK_7BA20613A76ED395');
        $this->addSql('ALTER TABLE zzzz_listing_report DROP FOREIGN KEY FK_520E32CAA76ED395');
        $this->addSql('ALTER TABLE user_message DROP FOREIGN KEY FK_EEB02E757036FD04');
        $this->addSql('ALTER TABLE zzzz_token_field DROP FOREIGN KEY FK_C38DA32E41DEE7B9');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE custom_field');
        $this->addSql('DROP TABLE custom_field_for_category');
        $this->addSql('DROP TABLE custom_field_option');
        $this->addSql('DROP TABLE featured_package');
        $this->addSql('DROP TABLE featured_package_for_category');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE listing');
        $this->addSql('DROP TABLE listing_custom_field_value');
        $this->addSql('DROP TABLE listing_file');
        $this->addSql('DROP TABLE listing_view');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_for_balance_top_up');
        $this->addSql('DROP TABLE payment_for_featured_package');
        $this->addSql('DROP TABLE search_history');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_balance_change');
        $this->addSql('DROP TABLE user_invoice_details');
        $this->addSql('DROP TABLE user_message');
        $this->addSql('DROP TABLE user_message_thread');
        $this->addSql('DROP TABLE user_observed_listing');
        $this->addSql('DROP TABLE zzzz_executed_upgrade');
        $this->addSql('DROP TABLE zzzz_listing_internal_data');
        $this->addSql('DROP TABLE zzzz_listing_report');
        $this->addSql('DROP TABLE zzzz_police_log_listing');
        $this->addSql('DROP TABLE zzzz_police_log_user_message');
        $this->addSql('DROP TABLE zzzz_system_log');
        $this->addSql('DROP TABLE zzzz_token');
        $this->addSql('DROP TABLE zzzz_token_field');
    }
}
