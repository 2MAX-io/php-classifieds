<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomFieldOption;
use App\Entity\ListingCustomFieldValue;
use App\Exception\UserVisibleMessageException;
use App\Service\System\Sort\SortService;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldOptionService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveOrderOfOptions(array $orderedCustomFieldOptionIdList): void
    {
        $customFieldOptions = $this->em->getRepository(CustomFieldOption::class)->getFromIds($orderedCustomFieldOptionIdList);

        $sort = SortService::START_REORDER_FROM;
        foreach ($orderedCustomFieldOptionIdList as $customFieldOptionId) {
            $customFieldOption = $customFieldOptions[$customFieldOptionId];
            $customFieldOption->setSort($sort);
            $this->em->persist($customFieldOption);
            $sort++;
        }
    }

    public function deleteOptionFromListingValues(CustomFieldOption $customFieldOption): void
    {
        $qb = $this->em->getRepository(ListingCustomFieldValue::class)->createQueryBuilder('listingCustomFieldValue');
        $qb->delete(ListingCustomFieldValue::class, 'listingCustomFieldValue');

        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.customFieldOption', ':customFieldOption'));
        $qb->setParameter('customFieldOption', $customFieldOption->getId());

        $qb->getQuery()->execute();
    }

    public function reorder(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('SET @count = :count');
        $stmt->execute([':count' => SortService::START_REORDER_FROM]);
        $pdo->query('UPDATE custom_field_option SET sort = @count:= @count + 1 WHERE 1 ORDER BY custom_field_id ASC, sort ASC;');
    }

    public function changeStringValue(CustomFieldOption $customFieldOption, string $replaceFrom, string $replaceTo): void
    {
        if ($replaceFrom === $replaceTo) {
            return;
        }

        $qb = $this->em->getRepository(ListingCustomFieldValue::class)->createQueryBuilder('listingCustomFieldValue');
        $qb->select('COUNT(listingCustomFieldValue.id) countResult');
        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.customField', ':customField'));
        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.value', ':value'));
        $qb->setParameter('customField', $customFieldOption->getCustomField());
        $qb->setParameter('value', $replaceTo);

        $newValuesUsed = $qb->getQuery()->getSingleScalarResult();
        if ($newValuesUsed > 0) {
            throw new UserVisibleMessageException('Can not change option value, because target value already exist');
        }

        $qb = $this->em->getRepository(ListingCustomFieldValue::class)->createQueryBuilder('listingCustomFieldValue');
        $qb->update(ListingCustomFieldValue::class, 'listingCustomFieldValue');

        $qb->set('listingCustomFieldValue.value', ':newValue');
        $qb->setParameter('newValue', $replaceTo);

        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.value', ':oldValue'));
        $qb->setParameter('oldValue', $replaceFrom);

        $qb->andWhere($qb->expr()->eq('listingCustomFieldValue.customField', ':customField'));
        $qb->setParameter(':customField', $customFieldOption->getCustomField());

        $qb->getQuery()->execute();
    }
}
