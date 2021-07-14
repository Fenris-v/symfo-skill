<?php

namespace App\Controller\Api;

use App\Homework\ArticleContentProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/api/v1/article_content/', name: 'api_article', methods: 'POST')]
    public function index(
        ArticleContentProviderInterface $articleContent
    ): Response {
        $request = Request::createFromGlobals();

        extract($request->toArray());

        if (!isset($paragraphs)) {
            return $this->json(
                [
                    'error' => 'Аргумент paragraphs является обязательным'
                ]
            );
        }

        if (!is_numeric($paragraphs) || (int)$paragraphs <= 0) {
            return $this->json(
                [
                    'error' => 'Аргумент paragraphs должен быть числом больше нуля'
                ]
            );
        }

        return $this->json(
            [
                'text' => $articleContent->get(
                    $paragraphs,
                    $word ?? null,
                    $wordsCount ?? 0
                )
            ]
        );
    }
}
