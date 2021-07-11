<?php

namespace App\Controller;

use App\Homework\ArticleProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @param ArticleProvider $articleProvider
     * @return Response
     * @Route("/", name="app_home")
     */
    public function homepage(ArticleProvider $articleProvider): Response
    {
        return $this->render(
            'articles/home.html.twig',
            ['articles' => $articleProvider->articles()]
        );
    }

    /**
     * @param string $slug
     * @param ArticleProvider $articleProvider
     * @return Response
     * @Route("/articles/{slug}/", name="app_article_show")
     */
    public function show(string $slug, ArticleProvider $articleProvider): Response
    {
        $comments = [
            'Why does the gull whine?',
            'Cur galatae prarere?',
            'Going to the next world doesn’t feel light anymore than emerging creates prime shame.'
        ];

        return $this->render(
            'articles/show.html.twig',
            [
                'article' => $articleProvider->article($slug),
                'comments' => $comments
            ]
        );
    }
}
