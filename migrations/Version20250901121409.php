<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901121409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles CHANGE quantity quantity INT NOT NULL, CHANGE price price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE orders ADD total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE orders_articles MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAE8F3EC46');
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAEFCDAEAAA');
        $this->addSql('DROP INDEX UNIQ_78FBECAE8F3EC46 ON orders_articles');
        $this->addSql('DROP INDEX IDX_78FBECAEFCDAEAAA ON orders_articles');
        $this->addSql('DROP INDEX `primary` ON orders_articles');
        $this->addSql('ALTER TABLE orders_articles ADD orders_id INT NOT NULL, ADD articles_id INT NOT NULL, DROP id, DROP order_id_id, DROP article_id_id');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAECFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAE1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_78FBECAECFFE9AD6 ON orders_articles (orders_id)');
        $this->addSql('CREATE INDEX IDX_78FBECAE1EBAF6CC ON orders_articles (articles_id)');
        $this->addSql('ALTER TABLE orders_articles ADD PRIMARY KEY (orders_id, articles_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles CHANGE quantity quantity VARCHAR(255) NOT NULL, CHANGE price price INT NOT NULL');
        $this->addSql('ALTER TABLE orders DROP total');
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAECFFE9AD6');
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAE1EBAF6CC');
        $this->addSql('DROP INDEX IDX_78FBECAECFFE9AD6 ON orders_articles');
        $this->addSql('DROP INDEX IDX_78FBECAE1EBAF6CC ON orders_articles');
        $this->addSql('ALTER TABLE orders_articles ADD id INT AUTO_INCREMENT NOT NULL, ADD order_id_id INT DEFAULT NULL, ADD article_id_id INT DEFAULT NULL, DROP orders_id, DROP articles_id, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAE8F3EC46 FOREIGN KEY (article_id_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAEFCDAEAAA FOREIGN KEY (order_id_id) REFERENCES orders (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_78FBECAE8F3EC46 ON orders_articles (article_id_id)');
        $this->addSql('CREATE INDEX IDX_78FBECAEFCDAEAAA ON orders_articles (order_id_id)');
    }
}
