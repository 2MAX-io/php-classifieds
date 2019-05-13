<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            RepeatedType::class,
            [
                'first_options' => [
                    'label' => 'trans.Email',
                ],
                'second_options' => [
                    'label' => 'trans.Repeat new email',
                ],
                'type' => TextType::class,
                'constraints' => [
                    new Email([
                        'mode' => Email::VALIDATION_MODE_STRICT
                    ]),
                ],
            ]
        );


        $builder->add(
            'username',
            RepeatedType::class,
            [
                'first_options' => [
                    'label' => 'trans.Username',
                ],
                'second_options' => [
                    'label' => 'trans.Repeat new email',
                ],
                'type' => TextType::class,
                'constraints' => [
                    new Length([
                        'min' => 2,
                    ]),
                ],
            ]
        );

        $builder->add(
            'plainPassword',
            RepeatedType::class,
            [
                'first_options' => [
                    'label' => 'trans.New password',
                ],
                'second_options' => [
                    'label' => 'trans.Repeat new password',
                ],
                'type' => PasswordType::class,
                'constraints' => [
                    new Length(
                        [
                            'min' => 4,
                        ]
                    ),
                ],
            ]
        );

        $builder->add('enabled', null, [
            'label' => 'trans.Enabled',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required' => false,
        ]);
    }
}
