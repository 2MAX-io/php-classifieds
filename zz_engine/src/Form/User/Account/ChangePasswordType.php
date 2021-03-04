<?php

declare(strict_types=1);

namespace App\Form\User\Account;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public const FORM_CURRENT_PASSWORD = 'currentPassword';
    public const FORM_NEW_PASSWORD = 'newPassword';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::FORM_CURRENT_PASSWORD, PasswordType::class, [
            'label' => 'trans.Current password',
            'constraints' => [
                new NotBlank(),
                new UserPassword(),
            ],
        ]);

        $builder->add(static::FORM_NEW_PASSWORD, RepeatedType::class, [
            'first_options' => [
                'label' => 'trans.New password',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'second_options' => [
                'label' => 'trans.Repeat new password',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'type' => PasswordType::class,
            'invalid_message' => 'Repeated value does note match',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 8, 'max' => 100]),
            ],
        ]);
    }
}
