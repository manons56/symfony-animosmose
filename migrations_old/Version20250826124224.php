<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826124224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, street VARCHAR(255) NOT NULL, zipcode VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D4E6F819D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, variant_id_id INT NOT NULL, quantity VARCHAR(255) NOT NULL, price INT NOT NULL, INDEX IDX_BFDD3168FFCE010A (variant_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, address_id_id INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, INDEX IDX_E52FFDEE9D86650F (user_id_id), INDEX IDX_E52FFDEE48E1E977 (address_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders_articles (id INT AUTO_INCREMENT NOT NULL, order_id_id INT DEFAULT NULL, article_id_id INT DEFAULT NULL, INDEX IDX_78FBECAEFCDAEAAA (order_id_id), UNIQUE INDEX UNIQ_78FBECAE8F3EC46 (article_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, img_id_id INT NOT NULL, category_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, capacity VARCHAR(255) NOT NULL, composition VARCHAR(255) NOT NULL, analytics_components VARCHAR(255) DEFAULT NULL, nutritionnal_additive VARCHAR(255) DEFAULT NULL, is_new TINYINT(1) DEFAULT NULL, is_bestseller TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_B3BA5A5A57883738 (img_id_id), INDEX IDX_B3BA5A5A9777D11E (category_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variants (id INT AUTO_INCREMENT NOT NULL, product_id_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, price INT DEFAULT NULL, is_default TINYINT(1) DEFAULT NULL, INDEX IDX_B39853E1DE18E50B (product_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F819D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168FFCE010A FOREIGN KEY (variant_id_id) REFERENCES variants (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE48E1E977 FOREIGN KEY (address_id_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAEFCDAEAAA FOREIGN KEY (order_id_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE orders_articles ADD CONSTRAINT FK_78FBECAE8F3EC46 FOREIGN KEY (article_id_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A57883738 FOREIGN KEY (img_id_id) REFERENCES pictures (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A9777D11E FOREIGN KEY (category_id_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE variants ADD CONSTRAINT FK_B39853E1DE18E50B FOREIGN KEY (product_id_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F819D86650F');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168FFCE010A');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9D86650F');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE48E1E977');
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAEFCDAEAAA');
        $this->addSql('ALTER TABLE orders_articles DROP FOREIGN KEY FK_78FBECAE8F3EC46');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A57883738');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A9777D11E');
        $this->addSql('ALTER TABLE variants DROP FOREIGN KEY FK_B39853E1DE18E50B');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE orders_articles');
        $this->addSql('DROP TABLE pictures');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE variants');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
