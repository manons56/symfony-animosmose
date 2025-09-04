<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904092938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAE1EBAF6CC');
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAECFFE9AD6');
        $this->addSql('DROP TABLE orders_articles');
        $this->addSql('ALTER TABLE articles ADD order_id INT NOT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31688D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_BFDD31688D9F6D38 ON articles (order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orders_articles (orders_id INT NOT NULL, articles_id INT NOT NULL, INDEX IDX_78FBECAE1EBAF6CC (articles_id), INDEX IDX_78FBECAECFFE9AD6 (orders_id), PRIMARY KEY(orders_id, articles_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAE1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAECFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31688D9F6D38');
        $this->addSql('DROP INDEX IDX_BFDD31688D9F6D38 ON articles');
        $this->addSql('ALTER TABLE articles DROP order_id');
    }
}
