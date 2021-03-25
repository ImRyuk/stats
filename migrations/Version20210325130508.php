<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325130508 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `sources` (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, text TEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE types ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE types ADD CONSTRAINT FK_98F1A634953C1C61 FOREIGN KEY (source_id) REFERENCES `sources` (id)');
        $this->addSql('CREATE INDEX IDX_98F1A634953C1C61 ON types (source_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Types DROP FOREIGN KEY FK_98F1A634953C1C61');
        $this->addSql('DROP TABLE `sources`');
        $this->addSql('DROP INDEX IDX_98F1A634953C1C61 ON Types');
        $this->addSql('ALTER TABLE Types DROP source_id');
    }
}
