<?php

declare(strict_types=1);

namespace App\Form\User\Account\Register;

use App\Validator\Constraints\UserEmailNotTaken;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Email',
            'constraints' => [
                new NotBlank(),
                new Email([
                    'mode' => Email::VALIDATION_MODE_STRICT,
                ]),
                new UserEmailNotTaken(),
            ],
        ]);
        $builder->add('password', RepeatedType::class, [
            'first_options' => [
                'label' => 'trans.Password',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'second_options' => [
                'label' => 'trans.Repeat password',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'type' => PasswordType::class,
            'invalid_message' => 'Repeated value does note match',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 8, 'allowEmptyString' => false]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisterUserDto::class,
        ]);
    }
}
