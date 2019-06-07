<?php

declare(strict_types=1);

namespace App\Service\Admin\User;

use App\Entity\User;
use App\Helper\Search;
use App\Service\System\Pagination\PaginationDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PaginationService
     */
    private $paginationService;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        PaginationService $paginationService
    ) {
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->paginationService = $paginationService;
    }

    public function getUserList(int $page): PaginationDto
    {
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        $qb = $this->em->getRepository(User::class)->createQueryBuilder('user');
        $qb->orderBy('user.id', 'DESC');

        if (!empty($request->get('query', false))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('user.username', ':query'),
                $qb->expr()->like('user.email', ':query')
            ));
            $qb->setParameter(':query', Search::optimizeLike($request->get('query')));
        }

        $pager = $this->paginationService->createPaginationForQb($qb);
        $pager->setMaxPerPage($this->paginationService->getMaxPerPage());
        $pager->setCurrentPage($page);

        return new PaginationDto($pager->getCurrentPageResults(), $pager);
    }
}
