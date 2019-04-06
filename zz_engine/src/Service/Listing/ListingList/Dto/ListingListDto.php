<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList\Dto;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\User;
use Pagerfanta\Pagerfanta;

class ListingListDto
{
    /**
     * @var iterable|Listing[]
     */
    private $results = [];

    /**
     * @var int
     */
    private $resultsCount;

    /**
     * @var string
     */
    private $route;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var Pagerfanta|null
     */
    private $pager;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var bool
     */
    private $lastAddedList = false;

    /**
     * @var int|null
     */
    private $redirectToPageNumber;

    /**
     * @var CustomField[]
     */
    private $categoryCustomFields;

    /**
     * @var User|null
     */
    private $filterByUser;

    /**
     * @var string|null
     */
    private $categorySlug;

    /**
     * @var string|null
     */
    private $redirectToRoute;

    /**
     * @var string|null
     */
    private $minPrice;

    /**
     * @var string|null
     */
    private $maxPrice;

    /**
     * @var string|null
     */
    private $searchQuery;

    /**
     * @var array<int, array>
     */
    private $filterByCustomFields = [];

    /**
     * @var bool
     */
    private $showOnMap = false;

    /**
     * @var bool
     */
    private $mapFullWidth = false;

    /**
     * @var bool
     */
    private $filterByUserObservedListings = false;

    /**
     * @var bool
     */
    private $paginationEnabled = true;

    /**
     * @var int|null
     */
    private $maxResults;

    /**
     * @return iterable|Listing[]
     */
    public function getResults(): iterable
    {
        return $this->results;
    }

    /**
     * @param iterable|Listing[] $results
     */
    public function setResults(iterable $results): void
    {
        $this->results = $results;
    }

    public function getPager(): ?Pagerfanta
    {
        return $this->pager;
    }

    public function getPagerNotNull(): Pagerfanta
    {
        if (null === $this->pager) {
            throw new \RuntimeException('page not found');
        }

        return $this->pager;
    }

    public function setPager(?Pagerfanta $pager): void
    {
        $this->pager = $pager;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryNotNull(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }

    public function isLastAddedList(): bool
    {
        return $this->lastAddedList;
    }

    public function setLastAddedList(bool $lastAddedList): void
    {
        $this->lastAddedList = $lastAddedList;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function getRedirectToPageNumber(): ?int
    {
        return $this->redirectToPageNumber;
    }

    public function setRedirectToPageNumber(?int $redirectToPageNumber): void
    {
        $this->redirectToPageNumber = $redirectToPageNumber;
    }

    /**
     * @return CustomField[]
     */
    public function getCategoryCustomFields(): array
    {
        return $this->categoryCustomFields;
    }

    /**
     * @param CustomField[] $categoryCustomFields
     */
    public function setCategoryCustomFields(array $categoryCustomFields): void
    {
        $this->categoryCustomFields = $categoryCustomFields;
    }

    public function getFilterByUser(): ?User
    {
        return $this->filterByUser;
    }

    public function setFilterByUser(?User $filterByUser): void
    {
        $this->filterByUser = $filterByUser;
    }

    public function getCategorySlug(): ?string
    {
        return $this->categorySlug;
    }

    public function setCategorySlug(?string $categorySlug): void
    {
        $this->categorySlug = $categorySlug;
    }

    public function getRedirectToRoute(): ?string
    {
        return $this->redirectToRoute;
    }

    public function setRedirectToRoute(?string $redirectToRoute): void
    {
        $this->redirectToRoute = $redirectToRoute;
    }

    public function getMinPrice(): ?string
    {
        return $this->minPrice;
    }

    public function setMinPrice(?string $minPrice): void
    {
        $this->minPrice = $minPrice;
    }

    public function getMaxPrice(): ?string
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?string $maxPrice): void
    {
        $this->maxPrice = $maxPrice;
    }

    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    public function setSearchQuery(?string $searchQuery): void
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * @return array<int, array>
     */
    public function getFilterByCustomFields(): array
    {
        return $this->filterByCustomFields;
    }

    /**
     * @param array<int, array> $filterByCustomFields
     */
    public function setFilterByCustomFields(array $filterByCustomFields): void
    {
        $this->filterByCustomFields = $filterByCustomFields;
    }

    public function getShowOnMap(): bool
    {
        return $this->showOnMap;
    }

    public function setShowOnMap(bool $showOnMap): void
    {
        $this->showOnMap = $showOnMap;
    }

    public function getPaginationEnabled(): bool
    {
        return $this->paginationEnabled;
    }

    public function setPaginationEnabled(bool $paginationEnabled): void
    {
        $this->paginationEnabled = $paginationEnabled;
    }

    public function getMaxResults(): ?int
    {
        return $this->maxResults;
    }

    public function setMaxResults(?int $maxResults): void
    {
        $this->maxResults = $maxResults;
    }

    public function getMapFullWidth(): bool
    {
        return $this->mapFullWidth;
    }

    public function setMapFullWidth(bool $mapFullWidth): void
    {
        $this->mapFullWidth = $mapFullWidth;
    }

    public function getResultsCount(): int
    {
        return $this->resultsCount;
    }

    public function setResultsCount(int $resultsCount): void
    {
        $this->resultsCount = $resultsCount;
    }

    public function getFilterByUserObservedListings(): bool
    {
        return $this->filterByUserObservedListings;
    }

    public function setFilterByUserObservedListings(bool $filterByUserObservedListings): void
    {
        $this->filterByUserObservedListings = $filterByUserObservedListings;
    }
}
