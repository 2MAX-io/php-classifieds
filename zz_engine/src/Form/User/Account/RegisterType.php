<?php

declare(strict_types=1);

namespace App\Form\User\Account;

use App\Validator\Constraints\UserEmailNotTaken;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public const EMAIL_FIELD = 'email';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::EMAIL_FIELD, EmailType::class, [
            'label' => 'trans.Email',
            'constraints' => [
                new NotBlank(),
                new Email([
                    'mode' => Email::VALIDATION_MODE_STRICT,
                ]),
                new UserEmailNotTaken(),
            ],
        ]);
    }
}
