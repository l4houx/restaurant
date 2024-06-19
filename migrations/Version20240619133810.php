<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619133810 extends AbstractMigration
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
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_1C52F9585E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, root_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, last_product_id INT DEFAULT NULL, number_of_products INT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug), INDEX IDX_64C19C179066886 (root_id), INDEX IDX_64C19C1727ACA70 (parent_id), INDEX IDX_64C19C1ACF00CB0 (last_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, post_id INT DEFAULT NULL, author_id INT NOT NULL, published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ip VARCHAR(46) DEFAULT NULL, is_approved TINYINT(1) DEFAULT 0 NOT NULL, is_rgpd TINYINT(1) DEFAULT 0 NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_9474526C727ACA70 (parent_id), INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, account_id INT DEFAULT NULL, member_id INT DEFAULT NULL, sales_person_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, vat_number VARCHAR(255) NOT NULL, company_number VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4FBF094F5E237E06 (name), INDEX IDX_4FBF094FF5B7AF75 (address_id), INDEX IDX_4FBF094F32C8A3DE (organization_id), INDEX IDX_4FBF094F9B6B5FBA (account_id), INDEX IDX_4FBF094F7597D3FE (member_id), INDEX IDX_4FBF094F1D35E30E (sales_person_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, ccy VARCHAR(3) NOT NULL, symbol VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_6956883FD2D95D97 (ccy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), INDEX general_translations_lookup_idx (object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE help_center_article (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, is_featured TINYINT(1) DEFAULT 0 NOT NULL, featuredorder INT DEFAULT NULL, views INT DEFAULT NULL, tags VARCHAR(500) DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_CEAD05455E237E06 (name), UNIQUE INDEX UNIQ_CEAD0545989D9B62 (slug), INDEX IDX_CEAD054512469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_center_category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, color VARCHAR(12) DEFAULT \'dark\', icon VARCHAR(50) DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_A4E7FFB85E237E06 (name), UNIQUE INDEX UNIQ_A4E7FFB8989D9B62 (slug), INDEX IDX_A4E7FFB8727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE help_center_faq (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, views INT DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE homepage_hero_setting (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) DEFAULT NULL, paragraph LONGTEXT DEFAULT NULL, content LONGTEXT NOT NULL, custom_background_name VARCHAR(50) DEFAULT NULL, custom_block_one_name VARCHAR(50) DEFAULT NULL, custom_block_two_name VARCHAR(50) DEFAULT NULL, custom_block_three_name VARCHAR(50) DEFAULT NULL, show_search_box TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, address_id INT DEFAULT NULL, state VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F5299398F5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order_line` (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, order_id INT NOT NULL, amount INT NOT NULL, quantity INT NOT NULL, INDEX IDX_9CE58EE14584665A (product_id), INDEX IDX_9CE58EE18D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, views INT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_140AB6205E237E06 (name), UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, category_id INT DEFAULT NULL, author_id INT NOT NULL, image_name VARCHAR(50) DEFAULT NULL, readtime INT DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, views INT DEFAULT NULL, tags VARCHAR(500) DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_5A8A6C8D5E237E06 (name), UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 (slug), INDEX IDX_5A8A6C8DC54C8C93 (type_id), INDEX IDX_5A8A6C8D12469DE2 (category_id), INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_category (id INT AUTO_INCREMENT NOT NULL, posts_count INT UNSIGNED NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, color VARCHAR(12) DEFAULT \'dark\', is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_B9A190605E237E06 (name), UNIQUE INDEX UNIQ_B9A19060989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_458B30225E237E06 (name), UNIQUE INDEX UNIQ_458B3022989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, brand_id INT NOT NULL, isonhomepageslider_id INT DEFAULT NULL, image_name VARCHAR(50) DEFAULT NULL, image_size INT DEFAULT NULL, image_mime_type VARCHAR(50) DEFAULT NULL, image_original_name TEXT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ref VARCHAR(20) NOT NULL, amount INT NOT NULL, tax DOUBLE PRECISION NOT NULL, stock INT NOT NULL, is_featured_product TINYINT(1) DEFAULT 0 NOT NULL, is_new_arrival TINYINT(1) DEFAULT 0 NOT NULL, enablereviews TINYINT(1) DEFAULT 1 NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, views INT DEFAULT NULL, tags VARCHAR(500) DEFAULT NULL, externallink VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, youtubeurl VARCHAR(255) DEFAULT NULL, twitterurl VARCHAR(255) DEFAULT NULL, instagramurl VARCHAR(255) DEFAULT NULL, facebookurl VARCHAR(255) DEFAULT NULL, googleplusurl VARCHAR(255) DEFAULT NULL, linkedinurl VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_D34A04AD146F3EA3 (ref), UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), UNIQUE INDEX UNIQ_D34A04AD989D9B62 (slug), INDEX IDX_D34A04AD12469DE2 (category_id), INDEX IDX_D34A04AD44F5D008 (brand_id), INDEX IDX_D34A04AD376C51EF (isonhomepageslider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorites (product_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E46960F54584665A (product_id), INDEX IDX_E46960F5A76ED395 (user_id), PRIMARY KEY(product_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_image (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, image_name VARCHAR(50) DEFAULT NULL, image_size INT UNSIGNED DEFAULT NULL, image_mime_type VARCHAR(50) DEFAULT NULL, image_original_name TEXT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', position INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_64617F034584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, author_id INT NOT NULL, is_visible TINYINT(1) DEFAULT 1 NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, rating INT DEFAULT 5, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_794381C65E237E06 (name), UNIQUE INDEX UNIQ_794381C6989D9B62 (slug), INDEX IDX_794381C64584665A (product_id), INDEX IDX_794381C6F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rules (id INT AUTO_INCREMENT NOT NULL, published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rules_agreement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, rules_id INT NOT NULL, accepted TINYINT(1) DEFAULT 0 NOT NULL, agreed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D4CE6AF2A76ED395 (user_id), INDEX IDX_D4CE6AF2FB699244 (rules_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9F74B8985E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, color VARCHAR(12) DEFAULT \'dark\', UNIQUE INDEX UNIQ_BCE3F7985E237E06 (name), UNIQUE INDEX UNIQ_BCE3F798989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subCategory_categories (sub_category_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_DA3235F9F7BFE87C (sub_category_id), INDEX IDX_DA3235F912469DE2 (category_id), PRIMARY KEY(sub_category_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE testimonial (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, rating INT DEFAULT 5, content LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E6BDCDF75E237E06 (name), UNIQUE INDEX UNIQ_E6BDCDF7989D9B62 (slug), INDEX IDX_E6BDCDF7F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, wallet_id INT NOT NULL, order_id INT DEFAULT NULL, transfer_id INT DEFAULT NULL, points INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', discr VARCHAR(255) NOT NULL, state VARCHAR(255) DEFAULT NULL, intern_reference VARCHAR(255) DEFAULT NULL, mode VARCHAR(255) DEFAULT NULL, INDEX IDX_723705D19B6B5FBA (account_id), INDEX IDX_723705D1712520F3 (wallet_id), INDEX IDX_723705D18D9F6D38 (order_id), INDEX IDX_723705D1537048AF (transfer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, from_id INT NOT NULL, to_id INT NOT NULL, points INT NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4034A3C078CED90B (from_id), INDEX IDX_4034A3C030354A65 (to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transfer_transactions (transfer_id INT NOT NULL, transaction_id INT NOT NULL, INDEX IDX_A475958F537048AF (transfer_id), INDEX IDX_A475958F2FC0CB0F (transaction_id), PRIMARY KEY(transfer_id, transaction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, member_id INT DEFAULT NULL, client_id INT DEFAULT NULL, firstname VARCHAR(20) NOT NULL, lastname VARCHAR(20) NOT NULL, username VARCHAR(30) NOT NULL, email VARCHAR(180) NOT NULL, avatar LONGTEXT DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_suspended TINYINT(1) DEFAULT 0 NOT NULL, is_verified TINYINT(1) DEFAULT 0 NOT NULL, is_agree_terms TINYINT(1) DEFAULT 0 NOT NULL, bill JSON DEFAULT NULL, last_login DATETIME DEFAULT NULL, last_login_ip VARCHAR(255) DEFAULT NULL, about LONGTEXT DEFAULT NULL, designation VARCHAR(255) DEFAULT NULL, is_team TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', discr VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, is_manual_delivery TINYINT(1) DEFAULT 0, INDEX IDX_8D93D6499B6B5FBA (account_id), INDEX IDX_8D93D6497597D3FE (member_id), INDEX IDX_8D93D64919EB6921 (client_id), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE superadministrator_members (super_administrator_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_2E68FADDA2C6C455 (super_administrator_id), INDEX IDX_2E68FADD7597D3FE (member_id), PRIMARY KEY(super_administrator_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manager_members (manager_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_FB6DE7E9783E3463 (manager_id), INDEX IDX_FB6DE7E97597D3FE (member_id), PRIMARY KEY(manager_id, member_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, purchase_id INT DEFAULT NULL, account_id INT NOT NULL, balance INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', expired_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7C68921F558FBEB9 (purchase_id), INDEX IDX_7C68921F9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C179066886 FOREIGN KEY (root_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1ACF00CB0 FOREIGN KEY (last_product_id) REFERENCES product (id)');
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
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE `order_line` ADD CONSTRAINT FK_9CE58EE14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order_line` ADD CONSTRAINT FK_9CE58EE18D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC54C8C93 FOREIGN KEY (type_id) REFERENCES post_type (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES post_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD376C51EF FOREIGN KEY (isonhomepageslider_id) REFERENCES homepage_hero_setting (id)');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rules_agreement ADD CONSTRAINT FK_D4CE6AF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rules_agreement ADD CONSTRAINT FK_D4CE6AF2FB699244 FOREIGN KEY (rules_id) REFERENCES rules (id)');
        $this->addSql('ALTER TABLE subCategory_categories ADD CONSTRAINT FK_DA3235F9F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subCategory_categories ADD CONSTRAINT FK_DA3235F912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE testimonial ADD CONSTRAINT FK_E6BDCDF7F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1537048AF FOREIGN KEY (transfer_id) REFERENCES transfer (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C078CED90B FOREIGN KEY (from_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C030354A65 FOREIGN KEY (to_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transfer_transactions ADD CONSTRAINT FK_A475958F537048AF FOREIGN KEY (transfer_id) REFERENCES transfer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transfer_transactions ADD CONSTRAINT FK_A475958F2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497597D3FE FOREIGN KEY (member_id) REFERENCES `company` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE superadministrator_members ADD CONSTRAINT FK_2E68FADDA2C6C455 FOREIGN KEY (super_administrator_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE superadministrator_members ADD CONSTRAINT FK_2E68FADD7597D3FE FOREIGN KEY (member_id) REFERENCES `company` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manager_members ADD CONSTRAINT FK_FB6DE7E9783E3463 FOREIGN KEY (manager_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manager_members ADD CONSTRAINT FK_FB6DE7E97597D3FE FOREIGN KEY (member_id) REFERENCES `company` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F558FBEB9 FOREIGN KEY (purchase_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C179066886');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1ACF00CB0');
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
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F5B7AF75');
        $this->addSql('ALTER TABLE `order_line` DROP FOREIGN KEY FK_9CE58EE14584665A');
        $this->addSql('ALTER TABLE `order_line` DROP FOREIGN KEY FK_9CE58EE18D9F6D38');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC54C8C93');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD376C51EF');
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F54584665A');
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F5A76ED395');
        $this->addSql('ALTER TABLE product_image DROP FOREIGN KEY FK_64617F034584665A');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE rules_agreement DROP FOREIGN KEY FK_D4CE6AF2A76ED395');
        $this->addSql('ALTER TABLE rules_agreement DROP FOREIGN KEY FK_D4CE6AF2FB699244');
        $this->addSql('ALTER TABLE subCategory_categories DROP FOREIGN KEY FK_DA3235F9F7BFE87C');
        $this->addSql('ALTER TABLE subCategory_categories DROP FOREIGN KEY FK_DA3235F912469DE2');
        $this->addSql('ALTER TABLE testimonial DROP FOREIGN KEY FK_E6BDCDF7F675F31B');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19B6B5FBA');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1712520F3');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18D9F6D38');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1537048AF');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C078CED90B');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C030354A65');
        $this->addSql('ALTER TABLE transfer_transactions DROP FOREIGN KEY FK_A475958F537048AF');
        $this->addSql('ALTER TABLE transfer_transactions DROP FOREIGN KEY FK_A475958F2FC0CB0F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499B6B5FBA');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497597D3FE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64919EB6921');
        $this->addSql('ALTER TABLE superadministrator_members DROP FOREIGN KEY FK_2E68FADDA2C6C455');
        $this->addSql('ALTER TABLE superadministrator_members DROP FOREIGN KEY FK_2E68FADD7597D3FE');
        $this->addSql('ALTER TABLE manager_members DROP FOREIGN KEY FK_FB6DE7E9783E3463');
        $this->addSql('ALTER TABLE manager_members DROP FOREIGN KEY FK_FB6DE7E97597D3FE');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921F558FBEB9');
        $this->addSql('ALTER TABLE wallet DROP FOREIGN KEY FK_7C68921F9B6B5FBA');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE app_layout_setting');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE help_center_article');
        $this->addSql('DROP TABLE help_center_category');
        $this->addSql('DROP TABLE help_center_faq');
        $this->addSql('DROP TABLE homepage_hero_setting');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE `order_line`');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_category');
        $this->addSql('DROP TABLE post_type');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE favorites');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE rules');
        $this->addSql('DROP TABLE rules_agreement');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE sub_category');
        $this->addSql('DROP TABLE subCategory_categories');
        $this->addSql('DROP TABLE testimonial');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transfer');
        $this->addSql('DROP TABLE transfer_transactions');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE superadministrator_members');
        $this->addSql('DROP TABLE manager_members');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
