<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829134918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products CHANGE composition composition LONGTEXT NOT NULL, CHANGE analytics_components analytics_components LONGTEXT DEFAULT NULL, CHANGE nutritionnal_additive nutritionnal_additive LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products CHANGE composition composition VARCHAR(255) NOT NULL, CHANGE analytics_components analytics_components VARCHAR(255) DEFAULT NULL, CHANGE nutritionnal_additive nutritionnal_additive VARCHAR(255) DEFAULT NULL');
    }
}
