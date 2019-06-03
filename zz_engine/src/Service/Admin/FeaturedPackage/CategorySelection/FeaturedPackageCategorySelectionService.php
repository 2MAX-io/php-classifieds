<?php

declare(strict_types=1);

namespace App\Service\Admin\FeaturedPackage\CategorySelection;

use App\Entity\Category;
use App\Entity\FeaturedPackage;
use App\Entity\FeaturedPackageForCategory;
use App\Helper\Arr;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FeaturedPackageCategorySelectionService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * @return FeaturedPackageCategorySelectionDto[]
     */
    public function getCategorySelectionList(FeaturedPackage $featuredPackage, Category $preselectedCategory = null): array
    {
        $categories = $this->getCategoriesWihJoinedFeaturedPackages();

        $return = [];
        foreach ($categories as $category) {
            $categorySelectionDto = new FeaturedPackageCategorySelectionDto();
            $categorySelectionDto->setCategory($category);
            $selectedInRequest = $this->requestStack->getMasterRequest()->get('selectedCategories', []);
            $categorySelectionDto->setSelected(
                \count($selectedInRequest) ?
                    Arr::inArray((string) $category->getId(), $selectedInRequest)
                    :
                    $this->isFeaturePackageSelectedInCategory($category, $featuredPackage)
            );

            if ($preselectedCategory && $preselectedCategory->getId() === $category->getId()) {
                $categorySelectionDto->setSelected(true);
            }

            $return[] = $categorySelectionDto;
        }

        return $return;
    }

    public function saveSelection(FeaturedPackage $featuredPackage, array $selectedCategoriesIds): void
    {
        $categories = $this->getCategoriesWihJoinedFeaturedPackages();

        foreach ($categories as $category) {
            $isCurrentlySelected = Arr::inArray($category->getId(), Arr::valuesToInt($selectedCategoriesIds));
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

    private function isFeaturePackageSelectedInCategory(Category $category, FeaturedPackage $featuredPackage): bool
    {
        $catFeaturedPackages = $category->getFeaturedPackages()->map(static function(FeaturedPackageForCategory $featuredPackageForCategory) {
            return $featuredPackageForCategory->getFeaturedPackage();
        });

        return $catFeaturedPackages->contains($featuredPackage);
    }

    /**
     * @return Category[]
     */
    private function getCategoriesWihJoinedFeaturedPackages(): array
    {
        $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');
        $qb->addSelect('featuredPackageJoin');
        $qb->addSelect('featuredPackage');
        $qb->leftJoin('category.featuredPackages', 'featuredPackageJoin');
        $qb->leftJoin('featuredPackageJoin.featuredPackage', 'featuredPackage');
        $qb->andWhere($qb->expr()->gt('category.lvl', 0));

        $qb->addOrderBy('category.sort', 'ASC');

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
