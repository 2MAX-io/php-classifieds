<?php

declare(strict_types=1);

namespace App\Service\User\Account;

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

    public function confirmRegistration(User $user): void
    {
        $user->setEnabled(true);

        $this->em->persist($user);
    }
}
