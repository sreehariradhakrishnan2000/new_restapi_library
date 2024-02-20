<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240219144144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user table and insert initial data';
    }

    public function up(Schema $schema): void
    {
        // Create the user table
        $this->addSql('CREATE TABLE user (
            id SERIAL PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL
        )');

        // Insert initial user data
        $email = 'sree@gmail.com';
        $roles = json_encode(['ROLE_ADMIN']);
        $password = password_hash('password', PASSWORD_DEFAULT);

        $this->addSql('INSERT INTO user (email, roles, password) VALUES (?, ?, ?)', [
            $email,
            $roles,
            $password,
        ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user');
    }
}
