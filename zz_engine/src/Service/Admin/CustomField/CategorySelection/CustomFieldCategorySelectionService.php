<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField\CategorySelection;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\CustomFieldJoinCategory;
use App\Helper\Arr;
use App\Repository\CategoryRepository;
use App\Service\System\Sort\SortService;
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
    public function getCategorySelectionList(CustomField $customField, Category $preselectedCategory = null): array
    {
        $categories = $this->getCategoriesWihJoinedCustomFields();

        $return = [];
        foreach ($categories as $category) {
            $customFieldCategorySelectionItemDto = new CustomFieldCategorySelectionItemDto();
            $customFieldCategorySelectionItemDto->setCategory($category);
            $customFieldCategorySelectionItemDto->setSelected(
                $this->categoryHasCustomField($category, $customField)
            );

            if ($preselectedCategory && $preselectedCategory->getId() === $category->getId()) {
                $customFieldCategorySelectionItemDto->setSelected(true);
            }

            $return[] = $customFieldCategorySelectionItemDto;
        }

        return $return;
    }

    public function saveSelection(CustomField $customField, array $selectedCategoriesIds): void
    {
        $categories = $this->getCategoriesWihJoinedCustomFields();

        foreach ($categories as $category) {
            $isCurrentlySelected = Arr::inArray($category->getId(), Arr::valuesToInt($selectedCategoriesIds));
            $selectedPreviously = $this->categoryHasCustomField($category, $customField);

            $alreadySelected = $isCurrentlySelected && $selectedPreviously;
            if ($alreadySelected) {
                continue;
            }

            $notSelected = !$isCurrentlySelected && !$selectedPreviously;
            if ($notSelected) {
                continue;
            }

            $added = $isCurrentlySelected && !$selectedPreviously;
            if ($added) {
                $this->addCategorySelection($customField, $category);
            }

            $removed = !$isCurrentlySelected && $selectedPreviously;
            if ($removed) {
                $this->removeCategorySelection($customField, $category);
            }
        }
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

    private function addCategorySelection(CustomField $customField, Category $category): void
    {
        $customFieldJoinCategory = new CustomFieldJoinCategory();
        $customFieldJoinCategory->setCategory($category);
        $customFieldJoinCategory->setCustomField($customField);
        $customFieldJoinCategory->setSort(SortService::LAST_VALUE);
        $this->em->persist($customFieldJoinCategory);
    }

    private function removeCategorySelection(CustomField $customField, Category $category): void
    {
        foreach ($category->getCustomFieldsJoin() as $customFieldJoinCategory) {
            if ($customFieldJoinCategory->getCustomField() === $customField) {
                $this->em->remove($customFieldJoinCategory);
            }
        }
    }
}
