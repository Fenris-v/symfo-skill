<?php

namespace SymfonySkillbox\HomeworkBundle;

interface StrategyInterface
{
    /**
     * @param Unit[] $units
     * @param int $resource
     * @return Unit|null
     */
    public function next(array $units, int $resource): ?Unit;

    /**
     * @param Unit[] $units
     * @return Unit[]
     */
    public function sortUnits(array $units): array;
}
