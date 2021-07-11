<?php

namespace App\Homework;

use Symfony\Component\String\Slugger\AsciiSlugger;

class ArticleProvider
{
    private array $articles;

    public function __construct()
    {
        $slugger = new AsciiSlugger();

        $this->articles = [
            [
                'title' => 'Что делать, если надо верстать?',
                'slug' => $slugger->slug('Что делать, если надо верстать?')
                    ->lower()
                    ->toString(),
                'image' => '/images/article-2.jpeg',
            ],
            [
                'title' => 'Facebook ест твои данные',
                'slug' => $slugger->slug('Facebook ест твои данные')
                    ->lower()
                    ->toString(),
                'image' => '/images/article-1.jpeg',
            ],
            [
                'title' => 'Когда пролил кофе на клавиатуру',
                'slug' => $slugger->slug('Когда пролил кофе на клавиатуру')
                    ->lower()
                    ->toString(),
                'image' => '/images/article-3.jpg',
            ],
        ];
    }

    /**
     * Возвращает статью
     * @param string $slug
     * @return array
     */
    public function article(string $slug): array
    {
        $key = array_search($slug, array_column($this->articles, 'slug'));

        return $this->articles[$key];
    }

    /**
     * Возвращает статьи
     * @return array[]
     */
    public function articles(): array
    {
        return $this->articles;
    }
}
