<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField\CategorySelection;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\CustomFieldForCategory;
use App\Enum\SortConfig;
use App\Helper\ArrayHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomFieldCategoriesService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param int[] $selectedCategoriesIdList
     *
     * @return CategoryForCustomFieldDto[]
     */
    public function getCategoriesForCustomFieldList(
        CustomField $customField,
        array $selectedCategoriesIdList = [],
        Category $preselectedCategory = null
    ): array {
        $return = [];
        $categories = $this->getCategoriesWihCustomFields();
        foreach ($categories as $category) {
            $categoryForCustomFieldDto = new CategoryForCustomFieldDto();
            $categoryForCustomFieldDto->setCategory($category);
            if ($selectedCategoriesIdList) {
                $categoryForCustomFieldDto->setSelected(
                    ArrayHelper::inArray((string) $category->getId(), $selectedCategoriesIdList)
                );
            } else {
                $categoryForCustomFieldDto->setSelected(
                    $this->isCustomFieldAddedToCategory($category, $customField)
                );
            }

            if ($preselectedCategory && $preselectedCategory->getId() === $category->getId()) {
                $categoryForCustomFieldDto->setSelected(true);
            }

            $return[] = $categoryForCustomFieldDto;
        }

        return $return;
    }

    /**
     * @param int[] $selectedCategoriesIds
     */
    public function saveCategoriesForCustomField(CustomField $customField, array $selectedCategoriesIds): void
    {
        $categories = $this->getCategoriesWihCustomFields();
        foreach ($categories as $category) {
            $isCurrentlySelected = ArrayHelper::inArray($category->getId(), ArrayHelper::valuesToInt($selectedCategoriesIds));
            $selectedPreviously = $this->isCustomFieldAddedToCategory($category, $customField);

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
                $this->addCustomFieldToCategory($customField, $category);
            }

            $removed = !$isCurrentlySelected && $selectedPreviously;
            if ($removed) {
                $this->removeCustomFieldFromCategory($customField, $category);
            }
        }
    }

    /**
     * @return int[]
     */
    public function getSelectedCategoriesFromRequest(Request $request): array
    {
        return $request->get('selectedCategories', []);
    }

    private function isCustomFieldAddedToCategory(Category $category, CustomField $customField): bool
    {
        $customFieldCategories = $category->getCustomFieldForCategoryList()->map(
            static function (CustomFieldForCategory $customFieldForCategory) {
                return $customFieldForCategory->getCustomField();
            }
        );

        return $customFieldCategories->contains($customField);
    }

    /**
     * @return Category[]
     */
    private function getCategoriesWihCustomFields(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('category');
        $qb->from(Category::class, 'category');
        $qb->addSelect('customFieldForCategory');
        $qb->addSelect('customField');
        $qb->leftJoin('category.customFieldForCategoryList', 'customFieldForCategory');
        $qb->leftJoin('customFieldForCategory.customField', 'customField');
        $qb->andWhere($qb->expr()->gt('category.lvl', 0));

        $qb->addOrderBy('category.sort', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    private function addCustomFieldToCategory(CustomField $customField, Category $category): void
    {
        $customFieldForCategory = new CustomFieldForCategory();
        $customFieldForCategory->setCategory($category);
        $customFieldForCategory->setCustomField($customField);
        $customFieldForCategory->setSort(SortConfig::LAST_VALUE);
        $this->em->persist($customFieldForCategory);
    }

    private function removeCustomFieldFromCategory(CustomField $customField, Category $category): void
    {
        foreach ($category->getCustomFieldForCategoryList() as $customFieldForCategory) {
            if ($customFieldForCategory->getCustomField() === $customField) {
                $this->em->remove($customFieldForCategory);
            }
        }
    }
}
