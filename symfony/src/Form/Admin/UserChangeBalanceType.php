<?php

declare(strict_types=1);

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UserChangeBalanceType extends AbstractType
{
    const NEW_BALANCE = 'newBalance';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            self::NEW_BALANCE, NumberType::class, [
            'mapped' => false,
            'constraints' => [
                new Constraints\NotBlank(),
            ],
            'label' => 'trans.Set balance to',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }
}
