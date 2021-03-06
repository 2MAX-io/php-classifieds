<?php

declare(strict_types=1);

namespace App\Service\Money;

use App\Entity\Payment;
use App\Entity\User;
use App\Entity\UserBalanceChange;
use App\Helper\DateHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
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

    public function forceSetBalance(int $newBalance, User $user): UserBalanceChange
    {
        $currentBalance = $this->getCurrentBalance($user);
        $calculatedChange = $newBalance - $currentBalance;

        $userBalanceChangeNew = new UserBalanceChange();
        $userBalanceChangeNew->setUser($user);
        $userBalanceChangeNew->setBalanceChange($calculatedChange);
        $userBalanceChangeNew->setBalanceFinal($currentBalance + $calculatedChange);
        $userBalanceChangeNew->setDatetime(DateHelper::create());

        if ($userBalanceChangeNew->getBalanceFinal() !== $newBalance) {
            throw new \UnexpectedValueException('new balanced set incorrectly');
        }

        $this->em->persist($userBalanceChangeNew);

        return $userBalanceChangeNew;
    }

    public function addBalance(int $addAmountPositive, User $user, Payment $payment): UserBalanceChange
    {
        if ($addAmountPositive <= 0) {
            throw new \UnexpectedValueException('should be positive');
        }

        $currentBalance = $this->getCurrentBalance($user);
        $userBalanceChangeNew = new UserBalanceChange();
        $userBalanceChangeNew->setUser($user);
        $userBalanceChangeNew->setBalanceChange($addAmountPositive);
        $userBalanceChangeNew->setBalanceFinal($currentBalance + $addAmountPositive);
        $userBalanceChangeNew->setDatetime(DateHelper::create());
        $userBalanceChangeNew->setPayment($payment);

        $this->em->persist($userBalanceChangeNew);

        return $userBalanceChangeNew;
    }

    public function removeBalance(int $removeAmountPositive, User $user, Payment $payment = null): UserBalanceChange
    {
        if ($removeAmountPositive <= 0) {
            throw new \UnexpectedValueException('should be positive');
        }

        $currentBalance = $this->getCurrentBalance($user);
        $userBalanceChangeNew = new UserBalanceChange();
        $userBalanceChangeNew->setUser($user);
        $userBalanceChangeNew->setBalanceChange(-$removeAmountPositive);
        $userBalanceChangeNew->setBalanceFinal($currentBalance - $removeAmountPositive);
        $userBalanceChangeNew->setDatetime(DateHelper::create());
        $userBalanceChangeNew->setPayment($payment);

        $this->em->persist($userBalanceChangeNew);

        return $userBalanceChangeNew;
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
        $lastBalance = null === $lastBalanceChange ? 0 : $lastBalanceChange->getBalanceFinal();
        $balanceSum = $this->getBalanceChangesSum($user);

        if ($lastBalance !== $balanceSum) {
            throw new \UnexpectedValueException('last balance, and balance sum does not match');
        }

        if (null === $lastBalanceChange) {
            return 0;
        }

        return $lastBalanceChange->getBalanceFinal();
    }

    public function getLastBalanceChange(User $user): ?UserBalanceChange
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('userBalanceChange');
        $qb->from(UserBalanceChange::class, 'userBalanceChange');
        $qb->andWhere($qb->expr()->eq('userBalanceChange.user', ':user'));
        $qb->setParameter(':user', $user->getId(), Types::INTEGER);
        $qb->orderBy('userBalanceChange.id', Criteria::DESC);
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getBalanceChangesSum(User $user): int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('userBalanceChange');
        $qb->from(UserBalanceChange::class, 'userBalanceChange');
        $qb->select('SUM(userBalanceChange.balanceChange) balanceChangeSum');
        $qb->andWhere($qb->expr()->eq('userBalanceChange.user', ':user'));
        $qb->setParameter(':user', $user->getId(), Types::INTEGER);
        $qb->setMaxResults(1);

        $sum = $qb->getQuery()->getSingleScalarResult();

        return (int) ($sum ?? 0);
    }
}
