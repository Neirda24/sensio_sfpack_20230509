<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final readonly class Movie
{
    /**
     * @param list<string> $genres
     */
    public function __construct(
        public string            $title,
        public DateTimeImmutable $releasedAt,
        public string            $plot,
        public array             $genres,
        public string            $slug,
    ) {
    }

    public function year(): string
    {
        return $this->releasedAt->format('Y');
    }
}
