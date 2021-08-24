<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestDurationSubscriber implements EventSubscriberInterface
{
    private $startedAt;

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function startTimer(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->startedAt = microtime(true);
    }

    public function endTimer(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->logger->info(
            sprintf(
                'Время выполнения запроса: %f мс',
                (microtime(true) - $this->startedAt) * 1000
            )
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => [
                ['startTimer', 5000],
                //                ['action1', 0],
                //                ['action2', 0]
            ],
            ResponseEvent::class => ['endTimer',],
        ];
    }
}
