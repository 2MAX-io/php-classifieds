<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Entity\Category;
use App\Entity\Package;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoriesHavePackagesHealthCheckerService implements HealthCheckerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        TranslatorInterface $trans,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->trans = $trans;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        if (!$this->allCategoriesHavePackage()) {
            return new HealthCheckResultDto(
                true,
                $this->trans->trans('trans.Categories without packages')
                .': '
                .$this->getCategoriesWithoutPackagesString()
            );
        }

        return new HealthCheckResultDto(false);
    }

    private function allCategoriesHavePackage(): bool
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('package');
        $qb->from(Package::class, 'package');
        $qb->select('COUNT(1)');
        $qb->andWhere($qb->expr()->eq('package.defaultPackage', 1));
        $qb->andWhere($qb->expr()->eq('package.removed', 0));
        $hasDefaultPackage = $qb->getQuery()->getSingleScalarResult() > 0;
        if ($hasDefaultPackage) {
            return true;
        }

        $qb = $this->getCategoriesWithoutPackagesQuery();
        $qb->select('COUNT(1)');
        $allCatsHavePackage = 0 === (int) $qb->getQuery()->getSingleScalarResult();
        if ($allCatsHavePackage) {
            return true;
        }

        return $allCatsHavePackage;
    }

    private function getCategoriesWithoutPackagesString(): string
    {
        $qb = $this->getCategoriesWithoutPackagesQuery();

        /** @var Category[] $categories */
        $categories = $qb->getQuery()->getResult();

        return \implode(', ', \array_map(static function (Category $category) {
            return $category->getPathString();
        }, $categories));
    }

    private function getCategoriesWithoutPackagesQuery(): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('category');
        $qb->from(Category::class, 'category');
        $qb->leftJoin('category.packageForCategoryList', 'packageForCategory');
        $qb->leftJoin(
            'packageForCategory.package',
            'package',
            Join::WITH,
            (string) $qb->expr()->eq('package.removed', 0),
        );
        $qb->andWhere('category.rgt - category.lft = 1');
        $qb->andWhere($qb->expr()->isNull('package.id'));

        return $qb;
    }
}
