<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoolType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('choices', [
            'trans.Yes' => 1,
            'trans.No' => 0,
        ]);

        $resolver->setDefault('placeholder', 'trans.not required');
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
