<?php

declare(strict_types=1);

namespace App\Service\User\Settings;

use App\Entity\User;
use App\Form\User\Setting\Dto\UserSettingsDto;
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

    public function save(UserSettingsDto $userSettingsDto, User $user): void
    {
        $user->setDisplayUsername($userSettingsDto->getDisplayUsername());
        $this->em->persist($user);
    }
}
