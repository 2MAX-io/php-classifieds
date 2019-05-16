<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing\ExecuteActionOnFiltered;

use App\Entity\Category;
use App\Entity\CustomFieldOption;
use App\Entity\Listing;
use App\Service\Admin\Listing\AdminListingSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use http\Exception\UnexpectedValueException;

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
        $qb = $this->getQuery();

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

        $selectSql = $qb->getQuery()->getSQL();

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

        $fields = \str_repeat(', ?', \count($params));
        $selectSql = \preg_replace('#SELECT(.+)FROM(.+)#', 'SELECT $1 '.$fields.' FROM $2', $selectSql);

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare("
REPLACE INTO listing_custom_field_value (id, listing_id, custom_field_id, custom_field_option_id, value)
$selectSql
");
        $stmt->execute($params);
    }

    public function setCategory(Category $category): void
    {
        $this->createTempTableWithFiltered();

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare(
            /** @lang MySQL */ "
UPDATE listing JOIN filtered_id_list ON listing.id = filtered_id_list.id
SET category_id = :category_id 
");
        $stmt->bindValue(':category_id', $category->getId());
        $stmt->execute();
    }

    public function createTempTableWithFiltered(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('CREATE TEMPORARY TABLE filtered_id_list (`id` int(11) UNSIGNED NOT NULL);');
        $stmt->execute();

        $qb = $this->getQuery();
        $qb->addSelect("listing.id");
        $selectSql = $qb->getQuery()->getSQL();

        $params = [];
        /** @var Parameter[] $doctrineQueryParamList */
        $doctrineQueryParamList = $qb->getParameters()->toArray();
        foreach ($doctrineQueryParamList as $key => $doctrineQueryParam) {
            $params[] = $doctrineQueryParam->getValue(); // TODO: incorrect keys
        }

        $stmt = $pdo->prepare("
INSERT INTO filtered_id_list (id)
$selectSql
");
        $stmt->execute($params);
    }

    public function getQuery(): QueryBuilder
    {
        $qb = $this->adminListingSearchService->getQuery();
        $qb->resetDQLPart('select');
        $qb->resetDQLPart('orderBy');

        return $qb;
    }

    public function getAffectedPercentage(): float
    {
        $filteredCount = $this->getAffectedCount();

        $allListingsCount = (int) $this->em->getRepository(Listing::class)->count([]);
        if ($allListingsCount < 1) {
            throw new UnexpectedValueException('there should be some listings');
        }
        $percentage = $filteredCount / $allListingsCount;

        return $percentage;
    }

    public function getAffectedCount(): int
    {
        $filteredQb = $this->getQuery();
        $filteredQb->select('COUNT(1)');
        $filteredCount = $filteredQb->getQuery()->getSingleScalarResult();

        return (int) $filteredCount;
    }
}
