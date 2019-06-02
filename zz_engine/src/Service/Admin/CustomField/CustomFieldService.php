<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomField;
use App\Service\System\Sort\SortService;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveOrder(array $orderedCustomFieldIds): void
    {
        $customFields = $this->em->getRepository(CustomField::class)->getFromIds($orderedCustomFieldIds);

        $sort = SortService::START_REORDER_FROM;
        foreach ($orderedCustomFieldIds as $id) {
            $customField = $customFields[$id];
            $customField->setSort($sort);
            $this->em->persist($customField);
            $sort++;
        }
    }

    public function reorder(): void
    {
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('SET @count = :count');
        $stmt->execute([':count' => SortService::START_REORDER_FROM]);
        $pdo->query('UPDATE custom_field SET sort = @count:= @count + 1 WHERE 1 ORDER BY sort ASC;');
    }
}
