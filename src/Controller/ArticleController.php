<?php

namespace App\Controller;

use App\Entity\Article;
use App\Exception\GenerateException;
use App\Homework\ArticleContentProviderInterface;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
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
     * @param ArticleRepository $repository
     * @param CommentRepository $commentRepository
     * @return Response
     * @Route("/", name="app_home")
     */
    public function homepage(
        ArticleRepository $repository,
        CommentRepository $commentRepository
    ): Response {
        $articles = $repository->findLatestPublished();

        $lastComments = $commentRepository->getLatest();

        return $this->render(
            'articles/home.html.twig',
            [
                'articles' => $articles,
                'comments' => $lastComments
            ]
        );
    }

    #[Route('/articles/article_content/', name: 'app_article_content')]
    public function articleGenerate(
        Request $request,
        ArticleContentProviderInterface $articleContent
    ): Response {
        $args = $request->query->all();

        try {
            if (!empty($args) && (!isset($args['paragraphs']) || !$args['paragraphs'])) {
                throw new GenerateException('Поле "Количество параграфов" является обязательным');
            }

            if (isset($args['paragraphs'])) {
                $article = $articleContent->get(
                    $args['paragraphs'],
                    $args['word'] ?: null,
                    $args['wordsCount'] ?: 0
                );
            }
        } catch (GenerateException $exception) {
            $error = $exception->getMessage();
        } finally {
            return $this->render(
                'articles/article_content.html.twig',
                [
                    'article' => $article ?? null,
                    'error' => $error ?? null
                ]
            );
        }
    }

    /**
     * @param Article $article
     * @param SlackClient $slackClient
     * @return Response
     * @throws Exception
     * @throws SlackApiException
     * @Route("/articles/{slug}/", name="app_article_show")
     */
    public function show(
        Article $article,
        SlackClient $slackClient,
    ): Response {
        if ($article->getSlug() === 'slack') {
            $slackClient->send('Hello World');
        }

        return $this->render(
            'articles/show.html.twig',
            ['article' => $article]
        );
    }
}
