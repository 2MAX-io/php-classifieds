<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ChangeEmailService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function changeEmail(User $user, string $newEmail)
    {
        $user->setEmail($newEmail);

        $this->em->persist($user);
    }
}
