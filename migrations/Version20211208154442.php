<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208154442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting ADD contact_id VARCHAR(255) DEFAULT NULL, ADD importance VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E139E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F515E139E7A1254A ON meeting (contact_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E139E7A1254A');
        $this->addSql('DROP INDEX IDX_F515E139E7A1254A ON meeting');
        $this->addSql('ALTER TABLE meeting DROP contact_id, DROP importance');
    }
}
