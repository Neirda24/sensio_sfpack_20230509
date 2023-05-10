<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Entity\Movie;
use Attribute;
use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
final class MovieSlug extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Length(min: 6, max: 255),
            new Regex('#'.Movie::SLUG_PATTERN.'#')
        ];
    }
}
