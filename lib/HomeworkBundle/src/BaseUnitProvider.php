<?php

namespace SymfonySkillbox\HomeworkBundle;

class BaseUnitProvider implements UnitProviderInterface
{
    /**
     * @return Unit[]
     */
    public function getUnits(): array
    {
        return [
            new Unit('Крестьянин', 33, 1, 1),
            new Unit('Солдат', 100, 5, 100),
            new Unit('Лучник', 150, 6, 50)
        ];
    }
}
