<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210222131818 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stat_value DROP FOREIGN KEY FK_71508522CCF9E01E');
        $this->addSql('ALTER TABLE departement DROP FOREIGN KEY FK_C1765B6398260155');
        $this->addSql('ALTER TABLE stat_value DROP FOREIGN KEY FK_71508522C54C8C93');
        $this->addSql('CREATE TABLE Departements (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(5) DEFAULT NULL, old_region VARCHAR(255) DEFAULT NULL, INDEX IDX_D745524A98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Stats (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, departement_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_968648AEC54C8C93 (type_id), INDEX IDX_968648AECCF9E01E (departement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Types (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Departements ADD CONSTRAINT FK_D745524A98260155 FOREIGN KEY (region_id) REFERENCES Regions (id)');
        $this->addSql('ALTER TABLE Stats ADD CONSTRAINT FK_968648AEC54C8C93 FOREIGN KEY (type_id) REFERENCES Types (id)');
        $this->addSql('ALTER TABLE Stats ADD CONSTRAINT FK_968648AECCF9E01E FOREIGN KEY (departement_id) REFERENCES Departements (id)');
        $this->addSql('DROP TABLE departement');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE stat_value');
        $this->addSql('DROP TABLE type');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Stats DROP FOREIGN KEY FK_968648AECCF9E01E');
        $this->addSql('ALTER TABLE Departements DROP FOREIGN KEY FK_D745524A98260155');
        $this->addSql('ALTER TABLE Stats DROP FOREIGN KEY FK_968648AEC54C8C93');
        $this->addSql('CREATE TABLE departement (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, old_region VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_C1765B6398260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE stat_value (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, departement_id INT NOT NULL, value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_71508522CCF9E01E (departement_id), INDEX IDX_71508522C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE departement ADD CONSTRAINT FK_C1765B6398260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE stat_value ADD CONSTRAINT FK_71508522C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE stat_value ADD CONSTRAINT FK_71508522CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('DROP TABLE Departements');
        $this->addSql('DROP TABLE Regions');
        $this->addSql('DROP TABLE Stats');
        $this->addSql('DROP TABLE Types');
    }
}
