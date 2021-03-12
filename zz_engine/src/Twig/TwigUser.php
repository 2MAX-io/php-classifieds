<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
use App\Entity\User;
use App\Enum\ParamEnum;
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

    public function isAdminInPublic(): bool
    {
        return $this->currentUserService->isAdminInPublic();
    }

    public function isCurrentUserListing(Listing $listing): bool
    {
        return $this->currentUserService->getUserOrNull() === $listing->getUser();
    }

    public function userOrAdmin(Listing $listing): bool
    {
        return $this->currentUserService->getUserOrNull() === $listing->getUser() || $this->currentUserService->isAdminInPublic();
    }

    public function displayAsExpired(Listing $listing, bool $showListingPreviewForOwner = false): bool
    {
        if ($this->requestStack->getMasterRequest()) {
            $showListingPreviewForOwner = $this->requestStack->getMasterRequest()->get(
                ParamEnum::SHOW_LISTING_PREVIEW_FOR_OWNER,
                $showListingPreviewForOwner,
            );
        }

        if ($showListingPreviewForOwner
            && (
                $this->currentUserService->getUserOrNull() === $listing->getUser()
                || $this->currentUserService->isAdminInPublic()
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

    public function displayUserName(?User $user): ?string
    {
        if (null === $user) {
            return null;
        }

        if (!$user->getDisplayUsername()) {
            return $this->trans->trans('trans.User').' #'.$user->getId();
        }

        return $user->getDisplayUsername();
    }
}
