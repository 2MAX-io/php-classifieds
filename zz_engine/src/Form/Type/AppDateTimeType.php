<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\System\EnvironmentService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppDateTimeType extends AbstractType
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
        $resolver->setDefault('view_timezone', $this->environmentService->getAppTimezone());
    }

    public function getParent(): string
    {
        return DateTimeType::class;
    }
}
