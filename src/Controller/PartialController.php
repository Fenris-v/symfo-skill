<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PartialController extends AbstractController
{
    /**
     * @param CommentRepository $commentRepository
     * @return Response
     * @throws Exception
     */
    public function lastComments(CommentRepository $commentRepository): Response {
        $lastComments = $commentRepository->getLatest();

        return $this->render(
            'partial/last_comments.html.twig',
            [
                'comments' => $lastComments
            ]
        );
    }
}
