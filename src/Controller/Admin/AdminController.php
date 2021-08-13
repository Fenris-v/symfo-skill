<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/", name="app_admin")]
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @return Response
     * @Route("/admin/articles/create/", name="app_admin_article_create")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function create(): Response
    {
        return new Response('Страница создания статьи');
    }

    /**
     * @param Article $article
     * @return Response
     * @Route("/admin/articles/{id}/edit/", name="app_admin_article_edit")
     * @IsGranted("MANAGE", subject="article")
     */
    public function edit(Article $article): Response
    {
        return new Response('Страница редактирования статьи: ' . $article->getTitle());
    }
}
