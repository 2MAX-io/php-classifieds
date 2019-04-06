<?php

declare(strict_types=1);

namespace App\Service\User\Listing\Dto;

use App\Entity\Listing;
use Pagerfanta\Pagerfanta;

class UserListingListDto
{
    /**
     * @var iterable|Listing[]
     */
    private $listings;

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @param iterable|Listing[] $listings
     */
    public function __construct(iterable $listings, Pagerfanta $pager)
    {
        $this->pager = $pager;
        $this->listings = $listings;
    }

    /**
     * @return iterable|Listing[]
     */
    public function getListings(): iterable
    {
        return $this->listings;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }
}
