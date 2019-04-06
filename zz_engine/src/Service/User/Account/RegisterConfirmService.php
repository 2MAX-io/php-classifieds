<?php

declare(strict_types=1);

namespace App\Service\User\Account;

use App\Entity\User;
use App\Form\User\Account\Register\RegisterUserDto;
use Doctrine\ORM\EntityManagerInterface;

class RegisterConfirmService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CreateUserService
     */
    private $createUserService;

    public function __construct(CreateUserService $createUserService, EntityManagerInterface $em)
    {
        $this->createUserService = $createUserService;
        $this->em = $em;
    }

    public function confirmRegistration(User $user): void
    {
        $user->setEnabled(true);

        $this->em->persist($user);
    }

    public function resendRegisterConfirm(RegisterUserDto $registerUserDto): void
    {
        $qb = $this->em->createQueryBuilder();
        $qb->from(User::class, 'user');
        $qb->select('user');
        $qb->andWhere($qb->expr()->eq('user.username', ':username'));
        $qb->setParameter(':username', $registerUserDto->getEmail());

        $qb->andWhere($qb->expr()->eq('user.enabled', 0));
        $qb->andWhere($qb->expr()->isNull('user.lastLogin'));

        $user = $qb->getQuery()->getOneOrNullResult();
        if (null === $user) {
            return;
        }

        $this->createUserService->setPasswordAndSendConfirmation($user, $registerUserDto);
    }
}
