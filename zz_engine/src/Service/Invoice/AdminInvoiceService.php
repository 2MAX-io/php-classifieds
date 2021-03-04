<?php

declare(strict_types=1);

namespace App\Service\Invoice;

use App\Entity\Invoice;
use App\Service\System\Pagination\Dto\PaginationDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class AdminInvoiceService
{
    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PaginationService $paginationService, EntityManagerInterface $em)
    {
        $this->paginationService = $paginationService;
        $this->em = $em;
    }

    public function getInvoiceList(int $page): PaginationDto
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('invoice');
        $qb->from(Invoice::class, 'invoice');
        $qb->orderBy('invoice.id', Criteria::DESC);

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getPerPage());
        $pager->setCurrentPage($page);

        return new PaginationDto($pager->getCurrentPageResults(), $pager);
    }
}
