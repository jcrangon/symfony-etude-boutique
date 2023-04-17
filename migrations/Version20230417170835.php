<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230417170835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membre CHANGE civilite civilite ENUM(\'m\', \'f\') NOT NULL DEFAULT \'m\'');
        $this->addSql('ALTER TABLE produit CHANGE sexe sexe enum(\'m\', \'f\') NOT NULL DEFAULT \'m\' COLLATE \'utf8mb4_general_ci\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE membre CHANGE civilite civilite VARCHAR(255) DEFAULT \'m\' NOT NULL');
        $this->addSql('ALTER TABLE produit CHANGE sexe sexe VARCHAR(255) DEFAULT \'m\' NOT NULL COLLATE `utf8mb4_general_ci`');
    }
}
