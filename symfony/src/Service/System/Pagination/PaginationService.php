<?php

declare(strict_types=1);

namespace App\Service\System\Pagination;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function createPaginationForQb(QueryBuilder $qb): Pagerfanta
    {
        $adapter = new DoctrineORMAdapter($qb, true, $qb->getDQLPart('having') !== null);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->getMaxPerPage());
        $pager->setNormalizeOutOfRangePages(true);

        return $pager;
    }

    public function getMaxPerPage(): int
    {
        $fromRequest = $this->requestStack->getMasterRequest()->get('perPage', 10);

        return (int) min($fromRequest, 100);
    }
}
