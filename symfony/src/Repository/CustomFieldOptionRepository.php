<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CustomFieldOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CustomFieldOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomFieldOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomFieldOption[]    findAll()
 * @method CustomFieldOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomFieldOptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CustomFieldOption::class);
    }

    /**
     * @return Category[]
     */
    public function getFromIds(array $customFieldOptionIds): array
    {
        $ids = [];
        foreach ($customFieldOptionIds as $customFieldOption) {
            if ($customFieldOption instanceof CustomFieldOption) {
                $ids[] = $customFieldOption->getId();
            } else {
                $ids[] = (int) $customFieldOption;
            }
        }

        $qb = $this->createQueryBuilder('customFieldOption');
        $qb->andWhere($qb->expr()->in('customFieldOption.id', ':ids'));
        $qb->setParameter('ids', $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
        $qb->indexBy('customFieldOption', 'customFieldOption.id');

        return $qb->getQuery()->getResult();
    }
}
