<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260517072458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates full-text index on news';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE FULLTEXT INDEX IDX_1DD399502B36786BCE286663B548D29F ON news (title, summary, published_by)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IDX_1DD399502B36786BCE286663B548D29F ON news');
    }
}
