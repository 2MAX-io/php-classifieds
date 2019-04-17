<?php

namespace App\Service\User\Listing;

use App\Entity\Listing;
use Pagerfanta\Pagerfanta;

class UserListingListDto
{
    /**
     * @var \Traversable
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    public function __construct(\Traversable $results, Pagerfanta $pager)
    {
        $this->pager = $pager;
        $this->results = $results;
    }

    /**
     * @return \Traversable|Listing[]
     */
    public function getResults(): \Traversable
    {
        return $this->results;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }
}
