<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\Security\UnderragedAccess;
use App\Repository\UserRepository;
use Psr\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function array_map;
use function implode;
use function sprintf;

class MovieSecuritySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly UserRepository $userRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UnderragedAccess::class => [
                ['notifyAdmins', 0]
            ],
        ];
    }

    public function notifyAdmins(UnderragedAccess $event): void
    {
        $user = $event->user;

        if (!$user instanceof User) {
            return;
        }

        dump(sprintf(
            'All admins (%s) will be notified that "%s (%d)" tried to access the movie "%s (%d+)"',
            implode(', ', array_map(fn (User $user): string => $user->getUsername(), $this->userRepository->listAllAdmins())),
            $user->getUsername(),
            $user->getAge($this->clock->now()),
            $event->movie->title,
            $event->movie->rated->minAgeRequired()
        ));
    }
}
