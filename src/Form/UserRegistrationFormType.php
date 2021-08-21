<?php

namespace App\Form;

use App\Form\Model\UserRegistrationFormModel;
use App\Form\Type\CustomEmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', CustomEmailType::class, [
                'attr' => ['placeholder' => 'Ваш Email...'],
            ])
            ->add('firstName', null, [
                'attr' => ['placeholder' => 'Ваше Имя...'],
                'label' => ' '
            ])
            ->add('plainPassword', PasswordType::class, [
                'attr' => ['placeholder' => 'Ваш Пароль...'],
                'label' => ' '
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Согласен с условиями',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Зарегистрироваться',
                'attr' => ['class' => 'btn btn-lg btn-primary btn-block']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UserRegistrationFormModel::class,
            ]
        );
    }
}
