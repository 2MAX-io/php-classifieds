<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing\ExecuteActionOnFiltered;

use App\Entity\CustomFieldOption;
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

    public function addCustomField(CustomFieldOption $customFieldOption): void
    {
        $qb = $this->adminListingSearchService->getQuery();
        $qb->resetDQLPart('select');
        $qb->addSelect("listingCustomFieldValue.id");
        $qb->addSelect("listing.id");
        $qb->join('listing.category', 'category');
        $qb->join('category.customFieldsJoin', 'categoryCustomFieldJoin');
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValue');

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->andX(
                $qb->expr()->eq('listingCustomFieldValue.customField', ':customField'),
                $qb->expr()->neq('listingCustomFieldValue.customFieldOption', ':customFieldOption'),
                $qb->expr()->neq('listingCustomFieldValue.value', ':value')
            ),
            $qb->expr()->isNull('listingCustomFieldValue.id')
        ));
        $qb->andWhere($qb->expr()->eq('categoryCustomFieldJoin.customField', ':categoryCustomField'));
        $qb->setParameter('customField', $customFieldOption->getCustomField()->getId());
        $qb->setParameter('customFieldOption', $customFieldOption->getId());
        $qb->setParameter('value', $customFieldOption->getValue());
        $qb->setParameter('categoryCustomField', $customFieldOption->getCustomField()->getId());

//        $qb->andWhere($qb->expr()->eq('listing.id', 412490));
//        $qb->setMaxResults(10);
        $qb->resetDQLPart('orderBy');
        $qb->getQuery()->execute();

        $selectSql = $qb->getQuery()->getSQL();
        $qb->getParameters()->toArray();

        $params = [
            $customFieldOption->getCustomField()->getId(),
            $customFieldOption->getId(),
            $customFieldOption->getValue()
        ];
        /** @var Parameter[] $doctrineQueryParamList */
        $doctrineQueryParamList = $qb->getParameters()->toArray();
        foreach ($doctrineQueryParamList as $key => $doctrineQueryParam) {
            $params[] = $doctrineQueryParam->getValue(); // TODO: incorrect keys
        }

        $fields = ", ?, ?, ?";
        $selectSql = \preg_replace('#SELECT(.+)FROM(.+)#', 'SELECT $1 '.$fields.' FROM $2', $selectSql);

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare("
REPLACE INTO listing_custom_field_value (id, listing_id, custom_field_id, custom_field_option_id, value)
$selectSql
");
        $stmt->execute($params);
    }
}
