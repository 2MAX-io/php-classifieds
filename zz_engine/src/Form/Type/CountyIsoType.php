<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountyIsoType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('choices', $this->getCountryChoices());
        $resolver->setDefault('placeholder', 'trans.Select');
        $resolver->setDefault('choice_translation_domain', false);
    }

    /**
     * @return array<string,string>
     */
    public function getCountryChoices(): array
    {
        $countryNames = \array_flip(Countries::getNames());

        return \array_combine($countryNames, $countryNames);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
