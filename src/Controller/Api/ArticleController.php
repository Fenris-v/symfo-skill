<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Homework\ArticleContentProviderInterface;
use App\Service\ApiLogger;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ArticleController extends AbstractController
{
    /**
     * @param ArticleContentProviderInterface $articleContent
     * @param Security $security
     * @param LoggerInterface $apiLogger
     * @param Request $request
     * @return Response
     * @Route("/api/v1/article_content/", name="api_article", methods="POST")
     */
    public function index(
        ArticleContentProviderInterface $articleContent,
        Security $security,
        LoggerInterface $apiLogger,
        Request $request,
    ): Response {
        if (!$this->isGranted('ROLE_API')) {
            return (new ApiLogger($apiLogger))->log($security, $request);
        }

        $request = Request::createFromGlobals();

        extract($request->toArray());

        if (!isset($paragraphs)) {
            return $this->json(
                [
                    'error' => 'Аргумент paragraphs является обязательным'
                ]
            );
        }

        try {
            return $this->json(
                [
                    'text' => $articleContent->get(
                        $paragraphs,
                        $word ?? null,
                        $wordsCount ?? 0
                    )
                ]
            );
        } catch (Exception $exception) {
            return $this->json(
                [
                    'error' => $exception->getMessage()
                ]
            );
        }
    }

    /**
     * @param Article $article
     * @param Security $security
     * @param LoggerInterface $apiLogger
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/v1/artices/{id}/")
     */
    public function show(
        Article $article,
        Security $security,
        LoggerInterface $apiLogger,
        Request $request,
    ): JsonResponse {
        if (!$this->isGranted('API_MANAGE', $article)) {
            return (new ApiLogger($apiLogger))->log($security, $request);
        }

        return $this->json($article, context: ['groups' => 'main']);
    }
}
