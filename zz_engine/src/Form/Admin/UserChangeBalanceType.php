<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Form\Type\AppMoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserChangeBalanceType extends AbstractType
{
    const NEW_BALANCE = 'newBalance';
    const CHANGE_REASON = 'changeReason';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            self::NEW_BALANCE, AppMoneyType::class, [
            'label' => 'trans.Set balance to',
            'mapped' => false,
            'constraints' => [
                new NotBlank(),
                new GreaterThanOrEqual(['value' => 0])
            ],
        ]);
        $builder->add(
            self::CHANGE_REASON, TextareaType::class, [
            'mapped' => false,
            'required' => false,
            'label' => 'trans.Change reason in description, visible to user',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }
}
