<?php

namespace App\Events;

use App\Entity\Article;

class ArticleCreatedEvent
{
    public function __construct(private Article $article)
    {
    }

    /**
     * @return Article
     */
    public function getArticle(): Article
    {
        return $this->article;
    }
}
