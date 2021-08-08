<?php

namespace App\Controller\Api;

use App\Homework\ArticleContentProviderInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @IsGranted("ROLE_USER")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/api/v1/article_content/", name="app_api_article", methods: 'POST')
     */
    public function index(
        ArticleContentProviderInterface $articleContent,
        Security $security,
        LoggerInterface $logger
    ): Response {
        $user = $security->getUser();
        if (!in_array('ROLE_API', $user->getRoles())) {
            $logger->warning('Пользователь пытался получить доступ к API: ', [
                'user' => $security->getUser(),
            ]);
        }

        $this->denyAccessUnlessGranted('ROLE_API');

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
}
