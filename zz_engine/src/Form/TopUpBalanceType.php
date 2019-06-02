<?php

declare(strict_types=1);

namespace App\Form;

use App\Service\Setting\SettingsService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class TopUpBalanceType extends AbstractType
{
    public const TOP_UP_AMOUNT = 'topUpAmount';

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            self::TOP_UP_AMOUNT,
            MoneyType::class,
            [
                'mapped' => false,
                'label' => 'trans.Top up amount',
                'attr' => [
                    'class' => 'input-money',
                ],
                'currency' => $this->settingsService->getCurrency(),
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(['value' => 0])
                ],
            ]
        );
        $builder->add(
            'accept',
            CheckboxType::class,
            [
                'mapped' => false,
                'required' => true,
                'label' => 'trans.I accept and confirm, that the paid but unused funds are not refundable',
                'constraints' => [
                    new NotBlank(),
                ]
            ]
        );
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
