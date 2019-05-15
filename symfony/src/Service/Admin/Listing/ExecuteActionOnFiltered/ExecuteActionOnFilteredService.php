<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing\ExecuteActionOnFiltered;

use App\Service\Admin\Listing\AdminListingSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;

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

    public function addCustomField(): void
    {
        $qb = $this->adminListingSearchService->getQuery();
        $qb->select("listing.id, 1, 2, 'fiat'");
        $qb->andWhere($qb->expr()->eq('listing.id', 412490));
        $qb->setMaxResults(1);
        $qb->resetDQLPart('orderBy');
        $qb->getQuery()->execute();

        $selectSql = $qb->getQuery()->getSQL();
        $qb->getParameters()->toArray();
        time();

        $params = [];
        /** @var Parameter[] $doctrineQueryParamList */
        $doctrineQueryParamList = $qb->getParameters()->toArray();
        foreach ($doctrineQueryParamList as $key => $doctrineQueryParam) {
            $params[$key] = $doctrineQueryParam->getValue();
        }

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare("
INSERT INTO listing_custom_field_value (listing_id, custom_field_id, custom_field_option_id, value)
$selectSql
");
        $stmt->execute($params);
    }
}
