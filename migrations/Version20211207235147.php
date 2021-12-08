<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207235147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE company CHANGE creator_id creator_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE contact CHANGE creator_id creator_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_note CHANGE creator_id creator_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE deal CHANGE creator_id creator_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE deal_user CHANGE user_id user_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE deal_note CHANGE creator_id creator_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE company CHANGE creator_id creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact CHANGE creator_id creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_note CHANGE creator_id creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deal CHANGE creator_id creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deal_note CHANGE creator_id creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deal_user CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
