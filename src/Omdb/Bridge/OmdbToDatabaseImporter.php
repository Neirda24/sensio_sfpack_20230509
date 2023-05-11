<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Entity\Movie as MovieEntity;
use App\Model\Rated;
use App\Omdb\Api\Movie;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTimeImmutable;
use function explode;
use function urlencode;

final class OmdbToDatabaseImporter implements OmdbToDatabaseImporterInterface
{
    public function __construct(
        private readonly MovieRepository $movieRepository,
        private readonly GenreRepository $genreRepository,
    ) {
    }

    public function importFromApiData(Movie $movie, bool $flush = false): MovieEntity
    {
        $newMovie = (new MovieEntity())
            ->setTitle($movie->Title)
            ->setPoster($movie->Poster)
            ->setRated(Rated::tryFrom($movie->Rated) ?? Rated::GeneralAudiences)
            ->setPlot($movie->Plot)
            ->setReleasedAt(new DateTimeImmutable($movie->Released))
            ->setSlug(sprintf("%s-%s", $movie->Year, urlencode($movie->Title)))
        ;

        foreach (explode(', ', $movie->Genre) as $genreName) {
            $newMovie->addGenre($this->genreRepository->get($genreName));
        }

        $this->movieRepository->save($newMovie, $flush);

        return $newMovie;
    }
}
