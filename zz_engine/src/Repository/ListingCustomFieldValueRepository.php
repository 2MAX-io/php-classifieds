<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ListingCustomFieldValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ListingCustomFieldValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListingCustomFieldValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListingCustomFieldValue[]    findAll()
 * @method ListingCustomFieldValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListingCustomFieldValueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ListingCustomFieldValue::class);
    }
}
