<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class AppNumberType extends AbstractType
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->resetViewTransformers();
        $builder->addViewTransformer(new NumberToLocalizedStringTransformer(
            $options['scale'],
            $options['grouping'],
            $options['rounding_mode'],
            'en'
        ));
        $builder->addViewTransformer(new CallbackTransformer(
            function ($value) {
                return \strtr($value, [
                    ',' => $this->settingsDto->getThousandSeparator() ?? '',
                    '.' => $this->settingsDto->getDecimalSeparator() ?? '.',
                ]);
            },
            function ($value) {
                $replace = [
                    $this->settingsDto->getDecimalSeparator() ?? '.' => '.',
                ];
                if ('' !== $this->settingsDto->getThousandSeparator()) {
                    $replace[$this->settingsDto->getThousandSeparator()] = ',';
                }

                return \strtr($value, $replace);
            }
        ));
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}
