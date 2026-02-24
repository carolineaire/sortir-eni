<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223143613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etats (id INT AUTO_INCREMENT NOT NULL, id_etat INT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE inscriptions (id INT AUTO_INCREMENT NOT NULL, date_inscription DATETIME NOT NULL, no_participants_id INT NOT NULL, no_sorties_id INT NOT NULL, INDEX IDX_74E0281C3E7DD423 (no_participants_id), INDEX IDX_74E0281CBCB4ED33 (no_sorties_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lieux (id INT AUTO_INCREMENT NOT NULL, id_lieu INT NOT NULL, nom_lieu VARCHAR(30) NOT NULL, rue VARCHAR(30) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, no_villes_id INT NOT NULL, INDEX IDX_9E44A8AEFFE0C550 (no_villes_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE participants (id INT AUTO_INCREMENT NOT NULL, id_participant INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, telephone VARCHAR(10) DEFAULT NULL, mail VARCHAR(255) NOT NULL, administrateur TINYINT DEFAULT NULL, actif TINYINT DEFAULT NULL, mot_de_passe VARCHAR(20) NOT NULL, pseudo VARCHAR(30) NOT NULL, no_sites_id INT NOT NULL, INDEX IDX_71697092F938E677 (no_sites_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sites (id INT AUTO_INCREMENT NOT NULL, id_site INT NOT NULL, nom_site VARCHAR(30) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sorties (id INT AUTO_INCREMENT NOT NULL, id_sortie INT NOT NULL, nom VARCHAR(30) NOT NULL, date_debut DATETIME NOT NULL, duree INT DEFAULT NULL, date_cloture DATETIME NOT NULL, nb_inscription_max INT NOT NULL, description LONGTEXT DEFAULT NULL, etat_sortie INT DEFAULT NULL, url_photo VARCHAR(255) DEFAULT NULL, organisateur INT NOT NULL, no_lieux_id INT NOT NULL, no_etats_id INT NOT NULL, INDEX IDX_488163E823C8044D (no_lieux_id), INDEX IDX_488163E84B7E0ECF (no_etats_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE villes (id INT AUTO_INCREMENT NOT NULL, id_ville INT NOT NULL, nom_ville VARCHAR(30) NOT NULL, cpo VARCHAR(10) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C3E7DD423 FOREIGN KEY (no_participants_id) REFERENCES participants (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281CBCB4ED33 FOREIGN KEY (no_sorties_id) REFERENCES sorties (id)');
        $this->addSql('ALTER TABLE lieux ADD CONSTRAINT FK_9E44A8AEFFE0C550 FOREIGN KEY (no_villes_id) REFERENCES villes (id)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_71697092F938E677 FOREIGN KEY (no_sites_id) REFERENCES sites (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E823C8044D FOREIGN KEY (no_lieux_id) REFERENCES lieux (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E84B7E0ECF FOREIGN KEY (no_etats_id) REFERENCES etats (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C3E7DD423');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281CBCB4ED33');
        $this->addSql('ALTER TABLE lieux DROP FOREIGN KEY FK_9E44A8AEFFE0C550');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_71697092F938E677');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E823C8044D');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E84B7E0ECF');
        $this->addSql('DROP TABLE etats');
        $this->addSql('DROP TABLE inscriptions');
        $this->addSql('DROP TABLE lieux');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE sorties');
        $this->addSql('DROP TABLE villes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
