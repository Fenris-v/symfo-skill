<?php

namespace App\Controller\Admin;

use App\Repository\TagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TagsController extends AbstractController
{
    /**
     * @param Request $request
     * @param TagRepository $tagRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/admin/tags/", name="app_admin_tags")]
     */
    public function index(
        Request $request,
        TagRepository $tagRepository,
        PaginatorInterface $paginator
    ): Response {
        $pagination = $paginator->paginate(
            $tagRepository->findAllWithSearchQuery(
                $request->query->get('q'),
                $request->query->has('showDeleted')
            ),
            $request->query->getInt('page', 1),
            $request->query->get('perPage') ?? 10
        );

        return $this->render('admin/tags/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
