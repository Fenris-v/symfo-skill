<?php

namespace App\EventSubscriber;

use App\Events\ArticleCreatedEvent;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArticleCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private Mailer $mailer
    ) {
    }

    /**
     * @param ArticleCreatedEvent $articleCreatedEvent
     */
    public function onArticleCreatedEvent(ArticleCreatedEvent $articleCreatedEvent)
    {
        $article = $articleCreatedEvent->getArticle();
        if (in_array('ROLE_ADMIN', $articleCreatedEvent->getArticle()->getAuthor()->getRoles())) {
            return;
        }

        foreach ($this->userRepository->getAllAdmins() as $user) {
            $this->mailer->sendArticleCreatedNotification($user, $article);
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ArticleCreatedEvent::class => 'onArticleCreatedEvent',
        ];
    }
}
