<?php

namespace App\EventSubscriber;

use App\Events\UserRegisteredEvent;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public function __construct(private Mailer $mailer)
    {
    }

    /**
     * @param UserRegisteredEvent $userRegisteredEvent
     * @throws TransportExceptionInterface
     */
    public function onUserRegistered(UserRegisteredEvent $userRegisteredEvent)
    {
        $this->mailer->sendWelcomeMail($userRegisteredEvent->getUser());
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'onUserRegistered'
        ];
    }
}
