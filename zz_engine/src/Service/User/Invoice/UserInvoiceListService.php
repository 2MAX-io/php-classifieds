<?php

declare(strict_types=1);

namespace App\Service\User\Invoice;

use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use App\Security\CurrentUserService;

class UserInvoiceListService
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        CurrentUserService $currentUserService
    ) {
        $this->currentUserService = $currentUserService;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @return Invoice[]
     */
    public function getInvoiceListForCurrentUser(): array
    {
        return $this->invoiceRepository->getListForUser($this->currentUserService->getUser());
    }
}
