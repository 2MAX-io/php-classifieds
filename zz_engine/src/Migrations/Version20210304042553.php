<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210304042553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9F74B8985E237E06 ON setting (name)');
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'itemsPerPage', '20', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'deleteExpiredListingFilesEnabled', '0', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'deleteExpiredListingFilesOlderThanDays', '736', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'timezone', 'Europe/Warsaw', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'decimalSeparator', '.', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'thousandSeparator', ' ', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'languageIso', 'pl', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'countryIso', 'PL', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'dateFormat', 'Y-m-d, H:i', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'dateFormatShort', 'Y-m-d', '2010-01-01 00:00:00');");
        $this->addSql("INSERT INTO setting (name, value, last_update_date) VALUES ( 'invoiceGenerationStrategy', 'disabled', '2010-01-01 00:00:00') ON DUPLICATE KEY UPDATE value = 'disabled';");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_9F74B8985E237E06 ON setting');
    }
}
