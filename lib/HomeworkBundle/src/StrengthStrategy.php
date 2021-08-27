<?php

namespace SymfonySkillbox\HomeworkBundle;

class StrengthStrategy extends AbstractStrategy
{
    /**
     * @param Unit[] $units
     * @return array
     */
    public function sortUnits(array $units): array
    {
        usort($units, fn(Unit $a, Unit $b) => $b->getStrength() <=> $a->getStrength());

        return $units;
    }
}
