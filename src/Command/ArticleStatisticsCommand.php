<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:article-statistics',
    description: 'Add a short description for your command',
)]
class ArticleStatisticsCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setDescription('Выводит статистику статьи')
            ->addArgument(
                'slug',
                InputArgument::REQUIRED,
                'Символьный код'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'Формат вывода',
                'text'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $slug = $input->getArgument('slug');

        $data = [
            'slug' => $slug,
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'likes' => rand(10, 100)
        ];

        switch ($input->getOption('format')) {
            case 'text':
                $io->table(array_keys($data), [$data]);
                $io->listing($data);
                break;
            case 'json':
                $io->write(json_encode($data));
                break;
            default:
                throw new Exception('Неизвестный формат вывода');
        }

        return Command::SUCCESS;
    }
}
