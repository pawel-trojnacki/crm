<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211210073805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE meeting ADD creator_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E13961220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F515E13961220EA6 ON meeting (creator_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E13961220EA6');
        $this->addSql('DROP INDEX IDX_F515E13961220EA6 ON meeting');
        $this->addSql('ALTER TABLE meeting DROP creator_id');
    }
}
