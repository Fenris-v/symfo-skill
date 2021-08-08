<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function loadData(ObjectManager $manager)
    {
        $this->create(User::class, function (User $user) {
            $user->setEmail('admin@symfony.skillbox')
                ->setFirstName('Admin')
                ->setPassword(
                    $this->passwordHasher
                        ->hashPassword($user, '123456')
                )->setRoles(['ROLE_ADMIN']);
        });

        $this->create(User::class, function (User $user) {
            $user->setEmail('api@symfony.skillbox')
                ->setFirstName('Api')
                ->setPassword(
                    $this->passwordHasher
                        ->hashPassword($user, '123456')
                )->setRoles(['ROLE_API']);
        });

        $this->createMany(
            User::class,
            $this->faker->numberBetween(10, 100),
            function (User $user) {
                $user->setEmail($this->faker->email)
                    ->setFirstName($this->faker->firstName)
                    ->setPassword(
                        $this->passwordHasher
                            ->hashPassword($user, '123456')
                    );

                if ($this->faker->boolean(30)) {
                    $user->setIsActive(false);
                }
            }
        );

        $manager->flush();
    }
}
