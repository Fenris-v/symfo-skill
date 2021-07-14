<?php

namespace App\Controller;

use App\Homework\ArticleContentProvider;
use App\Homework\ArticleProvider;
use App\Service\MarkdownParser;
use App\Service\SlackClient;
use Http\Client\Exception;
use Nexy\Slack\Exception\SlackApiException;
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
     * @param MarkdownParser $parser
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
        MarkdownParser $parser,
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
            $articleText = $parser->parse(
                $articleContent->get(rand(2, 10), $words[0], rand(5, 20))
            );
        } else {
            $articleText = $parser->parse(
                $articleContent->get(rand(2, 10))
            );
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
