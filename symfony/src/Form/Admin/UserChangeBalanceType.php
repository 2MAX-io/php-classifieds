<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Form\Type\CustomMoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class UserChangeBalanceType extends AbstractType
{
    const NEW_BALANCE = 'newBalance';
    const CHANGE_REASON = 'changeReason';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            self::NEW_BALANCE, CustomMoneyType::class, [
            'mapped' => false,
            'constraints' => [
                new Constraints\NotBlank(),
            ],
            'label' => 'trans.Set balance to',
        ]);
        $builder->add(
            self::CHANGE_REASON, TextareaType::class, [
            'mapped' => false,
            'required' => false,
            'label' => 'trans.Change reason in description, visible to user',
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
