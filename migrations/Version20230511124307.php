<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511124307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Movie] Create a unique index on movie slugs.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX movie_slug ON movie (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, title, slug, plot, poster, released_at, rated FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, plot CLOB NOT NULL, poster VARCHAR(255) NOT NULL, released_at DATETIME NOT NULL --(DC2Type:datetimetz_immutable)
        , rated VARCHAR(8) DEFAULT \'G\' NOT NULL)');
        $this->addSql('INSERT INTO movie (id, title, slug, plot, poster, released_at, rated) SELECT id, title, slug, plot, poster, released_at, rated FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
    }
}
