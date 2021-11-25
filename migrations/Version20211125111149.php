<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125111149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deal ADD workspace_id INT NOT NULL');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC11682D40A1F FOREIGN KEY (workspace_id) REFERENCES workspace (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E3FEC11682D40A1F ON deal (workspace_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC11682D40A1F');
        $this->addSql('DROP INDEX IDX_E3FEC11682D40A1F ON deal');
        $this->addSql('ALTER TABLE deal DROP workspace_id');
    }
}
