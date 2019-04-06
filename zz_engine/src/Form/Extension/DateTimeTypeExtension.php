<?php

declare(strict_types=1);

namespace App\Form\Extension;

use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeTypeExtension extends AbstractTypeExtension
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
     * @return iterable<mixed, class-string<FormTypeInterface>>
     */
    public static function getExtendedTypes(): iterable
    {
        return [DateTimeType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'view_timezone' => $this->settingsDto->getTimezone(),
        ]);
    }
}
