<?php

declare(strict_types=1);

namespace App\Event\Security;

use App\Model\Movie;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class UnderragedAccess extends Event
{
    public function __construct(
        public readonly UserInterface $user,
        public readonly Movie $movie,
    ) {
    }
}
