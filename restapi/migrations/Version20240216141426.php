<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216141426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert initial data into existing login table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        // Insert initial user data
        $email = 'admin@example.com';
        $roles = json_encode(['ROLE_ADMIN']);
        $password = password_hash('password', PASSWORD_DEFAULT);

        $this->addSql('INSERT INTO login (email, roles, password) VALUES (:email, :roles, :password)', [
            'email' => $email,
            'roles' => $roles,
            'password' => $password,
        ]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // You can add down migration if needed, e.g., deleting the inserted data
    }
}