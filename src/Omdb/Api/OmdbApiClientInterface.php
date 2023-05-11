<?php

declare(strict_types=1);

namespace App\Omdb\Api;

interface OmdbApiClientInterface
{
    /**
     * @throws NoResult When the IMDB ID was not found
     */
    public function getById(string $imdbId): Movie;

    /**
     * @return list<SearchResult>
     */
    public function searchByTitle(string $title): array;
}
