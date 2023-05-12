<?php

declare(strict_types=1);

namespace App\Omdb\Bridge;

use App\Omdb\Api\Movie;
use App\Omdb\Api\OmdbApiClientInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AutoImporterApiClient implements OmdbApiClientInterface, EventSubscriberInterface
{
    public function __construct(
        private readonly AutoImporterApiClientConfig $config,
        private readonly OmdbApiClientInterface $omdbApiClient,
        private readonly OmdbToDatabaseImporterInterface $omdbToDatabaseImporter,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleCommandEvent::class => [
                ['skipAutoImport', 0]
            ],
            ConsoleTerminateEvent::class => [
                ['restoreAutoImport', 0]
            ],
        ];
    }

    public function skipAutoImport(): void
    {
        $this->config->skip();
    }

    public function restoreAutoImport(): void
    {
        $this->config->restore();
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
