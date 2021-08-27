<?php

namespace App\Service;

use SymfonySkillbox\HomeworkBundle\Unit;
use SymfonySkillbox\HomeworkBundle\UnitProviderInterface;

class CustomUnitProvider implements UnitProviderInterface
{
    /**
     * @return Unit[]
     */
    public function getUnits(): array
    {
        return [
            new Unit('Крестьянин', 33, 1, 1),
            new Unit('Солдат', 100, 5, 100),
            new Unit('Лучник', 150, 6, 50),
            new Unit('Крестоносец', 160, 99, 70)
        ];
    }
}
