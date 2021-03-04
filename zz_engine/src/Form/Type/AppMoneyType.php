<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppMoneyType extends AbstractType
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('currency', $this->settingsDto->getCurrency());
        $resolver->setDefault('grouping', true);
        $resolver->setDefault('attr', [
            'class' => 'js__inputMoney',
        ]);
    }

    public function getParent(): string
    {
        return MoneyType::class;
    }
}
