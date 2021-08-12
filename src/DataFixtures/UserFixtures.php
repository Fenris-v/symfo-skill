<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
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
        $this->create(User::class, function (User $user) use ($manager) {
            $user->setEmail('admin@symfony.skillbox')
                ->setFirstName('Admin')
                ->setPassword(
                    $this->passwordHasher
                        ->hashPassword($user, '123456')
                )->setRoles(['ROLE_ADMIN']);

            $manager->persist(new ApiToken($user));
        });

        $this->create(User::class, function (User $user) use ($manager) {
            $user->setEmail('api@symfony.skillbox')
                ->setFirstName('Api')
                ->setPassword(
                    $this->passwordHasher
                        ->hashPassword($user, '123456')
                )->setRoles(['ROLE_API']);

            for ($i = 0; $i < 3; $i++) {
                $manager->persist(new ApiToken($user));
            }
        });

        $this->createMany(
            User::class,
            $this->faker->numberBetween(10, 15),
            function (User $user) use ($manager) {
                $user->setEmail($this->faker->email)
                    ->setFirstName($this->faker->firstName)
                    ->setPassword(
                        $this->passwordHasher
                            ->hashPassword($user, '123456')
                    );

                if ($this->faker->boolean(30)) {
                    $user->setIsActive(false);
                }

                $manager->persist(new ApiToken($user));
            }
        );

        $manager->flush();
    }
}
