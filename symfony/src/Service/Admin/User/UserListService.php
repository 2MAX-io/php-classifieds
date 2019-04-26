<?php

declare(strict_types=1);

namespace App\Service\Admin\User;

use App\Entity\User;
use App\Service\System\Pagination\PaginationDto;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class UserListService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUserList(int $page): PaginationDto
    {
        $qb = $this->em->getRepository(User::class)->createQueryBuilder('user');
        $qb->orderBy('user.id', 'DESC');

        $adapter = new DoctrineORMAdapter($qb, true, $qb->getDQLPart('having') !== null);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($page);

        $paginationDto = new PaginationDto($pager->getCurrentPageResults(), $pager);

        return $paginationDto;
    }
}
