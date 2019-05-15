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
        $qb->resetDQLPart('select');
        $qb->addSelect("listingCustomFieldValue.id");
        $qb->addSelect("listing.id");
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValue');

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->andX(
                $qb->expr()->eq('listingCustomFieldValue.customField', ':customField'),
                $qb->expr()->neq('listingCustomFieldValue.customFieldOption', ':customFieldOption'),
                $qb->expr()->neq('listingCustomFieldValue.value', ':value')
            ),
            $qb->expr()->isNull('listingCustomFieldValue.id')
        ));
        $qb->setParameter('customField', 1);
        $qb->setParameter('customFieldOption', 2);
        $qb->setParameter('value', 'fiat');

        $qb->andWhere($qb->expr()->eq('listing.id', 412490));
        $qb->setMaxResults(1);
        $qb->resetDQLPart('orderBy');
        $qb->getQuery()->execute();

        $selectSql = $qb->getQuery()->getSQL();
        $qb->getParameters()->toArray();

        $params = [1, 2, 'fiat'];
        /** @var Parameter[] $doctrineQueryParamList */
        $doctrineQueryParamList = $qb->getParameters()->toArray();
        foreach ($doctrineQueryParamList as $key => $doctrineQueryParam) {
            $params[] = $doctrineQueryParam->getValue();
        }

        $fields = ", ?, ?, ?";
        $selectSql = \preg_replace('#SELECT(.+)FROM(.+)#', 'SELECT $1 '.$fields.' FROM $2', $selectSql);

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare("
INSERT INTO listing_custom_field_value (id, listing_id, custom_field_id, custom_field_option_id, value)
$selectSql
");
        $stmt->execute($params);
    }
}
