<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Service\Setting\SettingsService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomMoneyType extends AbstractType
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('currency', $this->settingsService->getCurrency());
        $resolver->setDefault('grouping', true);
        $resolver->setDefault('attr',  [
            'class' => 'input-money',
        ]);
    }

    public function getParent()
    {
        return MoneyType::class;
    }
}
