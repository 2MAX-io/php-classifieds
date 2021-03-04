<?php

declare(strict_types=1);

namespace App\Service\Admin\User;

use App\Entity\User;
use App\Helper\SearchHelper;
use App\Service\System\Pagination\Dto\PaginationDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class UserListService
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

    public function getUserList(int $page, string $searchQuery = null): PaginationDto
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('user');
        $qb->from(User::class, 'user');
        $qb->orderBy('user.id', Criteria::DESC);

        if ($searchQuery) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('user.username', ':query'),
                $qb->expr()->like('user.email', ':query')
            ));
            $qb->setParameter(':query', SearchHelper::optimizeLike($searchQuery));
        }

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getPerPage());
        $pager->setCurrentPage($page);

        return new PaginationDto($pager->getCurrentPageResults(), $pager);
    }
}
