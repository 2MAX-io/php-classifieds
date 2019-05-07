<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomFieldJoinCategory;
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
}
