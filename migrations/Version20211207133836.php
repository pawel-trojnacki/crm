<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207133836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE contact_note DROP FOREIGN KEY FK_E74278EBE7A1254A');
        $this->addSql('DROP INDEX IDX_E74278EBE7A1254A ON contact_note');
        $this->addSql('ALTER TABLE contact_note CHANGE id id VARCHAR(255) NOT NULL, CHANGE contact_id parent_id INT NOT NULL');
        $this->addSql('ALTER TABLE contact_note ADD CONSTRAINT FK_E74278EB727ACA70 FOREIGN KEY (parent_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E74278EB727ACA70 ON contact_note (parent_id)');
        $this->addSql('ALTER TABLE deal_note DROP FOREIGN KEY FK_79C09FBBF60E2305');
        $this->addSql('DROP INDEX IDX_79C09FBBF60E2305 ON deal_note');
        $this->addSql('ALTER TABLE deal_note CHANGE id id VARCHAR(255) NOT NULL, CHANGE deal_id parent_id INT NOT NULL');
        $this->addSql('ALTER TABLE deal_note ADD CONSTRAINT FK_79C09FBB727ACA70 FOREIGN KEY (parent_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_79C09FBB727ACA70 ON deal_note (parent_id)');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('ALTER TABLE contact_note DROP FOREIGN KEY FK_E74278EB727ACA70');
        $this->addSql('DROP INDEX IDX_E74278EB727ACA70 ON contact_note');
        $this->addSql('ALTER TABLE contact_note CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE parent_id contact_id INT NOT NULL');
        $this->addSql('ALTER TABLE contact_note ADD CONSTRAINT FK_E74278EBE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E74278EBE7A1254A ON contact_note (contact_id)');
        $this->addSql('ALTER TABLE deal_note DROP FOREIGN KEY FK_79C09FBB727ACA70');
        $this->addSql('DROP INDEX IDX_79C09FBB727ACA70 ON deal_note');
        $this->addSql('ALTER TABLE deal_note CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE parent_id deal_id INT NOT NULL');
        $this->addSql('ALTER TABLE deal_note ADD CONSTRAINT FK_79C09FBBF60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_79C09FBBF60E2305 ON deal_note (deal_id)');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
