<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:weekly-newsletter',
    description: 'Еженедельная рассылка статей',
)]
class WeeklyNewsletterCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private ArticleRepository $articleRepository,
        private Mailer $mailer
    ) {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var User[] $users */
        $users = $this->userRepository->findAllSubscribedUsers();

        /** @var Article[] $articles */
        $articles = $this->articleRepository->findAllPublishedLastWeek();

        $io = new SymfonyStyle($input, $output);

        if (count($articles) == 0) {
            $io->warning('За последнюю неделю статьи не публиковались');
            return self::SUCCESS;
        }

        $io->progressStart(count($users));

        foreach ($users as $user) {
            $this->mailer->sendWeeklyNewsLetter($user, $articles);

            $io->progressAdvance();
        }

        $io->progressFinish();

        return Command::SUCCESS;
    }
}
