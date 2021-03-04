<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Enum\SortConfig;
use App\Repository\CustomFieldForCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldCategoriesOrderService
{
    /**
     * @var CustomFieldForCategoryRepository
     */
    private $customFieldForCategoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CustomFieldForCategoryRepository $customFieldForCategoryRepository, EntityManagerInterface $em)
    {
        $this->customFieldForCategoryRepository = $customFieldForCategoryRepository;
        $this->em = $em;
    }

    /**
     * @param int[] $orderedCustomFieldForCategoryIdList
     */
    public function saveOrder(array $orderedCustomFieldForCategoryIdList): void
    {
        $customFieldForCategoryList = $this->customFieldForCategoryRepository->getFromIds(
            $orderedCustomFieldForCategoryIdList
        );

        $sort = SortConfig::START_REORDER_FROM;
        foreach ($orderedCustomFieldForCategoryIdList as $customFieldForCategoryId) {
            $customFieldForCategory = $customFieldForCategoryList[$customFieldForCategoryId];
            $customFieldForCategory->setSort($sort);
            $this->em->persist($customFieldForCategory);
            ++$sort;
        }
    }

    public function reorder(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('SET @count = :count');
        $stmt->execute([':count' => SortConfig::START_REORDER_FROM]);
        $pdo->executeQuery('UPDATE custom_field_for_category SET sort = @count:= @count + 1 WHERE 1 ORDER BY category_id ASC, sort ASC;');
    }
}
