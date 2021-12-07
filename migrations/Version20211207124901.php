<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207124901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE company CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE contact CHANGE company_id company_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE deal CHANGE company_id company_id VARCHAR(255) NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE company CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE contact CHANGE company_id company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deal CHANGE company_id company_id INT NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
