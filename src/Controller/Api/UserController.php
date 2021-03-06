<?php

namespace App\Controller\Api;

use App\Service\ApiLogger;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param Security $security
     * @param LoggerInterface $apiLogger
     * @return Response
     * @Route("/api/v1/user/", name="api_user")]
     */
    public function index(
        Request $request,
        Security $security,
        LoggerInterface $apiLogger
    ): Response {
        if (!$this->isGranted('ROLE_API')) {
            return (new ApiLogger($apiLogger))->log($security, $request);
        }

        return $this->json($this->getUser(), context: ['groups' => 'main']);
    }
}
