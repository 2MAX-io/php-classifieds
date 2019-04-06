<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomField;
use App\Entity\ListingCustomFieldValue;
use App\Enum\SortConfig;
use App\Repository\CustomFieldRepository;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldService
{
    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CustomFieldRepository $customFieldRepository, EntityManagerInterface $em)
    {
        $this->customFieldRepository = $customFieldRepository;
        $this->em = $em;
    }

    /**
     * @param int[] $orderedCustomFieldIds
     */
    public function saveOrder(array $orderedCustomFieldIds): void
    {
        $customFields = $this->customFieldRepository->getFromIds($orderedCustomFieldIds);

        $sort = SortConfig::START_REORDER_FROM;
        foreach ($orderedCustomFieldIds as $id) {
            $customField = $customFields[$id];
            $customField->setSort($sort);
            $this->em->persist($customField);
            ++$sort;
        }
    }

    public function reorder(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('SET @count = :count');
        $stmt->execute([':count' => SortConfig::START_REORDER_FROM]);
        $pdo->executeQuery('UPDATE custom_field SET sort = @count:= @count + 1 WHERE 1 ORDER BY sort ASC;');
    }

    public function deleteValueFromListings(CustomField $customField): void
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listingCustomFieldValue');
        $qb->from(ListingCustomFieldValue::class, 'listingCustomFieldValue');
        $qb->delete(ListingCustomFieldValue::class, 'listingCustomFieldValue');

        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.customField', ':customField'));
        $qb->andWhere($qb->expr()->isNull('listingCustomFieldValue.customFieldOption'));
        $qb->setParameter('customField', $customField->getId());

        $qb->getQuery()->execute();
    }
}
