<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Helper\DateHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YearType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('choices', $this->getChoices());
        $resolver->setDefault('placeholder', 'trans.Select');
        $resolver->setDefault('choice_translation_domain', false);
    }

    /**
     * @return int[]
     */
    public function getChoices(): array
    {
        $choices = [];
        for ($i = DateHelper::date('Y'); $i >= 1980; --$i) {
            $year = (int) $i;
            $choices[$year] = $year;
        }

        return $choices;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
