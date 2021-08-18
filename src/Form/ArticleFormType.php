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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleFormType extends AbstractType
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options)
    {
        /** @var Article|null $article */
        $article = $options['data'] ?? null;

        $cannotEditAuthor = $article && $article->isPublished();

        $formBuilder
            ->add('title', null, [
                'label' => 'Название статьи',
                'help' => 'Укажите название статьи',
                'constraints' => [
                    new Length(
                        [
                            'min' => 3,
                            'minMessage' => 'Минимальная длина 3 символа'
                        ]
                    ),
                    new NotBlank(['message' => 'Поле не может быть пустым'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание статьи',
                'rows' => 3,
                'constraints' => [
                    new Length(
                        [
                            'max' => 100,
                            'maxMessage' => 'Максимальная длина 100 символов'
                        ]
                    ),
                    new NotBlank(['message' => 'Поле не может быть пустым'])
                ]
            ])
            ->add('body', null, [
                'label' => 'Содержимое статьи',
                'constraints' => [
                    new NotBlank(['message' => 'Поле не может быть пустым'])
                ]
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
                'choices' => $this->userRepository->findAllSortedByName(),
                'invalid_message' => 'Такой автор не существует',
                'constraints' => [
                    new NotBlank(['message' => 'Поле не может быть пустым'])
                ],
                'disabled' => $cannotEditAuthor
            ]);

        if ($options['enable_published_at']) {
            $formBuilder->add('publishedAt', null, [
                'label' => 'Дата публикации статьи',
                'widget' => 'single_text'
            ]);
        }

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
                'enable_published_at' => false
            ]
        );
    }
}
