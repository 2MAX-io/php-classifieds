<?php

declare(strict_types=1);

namespace App\Service\Listing\Featured;

use App\Entity\FeaturedPackage;
use Doctrine\ORM\EntityManagerInterface;

class FeaturedPackageService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @return FeaturedPackage[]
     */
    public function getPackages(): array
    {
        $qb = $this->em->getRepository(FeaturedPackage::class)->createQueryBuilder('featuredPackage');
        $qb->orderBy('featuredPackage.price', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
