<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Homework\ArticleContentProvider;
use App\Homework\CommentContentProvider;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends BaseFixtures
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

    private static array $authors = [
        'Николай',
        'Mr White',
        'Барон Сосискин',
        'Сметанка',
        'Рыжик',
    ];

    private static array $images = [
        '/images/article-1.jpeg',
        '/images/article-2.jpeg',
        '/images/article-3.jpg',
    ];

    private static array $words = [
        'это',
        'массив',
        'длиной',
        'пять',
        'дефис',
        'десять',
        'слов',
    ];

    public function __construct(
        private ArticleContentProvider $articleContent,
        private CommentContentProvider $commentContent
    ) {
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
                    ->setAuthor($this->faker->randomElement(self::$authors))
                    ->setTitle($this->faker->randomElement(self::$titles))
                    ->setBody($this->generateArticleText());

                if ($this->faker->boolean(60)) {
                    $article->setPublishedAt($this->faker->dateTimeBetween('-100days', '-1 days'));
                }

                for ($i = 0; $i < $this->faker->numberBetween(2, 10); $i++) {
                    $this->addComment($article, $manager);
                }
            }
        );
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
     * @param Article $article
     * @param ObjectManager $manager
     */
    public function addComment(Article $article, ObjectManager $manager): void
    {
        $comment = (new Comment())
            ->setAuthorName($this->faker->name)
            ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'))
            ->setArticle($article);

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

        $manager->persist($comment);
    }
}
