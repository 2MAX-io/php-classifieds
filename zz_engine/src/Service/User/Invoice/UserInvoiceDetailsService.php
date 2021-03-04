<?php

declare(strict_types=1);

namespace App\Service\User\Invoice;

use App\Entity\UserInvoiceDetails;
use App\Helper\DateHelper;
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
            $currentDatetime = DateHelper::create();
            $userInvoiceDetails = new UserInvoiceDetails();
            $userInvoiceDetails->setUser($user);
            $userInvoiceDetails->setCreatedDate($currentDatetime);
            $userInvoiceDetails->setUpdatedDate($currentDatetime);
        }

        return $userInvoiceDetails;
    }
}
