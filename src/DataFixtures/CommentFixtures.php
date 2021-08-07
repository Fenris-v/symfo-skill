<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Homework\CommentContentProvider;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function __construct(private CommentContentProvider $commentContent) {
    }

    /**
     * Устанавливает порядок фикстур в зависимости от указанных в массиве
     * @return string[]
     */
    public function getDependencies()
    {
        return [
            ArticleFixtures::class
        ];
    }

    function loadData(ObjectManager $manager)
    {
        $this->createMany(Comment::class, 100, function (Comment $comment) {
            $comment->setAuthorName($this->faker->name)
                ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'))
                ->setArticle($this->getRandomReference(Article::class));

            $word = null;
            if ($this->faker->boolean(70)) {
                $word = $this->faker->randomElement(self::$words);
            }

            $comment->setContent(
                $this->commentContent
                    ->get($word, $this->faker->numberBetween(1, 5))
            );

            if ($this->faker->boolean()) {
                $comment->setDeletedAt($this->faker->dateTimeThisMonth);
            }

        });

        $manager->flush();
    }
}
