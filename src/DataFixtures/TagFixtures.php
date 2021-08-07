<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(
            Tag::class,
            $this->faker->numberBetween(50, 60),
            function (Tag $tag) {
                $tag->setName($this->faker->realText(15))
                    ->setCreatedAt($this->faker->dateTimeBetween('-100days', '-1 days'));

                if ($this->faker->boolean()) {
                    $tag->setDeletedAt($this->faker->dateTimeThisMonth);
                }
            }
        );

        $manager->flush();
    }
}
