<?php

namespace App\Service;

use SymfonySkillbox\HomeworkBundle\AbstractStrategy;
use SymfonySkillbox\HomeworkBundle\Unit;

class BillGatesStrategy extends AbstractStrategy
{
    public function sortUnits(array $units): array
    {
        usort($units, fn(Unit $a, Unit $b) => $b->getCost() <=> $a->getCost());

        return $units;
    }
}
