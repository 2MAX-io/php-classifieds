<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomFieldOption;
use App\Entity\ListingCustomFieldValue;
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
        $customFields = $this->em->getRepository(CustomFieldOption::class)->getFromIds($orderedCustomFieldOptionIdList);

        $sort = 1;
        foreach ($orderedCustomFieldOptionIdList as $customFieldOptionId) {
            $customFieldOption = $customFields[$customFieldOptionId];
            $customFieldOption->setSort($sort);
            $this->em->persist($customFieldOption);
            $sort++;
        }
    }

    public function removeOptionFromListingValues(): void
    {
        $qb = $this->em->getRepository(ListingCustomFieldValue::class)->createQueryBuilder('listingCustomFieldValue');
        $qb->update(ListingCustomFieldValue::class, 'listingCustomFieldValue');
        $qb->set('listingCustomFieldValue.customFieldOption', ':customFieldOption');
        $qb->setParameter('customFieldOption', null);
        $qb->getQuery()->execute();
    }

    public function reorder(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('SET @count = :count');
        $stmt->execute([':count' => SortService::START_REORDER_FROM]);
        $pdo->query('UPDATE custom_field_option SET sort = @count:= @count + 1 WHERE 1 ORDER BY custom_field_id ASC, sort ASC;');
    }
}
