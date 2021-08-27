<?php

namespace SymfonySkillbox\HomeworkBundle;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UnitFactory extends Command
{
    protected static $defaultName = 'symfony-skillbox-homework:produce-units';

    public function __construct(
        private StrategyInterface $strategy,
        private bool $enableSolder,
        private bool $enableArcher
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Create army')
            ->addArgument('resources', InputArgument::REQUIRED, 'Resources');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $resources = $input->getArgument('resources');

        $army = $this->produceUnits($resources);

        $result = [];
        $totalPrice = 0;
        foreach ($army as $item) {
            $result[] = [
                $item->getName(),
                $item->getCost(),
                $item->getStrength(),
                $item->getHealth()
            ];

            $totalPrice += $item->getCost();
        }

        $io->text(sprintf("на %d было куплено %d юнитов", $resources, count($army)));
        $io->table(['Имя', 'Цена', 'Сила', 'Здоровье'], $result);
        $io->text(sprintf('Осталось ресурсов: %d', $resources - $totalPrice));

        return Command::SUCCESS;
    }

    /**
     * Производит армию
     * @param int $resources
     * @return Unit[]
     */
    private function produceUnits(int $resources): array
    {
        $units = $this->strategy->sortUnits($this->getUnits());

        $army = [];
        while ($unit = $this->strategy->next($units, $resources)) {
            $army[] = $unit;
            $resources -= $unit->getCost();
        }

        return $army;
    }

    /**
     * Формирует список доступных юнитов на фабрике
     * @return Unit[]
     */
    private function getUnits(): array
    {
        $units = [
            new Unit('Крестьянин', 33, 1, 1),
        ];

        if ($this->enableSolder) {
            $units[] = new Unit('Солдат', 100, 5, 100);
        }

        if ($this->enableArcher) {
            $units[] = new Unit('Лучник', 150, 6, 50);
        }

        return $units;
    }
}
