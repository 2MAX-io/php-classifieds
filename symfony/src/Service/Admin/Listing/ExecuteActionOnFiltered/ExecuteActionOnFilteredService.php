<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing\ExecuteActionOnFiltered;

use App\Service\Admin\Listing\AdminListingSearchService;
use Doctrine\ORM\EntityManagerInterface;

class ExecuteActionOnFilteredService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AdminListingSearchService
     */
    private $adminListingSearchService;

    public function __construct(EntityManagerInterface $em, AdminListingSearchService $adminListingSearchService)
    {
        $this->em = $em;
        $this->adminListingSearchService = $adminListingSearchService;
    }

    public function setTitle()
    {
        $qb = $this->adminListingSearchService->getQuery();
        $qb->update();
        $qb->set('listing.title', ':title');
        $qb->setParameter('title', 'test' . date(' Y-m-d H:i:s'));
        $qb->andWhere($qb->expr()->eq('listing.id', 412491));
        $qb->setMaxResults(1);
        $qb->resetDQLPart('orderBy');
        $qb->getQuery()->execute();
    }
}
