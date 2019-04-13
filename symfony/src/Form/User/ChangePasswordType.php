<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public const FORM_OLD_PASSWORD = 'oldPassword';
    public const FORM_NEW_PASSWORD = 'newPassword';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FORM_OLD_PASSWORD, PasswordType::class, [
            'label' => 'trans.Current password',
            'constraints' => [
                new NotBlank([]),
                new UserPassword(),
            ],
        ]);

        $builder->add(
            static::FORM_NEW_PASSWORD,
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
                    new NotBlank(),
                    new Length(
                        [
                            'min' => 8,
                        ]
                    ),
                ],
            ]
        );
    }
}
