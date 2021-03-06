<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceForType extends AbstractType
{
    /**
     * @return array<string,string>
     */
    public static function getPricePerChoices(): array
    {
        return [
            'trans.priceFor.PRICE_FOR_WHOLE' => Listing::PRICE_FOR_WHOLE,
            'trans.priceFor.PRICE_FOR_ITEM' => Listing::PRICE_FOR_ITEM,
            'trans.priceFor.PRICE_FOR_SEE_DESCRIPTION' => Listing::PRICE_FOR_SEE_DESCRIPTION,
            'trans.priceFor.PRICE_FOR_NETTO' => Listing::PRICE_FOR_NETTO,
            'trans.priceFor.PRICE_FOR_BRUTTO' => Listing::PRICE_FOR_BRUTTO,
            'trans.priceFor.PRICE_FOR_TONE' => Listing::PRICE_FOR_TONE,
            'trans.priceFor.PRICE_FOR_METER' => Listing::PRICE_FOR_METER,
            'trans.priceFor.PRICE_FOR_KILOMETER' => Listing::PRICE_FOR_KILOMETER,
            'trans.priceFor.PRICE_FOR_HECTARE' => Listing::PRICE_FOR_HECTARE,
            'trans.priceFor.PRICE_FOR_ACRE' => Listing::PRICE_FOR_ACRE,
            'trans.priceFor.PRICE_FOR_AR' => Listing::PRICE_FOR_AR,
            'trans.priceFor.PRICE_FOR_MINUTE' => Listing::PRICE_FOR_MINUTE,
            'trans.priceFor.PRICE_FOR_HOUR' => Listing::PRICE_FOR_HOUR,
            'trans.priceFor.PRICE_FOR_MONTH' => Listing::PRICE_FOR_MONTH,
            'trans.priceFor.PRICE_FOR_WEEK' => Listing::PRICE_FOR_WEEK,
            'trans.priceFor.PRICE_FOR_DAY' => Listing::PRICE_FOR_DAY,
            'trans.priceFor.PRICE_FOR_YEAR' => Listing::PRICE_FOR_YEAR,
            'trans.priceFor.PRICE_FOR_LITER' => Listing::PRICE_FOR_LITER,
            'trans.priceFor.PRICE_FOR_BAG' => Listing::PRICE_FOR_BAG,
            'trans.priceFor.PRICE_FOR_PACK' => Listing::PRICE_FOR_PACK,
            'trans.priceFor.PRICE_FOR_SET' => Listing::PRICE_FOR_SET,
            'trans.priceFor.PRICE_FOR_STERE' => Listing::PRICE_FOR_STERE,
            'trans.priceFor.PRICE_FOR_CM' => Listing::PRICE_FOR_CM,
            'trans.priceFor.PRICE_FOR_GRAM' => Listing::PRICE_FOR_GRAM,
            'trans.priceFor.PRICE_FOR_KILOGRAM' => Listing::PRICE_FOR_KILOGRAM,
            'trans.priceFor.PRICE_FOR_MILE' => Listing::PRICE_FOR_MILE,
            'trans.priceFor.PRICE_FOR_YARD' => Listing::PRICE_FOR_YARD,
            'trans.priceFor.PRICE_FOR_FOOT' => Listing::PRICE_FOR_FOOT,
            'trans.priceFor.PRICE_FOR_POUND' => Listing::PRICE_FOR_POUND,
            'trans.priceFor.PRICE_FOR_OUNCE' => Listing::PRICE_FOR_OUNCE,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('placeholder', '???');
        $resolver->setDefault('choices', static::getPricePerChoices());

        $resolver->setDefault('label', 'trans.Amount per');
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
