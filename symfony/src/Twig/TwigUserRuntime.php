<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use App\Security\CurrentUserService;
use Twig\Extension\RuntimeExtensionInterface;

class TwigUserRuntime implements RuntimeExtensionInterface
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(CurrentUserService $currentUserService)
    {
        $this->currentUserService = $currentUserService;
    }

    public function displayAsExpired(Listing $listing): bool
    {
        if ($this->currentUserService->getUser() === $listing->getUser() || $this->currentUserService->lowSecurityCheckIsAdminInPublic()) {
            return false;
        }

        return $listing->getUserRemoved()
            || $listing->getUserDeactivated()
            || $listing->getAdminRemoved()
            || $listing->getAdminRejected()
            || !$listing->getAdminConfirmed()
            || $listing->getValidUntilDate() < new \DateTime();
    }
}
