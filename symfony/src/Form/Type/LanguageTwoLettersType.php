<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageTwoLettersType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('choices', $this->getLanguagesList());
        $resolver->setDefault('placeholder', 'trans.Select');
        $resolver->setDefault('translation_domain', false);
    }

    public function getLanguagesList(): array
    {
        $countryNames = \array_flip(Intl::getRegionBundle()->getCountryNames());
        return \array_combine($countryNames, $countryNames);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
