<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Homework\ArticleWordsFilter;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User|null getUser()
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/articles/", name="app_admin_articles")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function index(
        ArticleRepository $articleRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
            $articleRepository->findAllWithSearchQuery(
                $request->query->get('q'),
                $request->query->has('showDeleted')
            ),
            $request->query->getInt('page', 1),
            $request->query->get('perPage') ?? 10
        );

        return $this->render('admin/article/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param ArticleWordsFilter $articleWordsFilter
     * @return Response
     * @Route("/admin/articles/create/", name="app_admin_article_create")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function create(
        EntityManagerInterface $em,
        Request $request,
        ArticleWordsFilter $articleWordsFilter
    ): Response {
        $form = $this->createForm(ArticleFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $article->setBody($articleWordsFilter->filter(
                $article->getBody(),
                ['стакан', 'слов', 'нескол', 'прост']
            ));

            $em->persist($article);
            $em->flush();

            $this->addFlash('flash_message', 'Статья успешно создана');

            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/article/create.html.twig', [
            'articleForm' => $form->createView()
        ]);
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
