<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611195435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, street_address LONGTEXT NOT NULL, rest_address LONGTEXT DEFAULT NULL, zip_code VARCHAR(15) NOT NULL, locality VARCHAR(50) NOT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_layout_setting (id INT AUTO_INCREMENT NOT NULL, logo_name VARCHAR(255) DEFAULT NULL, favicon_name VARCHAR(255) DEFAULT NULL, og_image_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, post_id INT DEFAULT NULL, author_id INT NOT NULL, published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ip VARCHAR(46) DEFAULT NULL, is_approved TINYINT(1) DEFAULT 0 NOT NULL, is_rgpd TINYINT(1) DEFAULT 0 NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_9474526C727ACA70 (parent_id), INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, account_id INT DEFAULT NULL, member_id INT DEFAULT NULL, sales_person_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, vat_number VARCHAR(255) NOT NULL, company_number VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4FBF094F5E237E06 (name), INDEX IDX_4FBF094FF5B7AF75 (address_id), INDEX IDX_4FBF094F32C8A3DE (organization_id), INDEX IDX_4FBF094F9B6B5FBA (account_id), INDEX IDX_4FBF094F7597D3FE (member_id), INDEX IDX_4FBF094F1D35E30E (sales_person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, ccy VARCHAR(3) NOT NULL, symbol VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_6956883FD2D95D97 (ccy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_center_article (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, is_featured TINYINT(1) DEFAULT 0 NOT NULL, featuredorder INT DEFAULT NULL, views INT DEFAULT NULL, tags VARCHAR(500) DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_CEAD05455E237E06 (name), UNIQUE INDEX UNIQ_CEAD0545989D9B62 (slug), INDEX IDX_CEAD054512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_center_category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, color VARCHAR(12) DEFAULT \'dark\', icon VARCHAR(50) DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_A4E7FFB85E237E06 (name), UNIQUE INDEX UNIQ_A4E7FFB8989D9B62 (slug), INDEX IDX_A4E7FFB8727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_center_faq (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, views INT DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE homepage_hero_setting (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) DEFAULT NULL, paragraph LONGTEXT DEFAULT NULL, content LONGTEXT NOT NULL, custom_background_name VARCHAR(50) DEFAULT NULL, custom_block_one_name VARCHAR(50) DEFAULT NULL, custom_block_two_name VARCHAR(50) DEFAULT NULL, custom_block_three_name VARCHAR(50) DEFAULT NULL, show_search_box TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, views INT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_140AB6205E237E06 (name), UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, category_id INT DEFAULT NULL, author_id INT NOT NULL, image_name VARCHAR(50) DEFAULT NULL, readtime INT DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, views INT DEFAULT NULL, tags VARCHAR(500) DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_5A8A6C8D5E237E06 (name), UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 (slug), INDEX IDX_5A8A6C8DC54C8C93 (type_id), INDEX IDX_5A8A6C8D12469DE2 (category_id), INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_category (id INT AUTO_INCREMENT NOT NULL, posts_count INT UNSIGNED NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, color VARCHAR(12) DEFAULT \'dark\', is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_B9A190605E237E06 (name), UNIQUE INDEX UNIQ_B9A19060989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_458B30225E237E06 (name), UNIQUE INDEX UNIQ_458B3022989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, is_visible TINYINT(1) DEFAULT 1 NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, rating INT DEFAULT 5, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_794381C65E237E06 (name), UNIQUE INDEX UNIQ_794381C6989D9B62 (slug), INDEX IDX_794381C6F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9F74B8985E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE testimonial (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, rating INT DEFAULT 5, content LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E6BDCDF75E237E06 (name), UNIQUE INDEX UNIQ_E6BDCDF7989D9B62 (slug), INDEX IDX_E6BDCDF7F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, member_id INT DEFAULT NULL, client_id INT DEFAULT NULL, firstname VARCHAR(20) NOT NULL, lastname VARCHAR(20) NOT NULL, username VARCHAR(30) NOT NULL, email VARCHAR(180) NOT NULL, avatar LONGTEXT DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_suspended TINYINT(1) DEFAULT 0 NOT NULL, is_verified TINYINT(1) DEFAULT 0 NOT NULL, is_agree_terms TINYINT(1) DEFAULT 0 NOT NULL, bill JSON DEFAULT NULL, last_login DATETIME DEFAULT NULL, last_login_ip VARCHAR(255) DEFAULT NULL, about LONGTEXT DEFAULT NULL, designation VARCHAR(255) DEFAULT NULL, is_team TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', discr VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, is_manual_delivery TINYINT(1) DEFAULT 0, INDEX IDX_8D93D6499B6B5FBA (account_id), INDEX IDX_8D93D6497597D3FE (member_id), INDEX IDX_8D93D64919EB6921 (client_id), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE superadministrator_members (super_administrator_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_2E68FADDA2C6C455 (super_administrator_id), INDEX IDX_2E68FADD7597D3FE (member_id), PRIMARY KEY(super_administrator_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manager_members (manager_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_FB6DE7E9783E3463 (manager_id), INDEX IDX_FB6DE7E97597D3FE (member_id), PRIMARY KEY(manager_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F32C8A3DE FOREIGN KEY (organization_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F7597D3FE FOREIGN KEY (member_id) REFERENCES `company` (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F1D35E30E FOREIGN KEY (sales_person_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE help_center_article ADD CONSTRAINT FK_CEAD054512469DE2 FOREIGN KEY (category_id) REFERENCES help_center_category (id)');
        $this->addSql('ALTER TABLE help_center_category ADD CONSTRAINT FK_A4E7FFB8727ACA70 FOREIGN KEY (parent_id) REFERENCES help_center_category (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC54C8C93 FOREIGN KEY (type_id) REFERENCES post_type (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES post_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE testimonial ADD CONSTRAINT FK_E6BDCDF7F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497597D3FE FOREIGN KEY (member_id) REFERENCES `company` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE superadministrator_members ADD CONSTRAINT FK_2E68FADDA2C6C455 FOREIGN KEY (super_administrator_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE superadministrator_members ADD CONSTRAINT FK_2E68FADD7597D3FE FOREIGN KEY (member_id) REFERENCES `company` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manager_members ADD CONSTRAINT FK_FB6DE7E9783E3463 FOREIGN KEY (manager_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manager_members ADD CONSTRAINT FK_FB6DE7E97597D3FE FOREIGN KEY (member_id) REFERENCES `company` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FF5B7AF75');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F32C8A3DE');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F9B6B5FBA');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F7597D3FE');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F1D35E30E');
        $this->addSql('ALTER TABLE help_center_article DROP FOREIGN KEY FK_CEAD054512469DE2');
        $this->addSql('ALTER TABLE help_center_category DROP FOREIGN KEY FK_A4E7FFB8727ACA70');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC54C8C93');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE testimonial DROP FOREIGN KEY FK_E6BDCDF7F675F31B');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499B6B5FBA');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497597D3FE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('ALTER TABLE superadministrator_members DROP FOREIGN KEY FK_2E68FADDA2C6C455');
        $this->addSql('ALTER TABLE superadministrator_members DROP FOREIGN KEY FK_2E68FADD7597D3FE');
        $this->addSql('ALTER TABLE manager_members DROP FOREIGN KEY FK_FB6DE7E9783E3463');
        $this->addSql('ALTER TABLE manager_members DROP FOREIGN KEY FK_FB6DE7E97597D3FE');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE app_layout_setting');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE help_center_article');
        $this->addSql('DROP TABLE help_center_category');
        $this->addSql('DROP TABLE help_center_faq');
        $this->addSql('DROP TABLE homepage_hero_setting');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_category');
        $this->addSql('DROP TABLE post_type');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE testimonial');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE superadministrator_members');
        $this->addSql('DROP TABLE manager_members');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
