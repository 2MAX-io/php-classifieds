<?php

declare(strict_types=1);

namespace App\Service\User\Settings;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserSettingsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
    }
}
