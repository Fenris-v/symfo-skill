<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        $formBuilder
            ->add('title', null, [
                'label' => 'Название статьи',
                'help' => 'Укажите название статьи',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание статьи',
                'attr' => ['rows' => 3]
            ])
            ->add('body', null, [
                'label' => 'Содержимое статьи',
                'attr' => ['rows' => 10]
            ])
            ->add('publishedAt', null, [
                'label' => 'Дата публикации статьи',
                'widget' => 'single_text'
            ])
            ->add('keywords', null, [
                'label' => 'Ключевые слова статьи'
            ])
            ->add('author', EntityType::class, [
                'label' => 'Автор статьи',
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%s (id: %d)', $user->getFirstName(), $user->getId());
                },
                'placeholder' => 'Выберите автора статьи',
                'choices' => $this->userRepository->findAllSortedByName()
            ]);

        $formBuilder->get('body')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($bodyFromDB) {
                        return str_replace('**собака**', 'собака', $bodyFromDB);
                    },
                    function ($bodyFromInput) {
                        return str_replace('собака', '**собака**', $bodyFromInput);
                    }
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Article::class,
            ]
        );
    }
}
