<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use App\Security\CurrentUserService;
use App\Service\Setting\SettingsService;
use Twig\Extension\RuntimeExtensionInterface;

class TwigUser implements RuntimeExtensionInterface
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(CurrentUserService $currentUserService, SettingsService $settingsService)
    {
        $this->currentUserService = $currentUserService;
        $this->settingsService = $settingsService;
    }

    public function lowSecurityCheckIsAdminInPublic(): bool
    {
        return $this->currentUserService->lowSecurityCheckIsAdminInPublic();
    }

    public function isCurrentUserListing(Listing $listing): bool
    {
        return $this->currentUserService->getUser() === $listing->getUser();
    }

    public function userOrAdmin(Listing $listing): bool
    {
        return $this->currentUserService->getUser() === $listing->getUser() || $this->currentUserService->lowSecurityCheckIsAdminInPublic();
    }

    public function displayAsExpired(Listing $listing): bool
    {
        if ($this->currentUserService->getUser() === $listing->getUser() || $this->currentUserService->lowSecurityCheckIsAdminInPublic()) {
            return false;
        }

        return $this->displayAsExpiredForEveryone($listing);
    }

    public function displayAsExpiredForEveryone(Listing $listing): bool
    {
        return $listing->getUserRemoved()
            || $listing->getUserDeactivated()
            || $listing->getAdminRemoved()
            || $listing->getAdminRejected()
            || (!$listing->getAdminActivated() && $this->settingsService->getSettingsDto()->getRequireListingAdminActivation())
            || $listing->isExpired();
    }
}
