<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240626152011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create event_user table and add necessary foreign keys';
    }

    public function up(Schema $schema): void
    {
        // Create event_user table
        $this->addSql('CREATE TABLE event_user (
            event_id INT NOT NULL,
            user_id INT NOT NULL,
            INDEX IDX_92589AE271F7E88B (event_id),
            INDEX IDX_92589AE2A76ED395 (user_id),
            PRIMARY KEY(event_id, user_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Drop event_user table
        $this->addSql('DROP TABLE event_user');
    }
}
