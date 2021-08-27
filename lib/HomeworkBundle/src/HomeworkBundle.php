<?php

namespace SymfonySkillbox\HomeworkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use SymfonySkillbox\HomeworkBundle\DependencyInjection\HomeworkExtension;

class HomeworkBundle extends Bundle
{
    /**
     * @return HomeworkExtension
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new HomeworkExtension();
        }

        return $this->extension;
    }
}
