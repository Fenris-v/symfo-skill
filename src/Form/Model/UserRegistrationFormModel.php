<?php

namespace App\Form\Model;

use App\Validator\RegistrationSpam;
use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormModel
{
    /**
     * @Assert\NotBlank(message="Поле не может быть пустым")
     * @Assert\Email(message="Поле должно быть действительным email адресом")
     * @UniqueUser()
     * @RegistrationSpam()
     */
    public string $email;

    public string $firstName;

    /**
     * @Assert\NotBlank(message="Пароль не указан")
     * @Assert\Length(min="6", minMessage="Пароль должен быть длиной не меньше 6 символов")
     */
    public string $plainPassword;

    /**
     * @Assert\IsTrue(message="Вы должны согласиться с условиями")
     */
    public bool $agreeTerms;
}
