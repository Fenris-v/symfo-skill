<?php

namespace App\Service;

use App\Entity\User;
use Closure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @param User $user
     * @throws TransportExceptionInterface
     */
    public function sendWelcomeMail(User $user)
    {
        $this->send('email/welcome.html.twig', $user, 'Добро пожаловать');
    }

    /**
     * @param User $user
     * @param array $articles
     * @throws TransportExceptionInterface
     */
    public function sendWeeklyNewsLetter(User $user, array $articles)
    {
        $this->send(
            'email/newsletter.html.twig',
            $user,
            'Еженедельная рассылка',
            function (TemplatedEmail $email) use ($articles) {
                $email->context(['articles' => $articles]);
            }
        );
    }

    /**
     * @param string $template
     * @param User $user
     * @param string $subject
     * @param Closure|null $callback
     * @throws TransportExceptionInterface
     */
    private function send(string $template, User $user, string $subject, Closure $callback = null)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@symfony.skillbox', 'Spill-Coffee-On-The-Keyboard'))
            ->to(new Address($user->getEmail(), $user->getFirstName()))
            ->subject($subject)
            ->htmlTemplate($template);

        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}
