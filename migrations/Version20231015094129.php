<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231015094129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE certificats (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, numero INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD ordre INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC256483C FOREIGN KEY (marques_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE0EA5904 FOREIGN KEY (statuts_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE product_categorie ADD CONSTRAINT FK_27DD60B94584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_categorie ADD CONSTRAINT FK_27DD60B9BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64938C751C4 FOREIGN KEY (roles_id) REFERENCES role (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_categorie DROP FOREIGN KEY FK_27DD60B9BCF5E72D');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADC256483C');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE0EA5904');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE certificats');
        $this->addSql('DROP TABLE marque');
        $this->addSql('DROP TABLE statut');
        $this->addSql('ALTER TABLE product_categorie DROP FOREIGN KEY FK_27DD60B94584665A');
        $this->addSql('ALTER TABLE product DROP ordre');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64938C751C4');
    }
}
