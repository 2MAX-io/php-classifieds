<?php

declare(strict_types=1);

namespace App\Service\User\Listing;

use App\Entity\Listing;
use App\Security\CurrentUserService;
use Doctrine\ORM\EntityManagerInterface;

class UserListingListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(EntityManagerInterface $em, CurrentUserService $currentUserService)
    {
        $this->em = $em;
        $this->currentUserService = $currentUserService;
    }

    /**
     * @return Listing[]
     */
    public function getList(): array
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');
        $qb->andWhere($qb->expr()->eq('listing.user', ':user'));
        $qb->setParameter(':user', $this->currentUserService->getUser());

        $qb->orderBy('listing.id', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
