<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511140653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__estimate AS SELECT id, client_id, created_at, amount, state FROM estimate');
        $this->addSql('DROP TABLE estimate');
        $this->addSql('CREATE TABLE estimate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , amount DOUBLE PRECISION NOT NULL, state VARCHAR(255) NOT NULL, CONSTRAINT FK_D2EA460719EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2EA4607A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO estimate (id, client_id, created_at, amount, state) SELECT id, client_id, created_at, amount, state FROM __temp__estimate');
        $this->addSql('DROP TABLE __temp__estimate');
        $this->addSql('CREATE INDEX IDX_D2EA460719EB6921 ON estimate (client_id)');
        $this->addSql('CREATE INDEX IDX_D2EA4607A76ED395 ON estimate (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__estimate AS SELECT id, client_id, created_at, amount, state FROM estimate');
        $this->addSql('DROP TABLE estimate');
        $this->addSql('CREATE TABLE estimate (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , amount DOUBLE PRECISION NOT NULL, state VARCHAR(255) NOT NULL, CONSTRAINT FK_D2EA460719EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO estimate (id, client_id, created_at, amount, state) SELECT id, client_id, created_at, amount, state FROM __temp__estimate');
        $this->addSql('DROP TABLE __temp__estimate');
        $this->addSql('CREATE INDEX IDX_D2EA460719EB6921 ON estimate (client_id)');
    }
}
