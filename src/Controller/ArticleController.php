<?php

namespace App\Controller;

use App\Homework\ArticleContentProvider;
use App\Homework\ArticleContentProviderInterface;
use App\Homework\ArticleProvider;
use App\Service\SlackClient;
use Http\Client\Exception;
use Nexy\Slack\Exception\SlackApiException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/articles/article_content/', name: 'app_article_content')]
    public function articleGenerate(
        Request $request,
        ArticleContentProviderInterface $articleContent
    ): Response {
        $args = $request->query->all();

        if (isset($args['paragraphs']) && is_numeric($args['paragraphs'])) {
            try {
                $article = $articleContent->get(
                $args['paragraphs'],
                $args['word'] ?: null,
                $args['wordsCount'] ?: 0
            );
            } catch (\Exception $exception) {
                return $this->render(
                    'articles/article_content.html.twig',
                    [
                        'error' => $exception->getMessage()
                    ]
                );
            }
        }

        return $this->render(
            'articles/article_content.html.twig',
            [
                'article' => $article ?? null,
            ]
        );
    }

    /**
     * @param string $slug
     * @param ArticleProvider $articleProvider
     * @param SlackClient $slackClient
     * @param ArticleContentProvider $articleContent
     * @return Response
     * @throws Exception
     * @throws SlackApiException
     * @Route("/articles/{slug}/", name="app_article_show")
     */
    public function show(
        string $slug,
        ArticleProvider $articleProvider,
        SlackClient $slackClient,
        ArticleContentProvider $articleContent
    ): Response {
        if ($slug === 'slack') {
            $slackClient->send('Hello World');
        }

        $comments = [
            'Why does the gull whine?',
            'Cur galatae prarere?',
            'Going to the next world doesn’t feel light anymore than emerging creates prime shame.'
        ];

        $words = [
            'кофе',
            'пролил',
            'боль',
            'клавиатура',
            'беда',
            'надо',
            'покупать',
        ];

        $hasWord = rand(1, 100) <= 70;

        if ($hasWord) {
            shuffle($words);
            $articleText = $articleContent->get(rand(2, 10), $words[0], rand(5, 20));
        } else {
            $articleText = $articleContent->get(rand(2, 10));
        }

        return $this->render(
            'articles/show.html.twig',
            [
                'article' => $articleProvider->article($slug),
                'comments' => $comments,
                'articleContent' => $articleText
            ]
        );
    }
}
