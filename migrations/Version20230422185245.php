<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422185245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transporteur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL COLLATE `utf8mb4_general_ci`, prix DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE membre CHANGE civilite civilite ENUM(\'m\', \'f\') NOT NULL DEFAULT \'m\'');
        $this->addSql('ALTER TABLE produit CHANGE sexe sexe enum(\'m\', \'f\') NOT NULL DEFAULT \'m\' COLLATE \'utf8mb4_general_ci\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE transporteur');
        $this->addSql('ALTER TABLE membre CHANGE civilite civilite VARCHAR(255) DEFAULT \'m\' NOT NULL');
        $this->addSql('ALTER TABLE produit CHANGE sexe sexe VARCHAR(255) DEFAULT \'m\' NOT NULL COLLATE `utf8mb4_general_ci`');
    }
}
