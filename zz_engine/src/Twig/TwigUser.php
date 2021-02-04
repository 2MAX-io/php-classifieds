<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use App\Entity\User;
use App\Security\CurrentUserService;
use App\Service\Setting\SettingsService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
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

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(
        CurrentUserService $currentUserService,
        SettingsService $settingsService,
        RequestStack $requestStack,
        TranslatorInterface $trans
    ) {
        $this->currentUserService = $currentUserService;
        $this->settingsService = $settingsService;
        $this->requestStack = $requestStack;
        $this->trans = $trans;
    }

    public function lowSecurityCheckIsAdminInPublic(): bool
    {
        return $this->currentUserService->lowSecurityCheckIsAdminInPublic();
    }

    public function isCurrentUserListing(Listing $listing): bool
    {
        return $this->currentUserService->getUserOrNull() === $listing->getUser();
    }

    public function userOrAdmin(Listing $listing): bool
    {
        return $this->currentUserService->getUserOrNull() === $listing->getUser() || $this->currentUserService->lowSecurityCheckIsAdminInPublic();
    }

    public function displayAsExpired(Listing $listing, bool $showPreviewForOwner = false): bool
    {
        $showPreviewForOwner = $this->requestStack->getMasterRequest()->query->get('showPreviewForOwner', $showPreviewForOwner);

        if ($showPreviewForOwner &&
            (
                $this->currentUserService->getUserOrNull() === $listing->getUser()
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

    public function displayUserName(User $user): string
    {
        if (!$user->getDisplayUsername()) {
            return $this->trans->trans('trans.User') . ' #' .$user->getId();
        }

        return $user->getDisplayUsername();
    }
}
