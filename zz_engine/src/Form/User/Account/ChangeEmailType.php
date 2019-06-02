<?php

declare(strict_types=1);

namespace App\Form\User\Account;

use App\Validator\Constraints\UserEmailNotTaken;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeEmailType extends AbstractType
{
    public const FORM_NEW_EMAIL = 'newEmail';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('currentPassword', PasswordType::class, [
            'label' => 'trans.Enter current password to confirm email change',
            'constraints' => [
                new NotBlank(),
                new UserPassword(),
            ],
        ]);

        $builder->add(
            static::FORM_NEW_EMAIL,
            RepeatedType::class,
            [
                'first_options' => [
                    'label' => 'trans.New email',
                ],
                'second_options' => [
                    'label' => 'trans.Repeat new email',
                ],
                'type' => EmailType::class,
                'constraints' => [
                    new NotBlank(),
                    new Email([
                        'mode' => Email::VALIDATION_MODE_STRICT
                    ]),
                    new UserEmailNotTaken(),
                ],
            ]
        );
    }
}
