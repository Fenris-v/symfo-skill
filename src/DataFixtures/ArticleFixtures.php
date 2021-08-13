<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Homework\ArticleContentProvider;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private static array $titles = [
        'Что делать, если надо верстать?',
        'Facebook ест твои данные',
        'Когда пролил кофе на клавиатуру',
        'Есть ли жизнь после девятой жизни?',
        'Когда в машинах поставят лоток?',
        'В погоне за красной точкой',
        'В чем смысл жизни сосисок',
    ];

    private static array $images = [
        '/images/article-1.jpeg',
        '/images/article-2.jpeg',
        '/images/article-3.jpg',
    ];

    public function __construct(private ArticleContentProvider $articleContent)
    {
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(
            Article::class,
            10,
            function (Article $article) use ($manager) {
                $article
                    ->setKeywords(implode(', ', $this->faker->words(3)))
                    ->setDescription($this->faker->words(10, true))
                    ->setImageFilename($this->faker->randomElement(self::$images))
                    ->setVoteCount($this->faker->numberBetween(-20, 20))
                    ->setAuthor($this->getRandomReference(User::class))
                    ->setTitle($this->faker->randomElement(self::$titles))
                    ->setBody($this->generateArticleText());

                if ($this->faker->boolean(60)) {
                    $article->setPublishedAt($this->faker->dateTimeBetween('-100days', '-1 days'));
                }

                $tags = [];
                for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++) {
                    $tags[] = $this->getRandomReference(Tag::class);
                }

                foreach ($tags as $tag) {
                    $article->addTag($tag);
                }
            }
        );

        $this->manager->flush();
    }

    private function generateArticleText(): string
    {
        $word = null;
        $repeat = 0;
        if ($this->faker->boolean(70)) {
            $word = $this->faker->randomElement(self::$words);
            $repeat = $this->faker->numberBetween(5, 10);
        }

        return $this->articleContent->get(rand(2, 10), $word, $repeat);
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
            UserFixtures::class
        ];
    }
}
