<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260224083941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etats MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE etats DROP id, CHANGE id_etat no_etat INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (no_etat)');
        $this->addSql('DROP INDEX IDX_74E0281C3E7DD423 ON inscriptions');
        $this->addSql('DROP INDEX IDX_74E0281CBCB4ED33 ON inscriptions');
        $this->addSql('ALTER TABLE inscriptions MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE inscriptions ADD participants_no_participant INT NOT NULL, ADD sorties_no_sortie INT NOT NULL, DROP id, DROP no_participants_id, DROP no_sorties_id, DROP PRIMARY KEY, ADD PRIMARY KEY (participants_no_participant, sorties_no_sortie)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281CEF759E07 FOREIGN KEY (participants_no_participant) REFERENCES participants (no_participant)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281CC731F823 FOREIGN KEY (sorties_no_sortie) REFERENCES sorties (no_sortie)');
        $this->addSql('CREATE INDEX IDX_74E0281CEF759E07 ON inscriptions (participants_no_participant)');
        $this->addSql('CREATE INDEX IDX_74E0281CC731F823 ON inscriptions (sorties_no_sortie)');
        $this->addSql('DROP INDEX IDX_9E44A8AEFFE0C550 ON lieux');
        $this->addSql('ALTER TABLE lieux MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE lieux ADD no_lieu INT NOT NULL, ADD villes_no_ville INT NOT NULL, DROP id, DROP id_lieu, DROP no_villes_id, DROP PRIMARY KEY, ADD PRIMARY KEY (no_lieu)');
        $this->addSql('ALTER TABLE lieux ADD CONSTRAINT FK_9E44A8AE395FAFC3 FOREIGN KEY (villes_no_ville) REFERENCES villes (no_ville)');
        $this->addSql('CREATE INDEX IDX_9E44A8AE395FAFC3 ON lieux (villes_no_ville)');
        $this->addSql('DROP INDEX IDX_71697092F938E677 ON participants');
        $this->addSql('ALTER TABLE participants MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE participants ADD no_participant INT NOT NULL, ADD sites_no_site INT NOT NULL, DROP id, DROP id_participant, DROP no_sites_id, DROP PRIMARY KEY, ADD PRIMARY KEY (no_participant)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_7169709251C3F4BB FOREIGN KEY (sites_no_site) REFERENCES sites (no_site)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7169709286CC499D ON participants (pseudo)');
        $this->addSql('CREATE INDEX IDX_7169709251C3F4BB ON participants (sites_no_site)');
        $this->addSql('ALTER TABLE sites MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE sites DROP id, CHANGE id_site no_site INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (no_site)');
        $this->addSql('DROP INDEX IDX_488163E823C8044D ON sorties');
        $this->addSql('DROP INDEX IDX_488163E84B7E0ECF ON sorties');
        $this->addSql('ALTER TABLE sorties MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE sorties ADD no_sortie INT NOT NULL, ADD lieux_no_lieu INT NOT NULL, ADD etats_no_etat INT NOT NULL, DROP id, DROP id_sortie, DROP no_lieux_id, DROP no_etats_id, DROP PRIMARY KEY, ADD PRIMARY KEY (no_sortie)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E84E23F7D7 FOREIGN KEY (lieux_no_lieu) REFERENCES lieux (no_lieu)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8FCD21D77 FOREIGN KEY (etats_no_etat) REFERENCES etats (no_etat)');
        $this->addSql('CREATE INDEX IDX_488163E84E23F7D7 ON sorties (lieux_no_lieu)');
        $this->addSql('CREATE INDEX IDX_488163E8FCD21D77 ON sorties (etats_no_etat)');
        $this->addSql('ALTER TABLE villes MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE villes DROP id, CHANGE id_ville no_ville INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (no_ville)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etats ADD id INT AUTO_INCREMENT NOT NULL, CHANGE no_etat id_etat INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281CEF759E07');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281CC731F823');
        $this->addSql('DROP INDEX IDX_74E0281CEF759E07 ON inscriptions');
        $this->addSql('DROP INDEX IDX_74E0281CC731F823 ON inscriptions');
        $this->addSql('ALTER TABLE inscriptions ADD id INT AUTO_INCREMENT NOT NULL, ADD no_participants_id INT NOT NULL, ADD no_sorties_id INT NOT NULL, DROP participants_no_participant, DROP sorties_no_sortie, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX IDX_74E0281C3E7DD423 ON inscriptions (no_participants_id)');
        $this->addSql('CREATE INDEX IDX_74E0281CBCB4ED33 ON inscriptions (no_sorties_id)');
        $this->addSql('ALTER TABLE lieux DROP FOREIGN KEY FK_9E44A8AE395FAFC3');
        $this->addSql('DROP INDEX IDX_9E44A8AE395FAFC3 ON lieux');
        $this->addSql('ALTER TABLE lieux ADD id INT AUTO_INCREMENT NOT NULL, ADD id_lieu INT NOT NULL, ADD no_villes_id INT NOT NULL, DROP no_lieu, DROP villes_no_ville, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX IDX_9E44A8AEFFE0C550 ON lieux (no_villes_id)');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_7169709251C3F4BB');
        $this->addSql('DROP INDEX UNIQ_7169709286CC499D ON participants');
        $this->addSql('DROP INDEX IDX_7169709251C3F4BB ON participants');
        $this->addSql('ALTER TABLE participants ADD id INT AUTO_INCREMENT NOT NULL, ADD id_participant INT NOT NULL, ADD no_sites_id INT NOT NULL, DROP no_participant, DROP sites_no_site, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX IDX_71697092F938E677 ON participants (no_sites_id)');
        $this->addSql('ALTER TABLE sites ADD id INT AUTO_INCREMENT NOT NULL, CHANGE no_site id_site INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E84E23F7D7');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8FCD21D77');
        $this->addSql('DROP INDEX IDX_488163E84E23F7D7 ON sorties');
        $this->addSql('DROP INDEX IDX_488163E8FCD21D77 ON sorties');
        $this->addSql('ALTER TABLE sorties ADD id INT AUTO_INCREMENT NOT NULL, ADD id_sortie INT NOT NULL, ADD no_lieux_id INT NOT NULL, ADD no_etats_id INT NOT NULL, DROP no_sortie, DROP lieux_no_lieu, DROP etats_no_etat, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE INDEX IDX_488163E823C8044D ON sorties (no_lieux_id)');
        $this->addSql('CREATE INDEX IDX_488163E84B7E0ECF ON sorties (no_etats_id)');
        $this->addSql('ALTER TABLE villes ADD id INT AUTO_INCREMENT NOT NULL, CHANGE no_ville id_ville INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
