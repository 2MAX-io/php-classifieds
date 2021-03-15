<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppMoneyType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('grouping', true);
        $resolver->setDefault('attr', [
            'class' => 'js__inputMoney',
        ]);
    }

    public function getParent(): string
    {
        return AppNumberType::class;
    }
}
