<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230528133545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD COLUMN job VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD COLUMN address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD COLUMN additional_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD COLUMN city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD COLUMN country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD COLUMN postal_code INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD COLUMN website VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD COLUMN comments VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, user_id, lastname, firstname, society, email, phone FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, society VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO client (id, user_id, lastname, firstname, society, email, phone) SELECT id, user_id, lastname, firstname, society, email, phone FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE INDEX IDX_C7440455A76ED395 ON client (user_id)');
    }
}
