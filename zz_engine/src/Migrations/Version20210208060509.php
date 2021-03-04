<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210208060509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'refactor, custom_field_for_category';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('rename table custom_field_join_category to custom_field_for_category;');
        $this->addSql('ALTER TABLE custom_field_for_category RENAME INDEX idx_13cf18a312469de2 TO IDX_C82630EE12469DE2');
        $this->addSql('ALTER TABLE custom_field_for_category RENAME INDEX idx_13cf18a3a1e5e0d4 TO IDX_C82630EEA1E5E0D4');
        $this->addSql('ALTER TABLE user_invoice_details CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('rename table custom_field_for_category to custom_field_join_category;');
        $this->addSql('ALTER TABLE user_invoice_details CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE custom_field_for_category RENAME INDEX idx_c82630eea1e5e0d4 TO IDX_13CF18A3A1E5E0D4');
        $this->addSql('ALTER TABLE custom_field_for_category RENAME INDEX idx_c82630ee12469de2 TO IDX_13CF18A312469DE2');
    }
}
