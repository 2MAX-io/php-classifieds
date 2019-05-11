<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomFieldJoinCategory;
use App\Service\System\Sort\SortService;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldForCategoryService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveOrder(array $orderedCustomFieldJoinCategoryIdList): void
    {
        $customFields = $this->em->getRepository(CustomFieldJoinCategory::class)->getFromIds($orderedCustomFieldJoinCategoryIdList);

        $sort = 1;
        foreach ($orderedCustomFieldJoinCategoryIdList as $customFieldJoinCategoryId) {
            $customFieldJoinCategory = $customFields[$customFieldJoinCategoryId];
            $customFieldJoinCategory->setSort($sort);
            $this->em->persist($customFieldJoinCategory);
            $sort++;
        }
    }

    public function reorder(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('SET @count = :count');
        $stmt->execute([':count' => SortService::START_REORDER_FROM]);
        $pdo->query('UPDATE custom_field_join_category SET sort = @count:= @count + 1 WHERE 1 ORDER BY category_id ASC, sort ASC;');
    }
}