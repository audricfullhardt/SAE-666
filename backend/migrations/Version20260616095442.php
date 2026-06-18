<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260616095442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE active_minigame (id INT AUTO_INCREMENT NOT NULL, minigame_type VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, scanned_at DATETIME NOT NULL, case_data JSON DEFAULT NULL, session_id INT NOT NULL, challenger_id INT NOT NULL, opponent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_6EDD876C613FECDF (session_id), INDEX IDX_6EDD876C2D521FDF (challenger_id), INDEX IDX_6EDD876C7F656CDC (opponent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE active_minigame ADD CONSTRAINT FK_6EDD876C613FECDF FOREIGN KEY (session_id) REFERENCES game_session (id)');
        $this->addSql('ALTER TABLE active_minigame ADD CONSTRAINT FK_6EDD876C2D521FDF FOREIGN KEY (challenger_id) REFERENCES game_player (id)');
        $this->addSql('ALTER TABLE active_minigame ADD CONSTRAINT FK_6EDD876C7F656CDC FOREIGN KEY (opponent_id) REFERENCES game_player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE active_minigame DROP FOREIGN KEY FK_6EDD876C613FECDF');
        $this->addSql('ALTER TABLE active_minigame DROP FOREIGN KEY FK_6EDD876C2D521FDF');
        $this->addSql('ALTER TABLE active_minigame DROP FOREIGN KEY FK_6EDD876C7F656CDC');
        $this->addSql('DROP TABLE active_minigame');
    }
}
