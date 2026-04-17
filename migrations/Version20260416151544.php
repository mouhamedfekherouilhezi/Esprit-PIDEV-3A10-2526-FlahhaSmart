<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416151544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire (id_commentaire INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, modere_ia TINYINT(1) DEFAULT 0 NOT NULL, flagge TINYINT(1) DEFAULT 0 NOT NULL, id_user INT DEFAULT NULL, id_thread INT DEFAULT NULL, INDEX IDX_67F068BC6B3CA4B (id_user), INDEX IDX_67F068BC1717F96E (id_thread), PRIMARY KEY(id_commentaire)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE thread (id_thread INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, id_user INT DEFAULT NULL, INDEX IDX_31204C836B3CA4B (id_user), PRIMARY KEY(id_thread)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, pseudo VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, reputation INT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC6B3CA4B FOREIGN KEY (id_user) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC1717F96E FOREIGN KEY (id_thread) REFERENCES thread (idThread)');
        $this->addSql('ALTER TABLE thread ADD CONSTRAINT FK_31204C836B3CA4B FOREIGN KEY (id_user) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE threads DROP FOREIGN KEY fk_thread_user');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE threads');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE jaime CHANGE date_jaime date_jaime DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE reputation CHANGE badge badge VARCHAR(20) DEFAULT \'🌱 Débutant\' NOT NULL');
        $this->addSql('ALTER TABLE reputation RENAME INDEX unique_user TO UNIQ_CA177A5D6B3CA4B');
        $this->addSql('ALTER TABLE votes DROP FOREIGN KEY votes_ibfk_2');
        $this->addSql('ALTER TABLE votes DROP FOREIGN KEY votes_ibfk_1');
        $this->addSql('DROP INDEX unique_vote ON votes');
        $this->addSql('DROP INDEX votes_ibfk_2 ON votes');
        $this->addSql('DROP INDEX IDX_518B7ACF1717F96E ON votes');
        $this->addSql('ALTER TABLE votes CHANGE date_vote date_vote DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaires (id_commentaire INT AUTO_INCREMENT NOT NULL, id_thread INT NOT NULL, id_user INT NOT NULL, contenu LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME NOT NULL, statut VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'\'\'actif\'\'\' NOT NULL COLLATE `utf8mb4_general_ci`, sentiment VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'\'\'neutre\'\'\' NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(id_commentaire)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE threads (id_thread INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, titre VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, contenu TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME DEFAULT \'current_timestamp()\' NOT NULL, date_update DATETIME DEFAULT \'current_timestamp()\' NOT NULL, statut VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'\'\'actif\'\'\' COLLATE `utf8mb4_general_ci`, sentiment VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'\'\'neutre\'\'\' COLLATE `utf8mb4_general_ci`, tags VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'\'\'\'\'\' COLLATE `utf8mb4_general_ci`, INDEX fk_thread_user (id_user), PRIMARY KEY(id_thread)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE users (id_user INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, email VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, telephone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, ville VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, photo_profil VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, role ENUM(\'ADMINISTRATEUR\', \'AGRICULTEUR\', \'CLIENT\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, actif TINYINT(1) DEFAULT 1, date_creation DATETIME DEFAULT \'current_timestamp()\' NOT NULL, UNIQUE INDEX email (email), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE threads ADD CONSTRAINT fk_thread_user FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC6B3CA4B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC1717F96E');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C836B3CA4B');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE thread');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('ALTER TABLE jaime CHANGE date_jaime date_jaime DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE reputation CHANGE badge badge VARCHAR(20) DEFAULT \'\'\'? Débutant\'\'\' NOT NULL');
        $this->addSql('ALTER TABLE reputation RENAME INDEX uniq_ca177a5d6b3ca4b TO unique_user');
        $this->addSql('ALTER TABLE votes CHANGE date_vote date_vote DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('ALTER TABLE votes ADD CONSTRAINT votes_ibfk_2 FOREIGN KEY (id_user) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE votes ADD CONSTRAINT votes_ibfk_1 FOREIGN KEY (id_thread) REFERENCES threads (id_thread) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX unique_vote ON votes (id_thread, id_user)');
        $this->addSql('CREATE INDEX votes_ibfk_2 ON votes (id_user)');
        $this->addSql('CREATE INDEX IDX_518B7ACF1717F96E ON votes (id_thread)');
    }
}
