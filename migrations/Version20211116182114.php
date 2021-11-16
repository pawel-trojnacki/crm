<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211116182114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F2B19A734');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F2B19A734 FOREIGN KEY (industry_id) REFERENCES industry (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F2B19A734');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F2B19A734 FOREIGN KEY (industry_id) REFERENCES industry (id)');
    }
}
