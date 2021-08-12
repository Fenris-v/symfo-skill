<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class ApiLogger
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function log(Security $security, Request $request): JsonResponse
    {
        $this->logger->warning(
            'Пользователь пытался получить доступ к API: ',
            [
                'user' => $security->getUser()->getEmail(),
                'token' => str_replace('Bearer ', '', $request->headers->get('Authorization')),
                'route' => $request->attributes->get('_route'),
                'url' =>  $request->getUri(),
            ]
        );

        return new JsonResponse(
            [
                'message' => 'Нет доступа'
            ], 401
        );
    }
}
