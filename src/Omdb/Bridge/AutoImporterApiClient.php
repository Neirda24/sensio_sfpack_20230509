<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Omdb\Api\Movie;
use App\Omdb\Api\OmdbApiClientInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class AutoImporterApiClient implements OmdbApiClientInterface
{
    public function __construct(
        private readonly AutoImporterApiClientConfig $config,
        private readonly OmdbApiClientInterface $omdbApiClient,
        private readonly OmdbToDatabaseImporterInterface $omdbToDatabaseImporter,
    ) {
    }

    public function getById(string $imdbId): Movie
    {
        $movie = $this->omdbApiClient->getById($imdbId);

        if ($this->config->getValue() === true) {
            try {
                $this->omdbToDatabaseImporter->importFromApiData($movie, true);
            } catch (UniqueConstraintViolationException) {
            }
        }

        return $movie;
    }

    public function searchByTitle(string $title): array
    {
        return $this->omdbApiClient->searchByTitle($title);
    }
}
