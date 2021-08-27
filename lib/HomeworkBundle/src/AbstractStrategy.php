<?php

namespace SymfonySkillbox\HomeworkBundle;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @param array $units
     * @param int $resource
     * @return Unit|null
     */
    public function next(array $units, int $resource): ?Unit
    {
        foreach ($units as $unit) {
            if ($unit->getCost() <= $resource) {
                return $unit;
            }
        }

        return null;
    }
}
