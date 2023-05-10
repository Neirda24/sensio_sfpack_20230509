<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var list<array{title: string, releasedAt: string, plot: string, genres: list<string>, poster: string}>
     */
    private const MOVIES = [
        [
            'slug' => '2017-le-sens-de-la-fete',
            'title' => 'Le sens de la fête',
            'releasedAt' => '2017/10/04',
            'plot' => <<<'EOT'
                Max est traiteur depuis trente ans. Des fêtes il en a organisé des centaines, il est même un peu au bout du parcours. Aujourd'hui c'est un sublime mariage dans un château du 17ème siècle, un de plus, celui de Pierre et Héléna. Comme d'habitude, Max a tout coordonné : il a recruté sa brigade de serveurs, de cuisiniers, de plongeurs, il a conseillé un photographe, réservé l'orchestre, arrangé la décoration florale, bref tous les ingrédients sont réunis pour que cette fête soit réussie... Mais la loi des séries va venir bouleverser un planning sur le fil où chaque moment de bonheur et d'émotion risque de se transformer en désastre ou en chaos. Des préparatifs jusqu'à l'aube, nous allons vivre les coulisses de cette soirée à travers le regard de ceux qui travaillent et qui devront compter sur leur unique qualité commune : Le sens de la fête.
                EOT,
            'genres' => ['Comédie', 'Famille'],
            'poster' => '2017-le-sens-de-la-fete.webp',
        ],
    ];

    public function getDependencies(): array
    {
        return [
            GenreFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::MOVIES as $rawMovie) {
            $movie = (new Movie())
                ->setTitle($rawMovie['title'])
                ->setSlug($rawMovie['slug'])
                ->setPlot($rawMovie['plot'])
                ->setPoster($rawMovie['poster'])
                ->setReleasedAt(DateTimeImmutable::createFromFormat('!Y/m/d', $rawMovie['releasedAt']))
            ;

            foreach ($rawMovie['genres'] as $genreName) {
                $genre = $this->getReference("Genre.{$genreName}");
                $movie->addGenre($genre);
            }

            $manager->persist($movie);
        }

        $manager->flush();
    }
}
