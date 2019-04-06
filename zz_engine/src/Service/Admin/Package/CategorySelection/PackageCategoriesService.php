<?php

declare(strict_types=1);

namespace App\Service\Admin\Package\CategorySelection;

use App\Entity\Category;
use App\Entity\Package;
use App\Entity\PackageForCategory;
use App\Helper\ArrayHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PackageCategoriesService
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
     * @return PackageCategoryDto[]
     */
    public function getCategorySelectionList(
        Package $package,
        array $selectedCategoriesIdList = [],
        Category $preselectedCategory = null
    ): array {
        $return = [];
        $categories = $this->getCategoriesWihJoinedPackages();
        foreach ($categories as $category) {
            $categoryDto = new PackageCategoryDto();
            $categoryDto->setCategory($category);
            if ($selectedCategoriesIdList) {
                $categoryDto->setSelected(
                    ArrayHelper::inArray((string) $category->getId(), $selectedCategoriesIdList),
                );
            } else {
                $categoryDto->setSelected(
                    $this->isFeaturePackageSelectedInCategory($category, $package),
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
    public function saveSelectedCategories(Package $package, array $selectedCategoriesIds): void
    {
        $categories = $this->getCategoriesWihJoinedPackages();
        foreach ($categories as $category) {
            $isCurrentlySelected = ArrayHelper::inArray($category->getId(), ArrayHelper::valuesToInt($selectedCategoriesIds));
            $selectedPreviously = $this->isFeaturePackageSelectedInCategory($category, $package);

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
                $this->addCategorySelection($package, $category);
            }

            $removed = !$isCurrentlySelected && $selectedPreviously;
            if ($removed) {
                $this->removeCategorySelection($package, $category);
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

    private function isFeaturePackageSelectedInCategory(Category $category, Package $package): bool
    {
        $categoryPackages = $category->getPackageForCategoryList()->map(static function (PackageForCategory $packageForCategory) {
            return $packageForCategory->getPackage();
        });

        return $categoryPackages->contains($package);
    }

    /**
     * @return Category[]
     */
    private function getCategoriesWihJoinedPackages(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('category');
        $qb->from(Category::class, 'category');
        $qb->addSelect('packageForCategory');
        $qb->addSelect('package');
        $qb->leftJoin('category.packageForCategoryList', 'packageForCategory');
        $qb->leftJoin('packageForCategory.package', 'package');
        $qb->andWhere($qb->expr()->gt('category.lvl', 0));

        $qb->addOrderBy('category.sort', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    private function addCategorySelection(Package $package, Category $category): void
    {
        $packageForCategory = new PackageForCategory();
        $packageForCategory->setCategory($category);
        $packageForCategory->setPackage($package);
        $this->em->persist($packageForCategory);
    }

    private function removeCategorySelection(Package $package, Category $category): void
    {
        foreach ($category->getPackageForCategoryList() as $packageForCategory) {
            if ($packageForCategory->getPackage() === $package) {
                $this->em->remove($packageForCategory);
            }
        }
    }
}
