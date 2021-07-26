<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Homework\ArticleContentProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends AbstractController
{
    private array $words = [
        'это',
        'массив',
        'длиной',
        'пять',
        'дефис',
        'десять',
        'слов',
    ];

    #[Route('/admin/articles/create', name: 'app_admin_articles_create', methods: 'get')]
    public function create(): Response
    {
        return $this->render('articles/create.twig');
    }

    #[Route('/admin/articles/create', name: 'app_admin_articles_save', methods: 'post')]
    public function save(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        ArticleContentProvider $contentProvider
    ): Response {
        $repository = $em->getRepository(Article::class);
        $article = $repository->findBy(['slug' => $request->request->get('slug')]);

        if ($article) {
            $this->addFlash('error', 'Статья с таким url уже существует');
            return $this->render('articles/create.twig');
        }

        $this->addFlash('notice', 'Статья успешно создана');

        $article = new Article();

        $article->setTitle($request->request->get('title'));
        $article->setSlug($request->request->get('slug'));
        $article->setDescription($request->request->get('description'));
        $article->setAuthor($request->request->get('author'));
        $article->setKeywords($request->request->get('keywords'));
        $article->setVoteCount($request->request->get('voteCount') ?: 0);
        $article->setImageFilename($request->request->get('imageFilename'));
        $article->setPublishedAt($request->request->get('publishedAt'));

        $word = null;
        $repeat = 0;
        if (rand(1, 100) < 71) {
            shuffle($this->words);
            $word = $this->words[0];
            $repeat = rand(5, 10);
        }

        $article->setBody($contentProvider->get(rand(2, 10), $word, $repeat));

        $em->persist($article);
        $em->flush();

        return $this->redirect($this->generateUrl('app_admin_articles_create'), 301);
    }
}
