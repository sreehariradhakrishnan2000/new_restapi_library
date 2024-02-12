<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212081030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create login table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "login" (
            id SERIAL PRIMARY KEY,
            username VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            roles JSON NOT NULL DEFAULT \'[]\' -- Default empty array for roles
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "login"');
    }
}
