<?php

declare(strict_types=1);

namespace App\Twig;

use App\Security\CurrentUserService;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(CurrentUserService $currentUserService)
    {
        $this->currentUserService = $currentUserService;
    }

    public function isAdmin(): bool
    {
        return $this->currentUserService->isAdmin();
    }
}
