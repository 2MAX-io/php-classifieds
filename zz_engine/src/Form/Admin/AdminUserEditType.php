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
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminUserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', RepeatedType::class, [
            'first_options' => [
                'label' => 'trans.Email',
            ],
            'second_options' => [
                'label' => 'trans.Repeat new email',
            ],
            'type' => TextType::class,
            'invalid_message' => 'Repeated value does note match',
            'constraints' => [
                new NotBlank(),
                new Email(['mode' => Email::VALIDATION_MODE_STRICT]),
            ],
        ]);
        $builder->add('username', RepeatedType::class, [
            'first_options' => [
                'label' => 'trans.Username',
            ],
            'second_options' => [
                'label' => 'trans.Repeat new username',
            ],
            'type' => TextType::class,
            'invalid_message' => 'Repeated value does note match',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2, 'allowEmptyString' => false]),
            ],
        ]);
        $builder->add('plainPassword', RepeatedType::class, [
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
                new Length(['min' => 8, 'allowEmptyString' => false]),
            ],
        ]);
        $builder->add('enabled', null, [
            'label' => 'trans.Enabled',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'required' => false,
        ]);
    }
}
