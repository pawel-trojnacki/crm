<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211121165813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4FBF094F61220EA6 ON company (creator_id)');
        $this->addSql('ALTER TABLE contact ADD creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63861220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4C62E63861220EA6 ON contact (creator_id)');
        $this->addSql('ALTER TABLE contact_note ADD creator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_note ADD CONSTRAINT FK_E74278EB61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_E74278EB61220EA6 ON contact_note (creator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F61220EA6');
        $this->addSql('DROP INDEX IDX_4FBF094F61220EA6 ON company');
        $this->addSql('ALTER TABLE company DROP creator_id');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63861220EA6');
        $this->addSql('DROP INDEX IDX_4C62E63861220EA6 ON contact');
        $this->addSql('ALTER TABLE contact DROP creator_id');
        $this->addSql('ALTER TABLE contact_note DROP FOREIGN KEY FK_E74278EB61220EA6');
        $this->addSql('DROP INDEX IDX_E74278EB61220EA6 ON contact_note');
        $this->addSql('ALTER TABLE contact_note DROP creator_id');
    }
}
