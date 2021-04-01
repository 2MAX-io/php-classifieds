<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Type\AppMoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class TopUpBalanceType extends AbstractType
{
    public const TOP_UP_AMOUNT = 'topUpAmount';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(self::TOP_UP_AMOUNT, AppMoneyType::class, [
            'mapped' => false,
            'label' => 'trans.Top up amount',
            'attr' => [
                'class' => 'js__inputMoney',
            ],
            'constraints' => [
                new NotBlank(),
                new GreaterThanOrEqual(['value' => 0]),
            ],
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
