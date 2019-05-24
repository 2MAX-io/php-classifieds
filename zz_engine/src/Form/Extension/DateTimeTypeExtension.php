<?php

declare(strict_types=1);

namespace App\Form\Extension;

use App\System\EnvironmentService;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EnvironmentService
     */
    private $environmentService;

    public function __construct(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'view_timezone' => $this->environmentService->getAppTimezone(),
        ));
    }

    public static function getExtendedTypes(): iterable
    {
        return [DateTimeType::class];
    }
}
