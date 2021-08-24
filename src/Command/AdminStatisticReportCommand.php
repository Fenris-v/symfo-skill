<?php

namespace App\Command;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

#[AsCommand(
    name: 'app:admin-statistic-report',
    description: 'Отчет по статьям',
)]
class AdminStatisticReportCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer,
        private ArticleRepository $articleRepository,
        private UserRepository $userRepository,
        private string $siteName,
        private string $reportEmail
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'email',
            InputArgument::REQUIRED,
            'Адрес получателя'
        )->addOption(
            'dateFrom',
            'f',
            InputOption::VALUE_OPTIONAL,
            'С какого срока'
        )->addOption(
            'dateTo',
            't',
            InputOption::VALUE_OPTIONAL,
            'По какой срок'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $dateFrom = $input->getOption('dateFrom') ?? '-1week';
        $dateTo = $input->getOption('dateTo') ?? 'now';

        $result = [
            'Период' => $this->getFormattedDate($dateFrom) . '-' . $this->getFormattedDate($dateTo),
            'Всего пользователей' => $this->userRepository->countUsers(),
            'Статей создано за период' => $this->articleRepository->countPublishedByDate($dateFrom, $dateTo),
            'Статей опубликовано за период' => $this->articleRepository->countCreatedByDate($dateFrom, $dateTo),
        ];

        $csv = (new CsvEncoder())->encode(
            $result,
            'csv',
            [
                'csv_headers' => [
                    'Период',
                    'Всего пользователей',
                    'Статей создано за период',
                    'Статей опубликовано за период'
                ]
            ]
        );

        $email = (new TemplatedEmail())
            ->from(new Address($this->reportEmail, $this->siteName))
            ->to($email)
            ->subject('Отчет')
            ->html('<h1>Отчет по сайту</h1>')
            ->attach($csv, 'report_' . date('Y-m-d') . '.csv');

        $this->mailer->send($email);

        $io->success('Отчет отправлен');

        return Command::SUCCESS;
    }

    /**
     * @param string $date
     * @return string
     */
    private function getFormattedDate(string $date): string
    {
        return Carbon::createFromDate($date)->format('d.m.Y');
    }
}
