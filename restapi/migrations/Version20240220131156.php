<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240220131156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if the table exists before proceeding
        if (!$schema->hasTable('user')) {
            // Create the user table
            $this->addSql('CREATE TABLE "user" (
                id SERIAL PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) NOT NULL
            )');

            // Insert initial user data
            $users = [
                [
                    'email' => 'sree@gmail.com',
                    'roles' => json_encode(['ROLE_ADMIN']),
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                ],
                [
                    'email' => 'hari@gmail.com',
                    'roles' => json_encode(['ROLE_USER']),
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                ],
                // Add more users as needed
            ];

            foreach ($users as $userData) {
                $this->addSql('INSERT INTO "user" (email, roles, password) VALUES (:email, :roles, :password)', [
                    'email' => $userData['email'],
                    'roles' => $userData['roles'],
                    'password' => $userData['password'],
                ]);
            }
        }
    }
    
    public function down(Schema $schema): void
    {
        // If you want to drop the table in the down migration, you can add the drop table SQL here.
        // $this->addSql('DROP TABLE IF EXISTS "user"');
    }
}
