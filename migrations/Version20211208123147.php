<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208123147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE deal CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE deal_user CHANGE deal_id deal_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE deal_note CHANGE parent_id parent_id VARCHAR(255) NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE deal CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE deal_note CHANGE parent_id parent_id INT NOT NULL');
        $this->addSql('ALTER TABLE deal_user CHANGE deal_id deal_id INT NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
