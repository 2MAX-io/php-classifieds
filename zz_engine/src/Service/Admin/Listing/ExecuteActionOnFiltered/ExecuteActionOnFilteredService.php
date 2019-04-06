<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing\ExecuteActionOnFiltered;

use App\Entity\Category;
use App\Entity\CustomFieldOption;
use App\Form\Admin\ExecuteAction\ExecuteActionDto;
use App\Helper\SqlHelper;
use App\Repository\ListingRepository;
use App\Service\Admin\Listing\AdminListingSearchService;
use App\Service\Admin\Listing\Dto\AdminListingListDto;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class ExecuteActionOnFilteredService
{
    /**
     * @var AdminListingSearchService
     */
    private $adminListingSearchService;

    /**
     * @var ListingRepository
     */
    private $listingRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        AdminListingSearchService $adminListingSearchService,
        ListingRepository $listingRepository,
        EntityManagerInterface $em
    ) {
        $this->adminListingSearchService = $adminListingSearchService;
        $this->listingRepository = $listingRepository;
        $this->em = $em;
    }

    public function addCustomField(ExecuteActionDto $executeActionDto): void
    {
        /** @var CustomFieldOption $customFieldOptionToSet */
        $customFieldOptionToSet = $executeActionDto->getCustomFieldOption();

        $qb = $this->getQuery($executeActionDto->getAdminListingListDto());
        $qb->addSelect('listing.id');
        $qb->addSelect('listingCustomFieldValue.id'); // can be null

        if (!\in_array('category', $qb->getAllAliases(), true)) {
            $qb->join('listing.category', 'category');
        }

        if (!\in_array('customFieldForCategory', $qb->getAllAliases(), true)) {
            $qb->join('category.customFieldForCategoryList', 'customFieldForCategory');
        }

        // check that custom field value not set already, even to different value
        $qb->leftJoin(
            'listing.listingCustomFieldValues',
            'listingCustomFieldValue',
            Join::WITH,
            (string) $qb->expr()->andX(
                $qb->expr()->eq('listingCustomFieldValue.customField', ':customField'),
            )
        );
        $qb->andWhere($qb->expr()->isNull('listingCustomFieldValue.id')); // do not change if value set

        // skip if custom field value already set
        $qb->leftJoin(
            'listing.listingCustomFieldValues',
            'listingCustomFieldValueAlreadySet',
            Join::WITH,
            (string) $qb->expr()->andX(
                $qb->expr()->eq('listingCustomFieldValueAlreadySet.customField', ':customField'),
                $qb->expr()->eq('listingCustomFieldValueAlreadySet.customFieldOption', ':customFieldOption'),
                $qb->expr()->eq('listingCustomFieldValueAlreadySet.value', ':customFieldValue')
            )
        );
        $qb->andWhere($qb->expr()->isNull('listingCustomFieldValueAlreadySet.id'));

        $qb->andWhere($qb->expr()->eq('customFieldForCategory.customField', ':customField'));
        $qb->setParameter(':customField', $customFieldOptionToSet->getCustomFieldNotNull()->getId());
        $qb->setParameter(':customFieldOption', $customFieldOptionToSet->getId());
        $qb->setParameter(':customFieldValue', $customFieldOptionToSet->getValue());

        $pdo = $this->em->getConnection();
        $pdo->executeQuery('DROP TABLE IF EXISTS zzzz_filtered_id_list');
        $stmt = $pdo->prepare('CREATE TEMPORARY TABLE zzzz_filtered_id_list (`listing_id` int(11) UNSIGNED NOT NULL, `listing_custom_field_value_id` int(11) UNSIGNED NULL);');
        $stmt->execute();

        $selectSql = $qb->getQuery()->getSQL();
        $stmt = $pdo->prepare("
            # noinspection SqlResolveForFile
            
            INSERT INTO zzzz_filtered_id_list (listing_id, listing_custom_field_value_id)
            {$selectSql} #safe, because contains only not bound prepared statement placeholders, that are bound latter
        ");
        $stmt->execute(SqlHelper::getParametersFromQb($qb));

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('
            # noinspection SqlResolveForFile
            
            REPLACE INTO listing_custom_field_value (id, listing_id, custom_field_id, custom_field_option_id, value)
            SELECT listing_custom_field_value_id, listing_id, :customField, :customFieldOption, :customFieldValue FROM zzzz_filtered_id_list
        ');
        $stmt->bindValue(':customField', $customFieldOptionToSet->getCustomFieldNotNull()->getId());
        $stmt->bindValue(':customFieldOption', $customFieldOptionToSet->getId());
        $stmt->bindValue(':customFieldValue', $customFieldOptionToSet->getValue());
        $stmt->execute();
    }

    public function setCategory(ExecuteActionDto $executeActionDto): void
    {
        /** @var Category $category */
        $category = $executeActionDto->getCategory();

        $this->createTempTableWithFiltered($executeActionDto);

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('
            # noinspection SqlResolveForFile
            
            UPDATE listing 
            JOIN zzzz_filtered_id_list ON listing.id = zzzz_filtered_id_list.id
            SET category_id = :category_id 
            WHERE 1
        ');
        $stmt->bindValue(':category_id', $category->getId());
        $stmt->execute();
    }

    public function rejectListing(ExecuteActionDto $executeActionDto): void
    {
        $this->createTempTableWithFiltered($executeActionDto);

        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('
            # noinspection SqlResolveForFile
            
            UPDATE listing 
            JOIN zzzz_filtered_id_list ON listing.id = zzzz_filtered_id_list.id
            SET admin_rejected = 1
            WHERE 1
        ');
        $stmt->execute();
    }

    public function createTempTableWithFiltered(ExecuteActionDto $executeActionDto): void
    {
        $pdo = $this->em->getConnection();
        $pdo->executeQuery('DROP TABLE IF EXISTS zzzz_filtered_id_list');
        $stmt = $pdo->prepare('CREATE TEMPORARY TABLE zzzz_filtered_id_list (`id` int(11) UNSIGNED NOT NULL);');
        $stmt->execute();

        $qb = $this->getQuery($executeActionDto->getAdminListingListDto());
        $qb->addSelect('listing.id');
        $selectSql = $qb->getQuery()->getSQL();

        $stmt = $pdo->prepare("
            # noinspection SqlResolveForFile
            
            INSERT INTO zzzz_filtered_id_list (id)
            {$selectSql} #safe, because contains only not bound prepared statement placeholders, that are bound latter
        ");
        $stmt->execute(SqlHelper::getParametersFromQb($qb));
    }

    public function getQuery(AdminListingListDto $adminListingListDto): QueryBuilder
    {
        $qb = $this->adminListingSearchService->getQuery($adminListingListDto);
        $qb->resetDQLPart('select');
        $qb->resetDQLPart('orderBy');

        return $qb;
    }

    public function getAffectedPercentage(ExecuteActionDto $executeActionDto): float
    {
        $filteredCount = $this->getAffectedCount($executeActionDto);
        $allListingsCount = (int) $this->listingRepository->count([]);
        if ($allListingsCount < 1) {
            return 0;
        }

        return $filteredCount / $allListingsCount;
    }

    public function getAffectedCount(ExecuteActionDto $executeActionDto): int
    {
        $filteredQb = $this->getQuery($executeActionDto->getAdminListingListDto());
        $filteredQb->select('COUNT(1)');
        $filteredCount = $filteredQb->getQuery()->getSingleScalarResult();

        return (int) $filteredCount;
    }
}
