<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArticleLikeController extends AbstractController
{
    /**
     * @param Article $article
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @Route("/articles/{slug}/vote/up/", methods="POST", name="app_article_like")
     */
    public function like(Article $article, EntityManagerInterface $em): JsonResponse
    {
        $article->like();
        $em->flush();
        return $this->json(['likes' => $article->getVoteCount()]);
    }

    /**
     * @param Article $article
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @Route("/articles/{slug}/vote/down/", methods="POST", name="app_article_dislike")
     */
    public function dislike(Article $article, EntityManagerInterface $em): JsonResponse
    {
        $article->dislike();
        $em->flush();
        return $this->json(['likes' => $article->getVoteCount()]);
    }
}
