<?php

namespace App\Service;

use SymfonySkillbox\HomeworkBundle\AbstractStrategy;
use SymfonySkillbox\HomeworkBundle\Unit;

class ZergRushStrategy extends AbstractStrategy
{
    public function sortUnits(array $units): array
    {
        usort($units, fn(Unit $a, Unit $b) => $a->getCost() <=> $b->getCost());

        return $units;
    }
}
