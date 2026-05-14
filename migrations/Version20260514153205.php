<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260514153205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates news and messenger_messages tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, summary VARCHAR(512) NOT NULL, content VARCHAR(4096) NOT NULL, published_by VARCHAR(255) NOT NULL, published_id VARCHAR(255) NOT NULL, published_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1DD3995043625D9F (updated_at), UNIQUE INDEX UNIQ_1DD39950B548D29F35BA678D (published_by, published_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
