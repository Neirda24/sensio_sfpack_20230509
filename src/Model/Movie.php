<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Genre as GenreEntity;
use App\Entity\Movie as MovieEntity;
use DateTimeImmutable;
use function array_map;
use function str_starts_with;

final class Movie
{
    /**
     * @param list<string> $genres
     */
    public function __construct(
        public readonly string            $title,
        public readonly DateTimeImmutable $releasedAt,
        public readonly string            $plot,
        public readonly array             $genres,
        public readonly string            $slug,
        public readonly string            $poster,
    ) {
    }

    public static function fromEntity(MovieEntity $movieEntity): self
    {
        return new self(
            title:      $movieEntity->getTitle(),
            releasedAt: $movieEntity->getReleasedAt(),
            plot:       $movieEntity->getPlot(),
            genres:     array_map(
                static fn(GenreEntity $genreEntity) => $genreEntity->getName(),
                $movieEntity->getGenres()->toArray()
            ),
            slug:       $movieEntity->getSlug(),
            poster:     $movieEntity->getPoster(),
        );
    }

    /**
     * @param list<MovieEntity> $movieEntities
     *
     * @return list<self>
     */
    public static function fromEntities(array $movieEntities): array
    {
        return array_map(self::fromEntity(...), $movieEntities);
    }

    public function year(): string
    {
        return $this->releasedAt->format('Y');
    }

    public function isRemotePoster(): bool
    {
        return str_starts_with($this->poster, 'http');
    }
}
