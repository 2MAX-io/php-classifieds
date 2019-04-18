<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Listing;
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

    public function lowSecurityCheckIsAdminInPublic(): bool
    {
        return $this->currentUserService->lowSecurityCheckIsAdminInPublic();
    }

    public function isCurrentUserListing(Listing $listing): bool
    {
        return $this->currentUserService->getUser() === $listing->getUser();
    }

    public function boolText($value): string
    {
        if ($value === true) {
            return 'trans.Yes';
        }

        if ($value === false) {
            return 'trans.No';
        }

        if ($value === '0' || $value === '' || $value === 'false' || $value === 'null') {
            return 'trans.No';
        }

        if (empty(trim($value))) {
            return 'trans.No';
        }

        if ($value === '1' || $value === 1 || $value === 'true') {
            return 'trans.Yes';
        }

        if ($value) {
            return 'trans.Yes';
        }

        return 'trans.No';
    }
}
