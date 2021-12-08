<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208100138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id VARCHAR(255) NOT NULL, workspace_id VARCHAR(255) NOT NULL, creator_id VARCHAR(255) DEFAULT NULL, industry_id INT DEFAULT NULL, country_id INT DEFAULT NULL, name VARCHAR(80) NOT NULL, slug VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(80) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4FBF094F82D40A1F (workspace_id), INDEX IDX_4FBF094F61220EA6 (creator_id), INDEX IDX_4FBF094F2B19A734 (industry_id), INDEX IDX_4FBF094FF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id VARCHAR(255) NOT NULL, workspace_id VARCHAR(255) NOT NULL, creator_id VARCHAR(255) DEFAULT NULL, company_id VARCHAR(255) DEFAULT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(30) NOT NULL, slug VARCHAR(255) NOT NULL, email VARCHAR(80) NOT NULL, phone VARCHAR(20) NOT NULL, position VARCHAR(30) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_4C62E638989D9B62 (slug), INDEX IDX_4C62E63882D40A1F (workspace_id), INDEX IDX_4C62E63861220EA6 (creator_id), INDEX IDX_4C62E638979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_note (id VARCHAR(255) NOT NULL, parent_id VARCHAR(255) NOT NULL, creator_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_E74278EB727ACA70 (parent_id), INDEX IDX_E74278EB61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(80) NOT NULL, iso_code VARCHAR(2) NOT NULL, isd_code VARCHAR(7) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deal (id INT AUTO_INCREMENT NOT NULL, creator_id VARCHAR(255) DEFAULT NULL, company_id VARCHAR(255) NOT NULL, workspace_id VARCHAR(255) NOT NULL, name VARCHAR(80) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, stage VARCHAR(25) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_E3FEC116989D9B62 (slug), INDEX IDX_E3FEC11661220EA6 (creator_id), INDEX IDX_E3FEC116979B1AD6 (company_id), INDEX IDX_E3FEC11682D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deal_user (deal_id INT NOT NULL, user_id VARCHAR(255) NOT NULL, INDEX IDX_3BEEB3E6F60E2305 (deal_id), INDEX IDX_3BEEB3E6A76ED395 (user_id), PRIMARY KEY(deal_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deal_note (id VARCHAR(255) NOT NULL, parent_id INT NOT NULL, creator_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_79C09FBB727ACA70 (parent_id), INDEX IDX_79C09FBB61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE industry (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting (id VARCHAR(255) NOT NULL, workspace_id VARCHAR(255) NOT NULL, name VARCHAR(50) NOT NULL, slug VARCHAR(255) NOT NULL, begin_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, INDEX IDX_F515E13982D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id VARCHAR(255) NOT NULL, workspace_id VARCHAR(255) NOT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(30) NOT NULL, slug VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649989D9B62 (slug), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64982D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workspace (id VARCHAR(255) NOT NULL, name VARCHAR(30) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D940019989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F82D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F2B19A734 FOREIGN KEY (industry_id) REFERENCES industry (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63882D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63861220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE contact_note ADD CONSTRAINT FK_E74278EB727ACA70 FOREIGN KEY (parent_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_note ADD CONSTRAINT FK_E74278EB61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC11661220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC116979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC11682D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal_user ADD CONSTRAINT FK_3BEEB3E6F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal_user ADD CONSTRAINT FK_3BEEB3E6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal_note ADD CONSTRAINT FK_79C09FBB727ACA70 FOREIGN KEY (parent_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal_note ADD CONSTRAINT FK_79C09FBB61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E13982D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64982D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638979B1AD6');
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC116979B1AD6');
        $this->addSql('ALTER TABLE contact_note DROP FOREIGN KEY FK_E74278EB727ACA70');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FF92F3E70');
        $this->addSql('ALTER TABLE deal_user DROP FOREIGN KEY FK_3BEEB3E6F60E2305');
        $this->addSql('ALTER TABLE deal_note DROP FOREIGN KEY FK_79C09FBB727ACA70');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F2B19A734');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F61220EA6');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63861220EA6');
        $this->addSql('ALTER TABLE contact_note DROP FOREIGN KEY FK_E74278EB61220EA6');
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC11661220EA6');
        $this->addSql('ALTER TABLE deal_user DROP FOREIGN KEY FK_3BEEB3E6A76ED395');
        $this->addSql('ALTER TABLE deal_note DROP FOREIGN KEY FK_79C09FBB61220EA6');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F82D40A1F');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63882D40A1F');
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC11682D40A1F');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E13982D40A1F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64982D40A1F');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE contact_note');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE deal');
        $this->addSql('DROP TABLE deal_user');
        $this->addSql('DROP TABLE deal_note');
        $this->addSql('DROP TABLE industry');
        $this->addSql('DROP TABLE meeting');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE workspace');
    }
}
