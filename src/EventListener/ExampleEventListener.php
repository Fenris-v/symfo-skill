<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ExampleEventListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function onEventHappen(RequestEvent $requestEvent)
    {
        $this->logger->info(sprintf('Был вызван обработчик %s', __METHOD__));
    }
}
