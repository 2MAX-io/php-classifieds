<?php

declare(strict_types=1);

namespace App\Service\User\Invoice;

use App\Entity\UserInvoiceDetails;
use App\Repository\UserInvoiceDetailsRepository;
use App\Security\CurrentUserService;

class UserInvoiceDetailsService
{
    /**
     * @var UserInvoiceDetailsRepository
     */
    private $userInvoiceDetailsRepository;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(
        UserInvoiceDetailsRepository $userInvoiceDetailsRepository,
        CurrentUserService $currentUserService
    ) {
        $this->userInvoiceDetailsRepository = $userInvoiceDetailsRepository;
        $this->currentUserService = $currentUserService;
    }

    public function getOrCreateUserInvoiceDetails(): UserInvoiceDetails
    {
        $user = $this->currentUserService->getUser();
        $userInvoiceDetails = $this->userInvoiceDetailsRepository->findByUser($user);
        if (null === $userInvoiceDetails) {
            $userInvoiceDetails = new UserInvoiceDetails();
            $userInvoiceDetails->setUser($user);
            $userInvoiceDetails->setCreatedDate(new \DateTime());
            $userInvoiceDetails->setUpdatedDate(new \DateTime());
        }

        return $userInvoiceDetails;
    }
}
