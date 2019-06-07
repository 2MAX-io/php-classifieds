<?php

declare(strict_types=1);

namespace App\Service\Money;

use App\Entity\Payment;
use App\Service\System\Pagination\PaginationDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;

class PaymentHistoryService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaginationService
     */
    private $paginationService;

    public function __construct(EntityManagerInterface $em, PaginationService $paginationService)
    {
        $this->em = $em;
        $this->paginationService = $paginationService;
    }

    public function getPaymentList(int $page): PaginationDto
    {
        $qb = $this->em->getRepository(Payment::class)->createQueryBuilder('payment');
        $qb->orderBy('payment.id', 'DESC');

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        return new PaginationDto($pager->getCurrentPageResults(), $pager);
    }
}
