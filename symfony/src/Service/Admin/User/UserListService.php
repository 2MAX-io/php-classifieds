<?php

declare(strict_types=1);

namespace App\Service\Admin\User;

use App\Entity\User;
use App\Helper\Search;
use App\Service\System\Pagination\PaginationDto;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
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

    public function __construct(EntityManagerInterface $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    public function getUserList(int $page): PaginationDto
    {
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

        $adapter = new DoctrineORMAdapter($qb, true, $qb->getDQLPart('having') !== null);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($page);

        $paginationDto = new PaginationDto($pager->getCurrentPageResults(), $pager);

        return $paginationDto;
    }
}
