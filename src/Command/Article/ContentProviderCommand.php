<?php

namespace App\Command\Article;

use App\Homework\ArticleContentProviderInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:article:content_provider',
)]
class ContentProviderCommand extends Command
{
    public function __construct(private ArticleContentProviderInterface $articleContent)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Создает контент для статьи')
            ->addArgument('paragraphs', InputArgument::REQUIRED, 'Количество параграфов')
            ->addArgument('word', InputArgument::OPTIONAL, 'Слово, которое должно содержаться в тексте')
            ->addArgument('wordsCount', InputArgument::OPTIONAL, 'Количество повторений слова');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $paragraphs = $input->getArgument('paragraphs');

        if (!is_numeric($paragraphs)) {
            throw new Exception('Количество параграфов должно быть числом');
        }

        if ((int)$paragraphs === 0) {
            throw new Exception('Количество параграфов должно быть больше нуля');
        }

        $word = $input->getArgument('word') ?? null;
        $wordsCount = $input->getArgument('wordsCount') ?? 0;

        if ($wordsCount < 0) {
            throw new Exception('Количество повторений не может быть отрицательным');
        }

        $io->write(
            $this->articleContent
                ->get($paragraphs, $word, $wordsCount) . PHP_EOL
        );

        return Command::SUCCESS;
    }
}
