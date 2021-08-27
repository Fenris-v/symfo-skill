<?php

namespace SymfonySkillbox\HomeworkBundle;

class Unit
{
    public function __construct(
        private string $name,
        private int $cost,
        private int $strength,
        private int $health
    ) {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * @return int
     */
    public function getStrength(): int
    {
        return $this->strength;
    }

    /**
     * @return int
     */
    public function getHealth(): int
    {
        return $this->health;
    }
}
