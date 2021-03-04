<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210304042552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE zzzz_listing_internal_data (id INT UNSIGNED AUTO_INCREMENT NOT NULL, listing_id INT UNSIGNED NOT NULL, last_search_text_regeneration_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', UNIQUE INDEX UNIQ_6E584F9FD4619D1A (listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE zzzz_listing_internal_data ADD CONSTRAINT FK_6E584F9FD4619D1A FOREIGN KEY (listing_id) REFERENCES listing (id)');
        $this->addSql('ALTER TABLE invoice ADD individual_client_name VARCHAR(255) DEFAULT NULL, CHANGE company_name company_name VARCHAR(255) DEFAULT NULL, CHANGE city city VARCHAR(100) DEFAULT NULL, CHANGE building_number building_number VARCHAR(50) DEFAULT NULL, CHANGE zip_code zip_code VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE listing_file ADD image_width SMALLINT UNSIGNED DEFAULT NULL, ADD image_height SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE zzzz_executed_upgrade CHANGE executed_at executed_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE zzzz_police_log_listing ADD source_port VARCHAR(10) NOT NULL, ADD destination_port VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE zzzz_police_log_user_message ADD recipient_user_id INT UNSIGNED DEFAULT NULL, ADD user_message_thread_id INT UNSIGNED DEFAULT NULL, ADD source_port VARCHAR(10) NOT NULL, ADD destination_port VARCHAR(10) NOT NULL, CHANGE user_id sender_user_id INT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE zzzz_listing_internal_data');
        $this->addSql('ALTER TABLE invoice DROP individual_client_name, CHANGE company_name company_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE city city VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE building_number building_number VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE zip_code zip_code VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE listing_file DROP image_width, DROP image_height');
        $this->addSql('ALTER TABLE zzzz_executed_upgrade CHANGE executed_datetime executed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE zzzz_police_log_listing DROP source_port, DROP destination_port');
        $this->addSql('ALTER TABLE zzzz_police_log_user_message ADD user_id INT UNSIGNED DEFAULT NULL, DROP sender_user_id, DROP recipient_user_id, DROP user_message_thread_id, DROP source_port, DROP destination_port');
    }
}
