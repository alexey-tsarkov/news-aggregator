<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260514171129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Expands news content column';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE news CHANGE content content LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE news CHANGE content content VARCHAR(4096) NOT NULL');
    }
}
