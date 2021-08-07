<?php

namespace App\Command\User;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:deactivate',
    description: 'Деактивировать пользователя по id',
)]
class UserDeactivateCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'id пользователя'
        )->addOption(
            'reverse',
            null,
            InputOption::VALUE_REQUIRED,
            'Активировать',
            false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = $this->userRepository
            ->findOneBy(['id' => $input->getArgument('id')]);

        $activate = (bool)$input->getOption('reverse');

        if ($user->getIsActive() === $activate) {
            $io->warning('Пользователь уже ' . ($activate ? 'активирован' : 'деактивирован'));

            return Command::SUCCESS;
        }

        $user->setIsActive($activate);
        $this->em->flush();

        $io->success('Пользователь успешно ' . ($activate ? 'активирован' : 'деактивирован'));

        return Command::SUCCESS;
    }
}
