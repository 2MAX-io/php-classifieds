<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use App\Security\CurrentUserService;
use App\Service\Setting\SettingsService;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        CurrentUserService $currentUserService,
        SettingsService $settingsService,
        RequestStack $requestStack
    ) {
        $this->currentUserService = $currentUserService;
        $this->settingsService = $settingsService;
        $this->requestStack = $requestStack;
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

    public function displayAsExpired(Listing $listing, bool $showPreviewForOwner = false): bool
    {
        $showPreviewForOwner = $this->requestStack->getMasterRequest()->query->get('showPreviewForOwner', $showPreviewForOwner);

        /** @noinspection NotOptimalIfConditionsInspection */
        if ($showPreviewForOwner &&
            (
                $this->currentUserService->getUser() === $listing->getUser()
                || $this->currentUserService->lowSecurityCheckIsAdminInPublic()
            )
        ) {
            return false;
        }

        return $listing->getUserRemoved()
            || $listing->getUserDeactivated()
            || $listing->getAdminRemoved()
            || $listing->getAdminRejected()
            || (!$listing->getAdminActivated() && $this->settingsService->getSettingsDto()->getRequireListingAdminActivation())
            || $listing->isExpired();
    }
}
