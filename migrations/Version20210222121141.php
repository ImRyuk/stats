<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210222121141 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stat_value ADD departement_id INT NOT NULL');
        $this->addSql('ALTER TABLE stat_value ADD CONSTRAINT FK_71508522CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('CREATE INDEX IDX_71508522CCF9E01E ON stat_value (departement_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stat_value DROP FOREIGN KEY FK_71508522CCF9E01E');
        $this->addSql('DROP INDEX IDX_71508522CCF9E01E ON stat_value');
        $this->addSql('ALTER TABLE stat_value DROP departement_id');
    }
}
