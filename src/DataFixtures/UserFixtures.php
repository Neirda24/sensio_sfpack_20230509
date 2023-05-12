<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Psr\Clock\ClockInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserFixtures extends Fixture
{
    /**
     * @var list<array{username: string, password: string, dateOfBirth: string, age: int, admin: bool}>
     */
    private const USERS = [
        [
            'username' => 'adrien',
            'password' => 'adrien',
            'dateOfBirth' => '12/10',
            'age' => 31,
            'admin' => true,
        ],
        [
            'username' => 'max',
            'password' => 'max',
            'dateOfBirth' => '02/06',
            'age' => 15,
            'admin' => false,
        ],
        [
            'username' => 'lou',
            'password' => 'lou',
            'dateOfBirth' => '25/01',
            'age' => 8,
            'admin' => false,
        ],
    ];

    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly ClockInterface $clock,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $rawUser) {
            $user = (new User())
                ->setUsername($rawUser['username'])
                ->setPassword($this->passwordHasherFactory->getPasswordHasher(User::class)->hash($rawUser['password']))
                ->setBirthdate(DateTimeImmutable::createFromFormat(
                    '!d/m/Y',
                    "{$rawUser['dateOfBirth']}/{$this->clock->now()->modify("-{$rawUser['age']} years")->format('Y')}"
                ))
            ;

            if (true === $rawUser['admin']) {
                $user->setRoles(['ROLE_ADMIN']);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
