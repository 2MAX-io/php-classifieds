<?php

declare(strict_types=1);

namespace App\Service\Admin\Listing\Dto;

use App\Entity\Listing;
use Pagerfanta\Pagerfanta;

class AdminListingListDto
{
    /**
     * @var iterable|Listing[]
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @var null|int
     */
    private $currentPage;

    /**
     * @var null|string
     */
    private $filterBySearchQuery;

    /**
     * @var null|string
     */
    private $filterByUser;

    /**
     * @var null|string
     */
    private $filterByCategory;

    /**
     * @var null|string
     */
    private $filterByPublicDisplay;

    /**
     * @var null|string
     */
    private $filterByAdminActivated;

    /**
     * @var null|string
     */
    private $filterByAdminRejected;

    /**
     * @var null|string
     */
    private $filterByAdminRemoved;

    /**
     * @var null|string
     */
    private $filterByUserDeactivated;

    /**
     * @var null|string
     */
    private $filterByUserRemoved;

    /**
     * @var null|string
     */
    private $filterByFeatured;

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

    public function getCurrentPage(): ?int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(?int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function getFilterBySearchQuery(): ?string
    {
        return $this->filterBySearchQuery;
    }

    public function setFilterBySearchQuery(?string $filterBySearchQuery): void
    {
        $this->filterBySearchQuery = $filterBySearchQuery;
    }

    public function getFilterByAdminActivated(): ?string
    {
        return $this->filterByAdminActivated;
    }

    public function setFilterByAdminActivated(?string $filterByAdminActivated): void
    {
        $this->filterByAdminActivated = $filterByAdminActivated;
    }

    public function getFilterByAdminRejected(): ?string
    {
        return $this->filterByAdminRejected;
    }

    public function setFilterByAdminRejected(?string $filterByAdminRejected): void
    {
        $this->filterByAdminRejected = $filterByAdminRejected;
    }

    public function getFilterByAdminRemoved(): ?string
    {
        return $this->filterByAdminRemoved;
    }

    public function setFilterByAdminRemoved(?string $filterByAdminRemoved): void
    {
        $this->filterByAdminRemoved = $filterByAdminRemoved;
    }

    public function getFilterByUserDeactivated(): ?string
    {
        return $this->filterByUserDeactivated;
    }

    public function setFilterByUserDeactivated(?string $filterByUserDeactivated): void
    {
        $this->filterByUserDeactivated = $filterByUserDeactivated;
    }

    public function getFilterByUserRemoved(): ?string
    {
        return $this->filterByUserRemoved;
    }

    public function setFilterByUserRemoved(?string $filterByUserRemoved): void
    {
        $this->filterByUserRemoved = $filterByUserRemoved;
    }

    public function getFilterByFeatured(): ?string
    {
        return $this->filterByFeatured;
    }

    public function setFilterByFeatured(?string $filterByFeatured): void
    {
        $this->filterByFeatured = $filterByFeatured;
    }

    public function getFilterByCategory(): ?string
    {
        return $this->filterByCategory;
    }

    public function setFilterByCategory(?string $filterByCategory): void
    {
        $this->filterByCategory = $filterByCategory;
    }

    public function getFilterByUser(): ?string
    {
        return $this->filterByUser;
    }

    public function setFilterByUser(?string $filterByUser): void
    {
        $this->filterByUser = $filterByUser;
    }

    /**
     * @param iterable|Listing[] $results
     */
    public function setResults(iterable $results): void
    {
        $this->results = $results;
    }

    public function setPager(Pagerfanta $pager): void
    {
        $this->pager = $pager;
    }

    public function getFilterByPublicDisplay(): ?string
    {
        return $this->filterByPublicDisplay;
    }

    public function setFilterByPublicDisplay(?string $filterByPublicDisplay): void
    {
        $this->filterByPublicDisplay = $filterByPublicDisplay;
    }
}
