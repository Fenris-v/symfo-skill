<?php

namespace SymfonySkillbox\HomeworkBundle;

class HealthStrategy extends AbstractStrategy
{
    /**
     * @param Unit[] $units
     * @return array
     */
    public function sortUnits(array $units): array
    {
        usort($units, fn(Unit $a, Unit $b) => $b->getHealth() <=> $a->getHealth());

        return $units;
    }
}
