<?php

declare(strict_types=1);

namespace App\Service\Invoice;

use App\Entity\Invoice;
use App\Service\System\Pagination\PaginationDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceService
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

    public function getInvoiceList(int $page): PaginationDto
    {
        $qb = $this->em->getRepository(Invoice::class)->createQueryBuilder('invoice');
        $qb->orderBy('invoice.id', 'DESC');

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        return new PaginationDto($pager->getCurrentPageResults(), $pager);

    }
}
