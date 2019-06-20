<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing\ExecuteActionOnFiltered;

use App\Entity\Category;
use App\Entity\CustomFieldOption;
use App\Entity\Listing;
use App\Helper\Sql;
use App\Service\Admin\Listing\AdminListingSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

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

        $qb->addSelect('listingCustomFieldValueExceptChanged.id');
        $qb->addSelect('listing.id');

        if (!\in_array('category', $qb->getAllAliases(), true)) {
            $qb->join('listing.category', 'category');
        }

        if (!\in_array('categoryCustomFieldJoin', $qb->getAllAliases(), true)) {
            $qb->join('category.customFieldsJoin', 'categoryCustomFieldJoin');
        }

        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValueExceptChanged', Join::WITH, $qb->expr()->andX(
            $qb->expr()->eq('listingCustomFieldValueExceptChanged.customField', ':customField'),
            $qb->expr()->eq('listingCustomFieldValueExceptChanged.customFieldOption', ':customFieldOption'),
            $qb->expr()->eq('listingCustomFieldValueExceptChanged.value', ':value')
        ));

        $qb->andWhere($qb->expr()->isNull('listingCustomFieldValueExceptChanged.id'));
        $qb->andWhere($qb->expr()->eq('categoryCustomFieldJoin.customField', ':categoryCustomField'));
        $qb->setParameter(':customField', $customFieldOption->getCustomFieldNotNull()->getId());
        $qb->setParameter(':customFieldOption', $customFieldOption->getId());
        $qb->setParameter(':value', $customFieldOption->getValue());
        $qb->setParameter(':categoryCustomField', $customFieldOption->getCustomFieldNotNull()->getId());

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('CREATE TEMPORARY TABLE filtered_id_list (`id` int(11) UNSIGNED NOT NULL, `listing_id` int(11) UNSIGNED NOT NULL);');
        $stmt->execute();

        $selectSql = $qb->getQuery()->getSQL();
        $stmt = $pdo->prepare(<<<TAG
# noinspection SqlResolveForFile

INSERT INTO filtered_id_list (id, listing_id)
$selectSql #safe, because contains only not bound prepared statement placeholders, that are bound latter
TAG
);
        $stmt->execute(Sql::getParametersFromQb($qb));

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare(<<<'TAG'
# noinspection SqlResolveForFile
REPLACE INTO listing_custom_field_value (id, listing_id, custom_field_id, custom_field_option_id, value)
SELECT id, listing_id, :customField, :customFieldOption, :val FROM filtered_id_list
TAG
);
        $stmt->bindValue(':customField', $customFieldOption->getCustomFieldNotNull()->getId());
        $stmt->bindValue(':customFieldOption', $customFieldOption->getId());
        $stmt->bindValue(':val', $customFieldOption->getValue());
        $stmt->execute();
    }

    public function setCategory(Category $category): void
    {
        $this->createTempTableWithFiltered();

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare(<<<'TAG'
# noinspection SqlResolveForFile
UPDATE listing JOIN filtered_id_list ON listing.id = filtered_id_list.id
SET category_id = :category_id 
WHERE 1
TAG
);
        $stmt->bindValue(':category_id', $category->getId());
        $stmt->execute();
    }

    public function createTempTableWithFiltered(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('CREATE TEMPORARY TABLE filtered_id_list (`id` int(11) UNSIGNED NOT NULL);');
        $stmt->execute();

        $qb = $this->getQuery();
        $qb->addSelect('listing.id');
        $selectSql = $qb->getQuery()->getSQL();

        $stmt = $pdo->prepare(<<<TAG
# noinspection SqlResolveForFile
INSERT INTO filtered_id_list (id)
$selectSql #safe, because contains only not bound prepared statement placeholders, that are bound latter
TAG
);
        $stmt->execute(Sql::getParametersFromQb($qb));
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
            throw new \UnexpectedValueException('there should be some listings');
        }

        return $filteredCount / $allListingsCount;
    }

    public function getAffectedCount(): int
    {
        $filteredQb = $this->getQuery();
        $filteredQb->select('COUNT(1)');
        $filteredCount = $filteredQb->getQuery()->getSingleScalarResult();

        return (int) $filteredCount;
    }
}
