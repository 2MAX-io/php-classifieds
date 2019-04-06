<?php

declare(strict_types=1);

namespace App\Service\Admin\User;

use App\Entity\System\Admin;
use App\Helper\SearchHelper;
use App\Service\System\Pagination\Dto\PaginationDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class AdministratorListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaginationService
     */
    private $paginationService;

    public function __construct(
        EntityManagerInterface $em,
        PaginationService $paginationService
    ) {
        $this->em = $em;
        $this->paginationService = $paginationService;
    }

    public function getAdminList(int $page, string $query = null): PaginationDto
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('admin');
        $qb->from(Admin::class, 'admin');
        $qb->orderBy('admin.id', Criteria::DESC);

        if ($query) {
            $qb->andWhere($qb->expr()->like('admin.email', ':query'));
            $qb->setParameter(':query', SearchHelper::optimizeLike($query));
        }

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getPerPage());
        $pager->setCurrentPage($page);

        return new PaginationDto($pager->getCurrentPageResults(), $pager);
    }
}
