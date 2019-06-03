<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ListingView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ListingView|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingView|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingView[]    findAll()
 * @method ListingView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingViewRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ListingView::class);
    }
}
