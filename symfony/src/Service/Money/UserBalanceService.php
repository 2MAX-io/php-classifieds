<?php

declare(strict_types=1);

namespace App\Service\Money;

use App\Entity\User;
use App\Entity\UserBalanceChange;
use Doctrine\ORM\EntityManagerInterface;

class UserBalanceService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function forceSetBalance(int $newBalance, User $user): void
    {
        $currentBalance = $this->getCurrentBalance($user);
        $calculatedChange = $newBalance -$currentBalance;

        $userBalanceChangeNew = new UserBalanceChange();
        $userBalanceChangeNew->setUser($user);
        $userBalanceChangeNew->setBalanceChange($calculatedChange);
        $userBalanceChangeNew->setBalanceFinal($currentBalance + $calculatedChange);
        $userBalanceChangeNew->setDatetime(new \DateTime());

        if ($userBalanceChangeNew->getBalanceFinal() !== $newBalance) {
            throw new \UnexpectedValueException('new balanced set incorrectly');
        }

        $this->em->persist($userBalanceChangeNew);
    }

    public function removeBalance(int $removeAmountPositive, User $user): void
    {
        // todo: make transactional
        if ($removeAmountPositive <= 0) {
            throw new \UnexpectedValueException('should be positive');
        }

        $currentBalance = $this->getCurrentBalance($user);
        $userBalanceChangeNew = new UserBalanceChange();
        $userBalanceChangeNew->setUser($user);
        $userBalanceChangeNew->setBalanceChange(- $removeAmountPositive);
        $userBalanceChangeNew->setBalanceFinal($currentBalance - $removeAmountPositive);
        $userBalanceChangeNew->setDatetime(new \DateTime());

        $this->em->persist($userBalanceChangeNew);
    }

    public function hasAmount(int $requestedAmount, User $user): bool
    {
        $currentBalance = $this->getCurrentBalance($user);

        if ($currentBalance < 1) {
            return false;
        }

        return $currentBalance >= $requestedAmount;
    }

    public function getCurrentBalance(User $user): int
    {
         $lastBalanceChange = $this->getLastBalanceChange($user);
         $lastBalance = $lastBalanceChange === null ? 0 : $lastBalanceChange->getBalanceFinal();
         $balanceSum = $this->getBalanceChangesSum($user);

        if ($lastBalance !== $balanceSum) {
            throw new \UnexpectedValueException('last balance, and balance sum does not match');
        }

        if ($lastBalanceChange === null) {
            return 0;
        }

        return $lastBalanceChange->getBalanceFinal();
    }

    public function getLastBalanceChange(User $user): ?UserBalanceChange
    {
        $qb = $this->em->getRepository(UserBalanceChange::class)->createQueryBuilder('userBalanceChange');
        $qb->andWhere($qb->expr()->eq('userBalanceChange.user', ':user'));
        $qb->setParameter(':user', $user);
        $qb->orderBy('userBalanceChange.id', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getBalanceChangesSum(User $user): int
    {
        $qb = $this->em->getRepository(UserBalanceChange::class)->createQueryBuilder('userBalanceChange');
        $qb->select('SUM(userBalanceChange.balanceChange) balanceChangeSum');
        $qb->andWhere($qb->expr()->eq('userBalanceChange.user', ':user'));
        $qb->setParameter(':user', $user);
        $qb->orderBy('userBalanceChange.id', 'DESC');
        $qb->setMaxResults(1);

        $sum = $qb->getQuery()->getSingleScalarResult();
        return (int) ($sum ?? 0);
    }
}
