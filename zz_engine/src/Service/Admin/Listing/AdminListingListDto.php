<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing;

use App\Entity\Listing;
use Pagerfanta\Pagerfanta;

class AdminListingListDto
{
    /**
     * @var Listing[]|iterable
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    public function __construct(iterable $results, Pagerfanta $pager)
    {
        $this->pager = $pager;
        $this->results = $results;
    }

    /**
     * @return iterable|Listing[]
     */
    public function getResults(): iterable
    {
        return $this->results;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }
}
