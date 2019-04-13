<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RegisterConfirmService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function confirmRegistration(string $token): void
    {
        $qb = $this->em->getRepository(User::class)->createQueryBuilder('user');
        $qb->andWhere($qb->expr()->eq('user.confirmationToken', ':confirmationToken'));
        $qb->setParameter(':confirmationToken', $token);

        /** @var User $user */
        $user = $qb->getQuery()->getOneOrNullResult();

        if ($user !== null) {
            if ($user->getEnabled()) {

            } else {
                $user->setEnabled(true);
                $this->em->flush();
            }
        } else {

        }
    }
}
