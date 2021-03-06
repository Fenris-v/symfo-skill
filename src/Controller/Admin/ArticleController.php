<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\User;
use App\Events\ArticleCreatedEvent;
use App\Form\ArticleFormType;
use App\Homework\ArticleWordsFilter;
use App\Repository\ArticleRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use League\Flysystem\FilesystemException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @param FileUploader $articleFileUploader
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     * @throws FilesystemException
     * @Route("/admin/articles/create/", name="app_admin_article_create")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function create(
        EntityManagerInterface $em,
        Request $request,
        ArticleWordsFilter $articleWordsFilter,
        FileUploader $articleFileUploader,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $form = $this->createForm(ArticleFormType::class, new Article());

        if ($article = $this->handlerFormRequest($form, $request, $em, $articleWordsFilter, $articleFileUploader)) {
            $eventDispatcher->dispatch(new ArticleCreatedEvent($article));

            $this->addFlash('flash_message', '???????????? ?????????????? ??????????????');

            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/article/create.html.twig', [
            'articleForm' => $form->createView(),
            'showError' => $form->isSubmitted()
        ]);
    }

    /**
     * @param Article $article
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ArticleWordsFilter $articleWordsFilter
     * @param FileUploader $articleFileUploader
     * @return Response
     * @Route("/admin/articles/{id}/edit/", name="app_admin_article_edit")
     * @IsGranted("MANAGE", subject="article")
     * @throws FilesystemException
     */
    public function edit(
        Article $article,
        Request $request,
        EntityManagerInterface $em,
        ArticleWordsFilter $articleWordsFilter,
        FileUploader $articleFileUploader
    ): Response {
        $form = $this->createForm(ArticleFormType::class, $article, [
            'enable_published_at' => true
        ]);

        if ($article = $this->handlerFormRequest($form, $request, $em, $articleWordsFilter, $articleFileUploader)) {
            $this->addFlash('flash_message', '???????????? ?????????????? ????????????????');

            return $this->redirectToRoute('app_admin_article_edit', ['id' => $article->getId()]);
        }

        return $this->render('admin/article/edit.html.twig', [
            'articleForm' => $form->createView(),
            'showError' => $form->isSubmitted()
        ]);
    }

    /**
     * ???????????????????? ????????????
     * @param FormInterface $form
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ArticleWordsFilter $articleWordsFilter
     * @param FileUploader $articleFileUploader
     * @return Article|null
     * @throws FilesystemException
     */
    private function handlerFormRequest(
        FormInterface $form,
        Request $request,
        EntityManagerInterface $em,
        ArticleWordsFilter $articleWordsFilter,
        FileUploader $articleFileUploader
    ): ?Article {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $article->setBody(
                $articleWordsFilter->filter(
                    $article->getBody(),
                    ['????????????', '????????', '????????????', '??????????']
                )
            );

            /** @var UploadedFile|null $image */
            $image = $form->get('image')->getData();

            if ($image) {
                $article->setImageFilename($articleFileUploader->uploadFile($image, $article->getImageFilename()));
            }

            $em->persist($article);
            $em->flush();

            return $article;
        }

        return null;
    }
}
