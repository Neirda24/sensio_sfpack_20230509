<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use LogicException;
use function array_column;
use function array_map;

final readonly class MovieRepository
{
    /**
     * @var list<array{title: string, releasedAt: string, plot: string, genres: list<string>}>
     */
    private const MOVIES = [
        [
            'slug' => '2017-le-sens-de-la-fete',
            'title' => 'Le sens de la fête',
            'releasedAt' => '2017/10/04',
            'plot' => <<<'EOT'
                Max est traiteur depuis trente ans. Des fêtes il en a organisé des centaines, il est même un peu au bout du parcours. Aujourd'hui c'est un sublime mariage dans un château du 17ème siècle, un de plus, celui de Pierre et Héléna. Comme d'habitude, Max a tout coordonné : il a recruté sa brigade de serveurs, de cuisiniers, de plongeurs, il a conseillé un photographe, réservé l'orchestre, arrangé la décoration florale, bref tous les ingrédients sont réunis pour que cette fête soit réussie... Mais la loi des séries va venir bouleverser un planning sur le fil où chaque moment de bonheur et d'émotion risque de se transformer en désastre ou en chaos. Des préparatifs jusqu'à l'aube, nous allons vivre les coulisses de cette soirée à travers le regard de ceux qui travaillent et qui devront compter sur leur unique qualité commune : Le sens de la fête.
                EOT,
            'genres' => ['Comedie', 'Famille']
        ],
    ];

    private static function convertRawToModel(array $rawMovie): Movie
    {
        return new Movie(
            $rawMovie['title'],
            DateTimeImmutable::createFromFormat('!Y/m/d', $rawMovie['releasedAt']),
            $rawMovie['plot'],
            $rawMovie['genres'],
            $rawMovie['slug'],
        );
    }

    /**
     * @return list<Movie>
     */
    public static function listAll(): array
    {
        return array_map(self::convertRawToModel(...), self::MOVIES);
    }

    public static function getBySlug(string $movieSlug): Movie
    {
        $indexedBySlug = array_column(self::MOVIES, null, 'slug');

        $rawMovie = $indexedBySlug[$movieSlug] ?? throw new LogicException('Movie slug not found');

        return self::convertRawToModel($rawMovie);
    }
}
