<?php

declare(strict_types=1);

namespace App\Twig;

use App\System\EnvironmentService;
use Twig\Extension\RuntimeExtensionInterface;

class TwigEnvironment implements RuntimeExtensionInterface
{
    /**
     * @var EnvironmentService
     */
    private $environmentService;

    public function __construct(EnvironmentService $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    public function environmentCssClass(): string
    {
        return $this->environmentService->getEnvironmentCssClass();
    }
}
