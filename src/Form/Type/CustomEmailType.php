<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class CustomEmailType extends AbstractType
{
    public function getParent(): string
    {
        return EmailType::class;
    }
}
