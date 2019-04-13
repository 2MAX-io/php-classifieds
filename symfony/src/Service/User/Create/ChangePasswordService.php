<?php

declare(strict_types=1);

namespace App\Service\User\Create;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ChangePasswordService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EncodePasswordService
     */
    private $encodePasswordService;

    public function __construct(EntityManagerInterface $em, EncodePasswordService $encodePasswordService)
    {
        $this->em = $em;
        $this->encodePasswordService = $encodePasswordService;
    }

    public function changePassword(User $user, string $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $this->encodePasswordService->setEncodedPassword($user, $newPassword);

        $this->em->persist($user);
    }
}
