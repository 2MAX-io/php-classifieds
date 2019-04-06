<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomFieldOption;
use App\Entity\ListingCustomFieldValue;
use App\Enum\SortConfig;
use App\Exception\UserVisibleException;
use App\Repository\CustomFieldOptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldOptionService
{
    /**
     * @var CustomFieldOptionRepository
     */
    private $customFieldOptionRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CustomFieldOptionRepository $customFieldOptionRepository, EntityManagerInterface $em)
    {
        $this->customFieldOptionRepository = $customFieldOptionRepository;
        $this->em = $em;
    }

    /**
     * @param int[] $orderedCustomFieldOptionIdList
     */
    public function saveOrderOfOptions(array $orderedCustomFieldOptionIdList): void
    {
        $customFieldOptions = $this->customFieldOptionRepository->getFromIds($orderedCustomFieldOptionIdList);

        $sort = SortConfig::START_REORDER_FROM;
        foreach ($orderedCustomFieldOptionIdList as $customFieldOptionId) {
            $customFieldOption = $customFieldOptions[$customFieldOptionId];
            $customFieldOption->setSort($sort);
            $this->em->persist($customFieldOption);
            ++$sort;
        }
    }

    public function deleteCustomFieldOptionValuesFromListings(CustomFieldOption $customFieldOption): void
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listingCustomFieldValue');
        $qb->from(ListingCustomFieldValue::class, 'listingCustomFieldValue');
        $qb->delete(ListingCustomFieldValue::class, 'listingCustomFieldValue');

        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.customFieldOption', ':customFieldOption'));
        $qb->setParameter('customFieldOption', $customFieldOption->getId());

        $qb->getQuery()->execute();
    }

    public function reorder(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('SET @count = :count');
        $stmt->execute([':count' => SortConfig::START_REORDER_FROM]);
        $pdo->executeQuery('UPDATE custom_field_option SET sort = @count:= @count + 1 WHERE 1 ORDER BY custom_field_id ASC, sort ASC;');
    }

    public function updateCustomFieldOptionValueForListings(CustomFieldOption $customFieldOption, string $oldValue, string $newValue): void
    {
        if ($oldValue === $newValue) {
            return;
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('listingCustomFieldValue');
        $qb->from(ListingCustomFieldValue::class, 'listingCustomFieldValue');
        $qb->select($qb->expr()->count('listingCustomFieldValue.id'));
        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.customField', ':customField'));
        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.value', ':oldValue'));
        $qb->setParameter('customField', $customFieldOption->getCustomField());
        $qb->setParameter('oldValue', $newValue);

        $newValuesUsed = $qb->getQuery()->getSingleScalarResult();
        if ($newValuesUsed > 0) {
            throw new UserVisibleException('Can not change option value, because target value already exist');
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('listingCustomFieldValue');
        $qb->from(ListingCustomFieldValue::class, 'listingCustomFieldValue');
        $qb->update(ListingCustomFieldValue::class, 'listingCustomFieldValue');

        $qb->set('listingCustomFieldValue.value', ':newValue');
        $qb->setParameter('newValue', $newValue);

        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.value', ':oldValue'));
        $qb->setParameter('oldValue', $oldValue);

        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.customField', ':customField'));
        $qb->setParameter(':customField', $customFieldOption->getCustomField());

        $qb->getQuery()->execute();
    }
}
