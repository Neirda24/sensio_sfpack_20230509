<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use App\Model\Rated;
use App\Repository\MovieRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var list<array{title: string, releasedAt: string, plot: string, genres: list<string>, poster: string, rated: Rated}>
     */
    private const MOVIES = [
        [
            'title' => 'Le sens de la fête',
            'releasedAt' => '2017/10/04',
            'plot' => <<<'EOT'
                Max est traiteur depuis trente ans. Des fêtes il en a organisé des centaines, il est même un peu au bout du parcours. Aujourd'hui c'est un sublime mariage dans un château du 17ème siècle, un de plus, celui de Pierre et Héléna. Comme d'habitude, Max a tout coordonné : il a recruté sa brigade de serveurs, de cuisiniers, de plongeurs, il a conseillé un photographe, réservé l'orchestre, arrangé la décoration florale, bref tous les ingrédients sont réunis pour que cette fête soit réussie... Mais la loi des séries va venir bouleverser un planning sur le fil où chaque moment de bonheur et d'émotion risque de se transformer en désastre ou en chaos. Des préparatifs jusqu'à l'aube, nous allons vivre les coulisses de cette soirée à travers le regard de ceux qui travaillent et qui devront compter sur leur unique qualité commune : Le sens de la fête.
                EOT,
            'genres' => ['Comédie', 'Famille'],
            'poster' => '2017-le-sens-de-la-fete.webp',
            'rated' => Rated::GeneralAudiences,
        ],
        [
            'title' => 'Astérix et Obélix : Mission Cléopâtre',
            'plot' => <<<EOT
                Cléopâtre, la reine d’Égypte, décide, pour défier l'Empereur romain Jules César, de construire en trois mois un palais somptueux en plein désert. Si elle y parvient, celui-ci devra concéder publiquement que le peuple égyptien est le plus grand de tous les peuples. Pour ce faire, Cléopâtre fait appel à Numérobis, un architecte d'avant-garde plein d'énergie. S'il réussit, elle le couvrira d'or. S'il échoue, elle le jettera aux crocodiles.
                Celui-ci, conscient du défi à relever, cherche de l'aide auprès de son vieil ami Panoramix. Le druide fait le voyage en Égypte avec Astérix et Obélix. De son côté, Amonbofis, l'architecte officiel de Cléopâtre, jaloux que la reine ait choisi Numérobis pour construire le palais, va tout mettre en œuvre pour faire échouer son concurrent.
                EOT,
            'releasedAt' => '2002/01/30',
            'poster' => 'mission-cleopatre.jpg',
            'genres' => ['Documentary', 'Adventure', 'Comedy', 'Family'],
            'rated' => Rated::ParentsStronglyCautioned,
        ],
        [
            'title' => 'Avatar',
            'releasedAt' => '2009/12/16',
            'plot' => <<<'EOT'
                Malgré sa paralysie, Jake Sully, un ancien marine immobilisé dans un fauteuil roulant, est resté un combattant au plus profond de son être. Il est recruté pour se rendre à des années-lumière de la Terre, sur Pandora, où de puissants groupes industriels exploitent un minerai rarissime destiné à résoudre la crise énergétique sur Terre. Parce que l'atmosphère de Pandora est toxique pour les humains, ceux-ci ont créé le Programme Avatar, qui permet à des \" pilotes\" humains de lier leur esprit à un avatar, un corps biologique commandé à distance, capable de survivre dans cette atmosphère létale. Ces avatars sont des hybrides créés génétiquement en croisant l'ADN humain avec celui des Na'vi, les autochtones de Pandora.
                Sous sa forme d'avatar, Jake peut de nouveau marcher. On lui confie une mission d'infiltration auprès des Na'vi, devenus un obstacle trop conséquent à l'exploitation du précieux minerai. Mais tout va changer lorsque Neytiri, une très belle Na'vi, sauve la vie de Jake...
                EOT,
            'genres' => ['Action', 'Adventure', 'Fantasy'],
            'poster' => 'avatar.webp',
            'rated' => Rated::Restricted,
        ],
    ];

    public function __construct(
        private readonly MovieRepository $movieRepository,
    ) {
    }

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
                ->setPlot($rawMovie['plot'])
                ->setPoster($rawMovie['poster'])
                ->setReleasedAt(DateTimeImmutable::createFromFormat('!Y/m/d', $rawMovie['releasedAt']))
                ->setRated($rawMovie['rated'])
            ;

            foreach ($rawMovie['genres'] as $genreName) {
                $genre = $this->getReference("Genre.{$genreName}");
                $movie->addGenre($genre);
            }

            $this->movieRepository->save($movie, false);
        }

        $this->movieRepository->flush();
    }
}
