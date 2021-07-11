<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArticleLikeController extends AbstractController
{
    /**
     * @param int $id
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @Route("/articles/{id<\d+>}/like/", methods="POST", name="app_article_like")
     */
    public function like(int $id, LoggerInterface $logger): JsonResponse
    {
            $logger->info('Like');
            return $this->json(['likes' => rand(121, 200)]);
    }

    /**
     * @param int $id
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @Route("/articles/{id<\d+>}/dislike/", methods="POST", name="app_article_dislike")
     */
    public function dislike(int $id, LoggerInterface $logger): JsonResponse
    {
        $logger->info('Dislike');
        return $this->json(['likes' => rand(0, 119)]);
    }
}
