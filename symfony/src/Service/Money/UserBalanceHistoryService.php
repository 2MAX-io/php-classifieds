<?php

declare(strict_types=1);

namespace App\Service\Money;

use App\Entity\User;
use App\Entity\UserBalanceChange;
use Doctrine\ORM\EntityManagerInterface;

class UserBalanceHistoryService
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
     * @return UserBalanceChange[]
     */
    public function getHistoryList(User $user): array
    {
        $qb = $this->em->getRepository(UserBalanceChange::class)->createQueryBuilder('userBalanceChange');
        $qb->andWhere($qb->expr()->eq('userBalanceChange.user', ':user'));
        $qb->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
}
