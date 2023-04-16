<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230416122747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier_detail (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, panier_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_9BFF9D8DF347EFB (produit_id), INDEX IDX_9BFF9D8DF77D927C (panier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE panier_detail ADD CONSTRAINT FK_9BFF9D8DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE panier_detail ADD CONSTRAINT FK_9BFF9D8DF77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A6F347EFB');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A6F77D927C');
        $this->addSql('DROP TABLE panier_produit');
        $this->addSql('ALTER TABLE membre CHANGE civilite civilite ENUM(\'m\', \'f\') NOT NULL DEFAULT \'m\'');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF27597D3FE');
        $this->addSql('DROP INDEX UNIQ_24CC0DF27597D3FE ON panier');
        $this->addSql('ALTER TABLE panier ADD membre VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP member_id');
        $this->addSql('ALTER TABLE produit CHANGE sexe sexe enum(\'m\', \'f\') NOT NULL DEFAULT \'m\' COLLATE \'utf8mb4_general_ci\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE panier_produit (panier_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_D31F28A6F77D927C (panier_id), INDEX IDX_D31F28A6F347EFB (produit_id), PRIMARY KEY(panier_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A6F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE panier_detail DROP FOREIGN KEY FK_9BFF9D8DF347EFB');
        $this->addSql('ALTER TABLE panier_detail DROP FOREIGN KEY FK_9BFF9D8DF77D927C');
        $this->addSql('DROP TABLE panier_detail');
        $this->addSql('ALTER TABLE membre CHANGE civilite civilite VARCHAR(255) DEFAULT \'m\' NOT NULL');
        $this->addSql('ALTER TABLE panier ADD member_id INT DEFAULT NULL, DROP membre, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF27597D3FE FOREIGN KEY (member_id) REFERENCES membre (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF27597D3FE ON panier (member_id)');
        $this->addSql('ALTER TABLE produit CHANGE sexe sexe VARCHAR(255) DEFAULT \'m\' NOT NULL COLLATE `utf8mb4_general_ci`');
    }
}
