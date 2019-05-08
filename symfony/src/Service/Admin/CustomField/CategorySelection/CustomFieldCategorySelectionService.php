<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField\CategorySelection;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\CustomFieldJoinCategory;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldCategorySelectionService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return CustomFieldCategorySelectionItemDto[]
     */
    public function getCategorySelectionList(CustomField $customField) : array
    {
        $categories = $this->getCategoriesWihJoinedCustomFields();

        $return = [];
        foreach ($categories as $category) {
            $customFieldCategorySelectionItemDto = new CustomFieldCategorySelectionItemDto();
            $customFieldCategorySelectionItemDto->setCategory($category);
            $customFieldCategorySelectionItemDto->setSelected(
                $this->categoryHasCustomField($category, $customField)
            );
            $return[] = $customFieldCategorySelectionItemDto;
        }

        return $return;
    }

    private function categoryHasCustomField(Category $category, CustomField $customField): bool
    {
        $catCustomFields = $category->getCustomFieldsJoin()->map(function(CustomFieldJoinCategory $customFieldJoinCategory) {
            return $customFieldJoinCategory->getCustomField();
        });

        return $catCustomFields->contains($customField);
    }

    /**
     * @return Category[]
     */
    private function getCategoriesWihJoinedCustomFields(): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');
        $qb->addSelect('customFieldJoinCategory');
        $qb->addSelect('customField');
        $qb->leftJoin('category.customFieldsJoin', 'customFieldJoinCategory');
        $qb->leftJoin('customFieldJoinCategory.customField', 'customField');
        $qb->andWhere($qb->expr()->gt('category.lvl', 0));

        $qb->addOrderBy('category.sort', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
