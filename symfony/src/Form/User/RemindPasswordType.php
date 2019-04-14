<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RemindPasswordType extends AbstractType
{
    public const FORM_NEW_EMAIL = 'newEmail';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Enter your email address',
            'constraints' => [
                new NotBlank(),
                new Email([
                    'mode' => Email::VALIDATION_MODE_STRICT
                ]),
            ],
        ]);
    }
}
