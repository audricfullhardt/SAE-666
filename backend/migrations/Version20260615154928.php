<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260615154928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_player (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(100) NOT NULL, position INT DEFAULT 0 NOT NULL, turn_order INT NOT NULL, is_host TINYINT NOT NULL, is_ready TINYINT NOT NULL, game_token VARCHAR(255) NOT NULL, session_id INT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_E52CD7AD6D04D8B4 (game_token), INDEX IDX_E52CD7AD613FECDF (session_id), INDEX IDX_E52CD7ADA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game_result (id INT AUTO_INCREMENT NOT NULL, minigame_type VARCHAR(50) NOT NULL, played_at DATETIME NOT NULL, session_id INT NOT NULL, winner_id INT NOT NULL, loser_id INT NOT NULL, INDEX IDX_6E5F6CDB613FECDF (session_id), INDEX IDX_6E5F6CDB5DFCD4B8 (winner_id), INDEX IDX_6E5F6CDB1BCAA5F6 (loser_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE game_session (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(10) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, host_id INT NOT NULL, UNIQUE INDEX UNIQ_4586AAFB77153098 (code), INDEX IDX_4586AAFB1FB8D185 (host_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE minigame (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, question_data JSON DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_7E8585C8E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE outlet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, zip_code VARCHAR(10) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, phone VARCHAR(20) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, is_active TINYINT DEFAULT 1 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(100) NOT NULL, roles JSON NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7AD613FECDF FOREIGN KEY (session_id) REFERENCES game_session (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE game_result ADD CONSTRAINT FK_6E5F6CDB613FECDF FOREIGN KEY (session_id) REFERENCES game_session (id)');
        $this->addSql('ALTER TABLE game_result ADD CONSTRAINT FK_6E5F6CDB5DFCD4B8 FOREIGN KEY (winner_id) REFERENCES game_player (id)');
        $this->addSql('ALTER TABLE game_result ADD CONSTRAINT FK_6E5F6CDB1BCAA5F6 FOREIGN KEY (loser_id) REFERENCES game_player (id)');
        $this->addSql('ALTER TABLE game_session ADD CONSTRAINT FK_4586AAFB1FB8D185 FOREIGN KEY (host_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7AD613FECDF');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADA76ED395');
        $this->addSql('ALTER TABLE game_result DROP FOREIGN KEY FK_6E5F6CDB613FECDF');
        $this->addSql('ALTER TABLE game_result DROP FOREIGN KEY FK_6E5F6CDB5DFCD4B8');
        $this->addSql('ALTER TABLE game_result DROP FOREIGN KEY FK_6E5F6CDB1BCAA5F6');
        $this->addSql('ALTER TABLE game_session DROP FOREIGN KEY FK_4586AAFB1FB8D185');
        $this->addSql('DROP TABLE game_player');
        $this->addSql('DROP TABLE game_result');
        $this->addSql('DROP TABLE game_session');
        $this->addSql('DROP TABLE minigame');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE outlet');
        $this->addSql('DROP TABLE `user`');
    }
}
