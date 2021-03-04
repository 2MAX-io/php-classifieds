<?php

declare(strict_types=1);

namespace App\Service\Admin\FeaturedPackage\CategorySelection;

use App\Entity\Category;
use App\Entity\FeaturedPackage;
use App\Entity\FeaturedPackageForCategory;
use App\Helper\ArrayHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class FeaturedPackageCategoriesService
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
     * @return FeaturedPackageCategoryDto[]
     */
    public function getCategorySelectionList(
        FeaturedPackage $featuredPackage,
        array $selectedCategoriesIdList = [],
        Category $preselectedCategory = null
    ): array {
        $return = [];
        $categories = $this->getCategoriesWihJoinedFeaturedPackages();
        foreach ($categories as $category) {
            $categoryDto = new FeaturedPackageCategoryDto();
            $categoryDto->setCategory($category);
            if ($selectedCategoriesIdList) {
                $categoryDto->setSelected(
                    ArrayHelper::inArray((string) $category->getId(), $selectedCategoriesIdList),
                );
            } else {
                $categoryDto->setSelected(
                    $this->isFeaturePackageSelectedInCategory($category, $featuredPackage),
                );
            }

            if ($preselectedCategory && $preselectedCategory->getId() === $category->getId()) {
                $categoryDto->setSelected(true);
            }

            $return[] = $categoryDto;
        }

        return $return;
    }

    /**
     * @param int[] $selectedCategoriesIds
     */
    public function saveSelectedCategories(FeaturedPackage $featuredPackage, array $selectedCategoriesIds): void
    {
        $categories = $this->getCategoriesWihJoinedFeaturedPackages();
        foreach ($categories as $category) {
            $isCurrentlySelected = ArrayHelper::inArray($category->getId(), ArrayHelper::valuesToInt($selectedCategoriesIds));
            $selectedPreviously = $this->isFeaturePackageSelectedInCategory($category, $featuredPackage);

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
                $this->addCategorySelection($featuredPackage, $category);
            }

            $removed = !$isCurrentlySelected && $selectedPreviously;
            if ($removed) {
                $this->removeCategorySelection($featuredPackage, $category);
            }
        }
    }

    /**
     * @return int[]
     */
    public function getSelectedCategoriesIdFromRequest(Request $request): array
    {
        return $request->get('selectedCategories', []);
    }

    private function isFeaturePackageSelectedInCategory(Category $category, FeaturedPackage $featuredPackage): bool
    {
        $catFeaturedPackages = $category->getFeaturedPackages()->map(static function (FeaturedPackageForCategory $featuredPackageForCategory) {
            return $featuredPackageForCategory->getFeaturedPackage();
        });

        return $catFeaturedPackages->contains($featuredPackage);
    }

    /**
     * @return Category[]
     */
    private function getCategoriesWihJoinedFeaturedPackages(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('category');
        $qb->from(Category::class, 'category');
        $qb->addSelect('featuredPackageJoin');
        $qb->addSelect('featuredPackage');
        $qb->leftJoin('category.featuredPackages', 'featuredPackageJoin');
        $qb->leftJoin('featuredPackageJoin.featuredPackage', 'featuredPackage');
        $qb->andWhere($qb->expr()->gt('category.lvl', 0));

        $qb->addOrderBy('category.sort', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    private function addCategorySelection(FeaturedPackage $featuredPackage, Category $category): void
    {
        $featuredPackageForCategory = new FeaturedPackageForCategory();
        $featuredPackageForCategory->setCategory($category);
        $featuredPackageForCategory->setFeaturedPackage($featuredPackage);
        $this->em->persist($featuredPackageForCategory);
    }

    private function removeCategorySelection(FeaturedPackage $featuredPackage, Category $category): void
    {
        foreach ($category->getFeaturedPackages() as $featuredPackageJoin) {
            if ($featuredPackageJoin->getFeaturedPackage() === $featuredPackage) {
                $this->em->remove($featuredPackageJoin);
            }
        }
    }
}
