<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends \Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @return Page[]
     */
    public function getRelatedPages(): array
    {
        $qb = $this->createQueryBuilder('page');
        $qb->andWhere($qb->expr()->eq('page.enabled', 1));
        $qb->orderBy('page.title', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }
}
