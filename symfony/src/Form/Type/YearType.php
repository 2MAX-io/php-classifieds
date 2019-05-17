<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YearType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('choices', $this->getChoices());
        $resolver->setDefault('placeholder', 'trans.Select');
        $resolver->setDefault('choice_translation_domain', false);
    }

    public function getChoices(): array
    {
        $choices = [];
        for ($i=date('Y'); $i >= 1900; --$i) {
            $choices[$i] = $i;
        }

        return $choices;
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
