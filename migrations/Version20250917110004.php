<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917110004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, street VARCHAR(255) NOT NULL, zipcode VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D4E6F819D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, variant_id_id INT NOT NULL, order_id INT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_BFDD3168FFCE010A (variant_id_id), INDEX IDX_BFDD31688D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, address_id_id INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, total NUMERIC(10, 2) NOT NULL, archived TINYINT(1) NOT NULL, delivery_method VARCHAR(50) DEFAULT NULL, INDEX IDX_E52FFDEEA76ED395 (user_id), INDEX IDX_E52FFDEE48E1E977 (address_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, filename VARCHAR(255) NOT NULL, INDEX IDX_8F7C2FC04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, capacity VARCHAR(255) NOT NULL, composition LONGTEXT NOT NULL, analytics_components LONGTEXT DEFAULT NULL, nutritionnal_additive LONGTEXT DEFAULT NULL, is_new TINYINT(1) DEFAULT NULL, is_bestseller TINYINT(1) DEFAULT NULL, is_out_of_stock TINYINT(1) DEFAULT NULL, INDEX IDX_B3BA5A5A12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE variants (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, label VARCHAR(255) NOT NULL, price INT DEFAULT NULL, is_default TINYINT(1) DEFAULT NULL, INDEX IDX_B39853E14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F819D86650F FOREIGN KEY (user_id_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168FFCE010A FOREIGN KEY (variant_id_id) REFERENCES variants (id)');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD31688D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE48E1E977 FOREIGN KEY (address_id_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC04584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE variants ADD CONSTRAINT FK_B39853E14584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F819D86650F');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168FFCE010A');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD31688D9F6D38');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE48E1E977');
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC04584665A');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A12469DE2');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE variants DROP FOREIGN KEY FK_B39853E14584665A');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE pictures');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE variants');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
